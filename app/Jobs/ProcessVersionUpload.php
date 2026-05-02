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

    public $timeout = 600;
    public $tries   = 3;

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

            // ========== PASO 1: Ensamblar CÓDIGO FUENTE (10% - 35%) ==========
            if ($upload->chunk_identifier && $upload->total_chunks > 0) {
                Log::info("📦 Ensamblando código fuente...");

                $codigoFuentePath = $this->ensamblarArchivo(
                    $upload->chunk_identifier,
                    $upload->total_chunks,
                    'codigo_fuente',
                    $upload,
                    10,
                    35
                );

                $upload->refresh();
                $upload->update(['temp_codigo_fuente' => $codigoFuentePath]);
                $upload->refresh();

                Log::info("✅ Código fuente ensamblado: {$codigoFuentePath}");
            } else {
                Log::info("⏭️ Sin código fuente nuevo");
                $upload->updateProgreso(35, "Sin código fuente nuevo");
            }

            // ========== PASO 2: Ensamblar MANUAL TÉCNICO (35% - 45%) ==========
            if ($upload->manual_tecnico_identifier && $upload->manual_tecnico_total_chunks > 0) {
                Log::info("📄 Ensamblando manual técnico...");

                $manualTecnicoPath = $this->ensamblarArchivo(
                    $upload->manual_tecnico_identifier,
                    $upload->manual_tecnico_total_chunks,
                    'manual_tecnico',
                    $upload,
                    35,
                    45
                );

                $upload->refresh();
                $upload->update(['temp_manual_tecnico' => $manualTecnicoPath]);
                $upload->refresh();

                Log::info("✅ Manual técnico ensamblado: {$manualTecnicoPath}");
            } else {
                Log::info("⏭️ Sin manual técnico nuevo");
                $upload->updateProgreso(45, "Sin manual técnico nuevo");
            }

            // ========== PASO 3: Ensamblar MANUAL USUARIO (45% - 55%) ==========
            if ($upload->manual_usuario_identifier && $upload->manual_usuario_total_chunks > 0) {
                Log::info("📘 Ensamblando manual usuario...");

                $manualUsuarioPath = $this->ensamblarArchivo(
                    $upload->manual_usuario_identifier,
                    $upload->manual_usuario_total_chunks,
                    'manual_usuario',
                    $upload,
                    45,
                    55
                );

                $upload->refresh();
                $upload->update(['temp_manual_usuario' => $manualUsuarioPath]);
                $upload->refresh();

                Log::info("✅ Manual usuario ensamblado: {$manualUsuarioPath}");
            } else {
                Log::info("⏭️ Sin manual usuario nuevo");
                $upload->updateProgreso(55, "Sin manual usuario nuevo");
            }

            // ========== PASO 4: Ensamblar ARCHIVO BD (55% - 65%) ==========
            if ($upload->archivo_bd_identifier && $upload->archivo_bd_total_chunks > 0) {
                Log::info("🗄️ Ensamblando archivo de base de datos...");

                // Recuperar nombre original para preservar extensión
                $data           = $upload->data;
                $nombreOriginal = $data['archivo_bd_nombre'] ?? 'backup.sql';
                $extension      = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

                $archivoBdPath = $this->ensamblarArchivo(
                    $upload->archivo_bd_identifier,
                    $upload->archivo_bd_total_chunks,
                    'archivo_bd',
                    $upload,
                    55,
                    65,
                    $extension // pasar extensión original
                );

                $upload->refresh();
                $upload->update(['temp_archivo_bd' => $archivoBdPath]);
                $upload->refresh();

                Log::info("✅ Archivo BD ensamblado: {$archivoBdPath}");
            } else {
                Log::info("⏭️ Sin archivo BD nuevo");
                $upload->updateProgreso(65, "Sin archivo BD nuevo");
            }

            // ========== PASO 5: Crear o actualizar versión (65% - 80%) ==========
            $upload->refresh();

            Log::info("📋 Estado antes de crear/actualizar versión:", [
                'temp_codigo_fuente'  => $upload->temp_codigo_fuente,
                'temp_manual_tecnico' => $upload->temp_manual_tecnico,
                'temp_manual_usuario' => $upload->temp_manual_usuario,
                'temp_archivo_bd'     => $upload->temp_archivo_bd,
                'temp_imagen'         => $upload->temp_imagen,
            ]);

            $resultado       = $this->crearVersionDefinitiva($upload, 70);
            $version         = $resultado['version'];
            $esNuevaVersion  = $resultado['es_nueva'];

            Log::info("✅ Versión procesada", ['version_id' => $version->id, 'es_nueva' => $esNuevaVersion]);

            // ========== PASO 6: Documentos adicionales (80% - 85%) ==========
            $upload->refresh();
            $this->procesarDocumentosAdicionales($upload, $version, 82);

            // ========== PASO 7: Limpiar temporales (85% - 90%) ==========
            $this->limpiarTemporales($upload, 90);

            // ========== PASO 8: Notificación Telegram (90% - 95%) ==========
            if ($esNuevaVersion) {
                $this->enviarNotificacionTelegram($version, $upload, 95);
            } else {
                $upload->updateProgreso(95, "Versión actualizada, sin notificación");
            }

            // ========== PASO 9: Completado ==========
            $upload->marcarCompletado();
            Log::info("✅ Upload {$this->uploadId} procesado exitosamente");
        } catch (\Exception $e) {
            $upload->marcarError($e->getMessage());
            Log::error("❌ Error procesando upload {$this->uploadId}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    // =========================================================================
    // ENSAMBLAR ARCHIVO DESDE CHUNKS
    // =========================================================================

    protected function ensamblarArchivo(
        string $identifier,
        int    $totalChunks,
        string $tipoArchivo,
        VersionUpload $upload,
        int    $progresoInicio,
        int    $progresoFin,
        string $extensionForzada = '' // para archivo_bd que tiene extensión variable
    ): string {
        $chunkDir = storage_path("app/chunks/{$identifier}");

        if (!is_dir($chunkDir)) {
            throw new \Exception("Directorio de chunks no encontrado: {$chunkDir}");
        }

        $upload->updateProgreso($progresoInicio, "Ensamblando {$tipoArchivo}...");

        // Determinar extensión
        if ($extensionForzada !== '') {
            $extension = '.' . ltrim($extensionForzada, '.');
        } else {
            $extension = match ($tipoArchivo) {
                'codigo_fuente'                  => '.zip',
                'manual_tecnico', 'manual_usuario' => '.pdf',
                default                          => '.bin',
            };
        }

        // Directorio de destino
        $subdir = match ($tipoArchivo) {
            'codigo_fuente'                  => 'codigo',
            'manual_tecnico', 'manual_usuario' => 'manuales',
            'archivo_bd'                     => 'bases_datos',
            default                          => 'otros',
        };

        $fileName  = time() . '_' . uniqid() . $extension;
        $finalPath = storage_path("app/public/versiones/{$subdir}/{$fileName}");

        if (!is_dir(dirname($finalPath))) {
            mkdir(dirname($finalPath), 0755, true);
        }

        $finalFile = fopen($finalPath, 'wb');

        if (!$finalFile) {
            throw new \Exception("No se pudo crear el archivo final: {$finalPath}");
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = "{$chunkDir}/chunk_{$i}";

            if (!file_exists($chunkPath)) {
                fclose($finalFile);
                throw new \Exception("Chunk {$i} no encontrado en {$chunkDir}");
            }

            $chunkData = file_get_contents($chunkPath);
            fwrite($finalFile, $chunkData);
            unset($chunkData);

            $progreso = $progresoInicio + (($i + 1) / $totalChunks) * ($progresoFin - $progresoInicio);
            $upload->updateProgreso((int)$progreso, "Ensamblando {$tipoArchivo}: " . ($i + 1) . "/{$totalChunks}");
        }

        fclose($finalFile);

        Log::info("✅ {$tipoArchivo} ensamblado: versiones/{$subdir}/{$fileName}");

        return "versiones/{$subdir}/{$fileName}";
    }

    // =========================================================================
    // CREAR O ACTUALIZAR VERSIÓN DEFINITIVA
    // =========================================================================

    protected function crearVersionDefinitiva(VersionUpload $upload, int $progreso): array
    {
        $upload->updateProgreso($progreso, "Creando/actualizando registro definitivo...");

        DB::beginTransaction();

        try {
            $data           = $upload->data;
            $esNuevaVersion = true;

            Log::info('🔍 Modo de operación:', [
                'version_id' => $data['version_id'] ?? null,
                'is_update'  => $data['is_update'] ?? false,
            ]);

            // ── MODO UPDATE ──────────────────────────────────────────────────
            if (!empty($data['version_id'])) {
                $version        = SistemaVersion::findOrFail($data['version_id']);
                $esNuevaVersion = false;

                Log::info('🟢 MODO UPDATE — versión ID: ' . $version->id);

                $updateData = [
                    'numero_version'   => $upload->numero_version,
                    'descripcion'      => $data['descripcion'] ?? $version->descripcion,
                    'fecha_lanzamiento' => $data['fecha_lanzamiento'],
                    'estado'           => $data['estado'],
                    'es_actual'        => $data['es_actual'] ?? $version->es_actual,
                ];

                if ($upload->temp_codigo_fuente) {
                    if ($version->codigo_fuente) Storage::disk('public')->delete($version->codigo_fuente);
                    $updateData['codigo_fuente'] = $upload->temp_codigo_fuente;
                }
                if ($upload->temp_manual_tecnico) {
                    if ($version->manual_tecnico) Storage::disk('public')->delete($version->manual_tecnico);
                    $updateData['manual_tecnico'] = $upload->temp_manual_tecnico;
                }
                if ($upload->temp_manual_usuario) {
                    if ($version->manual_usuario) Storage::disk('public')->delete($version->manual_usuario);
                    $updateData['manual_usuario'] = $upload->temp_manual_usuario;
                }
                // ✅ ARCHIVO BD
                if ($upload->temp_archivo_bd) {
                    if ($version->archivo_bd) Storage::disk('public')->delete($version->archivo_bd);
                    $updateData['archivo_bd'] = $upload->temp_archivo_bd;
                    Log::info("🗄️ Nuevo archivo BD: {$upload->temp_archivo_bd}");
                }
                if ($upload->temp_imagen) {
                    if ($version->imagen) Storage::disk('public')->delete($version->imagen);
                    $updateData['imagen'] = $upload->temp_imagen;
                }

                $version->update($updateData);
                Log::info('✅ Versión ACTUALIZADA', ['version_id' => $version->id]);

                // ── MODO CREATE ──────────────────────────────────────────────────
            } else {
                Log::info('🔵 MODO CREATE — nueva versión');

                if ($data['estado'] === 'estable') {
                    SistemaVersion::where('sistema_id', $upload->sistema_id)
                        ->where('es_actual', true)
                        ->update(['es_actual' => false]);
                }

                $version = SistemaVersion::create([
                    'sistema_id'        => $upload->sistema_id,
                    'numero_version'    => $upload->numero_version,
                    'descripcion'       => $data['descripcion'] ?? null,
                    'fecha_lanzamiento' => $data['fecha_lanzamiento'],
                    'estado'            => $data['estado'],
                    'es_actual'         => ($data['estado'] === 'estable'),
                    'publicado_por'     => $upload->user_id,
                    'imagen'            => $upload->temp_imagen,
                    'codigo_fuente'     => $upload->temp_codigo_fuente,
                    'manual_tecnico'    => $upload->temp_manual_tecnico,
                    'manual_usuario'    => $upload->temp_manual_usuario,
                    'archivo_bd'        => $upload->temp_archivo_bd, // ✅
                ]);

                Log::info('✅ Versión CREADA', ['version_id' => $version->id]);
            }

            $upload->updateProgreso(75, "Sincronizando relaciones...");

            if (!empty($data['tecnologias']))  $version->tecnologias()->sync($data['tecnologias']);
            if (!empty($data['servidores']))   $version->servidores()->sync($data['servidores']);
            if (!empty($data['bases_datos']))  $version->basesDatos()->sync($data['bases_datos']);
            if (!empty($data['credenciales'])) $version->credenciales()->sync($data['credenciales']);

            if (!empty($data['es_actual'])) {
                SistemaVersion::where('sistema_id', $upload->sistema_id)
                    ->where('id', '!=', $version->id)
                    ->update(['es_actual' => false]);
            }

            DB::commit();

            return ['version' => $version->fresh(), 'es_nueva' => $esNuevaVersion];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error en crearVersionDefinitiva: " . $e->getMessage());
            throw $e;
        }
    }

    // =========================================================================
    // DOCUMENTOS ADICIONALES
    // =========================================================================

    protected function procesarDocumentosAdicionales(VersionUpload $upload, SistemaVersion $version, int $progreso): void
    {
        $upload->updateProgreso($progreso, "Procesando documentos adicionales...");
        $data = $upload->data;

        // Eliminar documentos marcados (solo en UPDATE)
        if (!empty($data['documentos_eliminar'])) {
            foreach ($data['documentos_eliminar'] as $documentoId) {
                try {
                    $pivot = DB::table('documento_sistema_versiones')
                        ->where('sistema_version_id', $version->id)
                        ->where('documento_id', $documentoId)
                        ->first();

                    if ($pivot && $pivot->archivo && Storage::disk('public')->exists($pivot->archivo)) {
                        Storage::disk('public')->delete($pivot->archivo);
                    }

                    $version->documentos()->detach($documentoId);
                    Log::info("🗑️ Documento {$documentoId} eliminado");
                } catch (\Exception $e) {
                    Log::error("❌ Error eliminando documento {$documentoId}: " . $e->getMessage());
                }
            }
        }

        // Agregar documentos nuevos
        if (empty($data['documentos_adicionales'])) {
            Log::info("⏭️ Sin documentos adicionales nuevos");
            return;
        }

        foreach ($data['documentos_adicionales'] as $index => $docData) {
            try {
                if (empty($docData['documento_id']) || empty($docData['archivo_path'])) {
                    Log::warning("⚠️ Documento #{$index} con estructura inválida");
                    continue;
                }

                $documentoId = $docData['documento_id'];
                $tempPath    = $docData['archivo_path'];

                if (!\App\Models\Documento::find($documentoId)) {
                    Log::error("❌ Documento ID {$documentoId} no existe");
                    continue;
                }

                if (!Storage::disk('public')->exists($tempPath)) {
                    Log::error("❌ Archivo temporal no existe: {$tempPath}");
                    continue;
                }

                $finalPath = str_replace('documentos_temp', 'documentos', $tempPath);
                $finalDir  = dirname(storage_path('app/public/' . $finalPath));

                if (!is_dir($finalDir)) mkdir($finalDir, 0755, true);

                Storage::disk('public')->move($tempPath, $finalPath);

                $version->documentos()->attach($documentoId, ['archivo' => $finalPath]);

                Log::info("✅ Documento #{$index} guardado: {$finalPath}");
            } catch (\Exception $e) {
                Log::error("❌ Error en documento #{$index}: " . $e->getMessage());
            }
        }
    }

    // =========================================================================
    // LIMPIAR TEMPORALES
    // =========================================================================

    protected function limpiarTemporales(VersionUpload $upload, int $progreso): void
    {
        $upload->updateProgreso($progreso, "Limpiando archivos temporales...");

        $identifiers = array_filter([
            $upload->chunk_identifier,
            $upload->manual_tecnico_identifier,
            $upload->manual_usuario_identifier,
            $upload->archivo_bd_identifier,   // ✅ agregar
        ]);

        foreach ($identifiers as $identifier) {
            $chunkDir = storage_path("app/chunks/{$identifier}");

            if (is_dir($chunkDir)) {
                foreach (glob("{$chunkDir}/*") as $file) {
                    if (is_file($file)) unlink($file);
                }
                rmdir($chunkDir);
                Log::info("✅ Chunks limpiados: {$identifier}");
            }
        }
    }

    // =========================================================================
    // NOTIFICACIÓN TELEGRAM
    // =========================================================================

    protected function enviarNotificacionTelegram(SistemaVersion $version, VersionUpload $upload, int $progreso): void
    {
        $upload->updateProgreso($progreso, "Enviando notificación...");

        try {
            $telegramService = app(TelegramService::class);

            $data = [
                'sistema'          => $version->sistema->nombre,
                'numero_version'   => $version->numero_version,
                'fecha'            => now()->format('d/m/Y H:i'),
                'usuario'          => $version->publicadoPor->name ?? 'Sistema',
                'estado'           => $version->estado,
                'tecnologias'      => $version->tecnologias->map(fn($t) => ['nombre' => $t->nombre, 'tipo' => $t->tipo ?? null])->toArray(),
                'servidores'       => $version->servidores->map(fn($s) => ['nombre' => $s->nombre, 'ip' => $s->ip ?? null])->toArray(),
                'bds'              => $version->basesDatos->map(fn($b) => ['nombre' => $b->nombre ?? $b->gestor ?? 'BD'])->toArray(),
                'total_credenciales'          => $version->credenciales->count(),
                'documentos_adicionales'      => $version->documentos()->count(),
                'archivos' => [
                    'codigo_fuente'  => $version->codigo_fuente,
                    'manual_tecnico' => $version->manual_tecnico,
                    'manual_usuario' => $version->manual_usuario,
                    'archivo_bd'     => $version->archivo_bd,   // ✅
                    'imagen'         => $version->imagen,
                ],
            ];

            $mensaje = "Nueva versión publicada: {$version->sistema->nombre} v{$version->numero_version} por {$data['usuario']}";

            $notificacion = \App\Models\Notificacion::create([
                'sistema_version_id' => $version->id,
                'fecha'              => now(),
                'estado'             => 'pendiente',
                'mensaje'            => $mensaje,
                'usuario_enviado'    => $upload->user_id,
            ]);

            $enviado = $telegramService->sendNewVersionNotification($data);

            $notificacion->update(['estado' => $enviado ? 'enviado' : 'fallido']);

            Log::info($enviado ? '✅ Notificación enviada' : '⚠️ Notificación fallida');
        } catch (\Exception $e) {
            if (isset($notificacion)) $notificacion->update(['estado' => 'fallido']);
            Log::error('❌ Error enviando notificación: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // JOB FAILURE
    // =========================================================================

    public function failed(\Throwable $exception): void
    {
        $upload = VersionUpload::find($this->uploadId);
        if ($upload) $upload->marcarError("Job falló: " . $exception->getMessage());
        Log::error("❌ Job falló para upload {$this->uploadId}: " . $exception->getMessage());
    }
}
