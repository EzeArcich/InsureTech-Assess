<?php

namespace Database\Factories;

use App\Models\Inspector;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/** @extends Factory<Inspector> */
class InspectorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['phone' => $this->faker->phoneNumber()]),
            'name' => fn (array $attributes): string => isset($attributes['user_id'])
                ? User::findOrFail($attributes['user_id'])->name : $this->faker->name(),
            'email' => fn (array $attributes): string => isset($attributes['user_id'])
                ? User::findOrFail($attributes['user_id'])->email : $this->faker->unique()->safeEmail(),
            'phone' => fn (array $attributes): ?string => isset($attributes['user_id'])
                ? User::findOrFail($attributes['user_id'])->phone : $this->faker->phoneNumber(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Inspector $inspector): void {
            $inspector->user?->assignRole(Role::findOrCreate('Inspectores', 'web'));
        });
    }
}
