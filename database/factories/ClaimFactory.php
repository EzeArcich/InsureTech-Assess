<?php

namespace Database\Factories;

use App\Models\Claim;
use App\Models\Client;
use App\Models\Company;
use App\Models\Inspector;
use App\Models\Producer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Claim> */
class ClaimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'company_id' => fn (array $attributes): int => Client::findOrFail($attributes['client_id'])->company_id,
            'inspector_id' => Inspector::factory(),
            'producer_id' => Producer::factory(),
            'claim_number' => function (array $attributes): string {
                $prefix = Company::findOrFail($attributes['company_id'])->prefix;

                return $prefix.$this->faker->unique()->numerify(str_repeat('#', $this->faker->numberBetween(8, 16)));
            },
            'license_plate' => $this->faker->regexify('[A-Z]{2}[0-9]{3}[A-Z]{2}'),
            'inspection_date' => $this->faker->dateTimeBetween('-2 months', '+1 month')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['Pendiente', 'Ausente', 'Coordinado', 'Peritando', 'IPgestion']),
            'client_type' => $this->faker->randomElement(['Asegurado', 'Tercero', 'Cotizacion']),
            'inspection_mode' => $this->faker->randomElement(['presencial', 'videollamada', 'foto y presupuesto', 'foto']),
            'notes' => $this->faker->sentence(),
            'contact' => $this->faker->name(),
            'inspection_code' => $this->faker->bothify('IP-########'),
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (): array => [
            'company_id' => $company->id,
            'client_id' => Client::factory()->for($company),
        ]);
    }
}
