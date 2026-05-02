<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessVersionUpload;
use App\Models\BaseDato;
use App\Models\Credencial;
use App\Models\Documento;
use App\Models\Servidor;
use App\Models\Sistema;
use App\Models\SistemaVersion;
use App\Models\Tecnologia;
use App\Models\VersionUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SistemaVersionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.versiones.index')->only(['index', 'checkDuplicate', 'listarUploads']);
        $this->middleware('can:admin.versiones.store')->only(['create', 'store', 'iniciarUpload', 'uploadChunk', 'uploadManualChunk', 'completarUpload']);
        $this->middleware('can:admin.versiones.edit')->only(['edit', 'getUploadStatus']);
        $this->middleware('can:admin.versiones.update')->only('update');
        $this->middleware('can:admin.versiones.actual')->only('marcarActual');
        $this->middleware('can:admin.versiones.destroy')->only(['destroy', 'cancelarUpload']);
        $this->middleware('can:admin.versiones.restore')->only('restore');
        $this->middleware('can:admin.versiones.show')->only('descargar');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Sistema $sistema)
    {
        // Versiones activas con paginación (12 por página)
        $versiones = $sistema->versiones()
            ->with(['tecnologias', 'servidores', 'basesDatos', 'credenciales', 'publicadoPor'])
            ->orderBy('es_actual', 'desc')
            ->orderBy('fecha_lanzamiento', 'desc')
            ->paginate(12);

        // Versiones eliminadas con paginación separada
        $versionesEliminadas = $sistema->versiones()
            ->onlyTrashed()
            ->with(['publicadoPor'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(12, ['*'], 'papelera_page');

        // Para los selects
        $tecnologias = Tecnologia::where('estado', 'activo')->orderBy('nombre')->get();
        $servidores = Servidor::where('estado', 'activo')->orderBy('nombre')->get();
        $basesDatos = BaseDato::where('estado', 'activo')->orderBy('gestor')->get();
        $credenciales = Credencial::where('estado', 'activo')
            ->where('sistema_id', $sistema->id)
            ->orderBy('usuario')
            ->get();

        return view('admin.sistemas.versiones.index', compact(
            'sistema',
            'versiones',
            'versionesEliminadas',
            'tecnologias',
            'servidores',
            'basesDatos',
            'credenciales'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Sistema $sistema)
    {
        $tecnologias = Tecnologia::where('estado', 'activo')->orderBy('nombre')->get();
        $servidores = Servidor::where('estado', 'activo')->orderBy('nombre')->get();
        $basesDatos = BaseDato::where('estado', 'activo')->orderBy('gestor')->get();
        $credenciales = Credencial::where('estado', 'activo')
            ->where('sistema_id', $sistema->id)
            ->orderBy('usuario')
            ->get();

        $documentos = Documento::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $resumeUpload = null;
        if ($request->has('resume')) {
            $uploadId = $request->get('resume');
            $resumeUpload = VersionUpload::where('id', $uploadId)
                ->where('user_id', Auth::id())
                ->where('sistema_id', $sistema->id)
                ->whereIn('estado', ['pendiente', 'procesando'])
                ->first();

            if (!$resumeUpload) {
                return redirect()
                    ->route('admin.sistemas.versiones.index', $sistema)
                    ->with('error', 'El upload no existe o no se puede reanudar');
            }
        }

        return view('admin.sistemas.versiones.create', compact(
            'sistema',
            'tecnologias',
            'servidores',
            'basesDatos',
            'credenciales',
            'documentos',
            'resumeUpload'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Sistema $sistema)
    {
        // Log para ver qué llega
        Log::info('📦 Datos recibidos:', [
            'documentos' => $request->documentos,
            'files' => $request->allFiles()
        ]);

        $validated = $request->validate([
            'numero_version' => 'required|string|max:50|unique:sistema_versiones,numero_version,NULL,id,sistema_id,' . $sistema->id,
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'codigo_fuente' => 'required|file|mimes:zip,rar|max:10485760',
            'manual_tecnico' => 'nullable|file|mimes:pdf|max:102400',
            'manual_usuario' => 'nullable|file|mimes:pdf|max:102400',
            'fecha_lanzamiento' => 'required|date',
            'archivo_bd' => [
                'nullable',
                'file',
                'max:512000', // 500MB
                function ($attribute, $value, $fail) {
                    $extensiones = [
                        'sql',
                        'gz',
                        'xbk',           // MySQL/MariaDB
                        'dump',
                        'backup',
                        'tar',        // PostgreSQL
                        'bson',
                        'json',
                        'archive',      // MongoDB
                        'bak',
                        'bz2',
                        'zip',            // General
                    ];
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, $extensiones)) {
                        $fail('El archivo de BD no tiene un formato permitido.');
                    }
                }
            ],
            'estado' => 'required|in:estable,beta,deprecated',
            'tecnologias' => 'required|array|min:1',
            'servidores' => 'required|array|min:1',
            'bases_datos' => 'required|array|min:1',
            'credenciales' => 'nullable|array',

            // Documentos adicionales
            'documentos' => 'nullable|array',
            'documentos.*.documento_id' => 'required_with:documentos|exists:documentos,id',
            'documentos.*.archivo' => 'required_with:documentos|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:51200',
        ]);

        // Manejar archivos principales
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('versiones/imagenes', 'public');
        }

        if ($request->hasFile('codigo_fuente')) {
            $validated['codigo_fuente'] = $request->file('codigo_fuente')->store('versiones/codigo', 'public');
        }

        if ($request->hasFile('manual_tecnico')) {
            $validated['manual_tecnico'] = $request->file('manual_tecnico')->store('versiones/manuales', 'public');
        }

        if ($request->hasFile('manual_usuario')) {
            $validated['manual_usuario'] = $request->file('manual_usuario')->store('versiones/manuales', 'public');
        }

        if ($request->hasFile('archivo_bd')) {
            $validated['archivo_bd'] = $request->file('archivo_bd')
                ->store('versiones/bases_datos', 'public');
        }

        $validated['sistema_id'] = $sistema->id;
        $validated['publicado_por'] = Auth::id();

        // Al crear una versión SIEMPRE es la actual
        $sistema->versiones()->update(['es_actual' => 0]);
        $validated['es_actual'] = 1;

        // Crear la versión
        $version = SistemaVersion::create($validated);

        Log::info('✅ Versión creada:', ['version_id' => $version->id]);

        // Sincronizar relaciones
        $version->tecnologias()->sync($request->tecnologias);
        $version->servidores()->sync($request->servidores);
        $version->basesDatos()->sync($request->bases_datos);
        $version->credenciales()->sync($data['credenciales'] ?? []);

        // ✅ GUARDAR DOCUMENTOS ADICIONALES
        if ($request->has('documentos') && is_array($request->documentos)) {
            Log::info('📄 Procesando documentos adicionales...');

            foreach ($request->documentos as $index => $docData) {
                Log::info("Documento #{$index}:", $docData);

                // Verificar que tenga documento_id
                if (!isset($docData['documento_id']) || empty($docData['documento_id'])) {
                    Log::warning("⚠️ Documento #{$index} sin documento_id");
                    continue;
                }

                // Verificar que tenga archivo
                if (!isset($docData['archivo'])) {
                    Log::warning("⚠️ Documento #{$index} sin archivo");
                    continue;
                }

                $documentoId = $docData['documento_id'];
                $archivoFile = $docData['archivo'];

                // Guardar archivo en storage
                $archivoPath = $archivoFile->store('versiones/documentos', 'public');

                Log::info("💾 Archivo guardado:", [
                    'documento_id' => $documentoId,
                    'path' => $archivoPath,
                    'original_name' => $archivoFile->getClientOriginalName()
                ]);

                // Insertar en la tabla pivot
                try {
                    $version->documentos()->attach($documentoId, [
                        'archivo' => $archivoPath
                    ]);

                    Log::info("✅ Documento #{$index} guardado en BD");
                } catch (\Exception $e) {
                    Log::error("❌ Error al guardar documento #{$index}:", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Verificar qué se guardó
            $documentosGuardados = $version->documentos()->count();
            Log::info("📊 Total documentos guardados: {$documentosGuardados}");
        } else {
            Log::info('ℹ️ No se enviaron documentos adicionales');
        }

        return redirect()
            ->route('admin.sistemas.versiones.index', $sistema)
            ->with('success', 'Versión ' . $version->numero_version . ' creada correctamente y marcada como actual');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sistema $sistema, SistemaVersion $version)
    {
        $tecnologias = Tecnologia::where('estado', 'activo')->orderBy('nombre')->get();
        $servidores = Servidor::where('estado', 'activo')->orderBy('nombre')->get();
        $basesDatos = BaseDato::where('estado', 'activo')->orderBy('gestor')->get();
        $credenciales = Credencial::where('estado', 'activo')
            ->where('sistema_id', $sistema->id)
            ->orderBy('usuario')
            ->get();

        $version->load(['tecnologias', 'servidores', 'basesDatos', 'credenciales', 'documentos']);

        $documentosAsociadosIds = $version->documentos->pluck('id')->toArray();

        $documentos = Documento::where('activo', 1)
            ->whereNotIn('id', $documentosAsociadosIds)
            ->orderBy('nombre')
            ->get();

        // 🔹 BUSCAR SI HAY UN UPLOAD PENDIENTE PARA ESTA VERSIÓN
        $resumeUpload = VersionUpload::where('user_id', Auth::id())
            ->where('sistema_id', $sistema->id)
            ->where('numero_version', $version->numero_version)
            ->whereIn('estado', ['pendiente', 'procesando'])
            ->first();


        return view('admin.sistemas.versiones.edit', compact(
            'sistema',
            'version',
            'tecnologias',
            'servidores',
            'basesDatos',
            'credenciales',
            'documentos',
            'resumeUpload'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sistema $sistema, SistemaVersion $version)
    {
        // 🔹 VALIDACIÓN: Los archivos son OPCIONALES en actualización
        $validated = $request->validate([
            'numero_version' => 'required|string|max:50|unique:sistema_versiones,numero_version,' . $version->id . ',id,sistema_id,' . $sistema->id,
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'codigo_fuente' => 'nullable|file|mimes:zip,rar|max:10485760',
            'manual_tecnico' => 'nullable|file|mimes:pdf|max:102400',
            'manual_usuario' => 'nullable|file|mimes:pdf|max:102400',
            'fecha_lanzamiento' => 'required|date',
            'archivo_bd' => [
                'nullable',
                'file',
                'max:512000', // 500MB
                function ($attribute, $value, $fail) {
                    $extensiones = [
                        'sql',
                        'gz',
                        'xbk',           // MySQL/MariaDB
                        'dump',
                        'backup',
                        'tar',        // PostgreSQL
                        'bson',
                        'json',
                        'archive',      // MongoDB
                        'bak',
                        'bz2',
                        'zip',            // General
                    ];
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, $extensiones)) {
                        $fail('El archivo de BD no tiene un formato permitido.');
                    }
                }
            ],
            'estado' => 'required|in:estable,beta,deprecated',
            'es_actual' => 'nullable|boolean',
            'tecnologias' => 'required|array|min:1',
            'servidores' => 'required|array|min:1',
            'bases_datos' => 'required|array|min:1',
            'credenciales' => 'nullable|array',

            'documentos_nuevos_ids' => 'nullable|array',
            'documentos_nuevos_ids.*' => 'exists:documentos,id',
            'documentos_nuevos_archivos' => 'nullable|array',
            'documentos_nuevos_archivos.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,zip|max:51200',
            'documentos_eliminar' => 'nullable|array',
            'documentos_eliminar.*' => 'exists:documentos,id',
        ], [
            'numero_version.required' => 'El número de versión es obligatorio',
            'numero_version.unique' => 'Ya existe una versión con este número para este sistema',
            'codigo_fuente.mimes' => 'El código fuente debe ser un archivo ZIP o RAR',
            'codigo_fuente.max' => 'El código fuente no debe superar 10GB',
            'manual_tecnico.mimes' => 'El manual técnico debe ser PDF',
            'manual_tecnico.max' => 'El manual técnico no debe superar 100MB',
            'manual_usuario.mimes' => 'El manual de usuario debe ser PDF',
            'manual_usuario.max' => 'El manual de usuario no debe superar 100MB',
            'imagen.mimes' => 'La imagen debe ser JPG, PNG o GIF',
            'imagen.max' => 'La imagen no debe superar 2MB',
            'tecnologias.required' => 'Debe seleccionar al menos una tecnología',
            'servidores.required' => 'Debe seleccionar al menos un servidor',
            'bases_datos.required' => 'Debe seleccionar al menos una base de datos',
            'credenciales.required' => 'Debe seleccionar al menos una credencial',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 MANEJAR ARCHIVOS PEQUEÑOS (imagen)
            if ($request->hasFile('imagen')) {
                if ($version->imagen) {
                    Storage::disk('public')->delete($version->imagen);
                }
                $validated['imagen'] = $request->file('imagen')->store('versiones/imagenes', 'public');
            }

            // 🔹 MANEJAR ARCHIVOS GRANDES (si vienen directamente en el request)
            if ($request->hasFile('codigo_fuente')) {
                if ($version->codigo_fuente) {
                    Storage::disk('public')->delete($version->codigo_fuente);
                }
                $validated['codigo_fuente'] = $request->file('codigo_fuente')->store('versiones/codigo', 'public');
            }

            if ($request->hasFile('manual_tecnico')) {
                if ($version->manual_tecnico) {
                    Storage::disk('public')->delete($version->manual_tecnico);
                }
                $validated['manual_tecnico'] = $request->file('manual_tecnico')->store('versiones/manuales', 'public');
            }

            if ($request->hasFile('manual_usuario')) {
                if ($version->manual_usuario) {
                    Storage::disk('public')->delete($version->manual_usuario);
                }
                $validated['manual_usuario'] = $request->file('manual_usuario')->store('versiones/manuales', 'public');
            }
            if ($request->hasFile('archivo_bd')) {
                if ($version->archivo_bd) {
                    Storage::disk('public')->delete($version->archivo_bd);
                }
                $validated['archivo_bd'] = $request->file('archivo_bd')
                    ->store('versiones/bases_datos', 'public');
            }

            // 🔹 MANEJAR CHECKBOX es_actual
            $validated['es_actual'] = $request->has('es_actual') ? 1 : 0;

            // Si se marca como actual, desmarcar las demás versiones
            if ($validated['es_actual']) {
                $sistema->versiones()
                    ->where('id', '!=', $version->id)
                    ->update(['es_actual' => 0]);
            }

            // 🔹 ACTUALIZAR VERSIÓN
            $version->update($validated);

            // 🔹 SINCRONIZAR RELACIONES
            $version->tecnologias()->sync($request->tecnologias ?? []);
            $version->servidores()->sync($request->servidores ?? []);
            $version->basesDatos()->sync($request->bases_datos ?? []);
            $version->credenciales()->sync($request->credenciales ?? []);

            if ($request->has('documentos_eliminar') && is_array($request->documentos_eliminar)) {
                $idsEliminar = $request->documentos_eliminar;

                Log::info('📄 Eliminando documentos:', ['count' => count($idsEliminar)]);

                foreach ($idsEliminar as $documentoId) {
                    // Obtener el pivot para acceder al archivo
                    $pivot = DB::table('documento_sistema_versiones')
                        ->where('sistema_version_id', $version->id)
                        ->where('documento_id', $documentoId)
                        ->first();

                    if ($pivot && $pivot->archivo) {
                        // Eliminar archivo físico
                        Storage::disk('public')->delete($pivot->archivo);
                        Log::info("🗑️ Archivo eliminado: {$pivot->archivo}");
                    }

                    // Eliminar relación
                    $version->documentos()->detach($documentoId);
                    Log::info("✅ Documento {$documentoId} desvinculado");
                }
            }

            // ✅ DOCUMENTOS ADICIONALES - AGREGAR NUEVOS
            if ($request->has('documentos_nuevos_ids') && $request->hasFile('documentos_nuevos_archivos')) {
                $ids = $request->input('documentos_nuevos_ids');
                $archivos = $request->file('documentos_nuevos_archivos');

                Log::info('📄 Agregando nuevos documentos:', [
                    'total_ids' => count($ids),
                    'total_archivos' => count($archivos)
                ]);

                if (count($ids) === count($archivos)) {
                    for ($i = 0; $i < count($ids); $i++) {
                        $documentoId = $ids[$i];
                        $archivo = $archivos[$i];

                        $existe = DB::table('documento_sistema_versiones')
                            ->where('documento_id', $documentoId)
                            ->where('sistema_version_id', $version->id)
                            ->exists();

                        if ($existe) {
                            Log::warning("⚠️ Documento {$documentoId} ya existe, omitiendo...");
                            continue;
                        }

                        // Guardar archivo
                        $archivoPath = $archivo->store('versiones/documentos', 'public');

                        // Adjuntar a la versión
                        $version->documentos()->attach($documentoId, [
                            'archivo' => $archivoPath
                        ]);

                        Log::info("✅ Documento adicional #{$i} guardado:", [
                            'documento_id' => $documentoId,
                            'path' => $archivoPath
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.sistemas.versiones.index', $sistema)
                ->with('success', 'Versión ' . $version->numero_version . ' actualizada correctamente' .
                    ($validated['es_actual'] ? ' y marcada como actual' : ''));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar versión: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la versión: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sistema $sistema, SistemaVersion $version)
    {
        $version->delete();

        return response()->json([
            'success' => true,
            'message' => 'Versión eliminada correctamente'
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore(Sistema $sistema, $id)
    {
        $version = SistemaVersion::onlyTrashed()->findOrFail($id);
        $version->restore();

        return response()->json([
            'success' => true,
            'message' => 'Versión restaurada correctamente'
        ]);
    }

    public function descargar(Sistema $sistema, SistemaVersion $version, string $tipo)
    {
        $archivo = match ($tipo) {
            'codigo_fuente'  => $version->codigo_fuente,
            'manual_tecnico' => $version->manual_tecnico,
            'manual_usuario' => $version->manual_usuario,
            'archivo_bd'     => $version->archivo_bd, 
            default          => null,
        };

        if (!$archivo || !Storage::disk('public')->exists($archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        $ruta      = Storage::disk('public')->path($archivo);
        $extension = pathinfo($archivo, PATHINFO_EXTENSION);
        $nombre    = $tipo . '_v' . $version->numero_version . '.' . $extension;

        return response()->download($ruta, $nombre);
    }

    /**
     * Marcar versión como actual
     */
    public function marcarActual(Sistema $sistema, SistemaVersion $version)
    {
        // Desmarcar todas
        $sistema->versiones()->update(['es_actual' => 0]);

        // Marcar esta como actual
        $version->update(['es_actual' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Versión marcada como actual'
        ]);
    }

    /**
     * Verificar si existe una versión duplicada (AJAX)
     */
    public function checkDuplicate(Request $request, Sistema $sistema)
    {
        $numero = $request->input('numero');
        $exclude = $request->input('exclude');

        if (!$numero) {
            return response()->json(['exists' => false]);
        }

        $query = SistemaVersion::where('sistema_id', $sistema->id)
            ->where('numero_version', $numero);

        if ($exclude) {
            $query->where('id', '!=', $exclude);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    /**
     * PASO 1: Crear registro de upload (staging) - SOPORTA CREATE Y UPDATE
     */
    public function iniciarUpload(Request $request, Sistema $sistema)
    {
        Log::info('═══════════════════════════════════════════════');
        Log::info('🚀 iniciarUpload - INICIO');
        Log::info('═══════════════════════════════════════════════');

        // 🔹 Determinar si es CREATE o UPDATE
        $isUpdate = $request->filled('version_id') && $request->version_id > 0;

        Log::info('📥 Request recibido:', [
            'all_keys' => array_keys($request->all()),
            'files_keys' => array_keys($request->allFiles()),
            'is_update' => $isUpdate,
            // CREATE
            'has_documentos_ids' => $request->has('documentos_ids'),
            'has_documentos_archivos' => $request->hasFile('documentos_archivos'),
            // EDIT
            'has_documentos_nuevos_ids' => $request->has('documentos_nuevos_ids'),
            'has_documentos_nuevos_archivos' => $request->hasFile('documentos_nuevos_archivos'),
            'has_documentos_eliminar' => $request->has('documentos_eliminar'),
        ]);

        // 🔹 Validación base
        $rules = [
            'numero_version' => 'required|string',
            'fecha_lanzamiento' => 'required|date',
            'estado' => 'required|in:estable,beta,deprecated',
            'descripcion' => 'nullable|string',
            'tecnologias' => 'required|array|min:1',
            'servidores' => 'required|array|min:1',
            'bases_datos' => 'required|array|min:1',
            'credenciales' => 'nullable|array',
            'version_id' => 'nullable|integer|exists:sistema_versiones,id',
            'es_actual' => 'nullable|boolean',

            // ✅ DOCUMENTOS ADICIONALES - CREATE
            'documentos_ids' => 'nullable|array',
            'documentos_ids.*' => 'exists:documentos,id',
            'documentos_archivos' => 'nullable|array',
            'documentos_archivos.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,zip|max:51200',

            // ✅ DOCUMENTOS ADICIONALES - EDIT
            'documentos_nuevos_ids' => 'nullable|array',
            'documentos_nuevos_ids.*' => 'exists:documentos,id',
            'documentos_nuevos_archivos' => 'nullable|array',
            'documentos_nuevos_archivos.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,zip|max:51200',
            'documentos_eliminar' => 'nullable|array',
            'documentos_eliminar.*' => 'exists:documentos,id',
        ];

        // 🔹 Archivos principales (obligatorios en CREATE, opcionales en UPDATE)
        if (!$isUpdate) {
            $rules = array_merge($rules, [
                'codigo_fuente_nombre' => 'required|string',
                'codigo_fuente_tamano' => 'required|integer',
                'codigo_fuente_tipo' => 'required|string',
                'manual_tecnico_nombre' => 'nullable|string',
                'manual_tecnico_tamano' => 'nullable|integer',
                'manual_tecnico_tipo'   => 'nullable|string',
                'manual_usuario_nombre' => 'nullable|string',
                'manual_usuario_tamano' => 'nullable|integer',
                'manual_usuario_tipo'   => 'nullable|string',
                'archivo_bd_nombre' => 'nullable|string',
                'archivo_bd_tamano' => 'nullable|integer',
                'archivo_bd_tipo'   => 'nullable|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'codigo_fuente_nombre' => 'nullable|string',
                'codigo_fuente_tamano' => 'nullable|integer',
                'codigo_fuente_tipo' => 'nullable|string',
                'manual_tecnico_nombre' => 'nullable|string',
                'manual_tecnico_tamano' => 'nullable|integer',
                'manual_tecnico_tipo' => 'nullable|string',
                'manual_usuario_nombre' => 'nullable|string',
                'manual_usuario_tamano' => 'nullable|integer',
                'manual_usuario_tipo' => 'nullable|string',
            ]);
        }

        Log::info('🔍 Validando request...');

        try {
            $validated = $request->validate($rules);
            Log::info('✅ Validación exitosa');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validación:', [
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        if (!empty($validated['credenciales'])) {
            $credencialesValidas = Credencial::where('sistema_id', $sistema->id)
                ->whereIn('id', $validated['credenciales'])
                ->pluck('id')->toArray();
            $validated['credenciales'] = $credencialesValidas;
        }

        // Imagen
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $imagenPath = $file->storeAs('versiones/imagenes', $fileName, 'public');
            Log::info('✅ Imagen guardada:', ['path' => $imagenPath]);
        }

        // ✅ GUARDAR DOCUMENTOS ADICIONALES
        $documentosAdicionales = [];
        $documentosEliminar = [];

        // 🔹 DETECTAR MODO
        $hasDocumentosCreate = $request->has('documentos_ids') && $request->hasFile('documentos_archivos');
        $hasDocumentosEdit = $request->has('documentos_nuevos_ids') && $request->hasFile('documentos_nuevos_archivos');

        Log::info('🔍 Verificando documentos adicionales...', [
            'modo' => $isUpdate ? 'UPDATE' : 'CREATE',
            'has_documentos_create' => $hasDocumentosCreate,
            'has_documentos_edit' => $hasDocumentosEdit,
        ]);

        if ($hasDocumentosCreate || $hasDocumentosEdit) {
            // Determinar qué campos usar
            $idsKey = $hasDocumentosEdit ? 'documentos_nuevos_ids' : 'documentos_ids';
            $archivosKey = $hasDocumentosEdit ? 'documentos_nuevos_archivos' : 'documentos_archivos';

            $ids = $request->input($idsKey);
            $archivos = $request->file($archivosKey);

            Log::info('📄 Procesando documentos adicionales...', [
                'keys_usadas' => ['ids' => $idsKey, 'archivos' => $archivosKey],
                'total_ids' => count($ids),
                'total_archivos' => count($archivos)
            ]);

            if (count($ids) === count($archivos)) {
                for ($i = 0; $i < count($ids); $i++) {
                    $documentoId = $ids[$i];
                    $archivo = $archivos[$i];

                    Log::info("🔍 Documento #{$i}:", [
                        'documento_id' => $documentoId,
                        'archivo_nombre' => $archivo->getClientOriginalName(),
                        'archivo_size' => $archivo->getSize()
                    ]);

                    // Guardar en temp
                    $archivoPath = $archivo->store('versiones/documentos_temp', 'public');

                    $documentosAdicionales[] = [
                        'documento_id' => $documentoId,
                        'archivo_path' => $archivoPath,
                        'archivo_nombre' => $archivo->getClientOriginalName(),
                        'archivo_size' => $archivo->getSize(),
                        'archivo_mime' => $archivo->getMimeType(),
                    ];

                    Log::info("✅ Documento #{$i} guardado:", [
                        'documento_id' => $documentoId,
                        'path' => $archivoPath
                    ]);
                }
            } else {
                Log::error('❌ Mismatch entre IDs y archivos', [
                    'ids_count' => count($ids),
                    'archivos_count' => count($archivos)
                ]);
            }
        }

        // 🔹 DOCUMENTOS A ELIMINAR (solo en EDIT)
        if ($isUpdate && $request->has('documentos_eliminar')) {
            $documentosEliminar = $request->input('documentos_eliminar');
            Log::info('📝 Documentos marcados para eliminar:', [
                'total' => count($documentosEliminar),
                'ids' => $documentosEliminar
            ]);
        }

        Log::info('📊 RESUMEN:', [
            'documentos_nuevos' => count($documentosAdicionales),
            'documentos_eliminar' => count($documentosEliminar)
        ]);

        $upload = VersionUpload::create([
            'sistema_id' => $sistema->id,
            'user_id' => Auth::id(),
            'numero_version' => $validated['numero_version'],
            'estado' => 'pendiente',
            'progreso' => 0,
            'temp_imagen' => $imagenPath,

            'file_name' => $validated['codigo_fuente_nombre'] ?? null,
            'file_size' => $validated['codigo_fuente_tamano'] ?? null,
            'file_type' => $validated['codigo_fuente_tipo'] ?? null,
            'last_chunk_completed' => -1,

            'manual_tecnico_name' => $validated['manual_tecnico_nombre'] ?? null,
            'manual_tecnico_size' => $validated['manual_tecnico_tamano'] ?? null,

            'manual_usuario_name' => $validated['manual_usuario_nombre'] ?? null,
            'manual_usuario_size' => $validated['manual_usuario_tamano'] ?? null,



            'data' => [
                'fecha_lanzamiento' => $validated['fecha_lanzamiento'],
                'estado' => $validated['estado'],
                'descripcion' => $validated['descripcion'] ?? null,
                'tecnologias' => $validated['tecnologias'],
                'servidores' => $validated['servidores'],
                'bases_datos' => $validated['bases_datos'],
                'credenciales' => $validated['credenciales'] ?? [],
                'version_id' => $validated['version_id'] ?? null,
                'es_actual' => $validated['es_actual'] ?? false,
                'is_update' => $isUpdate,

                'archivo_bd_nombre' => $validated['archivo_bd_nombre'] ?? null,

                // ✅ DOCUMENTOS ADICIONALES
                'documentos_adicionales' => $documentosAdicionales,
                // ✅ DOCUMENTOS A ELIMINAR (solo en UPDATE)
                'documentos_eliminar' => $documentosEliminar,
            ],
        ]);

        Log::info('✅ Upload creado:', [
            'upload_id' => $upload->id,
            'version' => $upload->numero_version,
            'documentos_nuevos' => count($documentosAdicionales),
            'documentos_eliminar' => count($documentosEliminar)
        ]);

        // ✅ VERIFICAR
        $upload->refresh();
        $dataGuardada = $upload->data;
        Log::info('🔍 Verificación BD:', [
            'tiene_documentos' => isset($dataGuardada['documentos_adicionales']),
            'count_nuevos' => isset($dataGuardada['documentos_adicionales']) ? count($dataGuardada['documentos_adicionales']) : 0,
            'count_eliminar' => isset($dataGuardada['documentos_eliminar']) ? count($dataGuardada['documentos_eliminar']) : 0
        ]);

        Log::info('═══════════════════════════════════════════════');
        Log::info('✅ iniciarUpload - FIN');
        Log::info('═══════════════════════════════════════════════');

        return response()->json([
            'success' => true,
            'upload_id' => $upload->id,
            'numero_version' => $upload->numero_version,
            'is_update' => $isUpdate,
        ]);
    }

    /**
     * PASO 2: Recibir chunk de CÓDIGO FUENTE
     */
    public function uploadChunk(Request $request, Sistema $sistema)
    {
        $request->validate([
            'chunk' => 'required|file|max:5120', // 5MB
            'chunkIndex' => 'required|integer',
            'totalChunks' => 'required|integer',
            'identifier' => 'required|string',
            'fileName' => 'required|string',
            'upload_id' => 'required|exists:version_uploads,id',
        ]);

        $upload = VersionUpload::findOrFail($request->upload_id);

        // Guardar metadata en primera carga
        if ($request->chunkIndex == 0) {
            $upload->update([
                'chunk_identifier' => $request->identifier,
                'total_chunks' => $request->totalChunks,
            ]);
        }

        $chunkDir = storage_path("app/chunks/{$request->identifier}");

        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }

        // Guardar chunk
        $request->file('chunk')->move($chunkDir, "chunk_{$request->chunkIndex}");

        // Actualizar contador y progreso
        $newChunksReceived = $upload->chunks_received + 1;
        $progreso = round(($newChunksReceived / $request->totalChunks) * 100);

        $upload->update([
            'chunks_received' => $newChunksReceived,
            'last_chunk_completed' => $request->chunkIndex,
            'progreso' => $progreso,
        ]);

        return response()->json([
            'success' => true,
            'chunk_index' => $request->chunkIndex,
            'chunks_received' => $newChunksReceived,
            'total_chunks' => $request->totalChunks,
            'progreso' => $progreso,
        ]);
    }

    /**
     * Recibir chunks de MANUALES (técnico y usuario)
     */
    public function uploadManualChunk(Request $request, Sistema $sistema)
    {
        $request->validate([
            'chunk' => 'required|file|max:2048', // 2MB para manuales
            'chunkIndex' => 'required|integer',
            'totalChunks' => 'required|integer',
            'identifier' => 'required|string',
            'fileName' => 'required|string',
            'upload_id' => 'required|exists:version_uploads,id',
            'tipo' => 'required|in:manual_tecnico,manual_usuario,archivo_bd',
        ]);

        $upload = VersionUpload::findOrFail($request->upload_id);
        $tipo = $request->tipo;

        // Guardar metadata en primera carga
        if ($request->chunkIndex == 0) {
            $upload->update([
                "{$tipo}_identifier" => $request->identifier,
                "{$tipo}_total_chunks" => $request->totalChunks,
            ]);
        }

        $chunkDir = storage_path("app/chunks/{$request->identifier}");

        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }

        // Guardar chunk
        $request->file('chunk')->move($chunkDir, "chunk_{$request->chunkIndex}");

        // Actualizar contador del tipo específico
        $fieldChunksReceived = "{$tipo}_chunks_received";
        $newChunksReceived = $upload->$fieldChunksReceived + 1;

        $upload->update([
            $fieldChunksReceived => $newChunksReceived,
        ]);

        return response()->json([
            'success' => true,
            'tipo' => $tipo,
            'chunk_index' => $request->chunkIndex,
            'chunks_received' => $newChunksReceived,
            'total_chunks' => $request->totalChunks,
        ]);
    }

    /**
     * PASO 3: Finalizar upload - SOPORTA CREATE Y UPDATE
     */
    public function completarUpload(Request $request, Sistema $sistema)
    {
        $request->validate([
            'upload_id' => 'required|exists:version_uploads,id',
            'codigo_identifier' => 'nullable|string', // 👈 Nullable para UPDATE
            'manual_tecnico_identifier' => 'nullable|string', // 👈 Nullable para UPDATE
            'manual_usuario_identifier' => 'nullable|string', // 👈 Nullable para UPDATE
            'archivo_bd_identifier' => 'nullable|string',
        ]);

        $upload = VersionUpload::findOrFail($request->upload_id);

        // 🔹 Determinar si es UPDATE
        $isUpdate = isset($upload->data['is_update']) && $upload->data['is_update'];

        // 🔹 Solo verificar archivos que fueron subidos

        // Verificar código fuente (si fue subido)
        if ($request->codigo_identifier) {
            if ($upload->chunks_received !== $upload->total_chunks) {
                return response()->json([
                    'success' => false,
                    'message' => "Código fuente incompleto: {$upload->chunks_received}/{$upload->total_chunks}"
                ], 400);
            }
        }

        // Verificar manual técnico (si fue subido)
        if ($request->manual_tecnico_identifier) {
            if ($upload->manual_tecnico_chunks_received !== $upload->manual_tecnico_total_chunks) {
                return response()->json([
                    'success' => false,
                    'message' => "Manual técnico incompleto: {$upload->manual_tecnico_chunks_received}/{$upload->manual_tecnico_total_chunks}"
                ], 400);
            }
        }

        // Verificar manual usuario (si fue subido)
        if ($request->manual_usuario_identifier) {
            if ($upload->manual_usuario_chunks_received !== $upload->manual_usuario_total_chunks) {
                return response()->json([
                    'success' => false,
                    'message' => "Manual usuario incompleto: {$upload->manual_usuario_chunks_received}/{$upload->manual_usuario_total_chunks}"
                ], 400);
            }
        }

        if ($request->archivo_bd_identifier) {
            if ($upload->archivo_bd_chunks_received !== $upload->archivo_bd_total_chunks) {
                return response()->json([
                    'success' => false,
                    'message' => "Archivo BD incompleto: {$upload->archivo_bd_chunks_received}/{$upload->archivo_bd_total_chunks}"
                ], 400);
            }
        }

        // Actualizar identifiers
        $upload->update([
            'chunk_identifier' => $request->codigo_identifier,
            'manual_tecnico_identifier' => $request->manual_tecnico_identifier,
            'manual_usuario_identifier' => $request->manual_usuario_identifier,
            'archivo_bd_identifier' => $request->archivo_bd_identifier,
            'estado' => 'procesando',
            'progreso' => 5,
        ]);

        // Despachar Job
        dispatch(new ProcessVersionUpload($upload->id));

        return response()->json([
            'success' => true,
            'message' => 'Archivos recibidos. Procesando en segundo plano.',
            'upload_id' => $upload->id,
            'is_update' => $isUpdate,
        ]);
    }

    /**
     * Obtener estado del upload
     */
    public function getUploadStatus(Request $request, Sistema $sistema, VersionUpload $upload)
    {
        return response()->json([
            'success' => true,
            'upload' => [
                'id' => $upload->id,
                'numero_version' => $upload->numero_version,
                'estado' => $upload->estado,
                'progreso' => $upload->progreso,
                'error_message' => $upload->error_message,
                'created_at' => $upload->created_at->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Listar uploads pendientes/procesando del usuario
     */
    public function listarUploads(Request $request, Sistema $sistema)
    {
        $uploadsActivos = VersionUpload::where('sistema_id', $sistema->id)
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['pendiente', 'procesando'])
            ->latest()
            ->get();

        if ($uploadsActivos->isEmpty()) {
            $uploadsRecientes = VersionUpload::where('sistema_id', $sistema->id)
                ->where('user_id', Auth::id())
                ->where('estado', 'completado')
                ->where('updated_at', '>=', now()->subSeconds(10))
                ->latest()
                ->limit(1)
                ->get();

            $uploads = $uploadsRecientes;
        } else {
            $uploads = $uploadsActivos;
        }

        return response()->json([
            'success' => true,
            'uploads' => $uploads->map(function ($upload) {
                return [
                    'id' => $upload->id,
                    'numero_version' => $upload->numero_version,
                    'estado' => $upload->estado,
                    'progreso' => $upload->progreso,
                    'created_at' => $upload->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    /**
     * Cancelar upload
     */
    public function cancelarUpload(Request $request, Sistema $sistema, VersionUpload $upload)
    {
        if ($upload->estado === 'procesando') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar un upload que ya está siendo procesado'
            ], 400);
        }

        // Limpiar chunks si existen
        $identifiers = [
            $upload->chunk_identifier,
            $upload->manual_tecnico_identifier,
            $upload->manual_usuario_identifier,
        ];

        foreach ($identifiers as $identifier) {
            if ($identifier) {
                $chunkDir = storage_path("app/chunks/{$identifier}");
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
        }

        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'Upload cancelado correctamente'
        ]);
    }
}
