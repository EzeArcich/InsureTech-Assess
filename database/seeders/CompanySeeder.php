<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Mercantil Andina', 'prefix' => 'ML'],
            ['name' => 'Federación Patronal', 'prefix' => 'FP'],
            ['name' => 'Sancor Seguros', 'prefix' => 'SC'],
            ['name' => 'La Caja', 'prefix' => 'LC'],
            ['name' => 'San Cristóbal', 'prefix' => 'CR'],
        ] as $attributes) {
            Company::firstOrCreate(['name' => $attributes['name']], $attributes);
        }
    }
}
