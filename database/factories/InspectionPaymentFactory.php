<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InspectionPayment>
 */
class InspectionPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'claim_id' => \App\Models\Claim::factory()->state(['status' => 'Resuelto']),
            'company_id' => fn (array $attributes) => \App\Models\Claim::findOrFail($attributes['claim_id'])->company_id,
            'inspector_id' => fn (array $attributes) => \App\Models\Claim::findOrFail($attributes['claim_id'])->inspector_id,
            'modality' => 'in_person', 'amount' => '15000.00', 'completed_at' => now(),
        ];
    }
}
