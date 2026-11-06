<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionRate extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'in_person', 'photos', 'video_call'];

    protected $casts = ['in_person' => 'decimal:2', 'photos' => 'decimal:2', 'video_call' => 'decimal:2'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
