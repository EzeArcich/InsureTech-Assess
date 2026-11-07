<?php

namespace Database\Factories;

use App\Models\ClaimSimulationRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimSimulationItemFactory extends Factory
{
    public function definition(): array
    {
        return ['run_id' => ClaimSimulationRun::factory(), 'position' => 1, 'payload' => ['external_id' => 'test-'.fake()->uuid(), 'claim_number' => 'SIM-001', 'client_name' => 'SIMULADO Asegurado'], 'status' => 'preview'];
    }
}
