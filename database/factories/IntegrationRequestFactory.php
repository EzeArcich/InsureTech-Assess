<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IntegrationRequestFactory extends Factory
{
    public function definition(): array
    {
        return ['id' => fake()->uuid(), 'credential_id' => null, 'http_status' => 401, 'outcome' => 'rejected', 'duration_ms' => 1];
    }
}
