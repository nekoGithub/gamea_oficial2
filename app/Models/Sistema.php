<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sistema extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'sistemas';

    protected $fillable = [
        'nombre',
        'sigla',
        'dominio',
        'descripcion',
        'tipo',
        'unidad_id',
        'ssl_id',
        'estado',
        'disponibilidad_web',
        'http_status',
        'tiempo_respuesta',
        'ultima_verificacion_web',
    ];

    protected $with = ['unidad', 'ssl'];

    protected $casts = [
        'tipo' => 'array',
        'ultima_verificacion_web' => 'datetime',
    ];

    public function credenciales()
    {
        return $this->hasMany(Credencial::class);
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function ssl()
    {
        return $this->belongsTo(Ssl::class);
    }

    public function versiones()
    {
        return $this->hasMany(SistemaVersion::class);
    }

    /**
     * Verifica si es interno
     */
    public function esInterno()
    {
        return is_array($this->tipo) && in_array('interno', $this->tipo);
    }

    /**
     * Verifica si es externo
     */
    public function esExterno()
    {
        return is_array($this->tipo) && in_array('externo', $this->tipo);
    }

    /**
     * Verifica si es ambos
     */
    public function esAmbos()
    {
        return is_array($this->tipo) && count($this->tipo) === 2;
    }

    /**
     * Obtiene texto descriptivo del tipo
     */
    public function getTipoTextoAttribute()
    {
        if (!is_array($this->tipo)) {
            return 'No definido';
        }

        if ($this->esAmbos()) {
            return 'Interno y Externo';
        }

        if ($this->esInterno()) {
            return 'Interno';
        }

        if ($this->esExterno()) {
            return 'Externo';
        }

        return 'No definido';
    }
}
