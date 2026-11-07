<?php

namespace App\Console\Commands;

use Database\Seeders\ClaimIntegrationDemoSeeder;
use Illuminate\Console\Command;
use RuntimeException;

class SeedClaimIntegrationDemo extends Command
{
    protected $signature = 'integrations:seed-demo';

    protected $description = 'Agrega Seguro Norte ficticia y credencial cifrada del simulador; no crea siniestros';

    public function handle(ClaimIntegrationDemoSeeder $seeder): int
    {
        try {
            $seeder->run();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
        $this->info('DEMO Seguro Norte creada. El secreto del laboratorio permanece cifrado, no se muestra.');

        return self::SUCCESS;
    }
}
