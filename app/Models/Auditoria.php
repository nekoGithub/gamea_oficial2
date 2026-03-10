<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditoria extends Model
{
    use HasFactory;

    protected $table = 'auditorias';

    protected $fillable = [
        'user_id',
        'accion',
        'modulo',
        'entidad_id',
        'descripcion',
        'valores_anteriores',
        'valores_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Obtener el nombre del usuario (con fallback)
     */
    public function getNombreUsuarioAttribute()
    {
        return $this->user ? $this->user->name : 'Sistema';
    }

    /**
     * Obtener badge HTML según la acción
     */
    public function getAccionBadgeAttribute()
    {
        $badges = [
            'login' => '<span class="badge bg-info">Login</span>',
            'logout' => '<span class="badge bg-secondary">Logout</span>',
            'created' => '<span class="badge bg-success">Creado</span>',
            'updated' => '<span class="badge bg-warning">Actualizado</span>',
            'deleted' => '<span class="badge bg-danger">Eliminado</span>',
            'restored' => '<span class="badge bg-primary">Restaurado</span>',
        ];

        return $badges[$this->accion] ?? '<span class="badge bg-secondary">' . ucfirst($this->accion) . '</span>';
    }

    /**
     * Obtener icono según el módulo
     */
    public function getModuloIconoAttribute()
    {
        $iconos = [
            'sistemas' => 'ti-layout-grid',
            'sistema_versiones' => 'ti-versions',
            'credenciales' => 'ti-key',
            'ssls' => 'ti-certificate',
            'servidores' => 'ti-server',
            'users' => 'ti-users',
            'notificaciones' => 'ti-bell',
        ];

        return $iconos[$this->modulo] ?? 'ti-file';
    }
}
