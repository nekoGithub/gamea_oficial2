<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidad extends Model
{
    use SoftDeletes;

    protected $table = 'unidades';

    protected $fillable = [
        'nombre',
        'sigla',
        'descripcion',
        'celular',
        'estado'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function responsables()
    {
        return $this->belongsToMany(Responsable::class, 'unidad_responsables')->withTimestamps();;
    }

    public function sistemas()
    {
        return $this->hasMany(Sistema::class);
    }
}
