<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Wildside\Userstamps\Userstamps;

class Claim extends Model
{
    use HasFactory, Userstamps;

    protected $fillable = [
        'workshop_id', 'bodywork_unit', 'bodywork_units', 'mechanical_unit', 'mechanical_units', 'paint_panels',
        'company_id',
        'client_id',
        'inspector_id',
        'producer_id',
        'claim_number',
        'license_plate',
        'inspection_date',
        'status',
        'client_type',
        'inspection_mode',
        'notes',
        'closed_at',
        'contact',
        'inspection_code',
        'link',
        'image',
        'short_number',
        'reason',
        'workshop_name',
        'coordinator',
        'send_order',
        'inspection_time',
        'inspector_notes',
        'location',
        'third_party_name',
        'analyst',
        'closure',
        'quoted',
        'negotiation',
        'first_offer',
        'second_offer',
        'template',
        'estimate',
        'inspector_email',
        'inspector_phone',
        'claim_url',
        'producer_name',
        'producer_email',
        'email',
        'cc',
        'cc2',
    ];

    protected $with = ['company', 'client', 'inspector'];

    protected $appends = ['client_name', 'phone', 'address', 'city'];

    protected $casts = ['inspection_latitude' => 'decimal:7', 'inspection_longitude' => 'decimal:7', 'inspection_date' => 'date:Y-m-d', 'closed_at' => 'date:Y-m-d', 'bodywork_units' => 'decimal:2', 'mechanical_units' => 'decimal:2', 'paint_panels' => 'decimal:2', 'bodywork_rate' => 'decimal:2', 'mechanical_rate' => 'decimal:2', 'paint_rate' => 'decimal:2', 'bodywork_total' => 'decimal:2', 'mechanical_total' => 'decimal:2', 'paint_total' => 'decimal:2', 'repair_total' => 'decimal:2'];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isRestrictedInspector()) {
            $query->whereIn('status', ['Peritando', 'IPgestion'])
                ->whereHas('inspector', fn (Builder $inspector): Builder => $inspector->where('user_id', $user->id));
        }

        if ($user->isRoutePlanner()) {
            $query->whereIn('status', ['Coordinado', 'Peritando']);
        }

        if ($user->isRestrictedCoordinator()) {
            $query->whereIn('status', ['Pendiente', 'Ausente']);
        }

        return $query;
    }

    public function inspectionModality(): ?string
    {
        return match (mb_strtolower(trim($this->inspection_mode ?? ''))) {
            'presencial', 'in_person' => 'in_person',
            'foto', 'fotos', 'por fotos', 'foto y presupuesto', 'photos' => 'photos',
            'videollamada', 'video llamada', 'por videollamada', 'video_call' => 'video_call',
            default => null,
        };
    }

    public function inspectionPayment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InspectionPayment::class, 'claim_id');
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class, 'claim_part', 'claim_id', 'part_id');
    }

    public function integrationReceipt(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(IntegratedClaim::class, 'claim_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'claim_id');
    }

    public function getClientNameAttribute(): ?string
    {
        return $this->client?->name;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->client?->phone;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->client?->address;
    }

    public function getCityAttribute(): ?string
    {
        return $this->client?->city;
    }
}
