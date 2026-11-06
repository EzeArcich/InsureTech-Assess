<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionPayment extends Model
{
    use HasFactory;

    protected $fillable = ['claim_id', 'inspector_id', 'company_id', 'modality', 'amount', 'completed_at', 'resolved_by', 'paid_at', 'paid_by'];

    protected $casts = ['amount' => 'decimal:2', 'completed_at' => 'datetime', 'paid_at' => 'datetime'];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (! $user->canManageInspectionPayments()) {
            $query->whereHas('inspector', fn (Builder $inspector): Builder => $inspector->where('user_id', $user->id));
        }

        return $query;
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
