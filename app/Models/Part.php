<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Part extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'normalized_name'];

    public function claims(): BelongsToMany
    {
        return $this->belongsToMany(Claim::class);
    }
}
