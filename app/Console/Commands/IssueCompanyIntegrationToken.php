<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\IssueCompanyIntegrationCredential;
use Illuminate\Console\Command;

class IssueCompanyIntegrationToken extends Command
{
    protected $signature = 'integrations:issue-token {company : ID de compañía} {--name=Integración de siniestros} {--simulation : Credencial cifrada de laboratorio (sólo local/testing)}';

    protected $description = 'Emite una credencial de aseguradora; muestra el secreto sólo una vez';

    public function handle(IssueCompanyIntegrationCredential $issue): int
    {
        $simulation = (bool) $this->option('simulation');
        if ($simulation && (! app()->environment(['local', 'testing']) || ! config('claim_integrations.simulator_enabled'))) {
            $this->error('La emisión de laboratorio requiere entorno local/testing y habilitación explícita.');

            return self::FAILURE;
        }
        $company = Company::find($this->argument('company'));
        if (! $company) {
            $this->error('Compañía inexistente.');

            return self::FAILURE;
        }
        $result = $issue->handle($company, (string) $this->option('name'), $simulation);
        if ($simulation) {
            $this->info('Credencial de laboratorio '.$result['credential']->id.' creada; secreto cifrado y disponible para el motor.');

            return self::SUCCESS;
        }
        $this->info('Credencial '.$result['credential']->id.'. Guardar el Bearer token; no podrá recuperarse:');
        $this->line($result['token']);

        return self::SUCCESS;
    }
}
