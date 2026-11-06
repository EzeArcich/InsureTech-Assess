<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_approved', 'opening_hours',
        'holidays',
        'deductible_discount',
        'hail',
        'repair_availability',
        'name',
        'address',
        'neighborhood',
        'city',
        'phone',
        'email',
        'area',
        'business_name',
        'tax_id',
        'towing',
        'transport',
    ];
    protected $attributes = ['is_approved' => false];

    protected $casts = ['is_approved' => 'boolean'];

    public static function identityKey(string $value): string
    {
        return hash('sha256', mb_strtolower(preg_replace('/\s+/u', ' ', trim($value))));
    }

    protected static function booted(): void
    {
        static::saving(function (Workshop $workshop): void {
            foreach (['name', 'address'] as $field) {
                $workshop->{$field} = preg_replace('/\s+/u', ' ', trim($workshop->{$field}));
                $workshop->{$field.'_key'} = self::identityKey($workshop->{$field});
            }
        });
    }

    public function claims(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Claim::class);
    }
}
