<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogCollisionPart extends Model
{
    protected $table = 'catalog_collision_parts';

    protected $primaryKey = 'code';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $casts = ['equipment_dependent' => 'boolean'];
}
