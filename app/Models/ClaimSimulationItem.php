<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimSimulationItem extends Model
{
    use HasFactory;

    protected $fillable = ['run_id', 'position', 'payload', 'status', 'http_status', 'response', 'sent_at', 'external_id', 'scheduled_at', 'available_at', 'reserved_until', 'reservation', 'queue_job_id', 'attempts', 'claim_id', 'last_error'];

    protected $casts = ['payload' => 'array', 'response' => 'array', 'sent_at' => 'datetime', 'scheduled_at' => 'datetime', 'available_at' => 'datetime', 'reserved_until' => 'datetime', 'attempts' => 'integer'];
}
