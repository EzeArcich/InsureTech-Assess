<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function versions(): HasMany
    {
        return $this->hasMany(EmailTemplateVersion::class);
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(EmailTemplateVersion::class)->ofMany('number', 'max');
    }
}
