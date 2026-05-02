@extends('layouts.vertical', ['title' => 'Bases de Datos'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Bases de Datos', 'title' => 'Listado'])

    <div class="row">
        <div class="col-12">

            {{-- TABS --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-activos">
                        <i class="ti ti-list me-1"></i> Listado
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-papelera">
                        <i class="ti ti-trash me-1"></i> Papelera
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ================= TAB ACTIVOS ================= --}}
                <div class="tab-pane fade show active" id="tab-activos">

                    <div class="card">

                        <div class="card-header border-light justify-content-between">
                            <h4 class="card-title mb-0">Gestores de Bases de Datos</h4>
                            @can('admin.bases-datos.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBaseDatosModal"
                                    href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar Base de Datos
                                </a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-activos">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Gestor</th>
                                            <th>Versión</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Fecha Creación</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-activos">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="ID" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Gestor" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Versión" type="text">
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
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Estado" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Fecha" type="text">
                                                </div>
                                            </th>

                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-activos">
                                        @forelse ($basesDatos as $bd)
                                            <tr data-id="{{ $bd->id }}">
                                                <td>{{ $bd->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-database fs-4 text-primary me-2"></i>
                                                        <strong>{{ $bd->gestor }}</strong>
                                                    </div>
                                                </td>
                                                <td><code>{{ $bd->version }}</code></td>
                                                <td>{{ Str::limit($bd->descripcion ?? '—', 50) }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $bd->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($bd->estado) }}
                                                    </span>
                                                </td>
                                                <td>{{ $bd->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.bases-datos.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-bd-btn"
                                                                data-id="{{ $bd->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.bases-datos.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-bd-btn"
                                                                data-id="{{ $bd->id }}">
                                                                <i class="ti ti-trash fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ================= TAB PAPELERA ================= --}}
                <div class="tab-pane fade" id="tab-papelera">

                    <div class="card">

                        <div class="card-header border-light">
                            <h4 class="card-title mb-0">Bases de Datos Eliminadas</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Gestor</th>
                                            <th>Versión</th>
                                            <th>Eliminado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-papelera">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="ID" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Gestor" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Versión" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Fecha" type="text">
                                                </div>
                                            </th>

                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-papelera">
                                        @forelse ($basesDatosEliminadas as $bd)
                                            <tr data-id="{{ $bd->id }}">
                                                <td>{{ $bd->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-database fs-4 text-muted me-2"></i>
                                                        <strong>{{ $bd->gestor }}</strong>
                                                    </div>
                                                </td>
                                                <td><code>{{ $bd->version }}</code></td>
                                                <td>{{ $bd->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.bases-datos.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-bd-btn"
                                                                data-id="{{ $bd->id }}">
                                                                <i class="ti ti-rotate fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODALES --}}
    @include('admin.bases-datos.modals.add')
    @include('admin.bases-datos.modals.edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-bases-datos.js'])

    <script>
        const canEdit = @json(auth()->user()?->can('admin.bases-datos.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.bases-datos.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.bases-datos.restore') ?? false);
    </script>

    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

        function getGestorHtml(gestor) {
            return `
                <div class="d-flex align-items-center">
                    <i class="ti ti-database fs-4 text-primary me-2"></i>
                    <strong>${gestor}</strong>
                </div>`;
        }

        function getEstadoBadge(estado) {
            return estado === 'activo' ?
                '<span class="badge bg-success">Activo</span>' :
                '<span class="badge bg-secondary">Inactivo</span>';
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-ES', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }

        function truncarTexto(texto, limite = 50) {
            if (!texto || texto === '—') return '—';
            return texto.length > limite ? texto.substring(0, limite) + '...' : texto;
        }

        function accionesActivos(id) {
            let btns = '<div class="d-flex justify-content-center gap-1">';
            if (canEdit) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-bd-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-bd-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-bd-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= AGREGAR BASE DE DATOS ================= */
            document.getElementById('addBaseDatosForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    const data = await res.json();

                    if (res.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                let feedback = input.nextElementSibling;
                                if (feedback && feedback.classList.contains(
                                        'invalid-feedback')) {
                                    feedback.textContent = data.errors[field][0];
                                }
                            }
                        });
                        return;
                    }

                    if (!res.ok || !data.success) {
                        Swal.fire('Error', 'No se pudo crear la base de datos', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addBaseDatosModal')).hide();

                    window.basesDatosDataTables.activos.row.add([
                        data.base_datos.id,
                        getGestorHtml(data.base_datos.gestor),
                        `<code>${data.base_datos.version}</code>`,
                        truncarTexto(data.base_datos.descripcion),
                        getEstadoBadge(data.base_datos.estado),
                        formatearFecha(data.base_datos.created_at),
                        accionesActivos(data.base_datos.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Base de datos creada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear base de datos:', error);
                    Swal.fire('Error', 'No se pudo crear la base de datos', 'error');
                }
            });

            /* ================= EDITAR BASE DE DATOS ================= */
            document.getElementById('editBaseDatosForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editBaseDatosId').value;

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/bases-datos/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    const data = await res.json();

                    if (res.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                let feedback = input.nextElementSibling;
                                if (feedback && feedback.classList.contains(
                                        'invalid-feedback')) {
                                    feedback.textContent = data.errors[field][0];
                                }
                            }
                        });
                        return;
                    }

                    if (!res.ok || !data.success) {
                        Swal.fire('Error', 'No se pudo actualizar', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('editBaseDatosModal')).hide();

                    window.basesDatosDataTables.activos.row(`#bd-${id}`).data([
                        data.base_datos.id,
                        getGestorHtml(data.base_datos.gestor),
                        `<code>${data.base_datos.version}</code>`,
                        truncarTexto(data.base_datos.descripcion),
                        getEstadoBadge(data.base_datos.estado),
                        formatearFecha(data.base_datos.created_at),
                        accionesActivos(id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Base de datos actualizada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar base de datos:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-bd-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/bases-datos/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editBaseDatosId').value = data.base_datos.id;
                        document.getElementById('editGestor').value = data.base_datos.gestor;
                        document.getElementById('editVersion').value = data.base_datos.version;
                        document.getElementById('editDescripcion').value = data.base_datos
                            .descripcion || '';

                        if (data.base_datos.estado === 'activo') {
                            document.getElementById('editEstadoActivo').checked = true;
                        } else {
                            document.getElementById('editEstadoInactivo').checked = true;
                        }

                        new bootstrap.Modal(document.getElementById('editBaseDatosModal')).show();

                    } catch (error) {
                        console.error('Error al cargar base de datos:', error);
                        Swal.fire('Error', 'No se pudo cargar la base de datos', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-bd-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Base de Datos?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/bases-datos/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (!data.success) {
                            Swal.fire('Error', 'No se pudo eliminar', 'error');
                            return;
                        }

                        const dtA = window.basesDatosDataTables.activos;
                        const dtP = window.basesDatosDataTables.papelera;

                        const rowData = dtA.row(`#bd-${id}`).data();

                        dtA.row(`#bd-${id}`).remove().draw(false);

                        dtP.row.add([
                            rowData[0],
                            rowData[1],
                            rowData[2],
                            formatearFecha(new Date()),
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Base de datos enviada a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar base de datos:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-bd-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Base de Datos?',
                        text: 'Volverá a la lista activa',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/bases-datos/${id}/restore`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            Swal.fire('Error', 'No se pudo restaurar', 'error');
                            return;
                        }

                        const dtA = window.basesDatosDataTables.activos;
                        const dtP = window.basesDatosDataTables.papelera;

                        dtP.row(`#bd-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.base_datos.id,
                            getGestorHtml(data.base_datos.gestor),
                            `<code>${data.base_datos.version}</code>`,
                            truncarTexto(data.base_datos.descripcion),
                            getEstadoBadge(data.base_datos.estado),
                            formatearFecha(data.base_datos.created_at),
                            accionesActivos(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Base de datos restaurada correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar base de datos:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll(
                '#addBaseDatosForm input, #addBaseDatosForm textarea, #editBaseDatosForm input, #editBaseDatosForm textarea'
            ).forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addBaseDatosModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addBaseDatosForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            document.getElementById('editBaseDatosModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editBaseDatosForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

        });
    </script>
@endsection
