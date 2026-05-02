<?php

namespace App\Http\Controllers;

use App\Models\VersionUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.uploads.index')->only('index');
        $this->middleware('can:admin.uploads.show')->only(['show', 'chunksStatus', 'validarArchivo']);
        $this->middleware('can:admin.uploads.destroy')->only(['destroy', 'cancelar']);
    }
    /**
     * Mostrar lista de uploads del usuario
     */
    public function index()
    {
        $uploads = VersionUpload::where('user_id', Auth::id())
            ->with('sistema')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendientes = $uploads->whereIn('estado', ['pendiente', 'procesando'])->count();
        $completados = $uploads->where('estado', 'completado')->count();
        $errores = $uploads->where('estado', 'error')->count();

        return view('admin.uploads.index', compact('uploads', 'pendientes', 'completados', 'errores'));
    }

    /**
     * Mostrar detalles de un upload
     */
    public function show(VersionUpload $upload)
    {
        // Verificar que sea del usuario actual
        if ($upload->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $upload->load('sistema');

        return view('admin.uploads.show', compact('upload'));
    }

    /**
     * Validar archivo para reanudación
     */
    public function validarArchivo(Request $request, VersionUpload $upload)
    {
        // Verificar que sea del usuario actual
        if ($upload->user_id !== Auth::id()) {
            return response()->json([
                'es_el_mismo' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        // Verificar que el upload esté pendiente o procesando
        if (!in_array($upload->estado, ['pendiente', 'procesando'])) {
            return response()->json([
                'es_el_mismo' => false,
                'message' => 'Este upload ya fue completado o está en error'
            ]);
        }

        $request->validate([
            'nombre' => 'required|string',
            'tamano' => 'required|integer',
            'tipo' => 'required|string',
        ]);

        // VALIDAR: Nombre del archivo
        if ($upload->file_name && $request->nombre !== $upload->file_name) {
            return response()->json([
                'es_el_mismo' => false,
                'message' => 'El nombre del archivo no coincide',
                'upload_original' => [
                    'nombre' => $upload->file_name,
                    'tamano' => $this->formatBytes($upload->file_size),
                    'tipo' => $upload->file_type,
                ],
                'archivo_actual' => [
                    'nombre' => $request->nombre,
                    'tamano' => $this->formatBytes($request->tamano),
                    'tipo' => $request->tipo,
                ]
            ]);
        }

        // VALIDAR: Tamaño del archivo
        if ($upload->file_size && $request->tamano != $upload->file_size) {
            return response()->json([
                'es_el_mismo' => false,
                'message' => 'El tamaño del archivo no coincide',
                'upload_original' => [
                    'nombre' => $upload->file_name,
                    'tamano' => $this->formatBytes($upload->file_size),
                    'tipo' => $upload->file_type,
                ],
                'archivo_actual' => [
                    'nombre' => $request->nombre,
                    'tamano' => $this->formatBytes($request->tamano),
                    'tipo' => $request->tipo,
                ]
            ]);
        }

        // ARCHIVO VÁLIDO
        return response()->json([
            'success' => true,
            'es_el_mismo' => true,
            'message' => 'Archivo validado correctamente. Puedes continuar el upload.',
            'upload_data' => [
                'id' => $upload->id,
                'numero_version' => $upload->numero_version,
                'progreso' => $upload->progreso,
                'last_chunk_completed' => $upload->last_chunk_completed,
                'total_chunks' => $upload->total_chunks,
                'chunks_received' => $upload->chunks_received,
            ]
        ]);
    }

    /**
     * Cancelar upload
     */
    public function cancelar(Request $request, VersionUpload $upload)
    {
        // Verificar que sea del usuario actual
        if ($upload->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        // No permitir cancelar si ya está completado
        if ($upload->estado === 'completado') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar un upload completado'
            ], 400);
        }

        // Limpiar chunks
        if ($upload->chunk_identifier) {
            $chunkDir = storage_path("app/chunks/{$upload->chunk_identifier}");
            if (is_dir($chunkDir)) {
                $files = glob("{$chunkDir}/*");
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($chunkDir);
            }
        }

        // Limpiar archivos temporales
        if ($upload->temp_imagen) {
            Storage::disk('public')->delete($upload->temp_imagen);
        }
        if ($upload->temp_manual_tecnico) {
            Storage::disk('public')->delete($upload->temp_manual_tecnico);
        }
        if ($upload->temp_manual_usuario) {
            Storage::disk('public')->delete($upload->temp_manual_usuario);
        }

        // Eliminar registro
        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'Upload cancelado y archivos eliminados correctamente'
        ]);
    }

    /**
     * Iniciar reanudación de upload
     */
    public function resume(Request $request, VersionUpload $upload)
    {
        // Verificar que sea del usuario actual
        if ($upload->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        // Verificar que esté pendiente
        if ($upload->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Este upload no se puede reanudar'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Puede continuar con el upload',
            'resume_data' => [
                'upload_id' => $upload->id,
                'start_from_chunk' => $upload->last_chunk_completed + 1,
                'total_chunks' => $upload->total_chunks,
                'chunk_identifier' => $upload->chunk_identifier,
            ]
        ]);
    }

    /**
     * Obtener estado de chunks de TODOS los archivos (código fuente + manuales)
     */
    public function chunksStatus(VersionUpload $upload)
    {
        if ($upload->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $archivos = [];

        // ========== CÓDIGO FUENTE ==========
        if ($upload->chunk_identifier && $upload->file_name) {
            $chunkDir        = storage_path("app/chunks/{$upload->chunk_identifier}");
            $completedChunks = [];
            $directorioExiste = is_dir($chunkDir);

            if ($directorioExiste) {
                foreach (glob("{$chunkDir}/chunk_*") as $file) {
                    if (preg_match('/chunk_(\d+)$/', $file, $matches)) {
                        $completedChunks[] = (int) $matches[1];
                    }
                }
                sort($completedChunks);
            }

            $lastChunk = !empty($completedChunks) ? max($completedChunks) : -1;
            $progreso  = $upload->total_chunks > 0
                ? round((count($completedChunks) / $upload->total_chunks) * 100)
                : 0;

            $archivos['codigo_fuente'] = [
                'chunk_identifier'     => $upload->chunk_identifier,
                'total_chunks'         => $upload->total_chunks ?? 0,
                'chunks_received'      => count($completedChunks),
                'last_chunk_completed' => $lastChunk,
                'completed_chunks'     => $completedChunks,
                'next_chunk'           => $lastChunk + 1,
                'file_name'            => $upload->file_name,
                'file_size'            => $upload->file_size ?? 0,
                'progreso'             => $progreso,
                'directorio_existe'    => $directorioExiste,
            ];
        }

        // ========== ARCHIVO BASE DE DATOS ========== ✅
        if ($upload->archivo_bd_identifier && !empty($upload->data['archivo_bd_nombre'])) {
            $chunkDir        = storage_path("app/chunks/{$upload->archivo_bd_identifier}");
            $completedChunks = [];
            $directorioExiste = is_dir($chunkDir);

            if ($directorioExiste) {
                foreach (glob("{$chunkDir}/chunk_*") as $file) {
                    if (preg_match('/chunk_(\d+)$/', $file, $matches)) {
                        $completedChunks[] = (int) $matches[1];
                    }
                }
                sort($completedChunks);
            }

            $lastChunk = !empty($completedChunks) ? max($completedChunks) : -1;
            $progreso  = $upload->archivo_bd_total_chunks > 0
                ? round((count($completedChunks) / $upload->archivo_bd_total_chunks) * 100)
                : 0;

            $archivos['archivo_bd'] = [
                'chunk_identifier'     => $upload->archivo_bd_identifier,
                'total_chunks'         => $upload->archivo_bd_total_chunks ?? 0,
                'chunks_received'      => count($completedChunks),
                'last_chunk_completed' => $lastChunk,
                'completed_chunks'     => $completedChunks,
                'next_chunk'           => $lastChunk + 1,
                'file_name'            => $upload->data['archivo_bd_nombre'] ?? null,
                'file_size'            => $upload->data['archivo_bd_tamano'] ?? 0,
                'progreso'             => $progreso,
                'directorio_existe'    => $directorioExiste,
            ];
        }

        // ========== MANUAL TÉCNICO ==========
        if ($upload->manual_tecnico_identifier && $upload->manual_tecnico_name) {
            $chunkDir        = storage_path("app/chunks/{$upload->manual_tecnico_identifier}");
            $completedChunks = [];
            $directorioExiste = is_dir($chunkDir);

            if ($directorioExiste) {
                foreach (glob("{$chunkDir}/chunk_*") as $file) {
                    if (preg_match('/chunk_(\d+)$/', $file, $matches)) {
                        $completedChunks[] = (int) $matches[1];
                    }
                }
                sort($completedChunks);
            }

            $lastChunk = !empty($completedChunks) ? max($completedChunks) : -1;
            $progreso  = $upload->manual_tecnico_total_chunks > 0
                ? round((count($completedChunks) / $upload->manual_tecnico_total_chunks) * 100)
                : 0;

            $archivos['manual_tecnico'] = [
                'chunk_identifier'     => $upload->manual_tecnico_identifier,
                'total_chunks'         => $upload->manual_tecnico_total_chunks ?? 0,
                'chunks_received'      => count($completedChunks),
                'last_chunk_completed' => $lastChunk,
                'completed_chunks'     => $completedChunks,
                'next_chunk'           => $lastChunk + 1,
                'file_name'            => $upload->manual_tecnico_name,
                'file_size'            => $upload->manual_tecnico_size ?? 0,
                'progreso'             => $progreso,
                'directorio_existe'    => $directorioExiste,
            ];
        }

        // ========== MANUAL USUARIO ==========
        if ($upload->manual_usuario_identifier && $upload->manual_usuario_name) {
            $chunkDir        = storage_path("app/chunks/{$upload->manual_usuario_identifier}");
            $completedChunks = [];
            $directorioExiste = is_dir($chunkDir);

            if ($directorioExiste) {
                foreach (glob("{$chunkDir}/chunk_*") as $file) {
                    if (preg_match('/chunk_(\d+)$/', $file, $matches)) {
                        $completedChunks[] = (int) $matches[1];
                    }
                }
                sort($completedChunks);
            }

            $lastChunk = !empty($completedChunks) ? max($completedChunks) : -1;
            $progreso  = $upload->manual_usuario_total_chunks > 0
                ? round((count($completedChunks) / $upload->manual_usuario_total_chunks) * 100)
                : 0;

            $archivos['manual_usuario'] = [
                'chunk_identifier'     => $upload->manual_usuario_identifier,
                'total_chunks'         => $upload->manual_usuario_total_chunks ?? 0,
                'chunks_received'      => count($completedChunks),
                'last_chunk_completed' => $lastChunk,
                'completed_chunks'     => $completedChunks,
                'next_chunk'           => $lastChunk + 1,
                'file_name'            => $upload->manual_usuario_name,
                'file_size'            => $upload->manual_usuario_size ?? 0,
                'progreso'             => $progreso,
                'directorio_existe'    => $directorioExiste,
            ];
        }

        return response()->json([
            'upload_id'        => $upload->id,
            'numero_version'   => $upload->numero_version,
            'estado'           => $upload->estado,
            'progreso_general' => $upload->progreso,
            'archivos'         => $archivos,
            'is_update'        => $upload->data['is_update'] ?? false,
        ]);
    }

    /**
     * Cancelar y eliminar upload
     */
    public function destroy(VersionUpload $upload)
    {
        // Verificar que sea del usuario actual
        if ($upload->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No autorizado');
        }

        // Limpiar chunks
        if ($upload->chunk_identifier) {
            $chunkDir = storage_path("app/chunks/{$upload->chunk_identifier}");
            if (is_dir($chunkDir)) {
                $files = glob("{$chunkDir}/*");
                array_map('unlink', $files);
                rmdir($chunkDir);
            }
        }

        // Limpiar archivos temporales
        if ($upload->temp_imagen) {
            Storage::disk('public')->delete($upload->temp_imagen);
        }
        if ($upload->temp_manual_tecnico) {
            Storage::disk('public')->delete($upload->temp_manual_tecnico);
        }
        if ($upload->temp_manual_usuario) {
            Storage::disk('public')->delete($upload->temp_manual_usuario);
        }

        // Eliminar registro
        $upload->delete();

        return redirect()->route('admin.uploads.index')
            ->with('success', 'Upload cancelado correctamente');
    }

    /**
     * Formatear bytes
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
