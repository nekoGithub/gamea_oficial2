<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use SoftDeletes;

    protected $table = 'documentos';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Versiones que usan este documento
     */
    public function sistemaVersiones()
    {
        return $this->belongsToMany(
            SistemaVersion::class,
            'documento_sistema_versiones',
            'documento_id',
            'sistema_version_id'
        )
            ->withPivot('archivo')
            ->withTimestamps();
    }
    /**
     * Scope para documentos activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
