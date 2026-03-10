<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SistemaOperativo extends Model
{
    use SoftDeletes;    

    protected $table = 'sistemas_operativos';

    protected $fillable = [
        'nombre',
        'version',
        'descripcion',
        'estado'
    ];

    public function servidores()
    {
        return $this->hasMany(Servidor::class);
    }
}
