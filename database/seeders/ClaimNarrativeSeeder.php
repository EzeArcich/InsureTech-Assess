<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Company;
use App\Models\Inspector;
use App\Models\Producer;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClaimNarrativeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $companies = Company::orderBy('id')->get();
            $inspectors = Inspector::orderBy('id')->get();
            $producers = Producer::orderBy('id')->get();
            if (! Vehicle::whereNotNull('catalog_configuration_code')->exists()) {
                $this->call(DemoCollisionCatalogSeeder::class);
            }
            $vehicles = Vehicle::whereNotNull('catalog_configuration_code')->orderBy('id')->get();

            if ($companies->isEmpty() || $inspectors->isEmpty() || $producers->isEmpty()) {
                throw new \RuntimeException('Se necesitan compañías, peritos y productores antes de generar siniestros de ejemplo.');
            }

            for ($index = 0; $index < 20; $index++) {
                $producer = $producers[$index % $producers->count()];
                Claim::factory()->forCompany($companies[$index % $companies->count()])
                    ->for($inspectors[$index % $inspectors->count()])->for($producer)->create([
                        'vehicle_id' => $vehicles[$index % $vehicles->count()]->id,
                        'status' => ['Pendiente', 'Ausente', 'Coordinado', 'Peritando', 'IPgestion'][$index % 5],
                        'producer_name' => $producer->name,
                        'producer_email' => $producer->email,
                    ]);
            }
        });
    }
}
