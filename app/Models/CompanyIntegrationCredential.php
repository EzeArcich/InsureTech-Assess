<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyIntegrationCredential extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'token_hash', 'abilities', 'active', 'expires_at', 'revoked_at', 'is_simulation', 'simulator_token'];

    protected $hidden = ['token_hash', 'simulator_token'];

    protected $casts = ['company_id' => 'integer', 'abilities' => 'array', 'active' => 'boolean', 'is_simulation' => 'boolean', 'expires_at' => 'datetime', 'revoked_at' => 'datetime', 'simulator_token' => 'encrypted'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
