<?php

namespace App\Services;

use App\Jobs\SendSimulationEvent;
use App\Models\ClaimSimulationItem;
use App\Models\ClaimSimulationRun;
use App\Models\Company;
use App\Models\SimulationProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class SimulationEngine
{
    private const OPEN = ['pending', 'queued', 'processing', 'retry'];

    public function __construct(private SimulationTransport $transport, private GenerateSimulatedClaims $generator, private SimulationBehavior $behavior) {}

    public function start(SimulationProfile $profile, User $user): ClaimSimulationRun
    {
        $this->transport->ensureEnabled();
        $this->transport->endpoint();

        return DB::transaction(function () use ($profile, $user): ClaimSimulationRun {
            // Profiles are disabled rather than deleted; the oldest row serializes global admission.
            SimulationProfile::orderBy('id')->lockForUpdate()->firstOrFail();
            $profile = SimulationProfile::whereKey($profile->id)->lockForUpdate()->firstOrFail();
            abort_unless($profile->enabled, 422, 'El perfil está deshabilitado.');
            $this->transport->validateCredential($profile->credential);
            $config = $profile->configuration;
            abort_unless(ClaimSimulationRun::whereNotNull('profile_id')->whereIn('status', ['pending', 'running', 'paused'])->count() < 100, 422, 'Límite de ejecuciones activas alcanzado.');

            return ClaimSimulationRun::create(['profile_id' => $profile->id, 'credential_id' => $profile->credential_id, 'user_id' => $user->id,
                'configuration' => $config, 'reason' => array_key_first($config['reasons']), 'seed' => $config['seed'] ?? random_int(0, 2147483647),
                'status' => 'pending', 'started_at' => now(), 'ends_at' => empty($config['duration_minutes']) ? null : now()->addMinutes($config['duration_minutes']),
                'next_dispatch_at' => now()]);
        }, 5);
    }

    public function transition(ClaimSimulationRun $run, string $action): ClaimSimulationRun
    {
        $this->transport->ensureEnabled();
        abort_unless(in_array($action, ['pause', 'resume', 'stop'], true), 422);

        return DB::transaction(function () use ($run, $action): ClaimSimulationRun {
            $run = ClaimSimulationRun::whereKey($run->id)->lockForUpdate()->firstOrFail();
            abort_unless($run->profile_id, 404);
            $target = ['pause' => 'paused', 'resume' => 'running', 'stop' => 'stopped'][$action];
            if ($run->status === $target) {
                return $run;
            }
            abort_unless(in_array($run->status, ['pending', 'running', 'paused'], true), 409, 'La ejecución ya finalizó.');
            if ($action === 'resume') {
                abort_unless($run->status === 'paused', 409, 'La ejecución no está pausada.');
                if ($run->ends_at && $run->ends_at->lte(now())) {
                    $this->finish($run, 'completed');

                    return $run->fresh();
                }
                $this->removeQueuedJobs($run);
                $run->items()->whereIn('status', ['queued', 'pending', 'retry'])->update(['status' => 'retry', 'available_at' => now(), 'reservation' => null, 'reserved_until' => null]);
                $run->next_dispatch_at = now()->addSeconds($this->behavior->interval($run, $run->planned_count + 1));
            }
            if ($action === 'stop') {
                $this->finish($run, 'stopped');
            } else {
                $run->status = $target;
                $run->save();
            }

            return $run->fresh();
        }, 5);
    }

    public function tick(): int
    {
        $this->transport->ensureEnabled();
        $this->transport->endpoint();
        $dispatched = 0;
        $limit = min(100, max(1, (int) config('claim_integrations.engine.cycle_limit', 20)));
        $ids = ClaimSimulationRun::whereNotNull('profile_id')->where(function ($query): void {
            $query->whereIn('status', ['pending', 'running', 'paused'])->orWhereHas('items', fn ($items) => $items->where('status', 'processing')->where('reserved_until', '<=', now()));
        })
            ->orderBy('next_dispatch_at')->orderBy('id')->limit(100)->pluck('id');
        foreach ($ids as $id) {
            $dispatched += DB::transaction(function () use ($id, $limit, $dispatched): int {
                $run = ClaimSimulationRun::whereKey($id)->lockForUpdate()->firstOrFail();
                $this->recover($run);
                if (! in_array($run->status, ['pending', 'running', 'paused'], true)) {
                    $this->finish($run, $run->status);

                    return 0;
                }
                if ($run->ends_at && $run->ends_at->lte(now())) {
                    $this->finish($run, 'completed');

                    return 0;
                }
                if ($run->status === 'paused') {
                    return 0;
                }
                $run->update(['status' => 'running']);
                try {
                    $this->transport->validateCredential($run->credential);
                } catch (Throwable) {
                    $run->last_error = 'Credencial de laboratorio no disponible; ejecución finalizada.';
                    $run->save();
                    $this->finish($run, 'failed');

                    return 0;
                }
                if ($dispatched >= $limit) {
                    return 0;
                }
                $config = $run->configuration;
                if ($run->next_dispatch_at && $run->next_dispatch_at->lte(now())) {
                    $pending = $run->items()->whereIn('status', self::OPEN)->count();
                    $hour = $run->items()->where('scheduled_at', '>=', now()->subHour())->count();
                    $day = $run->items()->where('scheduled_at', '>=', now()->startOfDay())->count();
                    if ($pending < $config['max_pending'] && $hour < $config['max_per_hour'] && (empty($config['daily_limit']) || $day < $config['daily_limit'])) {
                        $position = $run->planned_count + 1;
                        $payload = $this->behavior->payload($run, $position, $this->generator);
                        $run->items()->create(['position' => $position, 'payload' => $payload, 'external_id' => $payload['external_id'], 'status' => 'pending', 'scheduled_at' => now(), 'available_at' => now()]);
                        $run->planned_count = $position;
                    }
                    // Never replay missed intervals: at most one new event per run per tick.
                    $run->next_dispatch_at = now()->addSeconds($this->behavior->interval($run, $run->planned_count + 1));
                    $run->save();
                }
                $items = $run->items()->whereIn('status', ['pending', 'retry'])->where('available_at', '<=', now())->limit($limit - $dispatched)->get();
                foreach ($items as $item) {
                    $reservation = (string) Str::uuid();
                    $item->update(['status' => 'queued', 'reservation' => $reservation, 'reserved_until' => now()->addSeconds(120)]);
                    // Database queue insertion shares this transaction: no commit/dispatch gap.
                    $jobId = Queue::connection('database')->push(new SendSimulationEvent($item->id, $reservation), '', 'simulations');
                    $item->update(['queue_job_id' => $jobId]);
                }

                return $items->count();
            }, 5);
        }

        return $dispatched;
    }

    private function recover(ClaimSimulationRun $run): void
    {
        foreach ($run->items()->whereIn('status', ['queued', 'processing'])->where('reserved_until', '<=', now())->get() as $item) {
            DB::table('jobs')->where('id', $item->queue_job_id)->delete();
            DB::table('simulation_event_attempts')->where('item_id', $item->id)->whereNull('finished_at')->update(['outcome' => 'unknown', 'finished_at' => now()]);
            $item->update(['status' => ! in_array($run->status, ['pending', 'running', 'paused'], true) || $item->attempts >= $this->maxAttempts() ? 'failed' : 'retry', 'reservation' => null, 'reserved_until' => null,
                'available_at' => now(), 'last_error' => 'Reserva vencida; resultado anterior desconocido.']);
        }
    }

    private function removeQueuedJobs(ClaimSimulationRun $run): void
    {
        DB::table('jobs')->whereIn('id', $run->items()->whereIn('status', ['pending', 'queued', 'retry'])->whereNotNull('queue_job_id')->select('queue_job_id'))->delete();
    }

    private function finish(ClaimSimulationRun $run, string $status): void
    {
        $this->removeQueuedJobs($run);
        $run->items()->whereIn('status', ['pending', 'queued', 'retry'])->update(['status' => 'cancelled', 'reservation' => null, 'reserved_until' => null]);
        $run->update(['status' => $status, 'next_dispatch_at' => null, 'finished_at' => $run->finished_at ?? now()]);
    }

    private function maxAttempts(): int
    {
        return min(8, max(1, (int) config('claim_integrations.engine.max_attempts', 4)));
    }

    public function send(int $itemId, string $reservation): void
    {
        $this->transport->ensureEnabled();
        $item = ClaimSimulationItem::findOrFail($itemId);
        $run = ClaimSimulationRun::with('credential')->findOrFail($item->run_id);
        $claimed = DB::transaction(function () use ($run, $itemId, $reservation): ?ClaimSimulationItem {
            // Serialize admission across runs of the same insurer, then lock run/item consistently.
            Company::whereKey($run->credential->company_id)->lockForUpdate()->firstOrFail();
            $run = ClaimSimulationRun::whereKey($run->id)->lockForUpdate()->firstOrFail();
            $item = ClaimSimulationItem::whereKey($itemId)->lockForUpdate()->firstOrFail();
            if ($item->status !== 'queued' || $item->reservation !== $reservation) {
                return null;
            }
            if (! in_array($run->status, ['pending', 'running'], true) || ($run->ends_at && $run->ends_at->lte(now()))) {
                $item->update(['status' => $run->status === 'paused' ? 'retry' : 'cancelled', 'reservation' => null, 'reserved_until' => null]);

                return null;
            }
            $config = $run->configuration;
            $companyRuns = ClaimSimulationRun::whereHas('credential', fn ($query) => $query->where('company_id', $run->credential->company_id))->select('id');
            $active = ClaimSimulationItem::whereIn('run_id', $companyRuns)->where('status', 'processing')->where('reserved_until', '>', now())->count();
            $attempts = DB::table('simulation_event_attempts')->join('claim_simulation_items', 'claim_simulation_items.id', '=', 'simulation_event_attempts.item_id')->where('claim_simulation_items.run_id', $run->id);
            $hour = (clone $attempts)->where('simulation_event_attempts.started_at', '>=', now()->subHour())->count();
            $day = (clone $attempts)->where('simulation_event_attempts.started_at', '>=', now()->startOfDay())->count();
            $runActive = $run->items()->where('status', 'processing')->where('reserved_until', '>', now())->count();
            $companyHour = DB::table('simulation_event_attempts')->join('claim_simulation_items', 'claim_simulation_items.id', '=', 'simulation_event_attempts.item_id')
                ->whereIn('claim_simulation_items.run_id', $companyRuns)->where('simulation_event_attempts.started_at', '>=', now()->subHour())->count();
            if ($runActive >= $config['max_concurrency'] || $active >= 3 || $companyHour >= 120 || $hour >= $config['max_per_hour'] || (! empty($config['daily_limit']) && $day >= $config['daily_limit'])) {
                $item->update(['status' => 'retry', 'available_at' => now()->addMinute(), 'reservation' => null, 'reserved_until' => null]);

                return null;
            }
            $item->update(['status' => 'processing', 'attempts' => $item->attempts + 1, 'reserved_until' => now()->addSeconds(60)]);
            DB::table('simulation_event_attempts')->insert(['item_id' => $item->id, 'number' => $item->attempts, 'outcome' => 'processing', 'started_at' => now()]);

            return $item;
        }, 5);
        if (! $claimed) {
            return;
        }
        try {
            $credential = $run->credential->fresh();
            $this->transport->validateCredential($credential);
            $fault = $this->behavior->fault($run, $claimed->position, $claimed->attempts);
            if (in_array($fault, ['timeout', 'transient', 'http_error'], true)) {
                $result = ['status' => 'simulated_'.$fault, 'http_status' => $fault === 'http_error' ? 503 : null,
                    'response' => ['message' => 'Falla de comunicación inyectada exclusivamente en el laboratorio.'], 'retryable' => true, 'claim_id' => null];
            } else {
                $result = $this->transport->send($credential, $claimed->payload);
                if (in_array($fault, ['lost_response', 'duplicate'], true) && in_array($result['status'], ['created', 'duplicate'], true)) {
                    $result = ['status' => 'simulated_'.$fault, 'http_status' => null,
                        'response' => ['message' => 'El envío real se realizó; se reintentará con el mismo ID para probar idempotencia.'], 'retryable' => true, 'claim_id' => null];
                }
            }
        } catch (Throwable $exception) {
            $result = ['status' => 'rejected', 'http_status' => null, 'response' => ['message' => 'No se pudo procesar el envío del laboratorio.'],
                'retryable' => ! $exception instanceof HttpExceptionInterface, 'claim_id' => null];
        }
        DB::transaction(function () use ($claimed, $run, $reservation, $result): void {
            $run = ClaimSimulationRun::whereKey($run->id)->lockForUpdate()->firstOrFail();
            $item = ClaimSimulationItem::whereKey($claimed->id)->lockForUpdate()->firstOrFail();
            if ($item->reservation !== $reservation || $item->status !== 'processing') {
                return;
            }
            $status = $result['status'];
            if ($result['retryable']) {
                $status = $item->attempts < $this->maxAttempts() ? 'retry' : 'failed';
                if (! in_array($run->status, ['pending', 'running', 'paused'], true)) {
                    $status = 'failed';
                }
            }
            $backoff = config('claim_integrations.engine.backoff', [10, 30, 60, 120]);
            $delay = min(3600, max(1, (int) ($backoff[min($item->attempts - 1, count($backoff) - 1)] ?? 60)));
            $item->update(['status' => $status, 'available_at' => now()->addSeconds($delay), 'reserved_until' => null, 'reservation' => null,
                'http_status' => $result['http_status'], 'response' => $result['response'], 'sent_at' => now(), 'claim_id' => $result['claim_id'],
                'last_error' => in_array($status, ['created', 'duplicate'], true) ? null : 'Envío no confirmado: '.$result['status']]);
            DB::table('simulation_event_attempts')->where('item_id', $item->id)->where('number', $item->attempts)->update(['outcome' => $result['status'], 'http_status' => $result['http_status'], 'finished_at' => now()]);
        }, 5);
    }
}
