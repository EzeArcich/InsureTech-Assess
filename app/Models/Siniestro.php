<?php

namespace App\Models;

use Database\Factories\ClaimFactory;

class Siniestro extends Claim
{
    protected $table = 'claims';

    protected static function newFactory(): ClaimFactory
    {
        return ClaimFactory::new()->model(static::class);
    }
}
