<?php

namespace App\Console\Commands;

use App\Services\SimulationEngine;
use Illuminate\Console\Command;

class DispatchClaimSimulations extends Command
{
    protected $signature = 'simulations:dispatch';

    protected $description = 'Planifica eventos vencidos del laboratorio y los publica en la cola simulations';

    public function handle(SimulationEngine $engine): int
    {
        if (! app()->environment(['local', 'testing']) || ! config('claim_integrations.simulator_enabled')) {
            $this->info('Laboratorio deshabilitado; no se planifican eventos.');

            return self::SUCCESS;
        }
        $this->info('Eventos encolados: '.$engine->tick());

        return self::SUCCESS;
    }
}
