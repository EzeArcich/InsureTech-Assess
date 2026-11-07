<?php

namespace App\Jobs;

use App\Services\SimulationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSimulationEvent implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 25;

    public function __construct(public int $itemId, public string $reservation) {}

    public function handle(SimulationEngine $engine): void
    {
        $engine->send($this->itemId, $this->reservation);
    }
}
