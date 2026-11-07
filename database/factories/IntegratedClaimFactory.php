<?php

namespace Database\Factories;

use App\Models\Claim;
use App\Models\CompanyIntegrationCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntegratedClaimFactory extends Factory
{
    public function definition(): array
    {
        return ['claim_id' => Claim::factory(), 'company_id' => fn (array $a): int => Claim::findOrFail($a['claim_id'])->company_id,
            'credential_id' => fn (array $a): int => CompanyIntegrationCredential::factory()->create(['company_id' => $a['company_id']])->id,
            'external_id' => 'test-'.fake()->uuid(), 'payload_hash' => str_repeat('a', 64), 'response_snapshot' => []];
    }
}
