<?php

namespace App\Services;

use App\Models\ClaimSimulationItem;
use App\Models\ClaimSimulationRun;
use App\Models\CompanyIntegrationCredential;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Random\Engine\Mt19937;
use Random\Randomizer;

class GenerateSimulatedClaims
{
    /** @return array<int,array<string,mixed>> */
    public function payloads(string $reason, int $quantity, int $seed, string $namespace): array
    {
        $rng = new Randomizer(new Mt19937($seed));
        $payloads = [];
        for ($i = 1; $i <= $quantity; $i++) {
            $number = $rng->getInt(100000, 999999);
            $payloads[] = ['external_id' => 'sim-'.strtolower($namespace).'-'.$i, 'claim_number' => 'SIM-'.$number.'-'.$i,
                'client_name' => 'SIMULADO '.['Alejandra', 'Bruno', 'Carolina', 'Diego'][$rng->getInt(0, 3)].' Asegurado',
                'client_email' => 'simulado.'.$number.'@example.test', 'phone' => '0000000000',
                'address' => 'Calle Ficticia '.$rng->getInt(1, 9999), 'city' => 'Ciudad Simulada',
                'license_plate' => 'AA'.str_pad((string) $rng->getInt(0, 999), 3, '0', STR_PAD_LEFT).'AA',
                'client_type' => 'Asegurado', 'inspection_mode' => 'foto', 'reason' => $reason,
                'notes' => 'SIMULACIÓN: datos ficticios sin efectos externos. '.($reason === 'Todo riesgo' ? 'Escenario de colisión.' : 'Escenario '.$reason.'.')];
        }

        return $payloads;
    }

    /** @param array<string,mixed> $data */
    public function handle(User $user, array $data): ClaimSimulationRun
    {
        return DB::transaction(function () use ($user, $data): ClaimSimulationRun {
            $credential = CompanyIntegrationCredential::whereKey($data['credential_id'])->where('is_simulation', true)->where('active', true)->whereNull('revoked_at')->firstOrFail();
            abort_unless($credential->simulator_token && (! $credential->expires_at || $credential->expires_at->isFuture()), 422, 'La credencial del laboratorio no está disponible.');
            $seed = $data['seed'] ?? random_int(0, 2147483647);
            $run = ClaimSimulationRun::create(['user_id' => $user->id, 'credential_id' => $credential->id, 'reason' => $data['reason'], 'seed' => $seed]);
            foreach ($this->payloads($data['reason'], (int) $data['quantity'], $seed, (string) Str::uuid()) as $i => $payload) {
                ClaimSimulationItem::create(['run_id' => $run->id, 'position' => $i + 1, 'payload' => $payload]);
            }

            return $run;
        });
    }
}
