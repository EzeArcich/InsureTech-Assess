<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;


//Agregamos spatie
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'created_by');
    }

    public function siniestros(): HasMany
    {
        return $this->claims();
    }
    public function isRestrictedInspector(): bool
    {
        return $this->hasRole('Inspectores') && ! $this->can('view-all-claims');
    }
    public function isRoutePlanner(): bool
    {
        return $this->hasRole('Hojas de ruta') && ! $this->can('view-all-claims');
    }

    public function canCoordinateInspections(): bool
    {
        return ! $this->isRestrictedInspector() && ! $this->isRoutePlanner()
            && ($this->hasAnyRole(['CEO', 'Coordinaciones']) || $this->canAny(['editar-siniestro', 'ceo-siniestro']));
    }

    public function canPlanInspectionRoutes(): bool
    {
        return $this->hasAnyRole(['CEO', 'Hojas de ruta']);
    }

    public function canManageInspectionPayments(): bool
    {
        return $this->hasAnyRole(['CEO', 'Administrativo']);
    }
}
