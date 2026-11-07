<?php

namespace App\Services;

use App\Models\ClaimSimulationRun;

class SimulationBehavior
{
    public function rate(ClaimSimulationRun $run): float
    {
        $config = $run->configuration;
        $rate = (float) $config['rate_per_hour'];
        if (in_array($config['scenario'], ['peak', 'weather'], true) && now()->lt($run->started_at->copy()->addMinutes($config['peak_minutes']))) {
            $rate *= $config['peak_multiplier'];
        }

        return min($rate, (float) $config['max_per_hour']);
    }

    public function interval(ClaimSimulationRun $run, int $position): int
    {
        $seconds = 3600 / $this->rate($run);
        if ($run->configuration['distribution'] === 'exponential') {
            $seconds *= -log(1 - $this->uniform($run, $position, 'interval'));
        }

        return max(1, min(604800, (int) ceil($seconds)));
    }

    /** @return array<string,mixed> */
    public function payload(ClaimSimulationRun $run, int $position, GenerateSimulatedClaims $generator): array
    {
        $weights = $run->configuration['reasons'];
        $ticket = $this->uniform($run, $position, 'reason') * array_sum($weights);
        $reason = 'Todo riesgo';
        foreach ($weights as $candidate => $weight) {
            $ticket -= $weight;
            if ($weight > 0 && $ticket < 0) {
                $reason = $candidate;
                break;
            }
        }
        if ($run->configuration['scenario'] === 'weather') {
            $reason = 'Todo riesgo';
        }
        $payload = $generator->payloads($reason, 1, ($run->seed + $position) % 2147483647, 'auto-'.$run->id.'-'.$position)[0];
        $payload['claim_number'] = $run->credential->company->prefix.'-SIM-'.$run->id.'-'.$position;
        if ($run->configuration['scenario'] === 'weather') {
            $payload['notes'] .= ' Evento climático ficticio: granizo.';
        }

        return $payload;
    }

    public function fault(ClaimSimulationRun $run, int $position, int $attempt): ?string
    {
        $config = $run->configuration;
        if (! config('claim_integrations.engine.faults_enabled') || $config['scenario'] !== 'communication' || $attempt !== 1) {
            return null;
        }

        return $this->uniform($run, $position, 'fault') * 100 < $config['fault_percent'] ? $config['fault_type'] : null;
    }

    private function uniform(ClaimSimulationRun $run, int $position, string $purpose): float
    {
        // Independent streams keep a seed reproducible without sharing mutable random state.
        $number = hexdec(substr(hash('sha256', $run->seed.':'.$position.':'.$purpose), 0, 8));

        return ($number + 0.5) / 4294967296;
    }
}
