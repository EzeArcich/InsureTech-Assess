<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyIntegrationCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompanyIntegrationCredential> */
class CompanyIntegrationCredentialFactory extends Factory
{
    public function definition(): array
    {
        return ['company_id' => Company::factory(), 'name' => 'Test integration', 'token_hash' => hash('sha256', fake()->uuid()), 'abilities' => ['claims:create'], 'active' => true];
    }
}
