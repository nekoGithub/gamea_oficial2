@extends('layouts.vertical', ['title' => 'Sistemas Operativos'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Sistemas Operativos', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0">Sistemas Operativos</h4>
                            @can('admin.sistemas-operativos.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSistemaOperativoModal"
                                    href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar Sistema Operativo
                                </a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-activos">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Nombre</th>
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
                                                        placeholder="Nombre" type="text">
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
                                        @forelse ($sistemasOperativos as $so)
                                            <tr data-id="{{ $so->id }}">
                                                <td>{{ $so->id }}</td>
                                                <td>{{ $so->nombre }}</td>
                                                <td>{{ $so->version }}</td>
                                                <td>{{ Str::limit($so->descripcion ?? '—', 50) }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $so->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($so->estado) }}
                                                    </span>
                                                </td>
                                                <td>{{ $so->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.sistemas-operativos.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-so-btn"
                                                                data-id="{{ $so->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.sistemas-operativos.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-so-btn"
                                                                data-id="{{ $so->id }}">
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
                            <h4 class="card-title mb-0">Sistemas Operativos Eliminados</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Nombre</th>
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
                                                        placeholder="Nombre" type="text">
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
                                        @forelse ($sistemasOperativosEliminados as $so)
                                            <tr data-id="{{ $so->id }}">
                                                <td>{{ $so->id }}</td>
                                                <td>{{ $so->nombre }}</td>
                                                <td>{{ $so->version }}</td>
                                                <td>{{ $so->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.sistemas-operativos.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-so-btn"
                                                                data-id="{{ $so->id }}">
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
    @include('admin.sistemas-operativos.modals.add')
    @include('admin.sistemas-operativos.modals.edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-sistemas-operativos.js'])

    <script>
        const canEdit = @json(auth()->user()?->can('admin.sistemas-operativos.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.sistemas-operativos.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.sistemas-operativos.restore') ?? false);
    </script>

    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

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
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-so-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-so-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-so-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= AGREGAR SISTEMA OPERATIVO ================= */
            document.getElementById('addSistemaOperativoForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;

                // Limpiar errores previos
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

                    // Manejar errores de validación
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
                        Swal.fire('Error', 'No se pudo crear el sistema operativo', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addSistemaOperativoModal'))
                        .hide();

                    window.sistemasOperativosDataTables.activos.row.add([
                        data.sistema_operativo.id,
                        data.sistema_operativo.nombre,
                        data.sistema_operativo.version,
                        truncarTexto(data.sistema_operativo.descripcion),
                        getEstadoBadge(data.sistema_operativo.estado),
                        formatearFecha(data.sistema_operativo.created_at),
                        accionesActivos(data.sistema_operativo.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Sistema operativo creado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear sistema operativo:', error);
                    Swal.fire('Error', 'No se pudo crear el sistema operativo', 'error');
                }
            });

            /* ================= EDITAR SISTEMA OPERATIVO ================= */
            document.getElementById('editSistemaOperativoForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editSistemaOperativoId').value;

                // Limpiar errores previos
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/sistemas-operativos/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    const data = await res.json();

                    // Manejar errores de validación
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

                    bootstrap.Modal.getInstance(document.getElementById('editSistemaOperativoModal'))
                        .hide();

                    window.sistemasOperativosDataTables.activos.row(`#so-${id}`).data([
                        data.sistema_operativo.id,
                        data.sistema_operativo.nombre,
                        data.sistema_operativo.version,
                        truncarTexto(data.sistema_operativo.descripcion),
                        getEstadoBadge(data.sistema_operativo.estado),
                        formatearFecha(data.sistema_operativo.created_at),
                        accionesActivos(id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Sistema operativo actualizado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar sistema operativo:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-so-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/sistemas-operativos/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editSistemaOperativoId').value = data.sistema_operativo
                            .id;
                        document.getElementById('editNombre').value = data.sistema_operativo.nombre;
                        document.getElementById('editVersion').value = data.sistema_operativo.version;
                        document.getElementById('editDescripcion').value = data.sistema_operativo
                            .descripcion || '';

                        // Establecer estado
                        if (data.sistema_operativo.estado === 'activo') {
                            document.getElementById('editEstadoActivo').checked = true;
                        } else {
                            document.getElementById('editEstadoInactivo').checked = true;
                        }

                        new bootstrap.Modal(document.getElementById('editSistemaOperativoModal'))
                            .show();

                    } catch (error) {
                        console.error('Error al cargar sistema operativo:', error);
                        Swal.fire('Error', 'No se pudo cargar el sistema operativo', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-so-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Sistema Operativo?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/sistemas-operativos/${id}`, {
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

                        const dtA = window.sistemasOperativosDataTables.activos;
                        const dtP = window.sistemasOperativosDataTables.papelera;

                        const rowData = dtA.row(`#so-${id}`).data();

                        dtA.row(`#so-${id}`).remove().draw(false);

                        dtP.row.add([
                            rowData[0], // ID
                            rowData[1], // Nombre
                            rowData[2], // Versión
                            formatearFecha(new Date()),
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Sistema operativo enviado a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar sistema operativo:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-so-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Sistema Operativo?',
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
                        const res = await fetch(`/admin/sistemas-operativos/${id}/restore`, {
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

                        const dtA = window.sistemasOperativosDataTables.activos;
                        const dtP = window.sistemasOperativosDataTables.papelera;

                        dtP.row(`#so-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.sistema_operativo.id,
                            data.sistema_operativo.nombre,
                            data.sistema_operativo.version,
                            truncarTexto(data.sistema_operativo.descripcion),
                            getEstadoBadge(data.sistema_operativo.estado),
                            formatearFecha(data.sistema_operativo.created_at),
                            accionesActivos(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Sistema operativo restaurado correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar sistema operativo:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll(
                '#addSistemaOperativoForm input, #addSistemaOperativoForm textarea, #editSistemaOperativoForm input, #editSistemaOperativoForm textarea'
            ).forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addSistemaOperativoModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addSistemaOperativoForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            document.getElementById('editSistemaOperativoModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editSistemaOperativoForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

        });
    </script>
@endsection
