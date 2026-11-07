<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyIntegrationCredential;
use App\Models\IntegratedClaim;
use Illuminate\Support\Facades\DB;

class ReceiveIntegrationClaim
{
    public function __construct(private SaveClaim $saveClaim) {}

    /** @param array<string,mixed> $payload
     * @return array{data:array<string,mixed>,replayed:bool,hash:string} */
    public function handle(CompanyIntegrationCredential $credential, array $payload): array
    {
        $payload = array_filter($payload, fn (mixed $value): bool => $value !== null);
        ksort($payload);
        $hash = hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

        return DB::transaction(function () use ($credential, $payload, $hash): array {
            $company = Company::whereKey($credential->company_id)->lockForUpdate()->firstOrFail();
            $current = CompanyIntegrationCredential::whereKey($credential->id)->firstOrFail();
            abort_unless($current->active && ! $current->revoked_at && (! $current->expires_at || $current->expires_at->isFuture()) && in_array('claims:create', $current->abilities ?? [], true), 403, 'Credencial revocada o sin autorización.');
            $existing = IntegratedClaim::where('company_id', $company->id)->where('external_id', $payload['external_id'])->first();
            if ($existing) {
                abort_unless(hash_equals($existing->payload_hash, $hash), 409, 'El identificador externo ya fue recibido con otros datos.');

                return ['data' => $existing->response_snapshot, 'replayed' => true, 'hash' => $hash];
            }
            $attributes = $payload;
            unset($attributes['external_id']);
            $claim = $this->saveClaim->handle([...$attributes, 'company_id' => $company->id, 'status' => 'Pendiente']);
            $data = ['id' => $claim->id, 'external_id' => $payload['external_id'], 'claim_number' => $claim->claim_number, 'company_id' => $company->id, 'status' => $claim->status, 'created_at' => $claim->created_at->toIso8601String()];
            IntegratedClaim::create(['company_id' => $company->id, 'credential_id' => $current->id, 'external_id' => $payload['external_id'], 'payload_hash' => $hash, 'claim_id' => $claim->id, 'response_snapshot' => $data]);

            return ['data' => $data, 'replayed' => false, 'hash' => $hash];
        }, 5);
    }
}
