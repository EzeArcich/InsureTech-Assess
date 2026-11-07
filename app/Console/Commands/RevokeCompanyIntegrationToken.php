<?php

namespace App\Console\Commands;

use App\Models\CompanyIntegrationCredential;
use Illuminate\Console\Command;

class RevokeCompanyIntegrationToken extends Command
{
    protected $signature = 'integrations:revoke-token {credential : ID de credencial}';

    protected $description = 'Revoca una credencial y elimina su copia cifrada de laboratorio';

    public function handle(): int
    {
        $credential = CompanyIntegrationCredential::find($this->argument('credential'));
        if (! $credential) {
            $this->error('Credencial inexistente.');

            return self::FAILURE;
        }
        $credential->update(['active' => false, 'revoked_at' => now(), 'simulator_token' => null]);
        $this->info('Credencial revocada.');

        return self::SUCCESS;
    }
}
