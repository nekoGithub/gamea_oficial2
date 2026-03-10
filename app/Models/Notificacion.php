<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'sistema_version_id',
        'fecha',
        'estado',
        'mensaje',
        'usuario_enviado'
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function sistemaVersion()
    {
        return $this->belongsTo(SistemaVersion::class, 'sistema_version_id');
    }

    public function usuarioEnviado()
    {
        return $this->belongsTo(User::class, 'usuario_enviado');
    }


    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnviadas($query)
    {
        return $query->where('estado', 'enviado');
    }

    public function scopeFallidas($query)
    {
        return $query->where('estado', 'fallido');
    }

    /**
     * Marcar notificación como enviada
     */
    public function marcarEnviado(): bool
    {
        return $this->update(['estado' => 'enviado']);
    }

    /**
     * Marcar notificación como fallida
     */
    public function marcarFallido(): bool
    {
        return $this->update(['estado' => 'fallido']);
    }

    /**
     * Marcar notificación como pendiente
     */
    public function marcarPendiente(): bool
    {
        return $this->update(['estado' => 'pendiente']);
    }   

    // Accesores
    public function getTipoAttribute()
    {
        if (str_contains($this->mensaje, '[critica]')) return 'critica';
        if (str_contains($this->mensaje, '[alta]')) return 'alta';
        if (str_contains($this->mensaje, '[media]')) return 'media';
        if (str_contains($this->mensaje, '[baja]')) return 'baja';
        return 'info';
    }

    public function getSeveridadBadgeAttribute()
    {
        return match ($this->tipo) {
            'critica' => '<span class="badge bg-danger">Crítica</span>',
            'alta' => '<span class="badge bg-warning">Alta</span>',
            'media' => '<span class="badge bg-info">Media</span>',
            'baja' => '<span class="badge bg-success">Baja</span>',
            default => '<span class="badge bg-secondary">Info</span>',
        };
    }

    public function getEstadoBadgeAttribute()
    {
        return match ($this->estado) {
            'enviado' => '<span class="badge bg-success">Enviado</span>',
            'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
            'fallido' => '<span class="badge bg-danger">Fallido</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    public function getIconoSeveridadAttribute()
    {
        return match ($this->tipo) {
            'critica' => '<i class="ti ti-alert-triangle text-danger fs-4"></i>',
            'alta' => '<i class="ti ti-alert-circle text-warning fs-4"></i>',
            'media' => '<i class="ti ti-info-circle text-info fs-4"></i>',
            'baja' => '<i class="ti ti-check text-success fs-4"></i>',
            default => '<i class="ti ti-bell text-secondary fs-4"></i>',
        };
    }

    public function getMensajeLimpioAttribute()
    {
        // Eliminar los tags de severidad para mostrar más limpio
        $mensaje = $this->mensaje;
        $mensaje = preg_replace('/\[(critica|alta|media|baja)\]\s*/i', '', $mensaje);
        return $mensaje;
    }
}
