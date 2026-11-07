<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationRequest extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'credential_id', 'payload_hash', 'http_status', 'outcome', 'duration_ms'];

    protected $casts = [];

    public $incrementing = false;

    protected $keyType = 'string';
}
