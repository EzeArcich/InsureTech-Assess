<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class TH extends Model
{
    use HasFactory;
    use Userstamps;
    // protected $fillable = ['vacaciones','descfranq','granizo','ponerep','taller','direccion','barrio','localidad','telefonos',
    // 'email','zona','razon','cuit','grua','traslado'];
    protected $table = 't_h_';
    protected $guarded = [''];
}
