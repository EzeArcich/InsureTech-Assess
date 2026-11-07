<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegratedClaim extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'credential_id', 'external_id', 'payload_hash', 'claim_id', 'response_snapshot'];

    protected $casts = ['response_snapshot' => 'array'];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
