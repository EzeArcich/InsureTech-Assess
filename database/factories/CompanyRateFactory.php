<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyRate>
 */
class CompanyRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'bodywork_day' => '80000.00', 'bodywork_hour' => '10000.00',
            'mechanical_day' => '96000.00', 'mechanical_hour' => '12000.00', 'paint_panel' => '50000.00',
        ];
    }
}
