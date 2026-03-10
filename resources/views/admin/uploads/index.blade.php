@extends('layouts.vertical', ['title' => 'Gestión de Uploads'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
    <style>
        .progress {
            height: 20px;
        }

        .badge-upload {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
        }

        .upload-progress-item {
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1rem;
        }

        .upload-progress-item:last-child {
            border-bottom: none;
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Uploads', 'title' => 'Gestión de Uploads'])

    <div class="row">
        <div class="col-12">

            {{-- ========== TABS ========== --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-todos">
                        <i class="ti ti-list me-1"></i> Todos ({{ $uploads->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-pendientes">
                        <i class="ti ti-clock me-1"></i> Pendientes ({{ $pendientes }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-completados">
                        <i class="ti ti-check me-1"></i> Completados ({{ $completados }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-errores">
                        <i class="ti ti-alert-circle me-1"></i> Con Errores ({{ $errores }})
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ================= TAB: TODOS ================= --}}
                <div class="tab-pane fade show active" id="tab-todos">
                    <div class="card">
                        <div class="card-header border-light">
                            <h4 class="card-title mb-0">Todos los Uploads</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-todos">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Sistema</th>
                                            <th>Versión</th>
                                            <th>Estado</th>
                                            <th>Progreso</th>
                                            <th>Archivos</th>
                                            <th>Creado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($uploads as $upload)
                                            <tr data-id="{{ $upload->id }}">
                                                <td>{{ $upload->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-server fs-4 text-primary me-2"></i>
                                                        <strong>{{ $upload->sistema->nombre }}</strong>
                                                    </div>
                                                </td>
                                                <td><code>v{{ $upload->numero_version }}</code></td>
                                                <td>
                                                    @if ($upload->estado === 'pendiente')
                                                        <span class="badge badge-upload bg-warning">
                                                            <i class="ti ti-clock me-1"></i>Pendiente
                                                        </span>
                                                    @elseif($upload->estado === 'procesando')
                                                        <span class="badge badge-upload bg-info">
                                                            <i class="ti ti-loader me-1"></i>Procesando
                                                        </span>
                                                    @elseif($upload->estado === 'completado')
                                                        <span class="badge badge-upload bg-success">
                                                            <i class="ti ti-check me-1"></i>Completado
                                                        </span>
                                                    @else
                                                        <span class="badge badge-upload bg-danger">
                                                            <i class="ti ti-x me-1"></i>Error
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar {{ $upload->progreso == 100 ? 'bg-success' : 'bg-primary' }}"
                                                            style="width: {{ $upload->progreso }}%">
                                                            {{ $upload->progreso }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="d-block">
                                                        <i class="ti ti-file-zip text-primary me-1"></i>
                                                        {{ $upload->chunks_received ?? 0 }}/{{ $upload->total_chunks ?? 0 }}
                                                    </small>
                                                    @if ($upload->manual_tecnico_name)
                                                        <small class="d-block">
                                                            <i class="ti ti-file-text text-success me-1"></i>
                                                            {{ $upload->manual_tecnico_chunks_received ?? 0 }}/{{ $upload->manual_tecnico_total_chunks ?? 0 }}
                                                        </small>
                                                    @endif
                                                    @if ($upload->manual_usuario_name)
                                                        <small class="d-block">
                                                            <i class="ti ti-file-description text-info me-1"></i>
                                                            {{ $upload->manual_usuario_chunks_received ?? 0 }}/{{ $upload->manual_usuario_total_chunks ?? 0 }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>{{ $upload->created_at->diffForHumans() }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">

                                                        {{-- ✅ BOTÓN REANUDAR --}}
                                                        @if (in_array($upload->estado, ['pendiente', 'procesando']) && $upload->progreso < 100)
                                                            <button type="button"
                                                                class="btn btn-success btn-icon btn-sm rounded-circle resume-upload-btn"
                                                                data-id="{{ $upload->id }}"
                                                                data-sistema="{{ $upload->sistema_id }}"
                                                                data-version="{{ $upload->numero_version }}"
                                                                data-codigo-filename="{{ $upload->file_name }}"
                                                                data-codigo-filesize="{{ $upload->file_size }}"
                                                                data-codigo-identifier="{{ $upload->chunk_identifier }}"
                                                                data-codigo-chunks="{{ $upload->chunks_received ?? 0 }}"
                                                                data-codigo-total="{{ $upload->total_chunks ?? 0 }}"
                                                                data-tecnico-filename="{{ $upload->manual_tecnico_name }}"
                                                                data-tecnico-filesize="{{ $upload->manual_tecnico_size }}"
                                                                data-tecnico-identifier="{{ $upload->manual_tecnico_identifier }}"
                                                                data-tecnico-chunks="{{ $upload->manual_tecnico_chunks_received ?? 0 }}"
                                                                data-tecnico-total="{{ $upload->manual_tecnico_total_chunks ?? 0 }}"
                                                                data-usuario-filename="{{ $upload->manual_usuario_name }}"
                                                                data-usuario-filesize="{{ $upload->manual_usuario_size }}"
                                                                data-usuario-identifier="{{ $upload->manual_usuario_identifier }}"
                                                                data-usuario-chunks="{{ $upload->manual_usuario_chunks_received ?? 0 }}"
                                                                data-usuario-total="{{ $upload->manual_usuario_total_chunks ?? 0 }}"
                                                                title="Reanudar Upload">
                                                                <i class="ti ti-player-play fs-lg"></i>
                                                            </button>
                                                        @endif

                                                        {{-- Ver Error --}}
                                                        @if ($upload->estado === 'error')
                                                            <button type="button"
                                                                class="btn btn-info btn-icon btn-sm rounded-circle view-error-btn"
                                                                data-error="{{ $upload->error_message }}"
                                                                title="Ver Error">
                                                                <i class="ti ti-info-circle fs-lg"></i>
                                                            </button>
                                                        @endif

                                                        {{-- Cancelar --}}
                                                        @if (in_array($upload->estado, ['pendiente', 'error']))
                                                            <button type="button"
                                                                class="btn btn-danger btn-icon btn-sm rounded-circle cancel-upload-btn"
                                                                data-id="{{ $upload->id }}" title="Cancelar">
                                                                <i class="ti ti-trash fs-lg"></i>
                                                            </button>
                                                        @endif

                                                        {{-- Ver Versión --}}
                                                        @if ($upload->estado === 'completado')
                                                            <a href="{{ route('admin.sistemas.versiones.index', $upload->sistema) }}"
                                                                class="btn btn-primary btn-icon btn-sm rounded-circle"
                                                                title="Ver Versión">
                                                                <i class="ti ti-eye fs-lg"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= TAB: PENDIENTES ================= --}}
                <div class="tab-pane fade" id="tab-pendientes">
                    <div class="card">
                        <div class="card-header border-light">
                            <h4 class="card-title mb-0">Uploads Pendientes</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Sistema</th>
                                            <th>Versión</th>
                                            <th>Progreso</th>
                                            <th>Archivos</th>
                                            <th>Creado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($uploads->whereIn('estado', ['pendiente', 'procesando']) as $upload)
                                            <tr>
                                                <td>{{ $upload->id }}</td>
                                                <td><strong>{{ $upload->sistema->nombre }}</strong></td>
                                                <td><code>v{{ $upload->numero_version }}</code></td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-warning"
                                                            style="width: {{ $upload->progreso }}%">
                                                            {{ $upload->progreso }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="d-block">
                                                        <i class="ti ti-file-zip text-primary me-1"></i>
                                                        {{ $upload->chunks_received ?? 0 }}/{{ $upload->total_chunks ?? 0 }}
                                                    </small>
                                                    <small class="d-block">
                                                        <i class="ti ti-file-text text-success me-1"></i>
                                                        {{ $upload->manual_tecnico_chunks_received ?? 0 }}/{{ $upload->manual_tecnico_total_chunks ?? 0 }}
                                                    </small>
                                                    <small class="d-block">
                                                        <i class="ti ti-file-description text-info me-1"></i>
                                                        {{ $upload->manual_usuario_chunks_received ?? 0 }}/{{ $upload->manual_usuario_total_chunks ?? 0 }}
                                                    </small>
                                                </td>
                                                <td>{{ $upload->created_at->diffForHumans() }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button"
                                                            class="btn btn-success btn-icon btn-sm rounded-circle resume-upload-btn"
                                                            data-id="{{ $upload->id }}"
                                                            data-sistema="{{ $upload->sistema_id }}"
                                                            data-version="{{ $upload->numero_version }}"
                                                            data-codigo-filename="{{ $upload->file_name }}"
                                                            data-codigo-filesize="{{ $upload->file_size }}"
                                                            data-codigo-identifier="{{ $upload->chunk_identifier }}"
                                                            data-codigo-chunks="{{ $upload->chunks_received ?? 0 }}"
                                                            data-codigo-total="{{ $upload->total_chunks ?? 0 }}"
                                                            data-tecnico-filename="{{ $upload->manual_tecnico_name }}"
                                                            data-tecnico-filesize="{{ $upload->manual_tecnico_size }}"
                                                            data-tecnico-identifier="{{ $upload->manual_tecnico_identifier }}"
                                                            data-tecnico-chunks="{{ $upload->manual_tecnico_chunks_received ?? 0 }}"
                                                            data-tecnico-total="{{ $upload->manual_tecnico_total_chunks ?? 0 }}"
                                                            data-usuario-filename="{{ $upload->manual_usuario_name }}"
                                                            data-usuario-filesize="{{ $upload->manual_usuario_size }}"
                                                            data-usuario-identifier="{{ $upload->manual_usuario_identifier }}"
                                                            data-usuario-chunks="{{ $upload->manual_usuario_chunks_received ?? 0 }}"
                                                            data-usuario-total="{{ $upload->manual_usuario_total_chunks ?? 0 }}">
                                                            <i class="ti ti-player-play fs-lg"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-danger btn-icon btn-sm rounded-circle cancel-upload-btn"
                                                            data-id="{{ $upload->id }}">
                                                            <i class="ti ti-trash fs-lg"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= TAB: COMPLETADOS ================= --}}
                <div class="tab-pane fade" id="tab-completados">
                    <div class="card">
                        <div class="card-header border-light">
                            <h4 class="card-title mb-0">Uploads Completados</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Sistema</th>
                                            <th>Versión</th>
                                            <th>Archivo</th>
                                            <th>Completado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($uploads->where('estado', 'completado') as $upload)
                                            <tr>
                                                <td>{{ $upload->id }}</td>
                                                <td><strong>{{ $upload->sistema->nombre }}</strong></td>
                                                <td><code>v{{ $upload->numero_version }}</code></td>
                                                <td>
                                                    <small>{{ Str::limit($upload->file_name, 25) }}</small><br>
                                                    <small
                                                        class="text-muted">{{ number_format($upload->file_size / 1024 / 1024, 2) }}
                                                        MB</small>
                                                </td>
                                                <td>{{ $upload->updated_at->diffForHumans() }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.sistemas.versiones.index', $upload->sistema) }}"
                                                        class="btn btn-primary btn-icon btn-sm rounded-circle">
                                                        <i class="ti ti-eye fs-lg"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= TAB: ERRORES ================= --}}
                <div class="tab-pane fade" id="tab-errores">
                    <div class="card">
                        <div class="card-header border-light">
                            <h4 class="card-title mb-0">Uploads con Errores</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Sistema</th>
                                            <th>Versión</th>
                                            <th>Error</th>
                                            <th>Fecha</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($uploads->where('estado', 'error') as $upload)
                                            <tr>
                                                <td>{{ $upload->id }}</td>
                                                <td><strong>{{ $upload->sistema->nombre }}</strong></td>
                                                <td><code>v{{ $upload->numero_version }}</code></td>
                                                <td>
                                                    <small
                                                        class="text-danger">{{ Str::limit($upload->error_message, 40) }}</small>
                                                </td>
                                                <td>{{ $upload->updated_at->diffForHumans() }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button"
                                                            class="btn btn-info btn-icon btn-sm rounded-circle view-error-btn"
                                                            data-error="{{ $upload->error_message }}">
                                                            <i class="ti ti-info-circle fs-lg"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-danger btn-icon btn-sm rounded-circle cancel-upload-btn"
                                                            data-id="{{ $upload->id }}">
                                                            <i class="ti ti-trash fs-lg"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-uploads.js'])

    {{-- ========== SCRIPT DE REANUDACIÓN DE 3 ARCHIVOS ========== --}}

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const CHUNK_SIZE_CODIGO = 5 * 1024 * 1024; // 5MB
        const CHUNK_SIZE_MANUAL = 2 * 1024 * 1024; // 2MB

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        async function uploadFileInChunks(file, uploadId, sistemaId, tipoArchivo, chunkSize, onProgress, resumeFromChunk =
            0, existingIdentifier = null) {
            const totalChunks = Math.ceil(file.size / chunkSize);
            const identifier = existingIdentifier || (Date.now() + '_' + Math.random().toString(36).substr(2, 9));

            const endpoint = tipoArchivo === 'codigo_fuente' ?
                `/admin/sistemas/${sistemaId}/versiones/upload-chunk` :
                `/admin/sistemas/${sistemaId}/versiones/upload-manual-chunk`;

            for (let chunkIndex = resumeFromChunk; chunkIndex < totalChunks; chunkIndex++) {
                const start = chunkIndex * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append('chunk', chunk);
                formData.append('chunkIndex', chunkIndex);
                formData.append('totalChunks', totalChunks);
                formData.append('identifier', identifier);
                formData.append('fileName', file.name);
                formData.append('upload_id', uploadId);

                if (tipoArchivo !== 'codigo_fuente') {
                    formData.append('tipo', tipoArchivo);
                }

                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Error en chunk ${chunkIndex} de ${tipoArchivo}`);
                }

                const progress = Math.round(((chunkIndex + 1) / totalChunks) * 100);

                if (onProgress) {
                    onProgress({
                        chunkIndex: chunkIndex + 1,
                        totalChunks,
                        progress,
                        bytesUploaded: Math.min(end, file.size),
                        totalBytes: file.size
                    });
                }
            }

            return identifier;
        }

        // ===== EVENTO: REANUDAR =====
        document.addEventListener('click', async function(e) {
            const resumeBtn = e.target.closest('.resume-upload-btn');
            if (!resumeBtn) return;

            e.preventDefault();

            const uploadId = resumeBtn.dataset.id;
            const sistemaId = resumeBtn.dataset.sistema;
            const version = resumeBtn.dataset.version;

            try {
                const statusResponse = await fetch(`/admin/uploads/${uploadId}/chunks-status`, {
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });

                if (!statusResponse.ok) throw new Error('No se pudo obtener estado');

                const statusData = await statusResponse.json();

                // ✅ VALIDACIÓN: Verificar que haya archivos
                const noTieneArchivos = !statusData.archivos || Object.keys(statusData.archivos).length === 0;

                if (noTieneArchivos) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Upload sin archivos',
                        html: `
                    <p>Este upload no tiene archivos asociados.</p>
                    <p class="text-muted mb-3">Probablemente fue cancelado durante la edición.</p>
                    <hr>
                    <p class="mb-2"><strong>¿Qué hacer?</strong></p>
                    <ol class="text-start mb-0">
                        <li>Elimina este registro</li>
                        <li>Ve a Sistemas → Versiones</li>
                        <li>Edita la versión nuevamente</li>
                        <li>Sube los archivos que necesites actualizar</li>
                    </ol>
                `,
                        confirmButtonText: 'Eliminar este upload',
                        showCancelButton: true,
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#dc3545',
                        width: '600px'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                const deleteResponse = await fetch(
                                    `/admin/uploads/${uploadId}/cancelar`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': csrf,
                                            'Accept': 'application/json'
                                        }
                                    });

                                if (deleteResponse.ok) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Upload eliminado',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                } else {
                                    throw new Error('No se pudo eliminar');
                                }
                            } catch (err) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo eliminar el upload',
                                    confirmButtonColor: '#6366f1'
                                });
                            }
                        }
                    });
                    return; // ✅ DETENER AQUÍ
                }

                // ✅ DETERMINAR QUÉ ARCHIVOS FALTAN
                const archivosPendientes = {
                    codigo_fuente: statusData.archivos?.codigo_fuente ? (statusData.archivos.codigo_fuente
                        .progreso < 100) : false,
                    manual_tecnico: statusData.archivos?.manual_tecnico ? (statusData.archivos
                        .manual_tecnico.progreso < 100) : false,
                    manual_usuario: statusData.archivos?.manual_usuario ? (statusData.archivos
                        .manual_usuario.progreso < 100) : false
                };

                console.log('📊 Estado de archivos:', {
                    codigo: statusData.archivos?.codigo_fuente?.progreso || 0,
                    tecnico: statusData.archivos?.manual_tecnico?.progreso || 0,
                    usuario: statusData.archivos?.manual_usuario?.progreso || 0,
                    pendientes: archivosPendientes
                });

                // ✅ CONSTRUIR HTML DINÁMICO (solo archivos pendientes)
                let htmlInputs = `
            <div class="text-start">
                <p class="text-muted mb-3">Para reanudar la <strong>versión ${version}</strong>, selecciona los archivos pendientes:</p>
        `;

                // CÓDIGO FUENTE
                if (archivosPendientes.codigo_fuente && statusData.archivos.codigo_fuente) {
                    const archivoMeta = statusData.archivos.codigo_fuente;
                    htmlInputs += `
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-file-zip text-primary me-1"></i>
                        Código Fuente <span class="text-danger">*</span>
                    </label>
                    <input type="file" id="file-codigo" class="form-control" accept=".zip,.rar">
                    <small class="text-muted d-block mt-1">
                        Esperado: ${archivoMeta.file_name || 'N/A'} (${formatBytes(archivoMeta.file_size || 0)})
                    </small>
                    <small class="text-info d-block">
                        Progreso actual: ${archivoMeta.progreso || 0}% (${archivoMeta.chunks_received || 0}/${archivoMeta.total_chunks || 0} chunks)
                    </small>
                </div>
            `;
                } else if (statusData.archivos?.codigo_fuente) {
                    htmlInputs += `
                <div class="mb-3">
                    <div class="alert alert-success mb-0 py-2">
                        <i class="ti ti-check me-1"></i>
                        <strong>Código Fuente:</strong> ✅ Completado (100%)
                    </div>
                </div>
            `;
                }

                // MANUAL TÉCNICO
                if (archivosPendientes.manual_tecnico && statusData.archivos.manual_tecnico) {
                    const archivoMeta = statusData.archivos.manual_tecnico;
                    htmlInputs += `
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-file-text text-success me-1"></i>
                        Manual Técnico <span class="text-danger">*</span>
                    </label>
                    <input type="file" id="file-tecnico" class="form-control" accept=".pdf,.doc,.docx">
                    <small class="text-muted d-block mt-1">
                        Esperado: ${archivoMeta.file_name || 'N/A'} (${formatBytes(archivoMeta.file_size || 0)})
                    </small>
                    <small class="text-info d-block">
                        Progreso actual: ${archivoMeta.progreso || 0}% (${archivoMeta.chunks_received || 0}/${archivoMeta.total_chunks || 0} chunks)
                    </small>
                </div>
            `;
                } else if (statusData.archivos?.manual_tecnico) {
                    htmlInputs += `
                <div class="mb-3">
                    <div class="alert alert-success mb-0 py-2">
                        <i class="ti ti-check me-1"></i>
                        <strong>Manual Técnico:</strong> ✅ Completado (100%)
                    </div>
                </div>
            `;
                }

                // MANUAL USUARIO
                if (archivosPendientes.manual_usuario && statusData.archivos.manual_usuario) {
                    const archivoMeta = statusData.archivos.manual_usuario;
                    htmlInputs += `
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="ti ti-file-description text-info me-1"></i>
                        Manual Usuario <span class="text-danger">*</span>
                    </label>
                    <input type="file" id="file-usuario" class="form-control" accept=".pdf,.doc,.docx">
                    <small class="text-muted d-block mt-1">
                        Esperado: ${archivoMeta.file_name || 'N/A'} (${formatBytes(archivoMeta.file_size || 0)})
                    </small>
                    <small class="text-info d-block">
                        Progreso actual: ${archivoMeta.progreso || 0}% (${archivoMeta.chunks_received || 0}/${archivoMeta.total_chunks || 0} chunks)
                    </small>
                </div>
            `;
                } else if (statusData.archivos?.manual_usuario) {
                    htmlInputs += `
                <div class="mb-3">
                    <div class="alert alert-success mb-0 py-2">
                        <i class="ti ti-check me-1"></i>
                        <strong>Manual Usuario:</strong> ✅ Completado (100%)
                    </div>
                </div>
            `;
                }

                htmlInputs += `</div>`;

                // ✅ MOSTRAR SWEETALERT CON CAMPOS DINÁMICOS
                const result = await Swal.fire({
                    title: '<i class="ti ti-upload me-2"></i>Selecciona los Archivos Pendientes',
                    html: htmlInputs,
                    width: '650px',
                    showCancelButton: true,
                    confirmButtonText: 'Continuar Upload',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745',
                    preConfirm: () => {
                        const files = {};
                        let error = null;

                        // ✅ VALIDAR SOLO ARCHIVOS PENDIENTES
                        if (archivosPendientes.codigo_fuente && statusData.archivos
                            ?.codigo_fuente) {
                            const fileCodigo = document.getElementById('file-codigo')?.files[0];
                            if (!fileCodigo) {
                                error = 'Debes seleccionar el código fuente';
                            } else {
                                const meta = statusData.archivos.codigo_fuente;
                                if (fileCodigo.name !== meta.file_name || fileCodigo.size !== meta
                                    .file_size) {
                                    error = 'El código fuente no coincide con el archivo original';
                                } else {
                                    files.codigo = fileCodigo;
                                }
                            }
                        }

                        if (archivosPendientes.manual_tecnico && statusData.archivos
                            ?.manual_tecnico && !error) {
                            const fileTecnico = document.getElementById('file-tecnico')?.files[0];
                            if (!fileTecnico) {
                                error = 'Debes seleccionar el manual técnico';
                            } else {
                                const meta = statusData.archivos.manual_tecnico;
                                if (fileTecnico.name !== meta.file_name || fileTecnico.size !== meta
                                    .file_size) {
                                    error = 'El manual técnico no coincide con el archivo original';
                                } else {
                                    files.tecnico = fileTecnico;
                                }
                            }
                        }

                        if (archivosPendientes.manual_usuario && statusData.archivos
                            ?.manual_usuario && !error) {
                            const fileUsuario = document.getElementById('file-usuario')?.files[0];
                            if (!fileUsuario) {
                                error = 'Debes seleccionar el manual de usuario';
                            } else {
                                const meta = statusData.archivos.manual_usuario;
                                if (fileUsuario.name !== meta.file_name || fileUsuario.size !== meta
                                    .file_size) {
                                    error =
                                        'El manual de usuario no coincide con el archivo original';
                                } else {
                                    files.usuario = fileUsuario;
                                }
                            }
                        }

                        if (error) {
                            Swal.showValidationMessage(error);
                            return false;
                        }

                        return files;
                    }
                });

                if (!result.isConfirmed || !result.value) return;

                const archivosSeleccionados = result.value;

                // ✅ CONTINUAR CON EL UPLOAD
                Swal.fire({
                    title: `<i class="ti ti-upload me-2"></i>Reanudando Versión ${version}`,
                    html: `
                <div class="text-start">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <strong><i class="ti ti-file-zip text-primary me-1"></i>Código Fuente</strong>
                            <span id="progress-codigo-text">${archivosPendientes.codigo_fuente ? '0%' : '100%'}</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div id="progress-codigo" class="progress-bar ${archivosPendientes.codigo_fuente ? 'bg-primary progress-bar-striped progress-bar-animated' : 'bg-success'}" style="width: ${archivosPendientes.codigo_fuente ? '0%' : '100%'}">${archivosPendientes.codigo_fuente ? '0%' : '100%'}</div>
                        </div>
                        <small id="status-codigo" class="text-muted d-block mt-1">${archivosPendientes.codigo_fuente ? 'Preparando...' : '✅ Completado'}</small>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <strong><i class="ti ti-file-text text-success me-1"></i>Manual Técnico</strong>
                            <span id="progress-tecnico-text">${archivosPendientes.manual_tecnico ? '0%' : '100%'}</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div id="progress-tecnico" class="progress-bar ${archivosPendientes.manual_tecnico ? 'bg-success progress-bar-striped progress-bar-animated' : 'bg-success'}" style="width: ${archivosPendientes.manual_tecnico ? '0%' : '100%'}">${archivosPendientes.manual_tecnico ? '0%' : '100%'}</div>
                        </div>
                        <small id="status-tecnico" class="text-muted d-block mt-1">${archivosPendientes.manual_tecnico ? 'Preparando...' : '✅ Completado'}</small>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <strong><i class="ti ti-file-description text-info me-1"></i>Manual Usuario</strong>
                            <span id="progress-usuario-text">${archivosPendientes.manual_usuario ? '0%' : '100%'}</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div id="progress-usuario" class="progress-bar ${archivosPendientes.manual_usuario ? 'bg-info progress-bar-striped progress-bar-animated' : 'bg-success'}" style="width: ${archivosPendientes.manual_usuario ? '0%' : '100%'}">${archivosPendientes.manual_usuario ? '0%' : '100%'}</div>
                        </div>
                        <small id="status-usuario" class="text-muted d-block mt-1">${archivosPendientes.manual_usuario ? 'Preparando...' : '✅ Completado'}</small>
                    </div>
                </div>
            `,
                    width: '700px',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });

                const updateProgress = (tipo, data) => {
                    const bar = document.getElementById(`progress-${tipo}`);
                    const text = document.getElementById(`progress-${tipo}-text`);
                    const status = document.getElementById(`status-${tipo}`);

                    if (bar) {
                        bar.style.width = data.progress + '%';
                        bar.textContent = data.progress + '%';
                        if (data.progress >= 100) {
                            bar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                        }
                    }

                    if (text) text.textContent = data.progress + '%';

                    if (status) {
                        const mb = (data.bytesUploaded / 1024 / 1024).toFixed(2);
                        const total = (data.totalBytes / 1024 / 1024).toFixed(2);
                        status.textContent = data.progress >= 100 ?
                            '✅ Completado' :
                            `Chunk ${data.chunkIndex}/${data.totalChunks} • ${mb} MB / ${total} MB`;
                    }
                };

                // ✅ SUBIR SOLO LOS ARCHIVOS PENDIENTES
                const uploadPromises = [];
                const identifiers = {};

                if (archivosPendientes.codigo_fuente && archivosSeleccionados.codigo && statusData.archivos
                    ?.codigo_fuente) {
                    const meta = statusData.archivos.codigo_fuente;
                    uploadPromises.push(
                        uploadFileInChunks(
                            archivosSeleccionados.codigo,
                            uploadId,
                            sistemaId,
                            'codigo_fuente',
                            CHUNK_SIZE_CODIGO,
                            (data) => updateProgress('codigo', data),
                            meta.next_chunk || 0,
                            meta.chunk_identifier
                        ).then(id => identifiers.codigo = id)
                    );
                } else if (!archivosPendientes.codigo_fuente && statusData.archivos?.codigo_fuente) {
                    identifiers.codigo = statusData.archivos.codigo_fuente.chunk_identifier;
                }

                if (archivosPendientes.manual_tecnico && archivosSeleccionados.tecnico && statusData.archivos
                    ?.manual_tecnico) {
                    const meta = statusData.archivos.manual_tecnico;
                    uploadPromises.push(
                        uploadFileInChunks(
                            archivosSeleccionados.tecnico,
                            uploadId,
                            sistemaId,
                            'manual_tecnico',
                            CHUNK_SIZE_MANUAL,
                            (data) => updateProgress('tecnico', data),
                            meta.next_chunk || 0,
                            meta.chunk_identifier
                        ).then(id => identifiers.tecnico = id)
                    );
                } else if (!archivosPendientes.manual_tecnico && statusData.archivos?.manual_tecnico) {
                    identifiers.tecnico = statusData.archivos.manual_tecnico.chunk_identifier;
                }

                if (archivosPendientes.manual_usuario && archivosSeleccionados.usuario && statusData.archivos
                    ?.manual_usuario) {
                    const meta = statusData.archivos.manual_usuario;
                    uploadPromises.push(
                        uploadFileInChunks(
                            archivosSeleccionados.usuario,
                            uploadId,
                            sistemaId,
                            'manual_usuario',
                            CHUNK_SIZE_MANUAL,
                            (data) => updateProgress('usuario', data),
                            meta.next_chunk || 0,
                            meta.chunk_identifier
                        ).then(id => identifiers.usuario = id)
                    );
                } else if (!archivosPendientes.manual_usuario && statusData.archivos?.manual_usuario) {
                    identifiers.usuario = statusData.archivos.manual_usuario.chunk_identifier;
                }

                await Promise.all(uploadPromises);

                // ✅ COMPLETAR UPLOAD
                const completeResponse = await fetch(
                    `/admin/sistemas/${sistemaId}/versiones/completar-upload`, {
                        method: 'POST',
                        body: JSON.stringify({
                            upload_id: uploadId,
                            codigo_identifier: identifiers.codigo,
                            manual_tecnico_identifier: identifiers.tecnico,
                            manual_usuario_identifier: identifiers.usuario
                        }),
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                const completeData = await completeResponse.json();

                if (!completeData.success) throw new Error(completeData.message);

                Swal.fire({
                    icon: 'success',
                    title: '¡Upload Completado!',
                    html: `<p>La versión <strong>${version}</strong> se está procesando.</p>`,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => location.reload());

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al procesar la solicitud',
                    confirmButtonColor: '#6366f1'
                });
            }
        });

        // ===== VER ERROR =====
        document.addEventListener('click', function(e) {
            const viewErrorBtn = e.target.closest('.view-error-btn');
            if (viewErrorBtn) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el Upload',
                    html: `<pre class="text-start" style="white-space: pre-wrap; word-break: break-word;">${viewErrorBtn.dataset.error}</pre>`,
                    width: '600px'
                });
            }
        });

        // ===== CANCELAR =====
        document.addEventListener('click', async function(e) {
            const cancelBtn = e.target.closest('.cancel-upload-btn');
            if (!cancelBtn) return;

            e.preventDefault();

            const confirm = await Swal.fire({
                title: '¿Cancelar Upload?',
                text: 'Se eliminarán todos los archivos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No',
                confirmButtonColor: '#dc3545'
            });

            if (!confirm.isConfirmed) return;

            try {
                const res = await fetch(`/admin/uploads/${cancelBtn.dataset.id}/cancelar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Cancelado',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        });
    </script>
@endsection
