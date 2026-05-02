@extends('layouts.vertical', ['title' => 'Documentos'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Documentos', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0">Tipos de Documentos</h4>
                            @can('admin.documentos.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentoModal"
                                    href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar Documento
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
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="ID" type="text" />
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
                                                        placeholder="Estado" type="text" />
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
                                        @forelse ($documentos as $doc)
                                            <tr data-id="{{ $doc->id }}">
                                                <td>{{ $doc->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-file-text fs-4 text-primary me-2"></i>
                                                        <strong>{{ $doc->nombre }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $doc->activo ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $doc->activo ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </td>
                                                <td>{{ $doc->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.documentos.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-doc-btn"
                                                                data-id="{{ $doc->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.documentos.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-doc-btn"
                                                                data-id="{{ $doc->id }}">
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
                            <h4 class="card-title mb-0">Documentos Eliminados</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Nombre</th>
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
                                                        placeholder="Fecha" type="text">
                                                </div>
                                            </th>

                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-papelera">
                                        @forelse ($documentosEliminados as $doc)
                                            <tr data-id="{{ $doc->id }}">
                                                <td>{{ $doc->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-file-text fs-4 text-muted me-2"></i>
                                                        <strong>{{ $doc->nombre }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ $doc->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        @can('admin.documentos.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-doc-btn"
                                                                data-id="{{ $doc->id }}">
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
    @include('admin.documentos.modals.add')
    @include('admin.documentos.modals.edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-documentos.js'])

    <script>
        const canEdit = @json(auth()->user()?->can('admin.documentos.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.documentos.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.documentos.restore') ?? false);
    </script>


    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

        function getNombreHtml(nombre) {
            return `
                <div class="d-flex align-items-center">
                    <i class="ti ti-file-text fs-4 text-primary me-2"></i>
                    <strong>${nombre}</strong>
                </div>`;
        }

        function getEstadoBadge(activo) {
            return activo ?
                '<span class="badge bg-success">Activo</span>' :
                '<span class="badge bg-secondary">Inactivo</span>';
        }

        function formatearFecha(fecha) {
            if (!fecha) return '—';
            const [y, m, d] = fecha.substring(0, 10).split('-');
            const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            return `${parseInt(d)} ${meses[parseInt(m) - 1]}, ${y}`;
        }

        function accionesActivos(id) {
            let btns = '<div class="d-flex justify-content-center gap-1">';
            if (canEdit) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-doc-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-doc-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-doc-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= AGREGAR DOCUMENTO ================= */
            document.getElementById('addDocumentoForm')?.addEventListener('submit', async function(e) {
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
                        Swal.fire('Error', 'No se pudo crear el documento', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addDocumentoModal')).hide();

                    window.documentosDataTables.activos.row.add([
                        data.documento.id,
                        getNombreHtml(data.documento.nombre),
                        getEstadoBadge(data.documento.activo),
                        formatearFecha(data.documento.created_at),
                        accionesActivos(data.documento.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Documento creado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear documento:', error);
                    Swal.fire('Error', 'No se pudo crear el documento', 'error');
                }
            });

            /* ================= EDITAR DOCUMENTO ================= */
            document.getElementById('editDocumentoForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editDocumentoId').value;

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/documentos/${id}`, {
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

                    bootstrap.Modal.getInstance(document.getElementById('editDocumentoModal')).hide();

                    window.documentosDataTables.activos.row(`#documento-${id}`).data([
                        data.documento.id,
                        getNombreHtml(data.documento.nombre),
                        getEstadoBadge(data.documento.activo),
                        formatearFecha(data.documento.created_at),
                        accionesActivos(data.documento.id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Documento actualizado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar documento:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-doc-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/documentos/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editDocumentoId').value = data.documento.id;
                        document.getElementById('editNombre').value = data.documento.nombre;

                        // Estado
                        if (data.documento.activo) {
                            document.getElementById('editEstadoActivo').checked = true;
                        } else {
                            document.getElementById('editEstadoInactivo').checked = true;
                        }

                        new bootstrap.Modal(document.getElementById('editDocumentoModal')).show();

                    } catch (error) {
                        console.error('Error al cargar documento:', error);
                        Swal.fire('Error', 'No se pudo cargar el documento', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-doc-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Documento?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/documentos/${id}`, {
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

                        const dtA = window.documentosDataTables.activos;
                        const dtP = window.documentosDataTables.papelera;

                        const row = deleteBtn.closest('tr');
                        const realRow = row.classList.contains('child') ? row.previousSibling : row;
                        const rowData = dtA.row(realRow).data();

                        dtA.row(realRow).remove().draw(false);

                        dtP.row.add([
                            rowData[0],
                            rowData[1],
                            formatearFecha(new Date().toISOString()),
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Documento enviado a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar documento:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-doc-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Documento?',
                        text: 'Volverá a la lista activa',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/documentos/${id}/restore`, {
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

                        const dtA = window.documentosDataTables.activos;
                        const dtP = window.documentosDataTables.papelera;

                        dtP.row(`#documento-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.documento.id,
                            getNombreHtml(data.documento.nombre),
                            getEstadoBadge(data.documento.activo),
                            formatearFecha(data.documento.created_at),
                            accionesActivos(data.documento.id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Documento restaurado correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar documento:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll('#addDocumentoForm input, #editDocumentoForm input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addDocumentoModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addDocumentoForm');
                if (!form) return;
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            document.getElementById('editDocumentoModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editDocumentoForm');
                if (!form) return;
                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

        });
    </script>
@endsection
