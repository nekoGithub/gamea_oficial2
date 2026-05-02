<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SistemaVersion extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'sistema_versiones';

    protected $fillable = [
        'sistema_id',
        'numero_version',
        'descripcion',
        'imagen',
        'codigo_fuente',
        'manual_tecnico',
        'manual_usuario',
        'archivo_bd',
        'fecha_lanzamiento',
        'publicado_por',
        'estado',
        'es_actual'
    ];

    protected $casts = [
        'fecha_lanzamiento' => 'datetime',
        'es_actual' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    public function tecnologias()
    {
        return $this->belongsToMany(
            Tecnologia::class,
            'sistema_version_tecnologias',
            'sistema_version_id',
            'tecnologia_id'
        );
    }

    public function servidores()
    {
        return $this->belongsToMany(
            Servidor::class,
            'sistema_version_servidores',
            'sistema_version_id',
            'servidor_id'
        );
    }

    public function basesDatos()
    {
        return $this->belongsToMany(
            BaseDato::class,
            'sistema_version_bases_datos',
            'sistema_version_id',
            'base_datos_id'
        )->withTimestamps();
    }

    public function credenciales()
    {
        return $this->belongsToMany(
            Credencial::class,
            'sistema_version_credenciales',
            'sistema_version_id',            
            'credencial_id'

        );
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function publicadoPor()
    {
        return $this->belongsTo(User::class, 'publicado_por');
    }

    public function documentos()
    {
        return $this->belongsToMany(Documento::class, 'documento_sistema_versiones', 'sistema_version_id', 'documento_id')
            ->withPivot('archivo')
            ->withTimestamps();
    }

    /**
     * Accessor para obtener la URL de la imagen
     */
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        return asset('images/default-version.png');
    }

    /**
     * Scope para obtener solo versiones activas
     */
    public function scopeActual($query)
    {
        return $query->where('es_actual', true);
    }

    /**
     * Scope para obtener por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
}
