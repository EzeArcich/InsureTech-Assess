<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Services\IssueCompanyIntegrationCredential;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ClaimIntegrationDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || ! config('claim_integrations.simulator_enabled')) {
            throw new RuntimeException('El laboratorio requiere local/testing y CLAIM_SIMULATOR_ENABLED=true.');
        }
        DB::transaction(function (): void {
            $existing = Company::where('prefix', 'SIM-NORTE')->orWhere('name', 'DEMO Seguro Norte')->exists();
            if ($existing) {
                throw new RuntimeException('La compañía demo ya existe; no se modificó ningún dato.');
            }
            $company = Company::create(['name' => 'DEMO Seguro Norte', 'prefix' => 'SIM-NORTE']);
            app(IssueCompanyIntegrationCredential::class)->handle($company, 'seguro-norte (laboratorio)', true);
        });
    }
}
