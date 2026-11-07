<?php

namespace Tests\Feature;

use App\Models\ClaimSimulationItem;
use App\Models\ClaimSimulationRun;
use App\Models\Company;
use App\Models\SimulationProfile;
use App\Models\User;
use App\Services\IssueCompanyIntegrationCredential;
use App\Services\SimulationEngine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AutomaticSimulationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function start(array $overrides = []): ClaimSimulationRun
    {
        config(['claim_integrations.simulator_enabled' => true, 'claim_integrations.simulator_origin' => 'http://127.0.0.1:8001']);
        $issued = app(IssueCompanyIntegrationCredential::class)->handle(Company::factory()->create(), 'Automatic laboratory', true);
        $profile = SimulationProfile::factory()->create(['credential_id' => $issued['credential']->id]);
        $profile->update(['configuration' => array_replace($profile->configuration, $overrides)]);

        return app(SimulationEngine::class)->start($profile, User::factory()->create());
    }

    private function send(ClaimSimulationItem $item): void
    {
        app(SimulationEngine::class)->send($item->id, $item->reservation);
    }

    public function test_planning_is_persisted_idempotent_and_snapshot_survives_profile_changes(): void
    {
        $this->freezeTime();
        $run = $this->start();
        $run->profile->update(['configuration' => array_replace($run->configuration, ['rate_per_hour' => 60])]);
        $engine = app(SimulationEngine::class);
        $this->assertSame(1, $engine->tick());
        $this->assertSame(0, $engine->tick());
        $this->assertSame(12, $run->fresh()->configuration['rate_per_hour']);
        $this->assertDatabaseCount('claim_simulation_items', 1);
        $this->assertDatabaseCount('jobs', 1);
        $this->assertDatabaseCount('claims', 0);
        $this->assertSame(now()->addSeconds(300)->toDateTimeString(), $run->fresh()->next_dispatch_at->toDateTimeString());
        $this->assertSame('queued', $run->items()->first()->status);
    }

    public function test_pause_resume_stop_and_duration_expiry_preserve_history_and_prevent_sends(): void
    {
        $this->freezeTime();
        $run = $this->start();
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        $payload = $item->payload;
        Http::preventStrayRequests();
        $engine->transition($run, 'pause');
        $engine->transition($run, 'pause');
        $this->send($item);
        Http::assertNothingSent();
        $this->assertSame('retry', $item->fresh()->status);
        $engine->transition($run, 'resume');
        $this->assertSame(1, $engine->tick());
        $engine->transition($run, 'stop');
        $engine->transition($run, 'stop');
        $this->assertSame('cancelled', $item->fresh()->status);
        $this->assertSame($payload, $item->fresh()->payload);
        $this->assertSame('stopped', $run->fresh()->status);
        $other = $this->start(['duration_minutes' => 1]);
        $engine->tick();
        $this->travel(2)->minutes();
        $engine->tick();
        $this->assertSame('completed', $other->fresh()->status);
        $this->assertSame('cancelled', $other->items()->first()->status);
    }

    public function test_transient_http_retry_keeps_identical_payload_and_duplicate_receipt(): void
    {
        $this->freezeTime();
        $run = $this->start();
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => Http::sequence()->pushStatus(503)->push([
            'data' => ['id' => 321, 'company_id' => $run->credential->company_id, 'external_id' => $item->external_id], 'meta' => ['replayed' => true],
        ], 200)]);
        $this->send($item);
        $this->assertSame('retry', $item->fresh()->status);
        $this->travel(11)->seconds();
        $engine->tick();
        $this->send($item->fresh());
        $this->assertSame('duplicate', $item->fresh()->status);
        $this->assertSame(321, (int) $item->fresh()->claim_id);
        $this->assertSame(2, $item->fresh()->attempts);
        $this->assertSame(['rejected', 'duplicate'], DB::table('simulation_event_attempts')->orderBy('id')->pluck('outcome')->all());
        Http::assertSentCount(2);
        foreach (Http::recorded() as [$request]) {
            $this->assertSame($item->payload, $request->data());
        }
    }

    public function test_connection_failure_retries_but_validation_failure_is_terminal(): void
    {
        $this->freezeTime();
        $run = $this->start();
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => Http::sequence()->pushFailedConnection()->push(['message' => 'Invalid'], 422)]);
        $this->send($item);
        $this->assertSame('retry', $item->fresh()->status);
        Http::assertSentCount(1);
        $this->travel(11)->seconds();
        $engine->tick();
        $this->send($item->fresh());
        $this->assertSame('rejected', $item->fresh()->status);
        $this->travel(1)->minutes();
        $this->assertSame(0, $engine->tick());
        Http::assertSentCount(2);
    }

    public function test_expired_reservations_recover_after_restart_without_replaying_missed_intervals(): void
    {
        $this->freezeTime();
        $run = $this->start(['duration_minutes' => null]);
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        $oldReservation = $item->reservation;
        $this->travel(3)->minutes();
        $this->assertSame(1, $engine->tick());
        $this->assertNotSame($oldReservation, $item->fresh()->reservation);
        Http::preventStrayRequests();
        $engine->send($item->id, $oldReservation);
        Http::assertNothingSent();
        $item->update(['status' => 'processing', 'attempts' => 1, 'reserved_until' => now()->subSecond()]);
        DB::table('simulation_event_attempts')->insert(['item_id' => $item->id, 'number' => 1, 'outcome' => 'processing', 'started_at' => now()->subMinute()]);
        $this->travel(2)->hours();
        $engine->tick();
        $this->assertSame(2, $run->fresh()->planned_count);
        $this->assertSame('unknown', DB::table('simulation_event_attempts')->value('outcome'));
        $this->assertSame($item->payload, $item->fresh()->payload);
        $this->assertSame('queued', $item->fresh()->status);
    }

    public function test_multi_insurer_isolation_cycle_pending_and_hourly_limits(): void
    {
        $this->freezeTime();
        $first = $this->start(['max_per_hour' => 1, 'max_pending' => 1]);
        $second = $this->start();
        $engine = app(SimulationEngine::class);
        config(['claim_integrations.engine.cycle_limit' => 1]);
        $this->assertSame(1, $engine->tick());
        $this->assertSame(1, $engine->tick());
        $this->assertSame(0, $engine->tick());
        $a = $first->items()->first();
        $b = $second->items()->first();
        $this->assertNotSame($a->external_id, $b->external_id);
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => Http::response([
            'data' => ['id' => 10, 'company_id' => $first->credential->company_id, 'external_id' => $a->external_id],
        ], 201)]);
        $this->send($a);
        $this->send($b);
        $this->assertSame('created', $a->fresh()->status);
        $this->assertSame('retry', $b->fresh()->status);
        Http::assertSentCount(2);
        $this->travel(6)->minutes();
        $engine->tick();
        $this->assertSame(1, $first->items()->count());
    }

    public function test_retry_exhaustion_and_revoked_credential_are_bounded_without_secrets(): void
    {
        $this->freezeTime();
        $run = $this->start();
        config(['claim_integrations.engine.max_attempts' => 1]);
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => Http::failedConnection()]);
        $this->send($item);
        $this->assertSame('failed', $item->fresh()->status);
        Http::assertSentCount(1);
        $other = $this->start();
        $engine->tick();
        $other->credential->update(['active' => false]);
        $this->send($other->items()->first());
        $this->assertSame('rejected', $other->items()->first()->status);
        $this->assertStringNotContainsString($run->credential->simulator_token, json_encode($item->fresh()->toArray()));
    }

    public function test_concurrent_admission_and_duplicate_job_delivery_send_only_once(): void
    {
        $this->freezeTime();
        $run = $this->start(['max_concurrency' => 1]);
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $run->refresh()->update(['next_dispatch_at' => now()]);
        $engine->tick();
        [$first, $second] = $run->items()->get()->all();
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => function () use ($engine, $first, $second, $run) {
            $engine->send($second->id, $second->reservation);

            return Http::response(['data' => ['id' => 987, 'company_id' => $run->credential->company_id, 'external_id' => $first->external_id]], 201);
        }]);
        $this->send($first);
        $this->send($first);
        Http::assertSentCount(1);
        $this->assertSame('created', $first->fresh()->status);
        $this->assertSame('retry', $second->fresh()->status);
        $this->assertSame(0, $second->fresh()->attempts);
        $this->assertDatabaseCount('simulation_event_attempts', 1);
    }

    public function test_daily_limits_and_stop_during_http_do_not_leave_retryable_work(): void
    {
        $this->freezeTime();
        $run = $this->start(['daily_limit' => 1]);
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        Http::preventStrayRequests();
        Http::fake(['http://127.0.0.1:8001/api/v1/integrations/claims' => function () use ($engine, $run) {
            $engine->transition($run, 'stop');

            return Http::response([], 503);
        }]);
        $this->send($item);
        $this->assertSame('failed', $item->fresh()->status);
        $this->assertSame('stopped', $run->fresh()->status);
        $this->travel(10)->minutes();
        $this->assertSame(0, $engine->tick());
        Http::assertSentCount(1);
        $other = $this->start(['daily_limit' => 1]);
        $engine->tick();
        $this->travel(6)->minutes();
        $engine->tick();
        $this->assertSame(1, $other->items()->count());
    }

    public function test_stopped_in_flight_work_is_closed_as_unknown_after_worker_crash(): void
    {
        $this->freezeTime();
        $run = $this->start();
        $engine = app(SimulationEngine::class);
        $engine->tick();
        $item = $run->items()->first();
        $item->update(['status' => 'processing', 'attempts' => 1, 'reserved_until' => now()->addMinute()]);
        DB::table('simulation_event_attempts')->insert(['item_id' => $item->id, 'number' => 1, 'outcome' => 'processing', 'started_at' => now()]);
        $engine->transition($run, 'stop');
        $finished = $run->fresh()->finished_at->toDateTimeString();
        $this->travel(2)->minutes();
        $engine->tick();
        $this->assertSame('failed', $item->fresh()->status);
        $this->assertSame('unknown', DB::table('simulation_event_attempts')->value('outcome'));
        $this->assertDatabaseCount('jobs', 0);
        $this->assertSame($finished, $run->fresh()->finished_at->toDateTimeString());
    }
}
