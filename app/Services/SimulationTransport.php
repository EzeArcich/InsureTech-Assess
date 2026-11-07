<?php

namespace App\Services;

use App\Models\CompanyIntegrationCredential;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SimulationTransport
{
    public function ensureEnabled(): void
    {
        abort_unless(app()->environment(['local', 'testing']) && config('claim_integrations.simulator_enabled'), 404);
    }

    public function endpoint(): string
    {
        $origin = rtrim((string) config('claim_integrations.simulator_origin'), '/');
        $url = parse_url($origin);
        abort_unless(is_array($url) && ($url['scheme'] ?? null) === 'http' && in_array($url['host'] ?? null, ['127.0.0.1', '[::1]'], true)
            && ! isset($url['user']) && ! isset($url['pass']) && ! isset($url['query']) && ! isset($url['fragment']) && empty($url['path']), 422, 'El simulador sólo permite un destino HTTP local de loopback.');

        return $origin.'/api/v1/integrations/claims';
    }

    public function validateCredential(CompanyIntegrationCredential $credential): void
    {
        abort_unless($credential->is_simulation && $credential->active && ! $credential->revoked_at && $credential->simulator_token
            && in_array('claims:create', $credential->abilities ?? [], true)
            && (! $credential->expires_at || $credential->expires_at->isFuture()), 422, 'Credencial del laboratorio no disponible.');
    }

    /** @param array<string,mixed> $payload
     * @return array{status:string,http_status:?int,response:array<string,mixed>,retryable:bool,claim_id:?int}
     */
    public function send(CompanyIntegrationCredential $credential, array $payload): array
    {
        $this->ensureEnabled();
        $endpoint = $this->endpoint();
        $this->validateCredential($credential);
        try {
            $response = Http::acceptJson()->asJson()->withToken($credential->simulator_token)->connectTimeout(2)
                ->timeout(min(15, max(1, (int) config('claim_integrations.engine.http_timeout', 8))))
                ->withOptions(['allow_redirects' => false, 'proxy' => ''])->post($endpoint, $payload);
            $body = $response->json();
            $valid = $response->successful() && is_array($body) && (int) data_get($body, 'data.company_id') === $credential->company_id
                && data_get($body, 'data.external_id') === $payload['external_id'] && is_int(data_get($body, 'data.id'));
            $status = $valid ? (data_get($body, 'meta.replayed') === true ? 'duplicate' : 'created') : 'rejected';
            // Keep only the receipt identifiers; never persist arbitrary upstream content or secrets.
            $safe = $valid ? ['data' => ['id' => $body['data']['id'], 'company_id' => $credential->company_id, 'external_id' => $payload['external_id']],
                'meta' => ['replayed' => $status === 'duplicate']] : ['message' => 'La API no confirmó un recibo válido.'];

            return ['status' => $status, 'http_status' => $response->status(), 'response' => $safe,
                'retryable' => in_array($response->status(), [408, 425, 429], true) || $response->serverError() || ($response->successful() && ! $valid),
                'claim_id' => $valid ? $body['data']['id'] : null];
        } catch (ConnectionException) {
            return ['status' => 'transport_error', 'http_status' => null, 'response' => ['message' => 'No se pudo confirmar el envío HTTP; se conserva external_id.'], 'retryable' => true, 'claim_id' => null];
        }
    }
}
