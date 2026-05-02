<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionUpload extends Model
{
    protected $fillable = [
        'sistema_id',
        'user_id',
        'numero_version',
        'estado',
        'progreso',
        'error_message',
        'data',
        'chunk_identifier',
        'file_name',
        'file_size',
        'file_type',
        'file_hash',
        'last_chunk_completed',
        'total_chunks',
        'chunks_received',

        'manual_tecnico_name',
        'manual_tecnico_size',
        'manual_tecnico_identifier',
        'manual_tecnico_total_chunks',
        'manual_tecnico_chunks_received',
        'manual_usuario_name',
        'manual_usuario_size',
        'manual_usuario_identifier',
        'manual_usuario_total_chunks',
        'manual_usuario_chunks_received',


        'temp_codigo_fuente',
        'temp_manual_tecnico',
        'temp_manual_usuario',
        'temp_imagen',

        'temp_archivo_bd',           
        'archivo_bd_identifier',     
        'archivo_bd_total_chunks',   
        'archivo_bd_chunks_received', 
    ];

    protected $casts = [
        'data' => 'array',
        'progreso' => 'integer',
        'total_chunks' => 'integer',
        'chunks_received' => 'integer',
    ];

    // ========== RELACIONES ==========

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========

    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeProcesando($query)
    {
        return $query->where('estado', 'procesando');
    }

    public function scopeCompletado($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeError($query)
    {
        return $query->where('estado', 'error');
    }

    // ========== HELPERS ==========

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isProcesando(): bool
    {
        return $this->estado === 'procesando';
    }

    public function isCompletado(): bool
    {
        return $this->estado === 'completado';
    }

    public function isError(): bool
    {
        return $this->estado === 'error';
    }

    public function updateProgreso(int $progreso, string $message = null): void
    {
        $this->update([
            'progreso' => $progreso,
            'error_message' => $message,
        ]);
    }

    public function marcarCompletado(): void
    {
        $this->update([
            'estado' => 'completado',
            'progreso' => 100,
        ]);
    }

    public function marcarError(string $mensaje): void
    {
        $this->update([
            'estado' => 'error',
            'error_message' => $mensaje,
        ]);
    }
}
