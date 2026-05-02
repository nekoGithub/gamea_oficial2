@extends('layouts.vertical', ['title' => 'Auditorías'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Auditorías',
        'title' => 'Registro de Actividades',
    ])

    <div class="row">
        <div class="col-12">

            {{-- FILTROS RÁPIDOS --}}
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Módulo</label>
                            <select class="form-select" id="filtro-modulo">
                                <option value="">Todos los módulos</option>
                                <option value="sistemas">Sistemas</option>
                                <option value="sistema_versiones">Versiones</option>
                                <option value="credenciales">Credenciales</option>
                                <option value="ssls">SSL</option>
                                <option value="servidores">Servidores</option>
                                <option value="users">Usuarios</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Acción</label>
                            <select class="form-select" id="filtro-accion">
                                <option value="">Todas las acciones</option>
                                <option value="created">Creado</option>
                                <option value="updated">Actualizado</option>
                                <option value="deleted">Eliminado</option>
                                <option value="restored">Restaurado</option>
                                <option value="login">Login</option>
                                <option value="logout">Logout</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Usuario</label>
                            <select class="form-select" id="filtro-usuario">
                                <option value="">Todos los usuarios</option>
                                @foreach ($usuarios as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            {{-- BOTÓN CON COLOR CYAN GAMEA --}}
                            <button class="btn btn-gamea-cyan w-100" id="btn-limpiar-filtros">
                                <i class="ti ti-filter-off me-1"></i> Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLA PRINCIPAL --}}
            <div class="card">

                <div class="card-header border-light justify-content-between">
                    <h4 class="card-title mb-0">Registro de Auditorías</h4>
                    <div class="d-flex gap-2">
                        @can('admin.reportes.generar')
                            <button class="btn btn-gamea-rojo" data-bs-toggle="modal" data-bs-target="#exportarModal">
                                <i class="ti ti-download me-1"></i> Exportar PDF
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-auditorias">
                            <thead class="bg-light bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Módulo</th>
                                    <th>Descripción</th>
                                    <th>IP</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Ver</th>
                                </tr>
                                <tr class="column-search-input-bar" id="column-search-auditorias">
                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="ID"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Usuario"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Acción"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Módulo"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light"
                                                placeholder="Descripción" type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="IP"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Fecha"
                                                type="text">
                                        </div>
                                    </th>

                                    <th></th>
                                </tr>
                            </thead>

                            <tbody id="tbody-auditorias">
                                {{-- Se carga dinámicamente con AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL EXPORTAR --}}
    @can('admin.reportes.generar')
        <div class="modal fade" id="exportarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-file-download me-2"></i>Exportar Auditorías a PDF
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.auditorias.exportar-pdf') }}" method="GET" target="_blank">
                        <div class="modal-body">

                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                El reporte incluirá máximo 1000 registros según los filtros aplicados.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Módulo</label>
                                <select class="form-select" name="modulo">
                                    <option value="">Todos los módulos</option>
                                    <option value="sistemas">Sistemas</option>
                                    <option value="sistema_versiones">Versiones</option>
                                    <option value="credenciales">Credenciales</option>
                                    <option value="ssls">SSL</option>
                                    <option value="servidores">Servidores</option>
                                    <option value="users">Usuarios</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Acción</label>
                                <select class="form-select" name="accion">
                                    <option value="">Todas las acciones</option>
                                    <option value="created">Creado</option>
                                    <option value="updated">Actualizado</option>
                                    <option value="deleted">Eliminado</option>
                                    <option value="restored">Restaurado</option>
                                    <option value="login">Login</option>
                                    <option value="logout">Logout</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <select class="form-select" name="usuario">
                                    <option value="">Todos los usuarios</option>
                                    @foreach ($usuarios as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" name="fecha_inicio">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" name="fecha_fin">
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                            <button type="button" id="btn-generar-pdf" class="btn btn-gamea-rojo">
                                <i class="ti ti-file-type-pdf me-1"></i> Generar PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- MODAL DETALLE --}}
    @include('admin.auditorias.modals.show')
@endsection

@section('scripts')
    <style>
        /* Colores institucionales GAMEA */
        .btn-gamea-cyan {
            background-color: #D32F2F;
            border-color: #D32F2F;
            color: #fff;
        }

        .btn-gamea-cyan:hover {
            background-color: #D32F2F;
            border-color: #D32F2F;
            color: #fff;
        }

        .btn-gamea-rojo {
            background-color: #D32F2F;
            border-color: #D32F2F;
            color: #fff;
        }

        .btn-gamea-rojo:hover {
            background-color: #B71C1C;
            border-color: #B71C1C;
            color: #fff;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-auditorias.js'])

    <script>
        document.getElementById('btn-generar-pdf').addEventListener('click', async function() {
            const form = this.closest('form');
            const params = new URLSearchParams(new FormData(form));

            try {
                const res = await fetch(`{{ route('admin.auditorias.count') }}?${params}`);
                const data = await res.json();
                const total = data.total;

                if (total === 0) {
                    Swal.fire('Sin registros', 'No hay auditorías que coincidan con los filtros.', 'warning');
                    return;
                }

                if (total > 1000) {
                    Swal.fire({
                        title: ' Límite excedido',
                        html: `Se encontraron <strong>${total.toLocaleString()}</strong> registros.<br><br>
                        El reporte PDF tiene un límite de <strong>1,000 registros</strong>.<br>
                        Por favor aplique filtros más específicos para reducir el número de resultados.`,
                        icon: 'error',
                        confirmButtonColor: '#D32F2F',
                        confirmButtonText: 'Entendido',
                    });
                    return;
                }

                // Si está dentro del límite, genera normalmente
                form.submit();

            } catch (error) {
                Swal.fire('Error', 'No se pudo verificar el número de registros.', 'error');
            }
        });
    </script>

    <script>
        /* ================= VER DETALLE DE AUDITORÍA ================= */
        document.addEventListener('click', async function(e) {
            const viewBtn = e.target.closest('.view-auditoria-btn');
            if (viewBtn) {
                e.preventDefault();

                const id = viewBtn.dataset.id;

                const traducciones = {
                    'created': 'Creado',
                    'updated': 'Actualizado',
                    'deleted': 'Eliminado',
                    'restored': 'Restaurado',
                    'login': 'Inicio de sesión',
                    'logout': 'Cierre de sesión',
                };

                try {
                    const res = await fetch(`/admin/auditorias/${id}`);
                    const data = await res.json();

                    const auditoria = data.auditoria;

                    // Llenar modal
                    document.getElementById('detalle-id').textContent = auditoria.id;
                    document.getElementById('detalle-usuario').textContent = auditoria.user?.name ?? auditoria
                        .nombre_usuario ?? 'Sistema';
                    const accion = auditoria.accion ?? '';
                    document.getElementById('detalle-accion').textContent = traducciones[accion] ?? accion ??
                        '—';
                    document.getElementById('detalle-modulo').textContent = auditoria.modulo.replace('_', ' ');
                    document.getElementById('detalle-entidad-id').textContent = auditoria.entidad_id || '—';
                    document.getElementById('detalle-descripcion').textContent = auditoria.descripcion;
                    document.getElementById('detalle-ip').textContent = auditoria.ip_address || '—';
                    document.getElementById('detalle-fecha').textContent = new Date(auditoria.created_at)
                        .toLocaleString('es-ES');
                    document.getElementById('detalle-user-agent').textContent = auditoria.user_agent || '—';

                    // Valores anteriores
                    const valoresAnteriores = document.getElementById('detalle-valores-anteriores');
                    if (auditoria.valores_anteriores && Object.keys(auditoria.valores_anteriores).length > 0) {
                        valoresAnteriores.textContent = JSON.stringify(auditoria.valores_anteriores, null, 2);
                    } else {
                        valoresAnteriores.textContent = 'No hay valores anteriores';
                    }

                    // Valores nuevos
                    const valoresNuevos = document.getElementById('detalle-valores-nuevos');
                    if (auditoria.valores_nuevos && Object.keys(auditoria.valores_nuevos).length > 0) {
                        valoresNuevos.textContent = JSON.stringify(auditoria.valores_nuevos, null, 2);
                    } else {
                        valoresNuevos.textContent = 'No hay valores nuevos';
                    }

                    new bootstrap.Modal(document.getElementById('showAuditoriaModal')).show();

                } catch (error) {
                    console.error('Error al cargar auditoría:', error);
                    Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
                }
            }
        });
    </script>
@endsection
