@extends('layouts.vertical', ['title' => 'Responsables'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Responsables', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0">Responsables Activos</h4>
                            @can('admin.responsables.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResponsableModal"
                                    href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar Responsable
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
                                            <th>Cargo</th>
                                            <th>Email</th>
                                            <th>Celular</th>
                                            <th>Fecha creación</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-activos">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input type="text" class="form-control bg-light-subtle border-light"
                                                        placeholder="ID">
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Nombre" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Cargo" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Email" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Celular" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Fecha" type="text" />
                                                </div>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-activos">
                                        @forelse ($responsables as $responsable)
                                            <tr data-id="{{ $responsable->id }}">
                                                <td>{{ $responsable->id }}</td>
                                                <td>{{ $responsable->nombre }}</td>
                                                <td>{{ $responsable->cargo }}</td>
                                                <td>{{ $responsable->email ? $responsable->email : 'no existe un correo' }}</td>
                                                <td>{{ $responsable->celular }}</td>
                                                <td>{{ $responsable->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.responsables.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-responsable-btn"
                                                                data-id="{{ $responsable->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.responsables.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-responsable-btn"
                                                                data-id="{{ $responsable->id }}">
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
                            <h4 class="card-title mb-0">Responsables Eliminados</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Cargo</th>
                                            <th>Email</th>
                                            <th>Eliminado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-papelera">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="ID" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input
                                                        class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Nombre" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input
                                                        class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Cargo" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input
                                                        class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Email" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input
                                                        class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Fecha" type="text" />
                                                </div>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-papelera">
                                        @forelse ($responsablesEliminados as $responsable)
                                            <tr data-id="{{ $responsable->id }}">
                                                <td>{{ $responsable->id }}</td>
                                                <td>{{ $responsable->nombre }}</td>
                                                <td>{{ $responsable->cargo }}</td>
                                                <td>{{ $responsable->email }}</td>
                                                <td>{{ $responsable->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.responsables.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-responsable-btn"
                                                                data-id="{{ $responsable->id }}">
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
    @include('admin.responsables.modals.add')
    @include('admin.responsables.modals.edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-responsables.js'])

    <script>
        const canEdit = @json(auth()->user()?->can('admin.responsables.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.responsables.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.responsables.restore') ?? false);
    </script>

    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

        function formatearFecha(fecha) {
            if (!fecha) return '—';
            const [y, m, d] = fecha.substring(0, 10).split('-');
            const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            return `${parseInt(d)} ${meses[parseInt(m) - 1]}, ${y}`;
        }

        function accionesActivos(id) {
            let btns = '<div class="d-flex justify-content-center gap-1">';
            if (canEdit) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-responsable-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-responsable-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-responsable-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= AGREGAR RESPONSABLE ================= */
            document.getElementById('addResponsableForm')?.addEventListener('submit', async function(e) {
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
                        Swal.fire('Error', 'No se pudo crear el responsable', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addResponsableModal')).hide();

                    window.responsablesDataTables.activos.row.add([
                        data.responsable.id,
                        data.responsable.nombre,
                        data.responsable.cargo,
                        data.responsable.email,
                        data.responsable.celular,
                        formatearFecha(data.responsable.created_at),
                        accionesActivos(data.responsable.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Responsable creado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear responsable:', error);
                    Swal.fire('Error', 'No se pudo crear el responsable', 'error');
                }
            });

            /* ================= EDITAR RESPONSABLE ================= */
            document.getElementById('editResponsableForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editResponsableId').value;

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/responsables/${id}`, {
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

                    bootstrap.Modal.getInstance(document.getElementById('editResponsableModal')).hide();

                    window.responsablesDataTables.activos.row(`#responsable-${id}`).data([
                        data.responsable.id,
                        data.responsable.nombre,
                        data.responsable.cargo,
                        data.responsable.email,
                        data.responsable.celular,
                        formatearFecha(data.responsable.created_at),
                        accionesActivos(data.responsable.id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Responsable actualizado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar responsable:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-responsable-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/responsables/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editResponsableId').value = data.responsable.id;
                        document.getElementById('editNombre').value = data.responsable.nombre;
                        document.getElementById('editCargo').value = data.responsable.cargo;
                        document.getElementById('editEmail').value = data.responsable.email;
                        document.getElementById('editCelular').value = data.responsable.celular;

                        new bootstrap.Modal(document.getElementById('editResponsableModal')).show();

                    } catch (error) {
                        console.error('Error al cargar responsable:', error);
                        Swal.fire('Error', 'No se pudo cargar el responsable', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-responsable-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Responsable?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/responsables/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        if (!res.ok) throw new Error('Error HTTP');

                        const data = await res.json();

                        if (!data.success) {
                            Swal.fire('Error', 'No se pudo eliminar', 'error');
                            return;
                        }

                        const dtA = window.responsablesDataTables.activos;
                        const dtP = window.responsablesDataTables.papelera;

                        const row = deleteBtn.closest('tr');
                        const realRow = row.classList.contains('child') ? row.previousSibling : row;
                        const rowData = dtA.row(realRow).data();

                        dtA.row(realRow).remove().draw(false);

                        dtP.row.add([
                            rowData[0],
                            rowData[1],
                            rowData[2],
                            rowData[3],
                            rowData[4],
                            formatearFecha(new Date().toISOString()),
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Responsable enviado a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar responsable:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-responsable-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Responsable?',
                        text: 'Volverá a la lista activa',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/responsables/${id}/restore`, {
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

                        const dtA = window.responsablesDataTables.activos;
                        const dtP = window.responsablesDataTables.papelera;

                        dtP.row(`#responsable-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.responsable.id,
                            data.responsable.nombre,
                            data.responsable.cargo,
                            data.responsable.email,
                            data.responsable.celular,
                            formatearFecha(data.responsable.created_at),
                            accionesActivos(data.responsable.id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Responsable restaurado correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar responsable:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll('#addResponsableForm input, #editResponsableForm input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addResponsableModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addResponsableForm');
                if (!form) return;
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            document.getElementById('editResponsableModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editResponsableForm');
                if (!form) return;
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

        });
    </script>
@endsection
