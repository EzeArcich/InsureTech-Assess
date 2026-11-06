<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'claim_id'];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
