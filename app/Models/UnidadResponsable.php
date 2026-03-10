<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadResponsable extends Model
{
    protected $table = 'unidad_responsables';    

    protected $fillable = ['unidad_id', 'responsable_id','created_at', 'updated_at'];

}
