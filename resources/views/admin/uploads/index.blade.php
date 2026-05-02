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

                {{-- TAB: TODOS --}}
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
                                                        <span class="badge badge-upload bg-warning"><i
                                                                class="ti ti-clock me-1"></i>Pendiente</span>
                                                    @elseif($upload->estado === 'procesando')
                                                        <span class="badge badge-upload bg-info"><i
                                                                class="ti ti-loader me-1"></i>Procesando</span>
                                                    @elseif($upload->estado === 'completado')
                                                        <span class="badge badge-upload bg-success"><i
                                                                class="ti ti-check me-1"></i>Completado</span>
                                                    @else
                                                        <span class="badge badge-upload bg-danger"><i
                                                                class="ti ti-x me-1"></i>Error</span>
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
                                                    @if (!empty($upload->data['archivo_bd_nombre']))
                                                        <small class="d-block">
                                                            <i class="ti ti-database text-warning me-1"></i>
                                                            {{ $upload->archivo_bd_chunks_received ?? 0 }}/{{ $upload->archivo_bd_total_chunks ?? 0 }}
                                                        </small>
                                                    @endif
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
                                                        @if (in_array($upload->estado, ['pendiente', 'procesando']) && $upload->progreso < 100)
                                                            <button type="button"
                                                                class="btn btn-success btn-icon btn-sm rounded-circle resume-upload-btn"
                                                                data-id="{{ $upload->id }}"
                                                                data-sistema="{{ $upload->sistema_id }}"
                                                                data-version="{{ $upload->numero_version }}"
                                                                title="Reanudar Upload">
                                                                <i class="ti ti-player-play fs-lg"></i>
                                                            </button>
                                                        @endif

                                                        @if ($upload->estado === 'error')
                                                            <button type="button"
                                                                class="btn btn-info btn-icon btn-sm rounded-circle view-error-btn"
                                                                data-error="{{ $upload->error_message }}"
                                                                title="Ver Error">
                                                                <i class="ti ti-info-circle fs-lg"></i>
                                                            </button>
                                                        @endif

                                                        @can('admin.uploads.destroy')
                                                            @if (in_array($upload->estado, ['pendiente', 'error']))
                                                                <button type="button"
                                                                    class="btn btn-danger btn-icon btn-sm rounded-circle cancel-upload-btn"
                                                                    data-id="{{ $upload->id }}" title="Cancelar">
                                                                    <i class="ti ti-trash fs-lg"></i>
                                                                </button>
                                                            @endif
                                                        @endcan

                                                        @can('admin.versiones.index')
                                                            @if ($upload->estado === 'completado')
                                                                <a href="{{ route('admin.sistemas.versiones.index', $upload->sistema) }}"
                                                                    class="btn btn-primary btn-icon btn-sm rounded-circle"
                                                                    title="Ver Versión">
                                                                    <i class="ti ti-eye fs-lg"></i>
                                                                </a>
                                                            @endif
                                                        @endcan
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

                {{-- TAB: PENDIENTES --}}
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
                                                    @if (!empty($upload->data['archivo_bd_nombre']))
                                                        <small class="d-block">
                                                            <i class="ti ti-database text-warning me-1"></i>
                                                            {{ $upload->archivo_bd_chunks_received ?? 0 }}/{{ $upload->archivo_bd_total_chunks ?? 0 }}
                                                        </small>
                                                    @endif
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
                                                        <button type="button"
                                                            class="btn btn-success btn-icon btn-sm rounded-circle resume-upload-btn"
                                                            data-id="{{ $upload->id }}"
                                                            data-sistema="{{ $upload->sistema_id }}"
                                                            data-version="{{ $upload->numero_version }}">
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

                {{-- TAB: COMPLETADOS --}}
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

                {{-- TAB: ERRORES --}}
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
                                                <td><small
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

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const CHUNK_SIZE_CODIGO = 5 * 1024 * 1024; // 5MB
        const CHUNK_SIZE_MANUAL = 2 * 1024 * 1024; // 2MB

        function formatBytes(bytes) {
            if (!bytes) return '0 Bytes';
            const k = 1024,
                sizes = ['Bytes', 'KB', 'MB', 'GB'];
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
                const fd = new FormData();
                fd.append('chunk', file.slice(start, end));
                fd.append('chunkIndex', chunkIndex);
                fd.append('totalChunks', totalChunks);
                fd.append('identifier', identifier);
                fd.append('fileName', file.name);
                fd.append('upload_id', uploadId);
                if (tipoArchivo !== 'codigo_fuente') fd.append('tipo', tipoArchivo);

                const res = await fetch(endpoint, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error(`Error en chunk ${chunkIndex} de ${tipoArchivo}`);

                const progress = Math.round(((chunkIndex + 1) / totalChunks) * 100);
                if (onProgress) onProgress({
                    chunkIndex: chunkIndex + 1,
                    totalChunks,
                    progress,
                    bytesUploaded: Math.min(end, file.size),
                    totalBytes: file.size
                });
            }
            return identifier;
        }

        // ===== REANUDAR =====
        document.addEventListener('click', async function(e) {
            const resumeBtn = e.target.closest('.resume-upload-btn');
            if (!resumeBtn) return;
            e.preventDefault();

            const uploadId = resumeBtn.dataset.id;
            const sistemaId = resumeBtn.dataset.sistema;
            const version = resumeBtn.dataset.version;

            try {
                const statusRes = await fetch(`/admin/uploads/${uploadId}/chunks-status`, {
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });
                if (!statusRes.ok) throw new Error('No se pudo obtener estado');
                const statusData = await statusRes.json();

                // Sin archivos
                if (!statusData.archivos || Object.keys(statusData.archivos).length === 0) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Upload sin archivos',
                        html: `<p>Este upload no tiene archivos asociados.</p><hr><ol class="text-start mb-0"><li>Elimina este registro</li><li>Ve a Versiones y edita nuevamente</li></ol>`,
                        confirmButtonText: 'Eliminar este upload',
                        showCancelButton: true,
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#dc3545',
                        width: '600px'
                    }).then(async r => {
                        if (r.isConfirmed) {
                            await fetch(`/admin/uploads/${uploadId}/cancelar`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf
                                }
                            });
                            location.reload();
                        }
                    });
                    return;
                }

                // Determinar pendientes
                const pend = {
                    codigo_fuente: statusData.archivos?.codigo_fuente ? statusData.archivos.codigo_fuente
                        .progreso < 100 : false,
                    archivo_bd: statusData.archivos?.archivo_bd ? statusData.archivos.archivo_bd.progreso <
                        100 : false,
                    manual_tecnico: statusData.archivos?.manual_tecnico ? statusData.archivos.manual_tecnico
                        .progreso < 100 : false,
                    manual_usuario: statusData.archivos?.manual_usuario ? statusData.archivos.manual_usuario
                        .progreso < 100 : false,
                };

                // ── HTML selección de archivos ──
                let htmlInputs =
                    `<div class="text-start"><p class="text-muted mb-3">Para reanudar la <strong>versión ${version}</strong>, selecciona los archivos pendientes:</p>`;

                const bloque = (pendiente, key, id, icono, color, label, accept, meta) => pendiente && meta ?
                    `<div class="mb-3">
                    <label class="form-label fw-bold"><i class="ti ${icono} ${color} me-1"></i>${label} <span class="text-danger">*</span></label>
                    <input type="file" id="${id}" class="form-control" accept="${accept}">
                    <small class="text-muted d-block mt-1">Esperado: ${meta.file_name || 'N/A'} (${formatBytes(meta.file_size || 0)})</small>
                    <small class="text-info d-block">Progreso: ${meta.progreso || 0}% (${meta.chunks_received || 0}/${meta.total_chunks || 0} chunks)</small>
                  </div>` :
                    (statusData.archivos?.[key] ?
                        `<div class="mb-3"><div class="alert alert-success mb-0 py-2"><i class="ti ti-check me-1"></i><strong>${label}:</strong> ✅ Completado (100%)</div></div>` :
                        '');

                htmlInputs += bloque(pend.codigo_fuente, 'codigo_fuente', 'file-codigo', 'ti-file-zip',
                    'text-primary', 'Código Fuente', '.zip,.rar', statusData.archivos?.codigo_fuente);
                htmlInputs += bloque(pend.archivo_bd, 'archivo_bd', 'file-archivoBd', 'ti-database',
                    'text-warning', 'Archivo Base de Datos',
                    '.sql,.gz,.xbk,.dump,.backup,.tar,.bson,.json,.archive,.bak,.bz2,.zip', statusData
                    .archivos?.archivo_bd);
                htmlInputs += bloque(pend.manual_tecnico, 'manual_tecnico', 'file-tecnico', 'ti-file-text',
                    'text-success', 'Manual Técnico', '.pdf,.doc,.docx', statusData.archivos?.manual_tecnico
                    );
                htmlInputs += bloque(pend.manual_usuario, 'manual_usuario', 'file-usuario',
                    'ti-file-description', 'text-info', 'Manual Usuario', '.pdf,.doc,.docx', statusData
                    .archivos?.manual_usuario);
                htmlInputs += '</div>';

                const result = await Swal.fire({
                    title: '<i class="ti ti-upload me-2"></i>Archivos Pendientes',
                    html: htmlInputs,
                    width: '650px',
                    showCancelButton: true,
                    confirmButtonText: 'Continuar Upload',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745',
                    preConfirm: () => {
                        const files = {};
                        let error = null;

                        if (pend.codigo_fuente && statusData.archivos?.codigo_fuente) {
                            const f = document.getElementById('file-codigo')?.files[0];
                            if (!f) {
                                error = 'Debes seleccionar el código fuente';
                            } else if (f.name !== statusData.archivos.codigo_fuente.file_name || f
                                .size !== statusData.archivos.codigo_fuente.file_size) {
                                error = 'El código fuente no coincide con el archivo original';
                            } else files.codigo = f;
                        }

                        if (pend.archivo_bd && statusData.archivos?.archivo_bd && !error) {
                            const f = document.getElementById('file-archivoBd')?.files[0];
                            if (!f) {
                                error = 'Debes seleccionar el archivo de base de datos';
                            } else files.archivoBd = f;
                        }

                        if (pend.manual_tecnico && statusData.archivos?.manual_tecnico && !error) {
                            const f = document.getElementById('file-tecnico')?.files[0];
                            if (!f) {
                                error = 'Debes seleccionar el manual técnico';
                            } else if (f.name !== statusData.archivos.manual_tecnico.file_name || f
                                .size !== statusData.archivos.manual_tecnico.file_size) {
                                error = 'El manual técnico no coincide';
                            } else files.tecnico = f;
                        }

                        if (pend.manual_usuario && statusData.archivos?.manual_usuario && !error) {
                            const f = document.getElementById('file-usuario')?.files[0];
                            if (!f) {
                                error = 'Debes seleccionar el manual de usuario';
                            } else if (f.name !== statusData.archivos.manual_usuario.file_name || f
                                .size !== statusData.archivos.manual_usuario.file_size) {
                                error = 'El manual de usuario no coincide';
                            } else files.usuario = f;
                        }

                        if (error) {
                            Swal.showValidationMessage(error);
                            return false;
                        }
                        return files;
                    }
                });

                if (!result.isConfirmed || !result.value) return;
                const sel = result.value;

                // ── Barras de progreso ──
                const barraHtml = (tipo, icono, color, label, pendiente) => `
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <strong><i class="ti ${icono} ${color} me-1"></i>${label}</strong>
                        <span id="progress-${tipo}-text">${pendiente ? '0%' : '100%'}</span>
                    </div>
                    <div class="progress" style="height:25px;">
                        <div id="progress-${tipo}" class="progress-bar ${pendiente ? color.replace('text-','bg-') + ' progress-bar-striped progress-bar-animated' : 'bg-success'}" style="width:${pendiente ? '0%' : '100%'}">${pendiente ? '0%' : '100%'}</div>
                    </div>
                    <small id="status-${tipo}" class="text-muted d-block mt-1">${pendiente ? 'Preparando...' : '✅ Completado'}</small>
                </div>`;

                Swal.fire({
                    title: `<i class="ti ti-upload me-2"></i>Reanudando v${version}`,
                    html: `<div class="text-start">
                    ${barraHtml('codigo',    'ti-file-zip',         'text-primary', 'Código Fuente',        pend.codigo_fuente)}
                    ${statusData.archivos?.archivo_bd     ? barraHtml('archivoBd', 'ti-database',         'text-warning', 'Base de Datos',        pend.archivo_bd)     : ''}
                    ${statusData.archivos?.manual_tecnico ? barraHtml('tecnico',   'ti-file-text',        'text-success', 'Manual Técnico',       pend.manual_tecnico)  : ''}
                    ${statusData.archivos?.manual_usuario ? barraHtml('usuario',   'ti-file-description', 'text-info',    'Manual Usuario',       pend.manual_usuario)  : ''}
                </div>`,
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
                        if (data.progress >= 100) bar.classList.remove('progress-bar-striped',
                            'progress-bar-animated');
                    }
                    if (text) text.textContent = data.progress + '%';
                    if (status) status.textContent = data.progress >= 100 ? '✅ Completado' :
                        `Chunk ${data.chunkIndex}/${data.totalChunks} • ${formatBytes(data.bytesUploaded)} / ${formatBytes(data.totalBytes)}`;
                };

                // ── Subir archivos pendientes ──
                const uploadPromises = [];
                const identifiers = {};

                if (pend.codigo_fuente && sel.codigo && statusData.archivos?.codigo_fuente) {
                    const meta = statusData.archivos.codigo_fuente;
                    uploadPromises.push(uploadFileInChunks(sel.codigo, uploadId, sistemaId, 'codigo_fuente',
                        CHUNK_SIZE_CODIGO, d => updateProgress('codigo', d), meta.next_chunk || 0, meta
                        .chunk_identifier).then(id => identifiers.codigo = id));
                } else if (statusData.archivos?.codigo_fuente) {
                    identifiers.codigo = statusData.archivos.codigo_fuente.chunk_identifier;
                }

                if (pend.archivo_bd && sel.archivoBd && statusData.archivos?.archivo_bd) {
                    const meta = statusData.archivos.archivo_bd;
                    uploadPromises.push(uploadFileInChunks(sel.archivoBd, uploadId, sistemaId, 'archivo_bd',
                        CHUNK_SIZE_MANUAL, d => updateProgress('archivoBd', d), meta.next_chunk || 0,
                        meta.chunk_identifier).then(id => identifiers.archivoBd = id));
                } else if (statusData.archivos?.archivo_bd) {
                    identifiers.archivoBd = statusData.archivos.archivo_bd.chunk_identifier;
                }

                if (pend.manual_tecnico && sel.tecnico && statusData.archivos?.manual_tecnico) {
                    const meta = statusData.archivos.manual_tecnico;
                    uploadPromises.push(uploadFileInChunks(sel.tecnico, uploadId, sistemaId, 'manual_tecnico',
                        CHUNK_SIZE_MANUAL, d => updateProgress('tecnico', d), meta.next_chunk || 0, meta
                        .chunk_identifier).then(id => identifiers.tecnico = id));
                } else if (statusData.archivos?.manual_tecnico) {
                    identifiers.tecnico = statusData.archivos.manual_tecnico.chunk_identifier;
                }

                if (pend.manual_usuario && sel.usuario && statusData.archivos?.manual_usuario) {
                    const meta = statusData.archivos.manual_usuario;
                    uploadPromises.push(uploadFileInChunks(sel.usuario, uploadId, sistemaId, 'manual_usuario',
                        CHUNK_SIZE_MANUAL, d => updateProgress('usuario', d), meta.next_chunk || 0, meta
                        .chunk_identifier).then(id => identifiers.usuario = id));
                } else if (statusData.archivos?.manual_usuario) {
                    identifiers.usuario = statusData.archivos.manual_usuario.chunk_identifier;
                }

                await Promise.all(uploadPromises);

                const completeRes = await fetch(`/admin/sistemas/${sistemaId}/versiones/completar-upload`, {
                    method: 'POST',
                    body: JSON.stringify({
                        upload_id: uploadId,
                        codigo_identifier: identifiers.codigo || null,
                        archivo_bd_identifier: identifiers.archivoBd || null,
                        manual_tecnico_identifier: identifiers.tecnico || null,
                        manual_usuario_identifier: identifiers.usuario || null,
                    }),
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const completeData = await completeRes.json();
                if (!completeData.success) throw new Error(completeData.message);

                Swal.fire({
                        icon: 'success',
                        title: '¡Upload Completado!',
                        html: `<p>La versión <strong>${version}</strong> se está procesando.</p>`,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    })
                    .then(() => location.reload());

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error',
                    confirmButtonColor: '#6366f1'
                });
            }
        });

        // ===== VER ERROR =====
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-error-btn');
            if (!btn) return;
            Swal.fire({
                icon: 'error',
                title: 'Error en el Upload',
                html: `<pre class="text-start" style="white-space:pre-wrap;word-break:break-word;">${btn.dataset.error}</pre>`,
                width: '600px'
            });
        });

        // ===== CANCELAR =====
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.cancel-upload-btn');
            if (!btn) return;
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
                const res = await fetch(`/admin/uploads/${btn.dataset.id}/cancelar`, {
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
                } else throw new Error(data.message);
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        });
    </script>
@endsection
