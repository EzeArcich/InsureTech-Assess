<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulationProfile extends Model
{
    use HasFactory;

    protected $fillable = ['credential_id', 'name', 'configuration', 'enabled'];

    protected $casts = ['configuration' => 'array', 'enabled' => 'boolean'];

    public function credential(): BelongsTo
    {
        return $this->belongsTo(CompanyIntegrationCredential::class, 'credential_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ClaimSimulationRun::class, 'profile_id');
    }
}
