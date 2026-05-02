<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Responsable extends Model
{
    use SoftDeletes;

    protected $table = 'responsables';

    protected $fillable = ['nombre', 'cargo', 'email','celular'];

    public function unidades()
    {
        return $this->belongsToMany(Unidad::class, 'unidad_responsables')->withTimestamps();
    }

}
