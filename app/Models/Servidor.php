<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servidor extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'servidores';

    protected $fillable = [
        'nombre',
        'ip_interna',      
        'ip_externa',
        'mac_address',
        'sistema_operativo_id',
        'tipo_servidor',
        'estado'
    ];

    public function sistemaOperativo()
    {
        return $this->belongsTo(SistemaOperativo::class);
    }

    public function versiones()
    {
        return $this->belongsToMany(
            SistemaVersion::class,
            'sistema_version_servidores'
        );
    }
}
