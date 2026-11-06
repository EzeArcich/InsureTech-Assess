<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prefix',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
    public function rates(): HasOne
    {
        return $this->hasOne(CompanyRate::class);
    }
    public function inspectionRates(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InspectionRate::class);
    }
}
