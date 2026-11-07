<?php

namespace Database\Factories;

use App\Models\CompanyIntegrationCredential;
use App\Models\SimulationProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SimulationProfile> */
class SimulationProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'credential_id' => CompanyIntegrationCredential::factory(),
            'name' => 'Laboratorio de prueba',
            'enabled' => true,
            'configuration' => [
                'scenario' => 'normal', 'rate_per_hour' => 12, 'duration_minutes' => 60,
                'max_per_hour' => 30, 'max_pending' => 10, 'max_concurrency' => 1,
                'daily_limit' => 100, 'seed' => 42, 'reasons' => ['Todo riesgo' => 100],
                'distribution' => 'fixed', 'peak_multiplier' => 2, 'peak_minutes' => 15,
                'fault_percent' => 0, 'fault_type' => 'lost_response',
            ],
        ];
    }
}
