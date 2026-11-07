<?php

namespace Database\Factories;

use App\Models\CompanyIntegrationCredential;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimSimulationRunFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'credential_id' => CompanyIntegrationCredential::factory(), 'reason' => 'Todo riesgo', 'seed' => 42, 'status' => 'preview'];
    }
}
