<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClaimSimulationRun extends Model
{
    use HasFactory;

    protected $attributes = ['status' => 'preview'];

    protected $fillable = ['user_id', 'credential_id', 'reason', 'seed', 'status', 'started_at', 'finished_at', 'profile_id', 'configuration', 'ends_at', 'next_dispatch_at', 'planned_count', 'last_error'];

    protected $casts = ['started_at' => 'datetime', 'finished_at' => 'datetime', 'configuration' => 'array', 'ends_at' => 'datetime', 'next_dispatch_at' => 'datetime'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(SimulationProfile::class, 'profile_id');
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(CompanyIntegrationCredential::class, 'credential_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClaimSimulationItem::class, 'run_id')->orderBy('position');
    }
}
