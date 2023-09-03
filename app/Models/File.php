<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siniestro;


class File extends Model
{
    use HasFactory;

    protected $fillable = ['url','siniestro_id'];

    
    public function siniestro()
    {
        return $this->BelongsTo('App\models\Siniestro');
    }


}
