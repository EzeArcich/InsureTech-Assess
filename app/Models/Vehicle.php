<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    protected $fillable = ['brand', 'model', 'year', 'version', 'doors'];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }
}
