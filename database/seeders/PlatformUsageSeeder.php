<?php

namespace Database\Seeders;

use App\Models\CatalogCollisionPart;
use App\Models\Claim;
use App\Models\Company;
use App\Models\CompanyRate;
use App\Models\InspectionRate;
use App\Models\Inspector;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ResolveInspection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformUsageSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('Los datos de uso simulado solo están disponibles fuera de producción.');
        }

        DB::transaction(function (): void {
            $inspectors = Inspector::orderBy('id')->get();
            $companies = Company::orderBy('id')->get();
            $vehicles = Vehicle::whereNotNull('catalog_configuration_code')->orderBy('id')->get();
            $manager = User::role('CEO')->first();
            if ($inspectors->isEmpty() || $companies->isEmpty() || $vehicles->isEmpty() || ! $manager) {
                throw new \RuntimeException('Cargá compañías, peritos, vehículos y un usuario CEO antes de simular el uso.');
            }
            foreach ($companies as $company) {
                CompanyRate::firstOrCreate(['company_id' => $company->id], ['bodywork_day' => 80000, 'bodywork_hour' => 10000, 'mechanical_day' => 96000, 'mechanical_hour' => 12000, 'paint_panel' => 50000]);
                InspectionRate::firstOrCreate(['company_id' => $company->id], ['in_person' => 15000, 'photos' => 7000, 'video_call' => 9000]);
            }
            $locations = [
                ['Palermo, Buenos Aires', -34.5884, -58.4304], ['Caballito, Buenos Aires', -34.6187, -58.4437],
                ['Belgrano, Buenos Aires', -34.5627, -58.4583], ['Almagro, Buenos Aires', -34.6090, -58.4203],
                ['Flores, Buenos Aires', -34.6288, -58.4636], ['San Telmo, Buenos Aires', -34.6215, -58.3731],
                ['Avellaneda, Buenos Aires', -34.6628, -58.3655], ['Lanús, Buenos Aires', -34.7025, -58.3950],
            ];
            $tomorrow = now()->addDay()->toDateString();
            $index = 0;
            foreach ($inspectors as $inspector) {
                foreach (['assigned-1', 'assigned-2', 'in-progress', 'resolved'] as $stage) {
                    $number = 'DEMO-USO-'.$inspector->id.'-'.$stage;
                    if (Claim::where('claim_number', $number)->exists()) { continue; }
                    $company = $companies[$index % $companies->count()];
                    $vehicle = $vehicles[$index % $vehicles->count()];
                    $location = $locations[$index % count($locations)];
                    $isActive = str_starts_with($stage, 'assigned');
                    $claim = Claim::factory()->forCompany($company)->for($inspector)->create([
                        'claim_number' => $number, 'vehicle_id' => $vehicle->id,
                        'status' => $isActive ? 'Peritando' : 'IPgestion', 'inspection_mode' => 'presencial',
                        'inspection_date' => $isActive ? $tomorrow : now()->subDays(2)->toDateString(),
                        'inspection_time' => $index % 2 ? null : sprintf('%02d:30', 9 + ($index % 7)),
                        'inspector_notes' => $isActive ? null : 'Inspección realizada. Se registraron daños y piezas a revisar.',
                        'bodywork_unit' => 'hour', 'bodywork_units' => $isActive ? 0 : 3,
                        'mechanical_unit' => 'hour', 'mechanical_units' => $isActive ? 0 : 1,
                        'paint_panels' => $isActive ? 0 : 2,
                    ]);
                    $claim->forceFill(['inspection_address' => $location[0].' (ubicación de ejemplo)', 'inspection_latitude' => $location[1] + (($index % 5) * 0.001), 'inspection_longitude' => $location[2] + (($index % 5) * 0.001)])->save();
                    if (! $isActive) {
                        app(\App\Services\SaveClaim::class)->handle(['bodywork_units' => 3, 'mechanical_units' => 1, 'paint_panels' => 2], $claim);
                        $codes = CatalogCollisionPart::where('vehicle_configuration_code', $vehicle->catalog_configuration_code)->orderBy('code')->limit(3)->pluck('code')->all();
                        $claim->catalogParts()->sync($codes);
                    }
                    if ($stage === 'resolved') { app(ResolveInspection::class)->handle($claim, $manager); }
                    $index++;
                }
            }
            for ($i = 0; $i < 11; $i++) {
                $number = 'DEMO-USO-COLA-'.$i;
                if (Claim::where('claim_number', $number)->exists()) { continue; }
                $company = $companies[$i % $companies->count()];
                $location = $locations[$i % count($locations)];
                $claim = Claim::factory()->forCompany($company)->create([
                    'claim_number' => $number, 'vehicle_id' => $vehicles[$i % $vehicles->count()]->id,
                    'inspector_id' => null, 'status' => $i < 8 ? 'Coordinado' : 'Pendiente',
                    'inspection_mode' => 'presencial', 'inspection_date' => $tomorrow,
                    'inspection_time' => $i % 2 ? null : sprintf('%02d:00', 9 + ($i % 7)),
                ]);
                if ($i < 8) { $claim->forceFill(['inspection_address' => $location[0].' (ubicación de ejemplo)', 'inspection_latitude' => $location[1] + (($i % 5) * 0.001), 'inspection_longitude' => $location[2] + (($i % 5) * 0.001)])->save(); }
            }
        });
    }
}
