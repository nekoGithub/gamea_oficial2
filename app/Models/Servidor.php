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
        'descripcion',
        'sistema_operativo_id',
        'tipo_servidor',
        'estado',
        'disponibilidad_interna',
        'disponibilidad_externa',
        'ultima_verificacion',
    ];

    protected $casts = [
        'ultima_verificacion' => 'datetime',
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
