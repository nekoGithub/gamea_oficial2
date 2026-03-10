<?php

namespace App\Jobs;

use App\Models\SistemaVersion;
use App\Models\VersionUpload;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVersionUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $uploadId;

    public function __construct(int $uploadId)
    {
        $this->uploadId = $uploadId;
    }

    public function handle(): void
    {
        $upload = VersionUpload::find($this->uploadId);

        if (!$upload) {
            Log::warning("Upload {$this->uploadId} no encontrado");
            return;
        }

        if (!in_array($upload->estado, ['pendiente', 'procesando'])) {
            Log::warning("Upload {$this->uploadId} ya fue procesado (estado: {$upload->estado})");
            return;
        }

        try {
            $upload->update(['estado' => 'procesando', 'progreso' => 10]);

            Log::info("🚀 Iniciando procesamiento de upload {$this->uploadId}");

            // ========== PASO 1: Ensamblar CÓDIGO FUENTE (10% - 40%) ==========
            if ($upload->chunk_identifier && $upload->total_chunks && $upload->total_chunks > 0) {
                Log::info("📦 Ensamblando código fuente...", [
                    'identifier' => $upload->chunk_identifier,
                    'total_chunks' => $upload->total_chunks
                ]);

                $codigoFuentePath = $this->ensamblarArchivo(
                    $upload->chunk_identifier,
                    $upload->total_chunks,
                    'codigo_fuente',
                    $upload,
                    10,
                    40
                );

                $upload->refresh();
                $upload->update(['temp_codigo_fuente' => $codigoFuentePath]);
                $upload->refresh();

                Log::info("✅ Código fuente ensamblado y guardado", [
                    'path' => $codigoFuentePath,
                    'temp_codigo_fuente_en_db' => $upload->temp_codigo_fuente
                ]);
            } else {
                Log::info("⏭️ Sin código fuente para ensamblar (UPDATE sin cambio)", [
                    'chunk_identifier' => $upload->chunk_identifier,
                    'total_chunks' => $upload->total_chunks
                ]);
                $upload->updateProgreso(40, "Sin código fuente nuevo");
            }

            // ========== PASO 2: Ensamblar MANUAL TÉCNICO (40% - 50%) ==========
            if ($upload->manual_tecnico_identifier && $upload->manual_tecnico_total_chunks && $upload->manual_tecnico_total_chunks > 0) {
                Log::info("📄 Ensamblando manual técnico...", [
                    'identifier' => $upload->manual_tecnico_identifier,
                    'total_chunks' => $upload->manual_tecnico_total_chunks
                ]);

                $manualTecnicoPath = $this->ensamblarArchivo(
                    $upload->manual_tecnico_identifier,
                    $upload->manual_tecnico_total_chunks,
                    'manual_tecnico',
                    $upload,
                    40,
                    50
                );

                $upload->refresh();
                $upload->update(['temp_manual_tecnico' => $manualTecnicoPath]);
                $upload->refresh();

                Log::info("✅ Manual técnico ensamblado y guardado", [
                    'path' => $manualTecnicoPath,
                    'temp_manual_tecnico_en_db' => $upload->temp_manual_tecnico
                ]);
            } else {
                Log::info("⏭️ Sin manual técnico para ensamblar (UPDATE sin cambio)", [
                    'manual_tecnico_identifier' => $upload->manual_tecnico_identifier,
                    'manual_tecnico_total_chunks' => $upload->manual_tecnico_total_chunks
                ]);
                $upload->updateProgreso(50, "Sin manual técnico nuevo");
            }

            // ========== PASO 3: Ensamblar MANUAL USUARIO (50% - 60%) ==========
            if ($upload->manual_usuario_identifier && $upload->manual_usuario_total_chunks && $upload->manual_usuario_total_chunks > 0) {
                Log::info("📘 Ensamblando manual usuario...", [
                    'identifier' => $upload->manual_usuario_identifier,
                    'total_chunks' => $upload->manual_usuario_total_chunks
                ]);

                $manualUsuarioPath = $this->ensamblarArchivo(
                    $upload->manual_usuario_identifier,
                    $upload->manual_usuario_total_chunks,
                    'manual_usuario',
                    $upload,
                    50,
                    60
                );

                $upload->refresh();
                $upload->update(['temp_manual_usuario' => $manualUsuarioPath]);
                $upload->refresh();

                Log::info("✅ Manual usuario ensamblado y guardado", [
                    'path' => $manualUsuarioPath,
                    'temp_manual_usuario_en_db' => $upload->temp_manual_usuario
                ]);
            } else {
                Log::info("⏭️ Sin manual usuario para ensamblar (UPDATE sin cambio)", [
                    'manual_usuario_identifier' => $upload->manual_usuario_identifier,
                    'manual_usuario_total_chunks' => $upload->manual_usuario_total_chunks
                ]);
                $upload->updateProgreso(60, "Sin manual usuario nuevo");
            }

            // ✅ REFRESCAR UPLOAD ANTES DE CREAR/ACTUALIZAR VERSIÓN
            $upload->refresh();

            Log::info("📋 Estado del upload antes de crear/actualizar versión:", [
                'upload_id' => $upload->id,
                'temp_codigo_fuente' => $upload->temp_codigo_fuente,
                'temp_manual_tecnico' => $upload->temp_manual_tecnico,
                'temp_manual_usuario' => $upload->temp_manual_usuario,
                'temp_imagen' => $upload->temp_imagen,
            ]);

            // ========== PASO 4: Crear o actualizar versión (60% - 80%) ==========
            $resultado = $this->crearVersionDefinitiva($upload, 70);
            $version = $resultado['version'];
            $esNuevaVersion = $resultado['es_nueva'];

            Log::info("✅ Versión procesada", [
                'version_id' => $version->id,
                'es_nueva' => $esNuevaVersion,
                'codigo_fuente' => $version->codigo_fuente,
                'manual_tecnico' => $version->manual_tecnico,
                'manual_usuario' => $version->manual_usuario,
            ]);

            // ========== PASO 4.5: Procesar DOCUMENTOS ADICIONALES (80% - 85%) ==========
            $upload->refresh();
            $this->procesarDocumentosAdicionales($upload, $version, 82);

            // ========== PASO 5: Limpiar temporales (85% - 90%) ==========
            $this->limpiarTemporales($upload, 90);

            // ========== PASO 6: Notificación Telegram (90% - 95%) ==========
            if ($esNuevaVersion) {
                $this->enviarNotificacionTelegram($version, $upload, 95);
            } else {
                $upload->updateProgreso(95, "Versión actualizada, sin notificación");
            }

            // ========== PASO 7: Completado (100%) ==========
            $upload->marcarCompletado();

            Log::info("✅ Version upload {$this->uploadId} procesado exitosamente");
        } catch (\Exception $e) {
            $upload->marcarError($e->getMessage());
            Log::error("❌ Error procesando upload {$this->uploadId}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * ✅ NUEVA FUNCIÓN: Procesar documentos adicionales
     */
    protected function procesarDocumentosAdicionales(VersionUpload $upload, SistemaVersion $version, int $progreso): void
    {
        $upload->updateProgreso($progreso, "Procesando documentos adicionales...");

        $data = $upload->data;

        // 🔍 LOG DE DEBUG DETALLADO
        Log::info('🔍 DEBUG - Verificando documentos adicionales:', [
            'upload_id' => $upload->id,
            'tiene_documentos_adicionales' => isset($data['documentos_adicionales']),
            'count_documentos_nuevos' => isset($data['documentos_adicionales']) && is_array($data['documentos_adicionales']) ? count($data['documentos_adicionales']) : 0,
            'tiene_documentos_eliminar' => isset($data['documentos_eliminar']),
            'count_eliminar' => isset($data['documentos_eliminar']) && is_array($data['documentos_eliminar']) ? count($data['documentos_eliminar']) : 0,
        ]);

        // ✅ PASO 1: ELIMINAR DOCUMENTOS (solo en UPDATE)
        if (isset($data['documentos_eliminar']) && is_array($data['documentos_eliminar']) && !empty($data['documentos_eliminar'])) {
            $idsEliminar = $data['documentos_eliminar'];

            Log::info('🗑️ Eliminando documentos:', ['count' => count($idsEliminar)]);

            foreach ($idsEliminar as $documentoId) {
                try {
                    // Obtener el pivot para acceder al archivo
                    $pivot = DB::table('documento_sistema_versiones')
                        ->where('sistema_version_id', $version->id)
                        ->where('documento_id', $documentoId)
                        ->first();

                    if ($pivot && $pivot->archivo) {
                        // Eliminar archivo físico
                        if (Storage::disk('public')->exists($pivot->archivo)) {
                            Storage::disk('public')->delete($pivot->archivo);
                            Log::info("🗑️ Archivo eliminado: {$pivot->archivo}");
                        }
                    }

                    // Eliminar relación
                    $version->documentos()->detach($documentoId);
                    Log::info("✅ Documento {$documentoId} desvinculado");
                } catch (\Exception $e) {
                    Log::error("❌ Error eliminando documento {$documentoId}:", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // ✅ PASO 2: AGREGAR DOCUMENTOS NUEVOS
        if (!isset($data['documentos_adicionales']) || !is_array($data['documentos_adicionales']) || empty($data['documentos_adicionales'])) {
            Log::info('⏭️ No hay documentos adicionales nuevos para procesar');
            return;
        }

        $documentos = $data['documentos_adicionales'];
        $totalDocumentos = count($documentos);

        Log::info("📄 Procesando {$totalDocumentos} documentos adicionales nuevos");

        foreach ($documentos as $index => $docData) {
            try {
                Log::info("📝 Procesando documento #{$index}:", [
                    'documento_id' => $docData['documento_id'] ?? 'NO DEFINIDO',
                    'archivo_path_temp' => $docData['archivo_path'] ?? 'NO DEFINIDO',
                ]);

                // ✅ VALIDAR ESTRUCTURA
                if (!isset($docData['documento_id']) || !isset($docData['archivo_path'])) {
                    Log::error("❌ Documento #{$index} tiene estructura inválida");
                    continue;
                }

                $documentoId = $docData['documento_id'];
                $tempPath = $docData['archivo_path'];

                // ✅ VALIDAR QUE EL DOCUMENTO EXISTE
                $documentoExiste = \App\Models\Documento::find($documentoId);
                if (!$documentoExiste) {
                    Log::error("❌ Documento ID {$documentoId} NO EXISTE en tabla documentos");
                    continue;
                }
                Log::info("✅ Documento ID {$documentoId} existe: {$documentoExiste->nombre}");

                // ✅ VALIDAR QUE EL ARCHIVO EXISTE
                if (!Storage::disk('public')->exists($tempPath)) {
                    Log::error("❌ Archivo temporal NO EXISTE: {$tempPath}");
                    continue;
                }
                Log::info("✅ Archivo temporal existe");

                // ✅ MOVER ARCHIVO DE TEMP A UBICACIÓN FINAL
                $finalPath = str_replace('documentos_temp', 'documentos', $tempPath);

                Log::info("📦 Moviendo archivo:", [
                    'de' => $tempPath,
                    'a' => $finalPath
                ]);

                // Crear directorio si no existe
                $finalDir = dirname(storage_path('app/public/' . $finalPath));
                if (!is_dir($finalDir)) {
                    mkdir($finalDir, 0755, true);
                    Log::info("📁 Directorio creado: {$finalDir}");
                }

                // Mover archivo
                Storage::disk('public')->move($tempPath, $finalPath);
                Log::info("✅ Archivo movido correctamente");

                // ✅ VERIFICAR QUE EL ARCHIVO SE MOVIÓ
                if (!Storage::disk('public')->exists($finalPath)) {
                    Log::error("❌ Archivo NO EXISTE después de mover: {$finalPath}");
                    continue;
                }

                // ✅ ADJUNTAR A LA VERSIÓN
                Log::info("🔗 Adjuntando documento a la versión...");

                $version->documentos()->attach($documentoId, [
                    'archivo' => $finalPath
                ]);

                Log::info("✅ Documento adicional #{$index} GUARDADO:", [
                    'documento_id' => $documentoId,
                    'version_id' => $version->id,
                    'archivo_final' => $finalPath,
                    'nombre_original' => $docData['archivo_nombre'] ?? 'N/A'
                ]);

                // ✅ VERIFICAR QUE SE GUARDÓ EN LA TABLA PIVOT
                $pivotCount = DB::table('documento_sistema_versiones')
                    ->where('documento_id', $documentoId)
                    ->where('sistema_version_id', $version->id)
                    ->count();

                Log::info("🔍 Verificación pivot - Registros encontrados: {$pivotCount}");
            } catch (\Exception $e) {
                Log::error("❌ Error procesando documento #{$index}:", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // ✅ VERIFICACIÓN FINAL
        $totalGuardados = DB::table('documento_sistema_versiones')
            ->where('sistema_version_id', $version->id)
            ->count();

        Log::info("✅ RESUMEN DOCUMENTOS ADICIONALES:", [
            'total_nuevos_procesados' => $totalDocumentos,
            'total_en_bd' => $totalGuardados
        ]);
    }

    /**
     * ✅ Ensamblar CUALQUIER archivo desde chunks
     */
    protected function ensamblarArchivo(
        string $identifier,
        int $totalChunks,
        string $tipoArchivo,
        VersionUpload $upload,
        int $progresoInicio,
        int $progresoFin
    ): string {
        $chunkDir = storage_path("app/chunks/{$identifier}");

        if (!is_dir($chunkDir)) {
            throw new \Exception("Directorio de chunks no encontrado: {$chunkDir}");
        }

        $upload->updateProgreso($progresoInicio, "Ensamblando {$tipoArchivo}...");

        // Determinar extensión según tipo
        $extension = match ($tipoArchivo) {
            'codigo_fuente' => '.zip',
            'manual_tecnico', 'manual_usuario' => '.pdf',
            default => '.bin',
        };

        // Nombre único del archivo
        $fileName = time() . '_' . uniqid() . $extension;

        // Directorio de destino según tipo
        $subdir = match ($tipoArchivo) {
            'codigo_fuente' => 'codigo',
            'manual_tecnico', 'manual_usuario' => 'manuales',
            default => 'otros',
        };

        $finalPath = storage_path("app/public/versiones/{$subdir}/{$fileName}");

        // Crear directorio si no existe
        if (!is_dir(dirname($finalPath))) {
            mkdir(dirname($finalPath), 0755, true);
        }

        // Abrir archivo final
        $finalFile = fopen($finalPath, 'wb');

        if (!$finalFile) {
            throw new \Exception("No se pudo crear el archivo final: {$finalPath}");
        }

        // Unir chunks
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = "{$chunkDir}/chunk_{$i}";

            if (!file_exists($chunkPath)) {
                fclose($finalFile);
                throw new \Exception("Chunk {$i} no encontrado en {$chunkDir}");
            }

            $chunkData = file_get_contents($chunkPath);
            fwrite($finalFile, $chunkData);
            unset($chunkData);

            // Actualizar progreso
            $progreso = $progresoInicio + (($i + 1) / $totalChunks) * ($progresoFin - $progresoInicio);
            $upload->updateProgreso((int)$progreso, "Ensamblando {$tipoArchivo}: " . ($i + 1) . "/{$totalChunks}");
        }

        fclose($finalFile);

        Log::info("✅ {$tipoArchivo} ensamblado: versiones/{$subdir}/{$fileName}");

        // Retornar ruta relativa
        return "versiones/{$subdir}/{$fileName}";
    }

    /**
     * Crear o actualizar versión definitiva
     */
    protected function crearVersionDefinitiva(VersionUpload $upload, int $progreso): array
    {
        $upload->updateProgreso($progreso, "Creando/actualizando registro definitivo...");

        DB::beginTransaction();

        try {
            $data = $upload->data;
            $esNuevaVersion = true;

            Log::info('🔍 Verificando modo de operación:', [
                'upload_id' => $upload->id,
                'tiene_version_id' => isset($data['version_id']),
                'version_id' => $data['version_id'] ?? null,
                'is_update' => $data['is_update'] ?? false,
            ]);

            // ✅ MODO UPDATE
            if (isset($data['version_id']) && $data['version_id']) {
                $version = SistemaVersion::findOrFail($data['version_id']);
                $esNuevaVersion = false;

                Log::info('🟢 MODO UPDATE - Actualizando versión ID: ' . $version->id);

                // Preparar datos para actualizar
                $updateData = [
                    'numero_version' => $upload->numero_version,
                    'descripcion' => $data['descripcion'] ?? $version->descripcion,
                    'fecha_lanzamiento' => $data['fecha_lanzamiento'],
                    'estado' => $data['estado'],
                    'es_actual' => ($data['es_actual'] ?? $version->es_actual),
                ];

                // ✅ SOLO actualizar archivos que fueron subidos
                if ($upload->temp_codigo_fuente) {
                    if ($version->codigo_fuente) {
                        Storage::disk('public')->delete($version->codigo_fuente);
                        Log::info("🗑️ Código fuente anterior eliminado: {$version->codigo_fuente}");
                    }
                    $updateData['codigo_fuente'] = $upload->temp_codigo_fuente;
                    Log::info("📦 Nuevo código fuente: {$upload->temp_codigo_fuente}");
                }

                if ($upload->temp_manual_tecnico) {
                    if ($version->manual_tecnico) {
                        Storage::disk('public')->delete($version->manual_tecnico);
                        Log::info("🗑️ Manual técnico anterior eliminado: {$version->manual_tecnico}");
                    }
                    $updateData['manual_tecnico'] = $upload->temp_manual_tecnico;
                    Log::info("📄 Nuevo manual técnico: {$upload->temp_manual_tecnico}");
                }

                if ($upload->temp_manual_usuario) {
                    if ($version->manual_usuario) {
                        Storage::disk('public')->delete($version->manual_usuario);
                        Log::info("🗑️ Manual usuario anterior eliminado: {$version->manual_usuario}");
                    }
                    $updateData['manual_usuario'] = $upload->temp_manual_usuario;
                    Log::info("📘 Nuevo manual usuario: {$upload->temp_manual_usuario}");
                }

                if ($upload->temp_imagen) {
                    if ($version->imagen) {
                        Storage::disk('public')->delete($version->imagen);
                        Log::info("🗑️ Imagen anterior eliminada: {$version->imagen}");
                    }
                    $updateData['imagen'] = $upload->temp_imagen;
                    Log::info("🖼️ Nueva imagen: {$upload->temp_imagen}");
                }

                $version->update($updateData);

                Log::info('✅ Versión ACTUALIZADA exitosamente', [
                    'version_id' => $version->id,
                    'numero_version' => $version->numero_version,
                ]);
            } else {
                // ✅ MODO CREATE
                $esNuevaVersion = true;

                Log::info('🔵 MODO CREATE - Creando nueva versión');

                // Desmarcar versión actual anterior si esta es estable
                if ($data['estado'] === 'estable') {
                    SistemaVersion::where('sistema_id', $upload->sistema_id)
                        ->where('es_actual', true)
                        ->update(['es_actual' => false]);
                }

                $version = SistemaVersion::create([
                    'sistema_id' => $upload->sistema_id,
                    'numero_version' => $upload->numero_version,
                    'descripcion' => $data['descripcion'] ?? null,
                    'fecha_lanzamiento' => $data['fecha_lanzamiento'],
                    'estado' => $data['estado'],
                    'es_actual' => ($data['estado'] === 'estable'),
                    'publicado_por' => $upload->user_id,
                    'imagen' => $upload->temp_imagen,
                    'codigo_fuente' => $upload->temp_codigo_fuente,
                    'manual_tecnico' => $upload->temp_manual_tecnico,
                    'manual_usuario' => $upload->temp_manual_usuario,
                ]);

                Log::info('✅ Nueva versión CREADA exitosamente', [
                    'version_id' => $version->id,
                    'numero_version' => $version->numero_version,
                ]);
            }

            $upload->updateProgreso(75, "Sincronizando relaciones...");

            // Sincronizar relaciones
            if (!empty($data['tecnologias'])) {
                $version->tecnologias()->sync($data['tecnologias']);
                Log::info("✅ Tecnologías sincronizadas: " . count($data['tecnologias']));
            }
            if (!empty($data['servidores'])) {
                $version->servidores()->sync($data['servidores']);
                Log::info("✅ Servidores sincronizados: " . count($data['servidores']));
            }
            if (!empty($data['bases_datos'])) {
                $version->basesDatos()->sync($data['bases_datos']);
                Log::info("✅ Bases de datos sincronizadas: " . count($data['bases_datos']));
            }
            if (!empty($data['credenciales'])) {
                $version->credenciales()->sync($data['credenciales']);
                Log::info("✅ Credenciales sincronizadas: " . count($data['credenciales']));
            }

            // Si se marcó como actual, desmarcar otras
            if ($data['es_actual'] ?? false) {
                SistemaVersion::where('sistema_id', $upload->sistema_id)
                    ->where('id', '!=', $version->id)
                    ->update(['es_actual' => false]);
                Log::info("✅ Versión marcada como actual, otras desmarcadas");
            }

            DB::commit();

            return [
                'version' => $version->fresh(),
                'es_nueva' => $esNuevaVersion
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error en crearVersionDefinitiva: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Limpiar chunks de TODOS los archivos
     */
    protected function limpiarTemporales(VersionUpload $upload, int $progreso): void
    {
        $upload->updateProgreso($progreso, "Limpiando archivos temporales...");

        $identifiers = [
            $upload->chunk_identifier,
            $upload->manual_tecnico_identifier,
            $upload->manual_usuario_identifier,
        ];

        foreach ($identifiers as $identifier) {
            if (!$identifier) continue;

            $chunkDir = storage_path("app/chunks/{$identifier}");

            if (is_dir($chunkDir)) {
                $files = glob("{$chunkDir}/*");
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($chunkDir);
                Log::info("✅ Chunks limpiados: {$identifier}");
            }
        }
    }

    /**
     * Enviar notificación a Telegram
     */
    protected function enviarNotificacionTelegram(SistemaVersion $version, VersionUpload $upload, int $progreso): void
    {
        $upload->updateProgreso($progreso, "Enviando notificación...");

        try {
            $telegramService = app(TelegramService::class);

            Log::info('🔍 TelegramService obtenido:', [
                'clase' => get_class($telegramService),
                'tiene_metodo_send' => method_exists($telegramService, 'sendMessage')
            ]);

            // ✅ CONTAR DOCUMENTOS ADICIONALES
            $totalDocumentosAdicionales = $version->documentos()->count();

            $data = [
                'sistema' => $version->sistema->nombre,
                'numero_version' => $version->numero_version,
                'fecha' => now()->format('d/m/Y H:i'),
                'usuario' => $version->publicadoPor->name ?? 'Sistema',
                'estado' => $version->estado,
                'tecnologias' => $version->tecnologias->map(fn($t) => ['nombre' => $t->nombre, 'tipo' => $t->tipo ?? null])->toArray(),
                'servidores' => $version->servidores->map(fn($s) => ['nombre' => $s->nombre, 'ip' => $s->ip ?? null])->toArray(),
                'bds' => $version->basesDatos->map(fn($b) => ['nombre' => $b->nombre ?? $b->gestor ?? 'BD'])->toArray(),
                'total_credenciales' => $version->credenciales->count(),
                'archivos' => [
                    'codigo_fuente' => $version->codigo_fuente,
                    'manual_tecnico' => $version->manual_tecnico,
                    'manual_usuario' => $version->manual_usuario,
                    'imagen' => $version->imagen,
                ],
                'documentos_adicionales' => $totalDocumentosAdicionales, // ✅ NUEVO
            ];

            $mensaje = "Nueva versión publicada: {$version->sistema->nombre} v{$version->numero_version} por {$data['usuario']}";

            $notificacion = \App\Models\Notificacion::create([
                'sistema_version_id' => $version->id,
                'fecha' => now(),
                'estado' => 'pendiente',
                'mensaje' => $mensaje,
                'usuario_enviado' => $upload->user_id,
            ]);

            $enviado = $telegramService->sendNewVersionNotification($data);

            if ($enviado) {
                $notificacion->update(['estado' => 'enviado']);
                Log::info('✅ Notificación enviada a Telegram');
            } else {
                $notificacion->update(['estado' => 'fallido']);
                Log::warning('⚠️ No se pudo enviar notificación a Telegram');
            }
        } catch (\Exception $e) {
            if (isset($notificacion)) {
                $notificacion->update(['estado' => 'fallido']);
            }
            Log::error('❌ Error enviando notificación: ' . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $upload = VersionUpload::find($this->uploadId);

        if ($upload) {
            $upload->marcarError("Job falló: " . $exception->getMessage());
        }

        Log::error("❌ Job falló para upload {$this->uploadId}: " . $exception->getMessage());
    }
}
