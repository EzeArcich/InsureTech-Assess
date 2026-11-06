<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CompanySeeder::class);

        $companies = Company::orderBy('id')->get();
        $missing = max(0, 200 - Client::count());
        for ($index = 0; $index < $missing; $index++) {
            Client::factory()->for($companies[$index % $companies->count()])->create();
        }
    }
}
