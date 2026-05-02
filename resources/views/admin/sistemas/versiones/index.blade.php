@extends('layouts.vertical', ['title' => 'Versiones de ' . $sistema->nombre])

@section('css')
    <style>
        .version-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .version-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .version-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .badge-actual {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.7rem;
            padding: 0.35rem 0.65rem;
        }

        .stats-item {
            text-align: center;
            padding: 0.5rem;
        }

        .stats-number {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .stats-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
        }

        .action-buttons .btn {
            margin: 0 0.25rem;
        }

        .view-toggle {
            display: flex;
            gap: 0.5rem;
        }

        .view-toggle .btn {
            padding: 0.5rem 1rem;
        }

        /* Estilos de Paginación Mejorados */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 0.25rem;
            border: 1px solid #e5e7eb;
            color: #6b7280;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: #f3f4f6;
            border-color: #6366f1;
            color: #6366f1;
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-color: #6366f1;
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            background-color: #f9fafb;
            border-color: #e5e7eb;
            color: #d1d5db;
        }

        .pagination .page-link i {
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.sistemas.index') }}">Sistemas</a></li>
                        <li class="breadcrumb-item active">Versiones</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="ti ti-versions me-2"></i>
                    Versiones de: {{ $sistema->nombre }}
                </h4>
            </div>
        </div>
    </div>

    {{-- Header con filtros y acciones --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchVersions"
                                    placeholder="Buscar por número de versión...">
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <select class="form-select" id="filterEstado">
                                <option value="">Todos los Estados</option>
                                <option value="estable">Estable</option>
                                <option value="beta">Beta</option>
                                <option value="deprecated">Deprecated</option>
                            </select>
                        </div>

                        <div class="col-lg-4 text-end">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                {{-- Toggle Vista --}}
                                <div class="view-toggle">
                                    <button class="btn btn-sm btn-primary active" id="viewGrid">
                                        <i class="ti ti-grid-dots"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light" id="viewList">
                                        <i class="ti ti-list"></i>
                                    </button>
                                </div>

                                {{-- Botón Agregar --}}
                                @can('admin.versiones.store')
                                    <a href="{{ route('admin.sistemas.versiones.create', $sistema) }}" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i> Nueva Versión
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-versiones">
                <i class="ti ti-list me-1"></i> Versiones ({{ $versiones->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-papelera">
                <i class="ti ti-trash me-1"></i> Papelera ({{ $versionesEliminadas->count() }})
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- TAB VERSIONES ACTIVAS --}}
        <div class="tab-pane fade show active" id="tab-versiones">
            <div class="row g-3" id="versionesContainer">
                @forelse ($versiones as $version)
                    @include('admin.sistemas.versiones.partials.version-card', ['version' => $version])
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center py-5">
                            <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                            <h5 class="mb-2">No hay versiones registradas</h5>
                            <p class="text-muted mb-3">Aún no se han creado versiones para este sistema</p>
                            @can('admin.versiones.store')
                                <a href="{{ route('admin.sistemas.versiones.create', $sistema) }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i> Crear Primera Versión
                                </a>
                            @endcan
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Paginación de Versiones Activas --}}
            @if ($versiones->hasPages())
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $versiones->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

        {{-- TAB PAPELERA --}}
        <div class="tab-pane fade" id="tab-papelera">
            <div class="row g-3" id="papeleraContainer">
                @forelse ($versionesEliminadas as $version)
                    @include('admin.sistemas.versiones.partials.version-card-deleted', [
                        'version' => $version,
                    ])
                @empty
                    <div class="col-12">
                        <div class="alert alert-secondary text-center py-5">
                            <i class="ti ti-trash-off fs-1 mb-3 d-block"></i>
                            <h5 class="mb-2">No hay versiones eliminadas</h5>
                            <p class="text-muted mb-0">La papelera está vacía</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Paginación de Papelera --}}
            @if ($versionesEliminadas->hasPages())
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $versiones->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const sistemaId = {{ $sistema->id }};

            // ========== VERIFICAR UPLOADS EN PROCESO (CORREGIDO) ==========
            let lastProgress = 0;
            let toastVisible = false;
            let reloadTriggered = false;
            let intervalId = null;

            // Iniciar verificación inmediatamente
            verificarUploadsEnProceso();
            intervalId = setInterval(verificarUploadsEnProceso, 5000); // Cada 5 segundos

            function verificarUploadsEnProceso() {
                fetch(`/admin/sistemas/${sistemaId}/versiones/listar-uploads`, {
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('🔄 Verificando uploads...', data);

                        if (data.success && data.uploads.length > 0) {
                            const upload = data.uploads[0];

                            // ✅ FILTRO: Solo mostrar uploads recientes (últimos 5 minutos)
                            // Parsear la fecha "3 hours ago" no es confiable, usar timestamp
                            const ahora = new Date();
                            const hace5Min = new Date(ahora.getTime() - 5 * 60 * 1000);

                            // Si el upload está completado y no estamos mostrando el toast, ignorarlo
                            if (upload.estado === 'completado' && !toastVisible) {
                                console.log('⏭️  Upload completado antiguo - Ignorando');
                                ocultarToastProceso();
                                return;
                            }

                            mostrarUploadEnProceso(upload);
                        } else {
                            // Si no hay uploads pendientes, verificar si acabamos de completar uno
                            if (toastVisible && lastProgress >= 100) {
                                console.log('✅ Upload completado - Recargando...');
                                recargarPagina();
                            } else {
                                ocultarToastProceso();
                            }
                        }
                    })
                    .catch(error => console.error('❌ Error verificando uploads:', error));
            }

            function mostrarUploadEnProceso(upload) {
                console.log(
                    `📊 Upload: ${upload.numero_version} | Estado: ${upload.estado} | Progreso: ${upload.progreso}%`
                );

                let toast = document.getElementById('upload-toast');

                // Crear toast si no existe
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'upload-toast';
                    toast.className = 'position-fixed bottom-0 end-0 p-3';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                        <div class="toast show" role="alert" style="min-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            <div class="toast-header bg-primary text-white">
                                <strong class="me-auto">
                                    <i class="ti ti-upload me-2"></i>
                                    Procesando Versión
                                </strong>
                                <small id="upload-time" class="text-white-50"></small>
                            </div>
                            <div class="toast-body bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong id="upload-version" class="text-dark"></strong>
                                    <span id="upload-estado" class="badge bg-info"></span>
                                </div>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div id="upload-progress" 
                                         class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar"
                                         style="width: 0%">0%</div>
                                </div>
                                <small id="upload-status" class="text-muted d-block fw-semibold"></small>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    toastVisible = true;
                    console.log('✅ Toast creado y visible');
                }

                // Actualizar contenido
                document.getElementById('upload-version').textContent = `Versión ${upload.numero_version}`;
                document.getElementById('upload-time').textContent = upload.created_at;

                const estadoBadge = document.getElementById('upload-estado');
                if (upload.estado === 'pendiente') {
                    estadoBadge.textContent = 'En cola';
                    estadoBadge.className = 'badge bg-warning';
                } else if (upload.estado === 'procesando') {
                    estadoBadge.textContent = 'Procesando';
                    estadoBadge.className = 'badge bg-info';
                } else if (upload.estado === 'completado') {
                    estadoBadge.textContent = 'Completado';
                    estadoBadge.className = 'badge bg-success';
                }

                const progressBar = document.getElementById('upload-progress');
                const statusText = document.getElementById('upload-status');

                // Actualizar barra de progreso
                progressBar.style.width = upload.progreso + '%';
                progressBar.textContent = upload.progreso + '%';

                // Cambiar color según progreso
                progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
                if (upload.progreso < 50) {
                    progressBar.classList.add('bg-info');
                } else if (upload.progreso < 100) {
                    progressBar.classList.add('bg-primary');
                } else {
                    progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                    progressBar.classList.add('bg-success');
                }

                // Estado textual detallado
                if (upload.progreso < 20) {
                    statusText.textContent = '📦 Preparando archivos...';
                } else if (upload.progreso < 60) {
                    statusText.textContent = '⚙️ Ensamblando código fuente en chunks...';
                } else if (upload.progreso < 80) {
                    statusText.textContent = '💾 Creando versión en base de datos...';
                } else if (upload.progreso < 95) {
                    statusText.textContent = '🔗 Sincronizando relaciones...';
                } else if (upload.progreso < 100) {
                    statusText.textContent = '🧹 Limpiando archivos temporales...';
                } else {
                    statusText.textContent = '✅ Proceso completado!';
                }

                // ✅ RECARGAR cuando progreso = 100 Y estado = completado
                if (upload.progreso >= 100 && upload.estado === 'completado' && !reloadTriggered) {
                    console.log('🎉 UPLOAD COMPLETADO AL 100% - Preparando recarga...');
                    reloadTriggered = true;

                    // Actualizar visual del toast para mostrar éxito
                    statusText.textContent = '🎉 ¡Versión guardada! Recargando página...';
                    estadoBadge.textContent = 'Completado';
                    estadoBadge.className = 'badge bg-success';

                    // Esperar 3 segundos para que el usuario vea el 100%
                    setTimeout(() => {
                        recargarPagina();
                    }, 3000);
                }

                lastProgress = upload.progreso;
            }

            function recargarPagina() {
                console.log('🔄 Recargando página...');
                clearInterval(intervalId); // Detener polling

                Swal.fire({
                    icon: 'success',
                    title: '¡Versión Completada!',
                    html: '<p>La nueva versión se ha guardado correctamente.</p>',
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    window.location.reload();
                });
            }

            function ocultarToastProceso() {
                // NO ocultar el toast si está en proceso de recarga
                if (reloadTriggered) {
                    console.log('⏳ Esperando recarga - Toast permanece visible');
                    return;
                }

                const toast = document.getElementById('upload-toast');
                if (toast && toastVisible) {
                    console.log('🗑️ Ocultando toast - No hay uploads pendientes');
                    toast.remove();
                    toastVisible = false;
                    lastProgress = 0;
                    reloadTriggered = false;
                }
            }

            // ========== BÚSQUEDA DE VERSIONES ==========
            const searchInput = document.getElementById('searchVersions');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    document.querySelectorAll('.version-card').forEach(card => {
                        const version = card.querySelector('.version-number');
                        if (version) {
                            const col = card.closest('.col-lg-3');
                            if (col) {
                                col.style.display = version.textContent.toLowerCase().includes(
                                    searchTerm) ? '' : 'none';
                            }
                        }
                    });
                });
            }

            // ========== FILTRO POR ESTADO ==========
            const filterEstado = document.getElementById('filterEstado');
            if (filterEstado) {
                filterEstado.addEventListener('change', function() {
                    const estado = this.value.toLowerCase();
                    document.querySelectorAll('.version-card').forEach(card => {
                        const col = card.closest('.col-lg-3');
                        if (col) {
                            col.style.display = (!estado || card.dataset.estado === estado) ? '' :
                                'none';
                        }
                    });
                });
            }

            // ========== TOGGLE VISTA GRID/LIST ==========
            const viewGrid = document.getElementById('viewGrid');
            const viewList = document.getElementById('viewList');

            if (viewGrid) {
                viewGrid.addEventListener('click', function() {
                    document.querySelectorAll('#versionesContainer > div').forEach(col => {
                        col.className = 'col-lg-3 col-md-4 col-sm-6 mb-3';
                    });
                    this.classList.add('btn-primary', 'active');
                    this.classList.remove('btn-light');
                    if (viewList) {
                        viewList.classList.remove('btn-primary', 'active');
                        viewList.classList.add('btn-light');
                    }
                });
            }

            if (viewList) {
                viewList.addEventListener('click', function() {
                    document.querySelectorAll('#versionesContainer > div').forEach(col => {
                        col.className = 'col-12 mb-3';
                    });
                    this.classList.add('btn-primary', 'active');
                    this.classList.remove('btn-light');
                    if (viewGrid) {
                        viewGrid.classList.remove('btn-primary', 'active');
                        viewGrid.classList.add('btn-light');
                    }
                });
            }

            // ========== FUNCIÓN PARA ELIMINAR VERSIÓN ==========
            function deleteVersion(versionId) {
                fetch(`/admin/sistemas/${sistemaId}/versiones/${versionId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Versión eliminada',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1000);
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'No se pudo eliminar la versión', 'error');
                    });
            }

            // ========== DELEGACIÓN DE EVENTOS ==========
            document.addEventListener('click', async function(e) {

                // ELIMINAR VERSIÓN
                const deleteBtn = e.target.closest('.delete-version-btn');
                if (deleteBtn) {
                    e.preventDefault();
                    const versionId = deleteBtn.dataset.id;

                    const result = await Swal.fire({
                        title: '¿Eliminar Versión?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc3545'
                    });

                    if (result.isConfirmed) {
                        deleteVersion(versionId);
                    }
                }

                // RESTAURAR VERSIÓN
                const restoreBtn = e.target.closest('.restore-version-btn');
                if (restoreBtn) {
                    e.preventDefault();
                    const versionId = restoreBtn.dataset.id;

                    try {
                        const response = await fetch(
                            `/admin/sistemas/${sistemaId}/versiones/${versionId}/restaurar`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                }
                            });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Versión restaurada',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1000);
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }
                }

                // MARCAR COMO ACTUAL
                const marcarActualBtn = e.target.closest('.marcar-actual-btn');
                if (marcarActualBtn) {
                    e.preventDefault();
                    const versionId = marcarActualBtn.dataset.id;

                    try {
                        const response = await fetch(
                            `/admin/sistemas/${sistemaId}/versiones/${versionId}/marcar-actual`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                }
                            });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Versión marcada como actual',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1000);
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo marcar como actual', 'error');
                    }
                }
            });

        });
    </script>
@endsection
