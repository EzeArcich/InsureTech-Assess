<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Workshop> */
class WorkshopFactory extends Factory
{
    public function definition(): array
    {
        return [
            'is_approved' => true,
            'opening_hours' => 'Lunes a viernes de 9 a 18',
            'name' => 'Taller '.$this->faker->unique()->company(),
            'business_name' => $this->faker->company().' SRL',
            'tax_id' => $this->faker->unique()->numerify('30-########-#'),
            'address' => $this->faker->streetAddress(),
            'neighborhood' => $this->faker->streetName(),
            'city' => $this->faker->randomElement(['Buenos Aires', 'La Plata', 'Córdoba', 'Rosario', 'Mendoza']),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'area' => $this->faker->randomElement(['Norte', 'Sur', 'Oeste', 'Centro']),
            'holidays' => 'Sin vacaciones programadas',
            'deductible_discount' => $this->faker->randomElement(['0%', '10%', '15%']),
            'hail' => $this->faker->randomElement(['Sí', 'No']),
            'repair_availability' => $this->faker->randomElement(['Disponible', 'Con turno previo']),
            'towing' => $this->faker->randomElement(['Sí', 'No']),
            'transport' => $this->faker->randomElement(['Sí', 'No']),
        ];
    }
}
