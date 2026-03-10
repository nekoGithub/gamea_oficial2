<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseDato extends Model
{
    use SoftDeletes;

    protected $table = 'bases_datos';

    protected $fillable = [
        'gestor',
        'version',
        'descripcion',
        'estado'
    ];

    public function versiones()
    {
        return $this->belongsToMany(
            SistemaVersion::class,
            'sistema_version_bases_datos',
            'base_datos_id',      // FK REAL en la tabla pivote
            'sistema_version_id'  // FK REAL en la tabla pivote
        );
    }
}
