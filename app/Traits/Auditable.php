<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot del trait
     */
    public static function bootAuditable()
    {
        // CREATED
        static::created(function ($model) {
            $model->registrarAuditoria('created', 'Registro creado', null, $model->toArray());
        });

        // UPDATED
        static::updated(function ($model) {
            $valoresAnteriores = $model->getOriginal();
            $valoresNuevos = $model->getChanges();

            // Filtrar solo los campos que cambiaron (excluyendo timestamps)
            unset($valoresNuevos['updated_at']);

            if (!empty($valoresNuevos)) {
                $model->registrarAuditoria('updated', 'Registro actualizado', $valoresAnteriores, $valoresNuevos);
            }
        });

        // DELETED (soft delete)
        static::deleted(function ($model) {
            $accion = $model->isForceDeleting() ? 'deleted' : 'deleted';
            $descripcion = $model->isForceDeleting() ? 'Registro eliminado permanentemente' : 'Registro enviado a papelera';
            
            $model->registrarAuditoria($accion, $descripcion, $model->toArray(), null);
        });

        // RESTORED
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->registrarAuditoria('restored', 'Registro restaurado desde papelera', null, $model->toArray());
            });
        }
    }

    /**
     * Registrar auditoría
     */
    protected function registrarAuditoria(string $accion, string $descripcion, ?array $valoresAnteriores = null, ?array $valoresNuevos = null)
    {
        // Obtener el nombre del módulo desde la tabla del modelo
        $modulo = $this->getTable();

        // Limpiar campos sensibles (passwords, tokens, etc.)
        $valoresAnteriores = $this->limpiarDatosSensibles($valoresAnteriores);
        $valoresNuevos = $this->limpiarDatosSensibles($valoresNuevos);

        Auditoria::create([
            'user_id' => Auth::id(),
            'accion' => $accion,
            'modulo' => $modulo,
            'entidad_id' => $this->id ?? null,
            'descripcion' => $descripcion,
            'valores_anteriores' => $valoresAnteriores,
            'valores_nuevos' => $valoresNuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Limpiar datos sensibles antes de guardar en auditoría
     */
    protected function limpiarDatosSensibles(?array $datos): ?array
    {
        if (!$datos) return null;

        $camposSensibles = ['password', 'password_encrypted', 'remember_token', 'api_token'];

        foreach ($camposSensibles as $campo) {
            if (isset($datos[$campo])) {
                $datos[$campo] = '***OCULTO***';
            }
        }

        return $datos;
    }

    /**
     * Relación con auditorías
     */
    public function auditorias()
    {
        return $this->morphMany(Auditoria::class, 'entidad');
    }
}