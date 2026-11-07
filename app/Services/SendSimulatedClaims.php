<?php

namespace App\Services;

use App\Models\ClaimSimulationRun;

class SendSimulatedClaims
{
    public function __construct(private SimulationTransport $transport) {}

    public function endpoint(): string
    {
        return $this->transport->endpoint();
    }

    public function handle(ClaimSimulationRun $run): ClaimSimulationRun
    {
        abort_unless(app()->environment(['local', 'testing']) && config('claim_integrations.simulator_enabled'), 404);
        $endpoint = $this->endpoint();
        $credential = $run->credential;
        abort_unless($credential->is_simulation && $credential->active && ! $credential->revoked_at && $credential->simulator_token
            && (! $credential->expires_at || $credential->expires_at->isFuture()), 422, 'Credencial del laboratorio no disponible.');
        $started = ClaimSimulationRun::whereKey($run->id)->where('status', 'preview')->update(['status' => 'sending', 'started_at' => now()]);
        abort_unless($started === 1, 409, 'Este lote ya fue enviado o está en ejecución.');
        foreach ($run->items as $item) {
            $result = $this->transport->send($credential, $item->payload);
            $item->update(['status' => $result['status'], 'http_status' => $result['http_status'], 'response' => $result['response'], 'sent_at' => now()]);
        }
        $run->update(['status' => 'finished', 'finished_at' => now()]);

        return $run->fresh(['items']);
    }
}
