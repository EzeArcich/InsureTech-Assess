<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyRate extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'bodywork_day', 'bodywork_hour', 'mechanical_day', 'mechanical_hour', 'paint_panel'];

    protected $casts = ['bodywork_day' => 'decimal:2', 'bodywork_hour' => 'decimal:2', 'mechanical_day' => 'decimal:2', 'mechanical_hour' => 'decimal:2', 'paint_panel' => 'decimal:2'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
