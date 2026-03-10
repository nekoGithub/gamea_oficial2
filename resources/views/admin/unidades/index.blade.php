@extends('layouts.vertical', ['title' => 'Unidades'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
    @vite(['resources/css/tom-select-ubold.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Unidades', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0">Unidades Activas</h4>
                            <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnidadModal"
                                href="#">
                                <i class="ti ti-plus me-1"></i> Agregar Unidad
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-activos">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>Nro</th>
                                            <th>Nombre</th>
                                            <th>Sigla</th>
                                            <th>Celular</th>
                                            <th>Responsables</th>
                                            <th>Estado</th>
                                            <th>Fecha creación</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-activos">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Nro" type="text" />
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
                                                        placeholder="Sigla" type="text" />
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
                                                        placeholder="Responsables" type="text" />
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
                                        @forelse ($unidades as $unidad)
                                            <tr data-id="{{ $unidad->id }}">
                                                <td>{{ $unidad->id }}</td>
                                                <td>{{ $unidad->nombre }}</td>
                                                <td>{{ $unidad->sigla }}</td>
                                                <td>{{ $unidad->celular ?? '-' }}</td>
                                                <td>
                                                    @if ($unidad->responsables->isNotEmpty())
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach ($unidad->responsables as $responsable)
                                                                <span class="badge bg-soft-primary text-primary">
                                                                    {{ $responsable->nombre }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $unidad->estado === 'activa' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $unidad->estado }}
                                                    </span>
                                                </td>
                                                <td>{{ $unidad->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="#"
                                                            class="btn btn-default btn-icon btn-sm rounded-circle edit-unidad-btn"
                                                            data-id="{{ $unidad->id }}">
                                                            <i class="ti ti-edit fs-lg"></i>
                                                        </a>
                                                        <a href="#"
                                                            class="btn btn-default btn-icon btn-sm rounded-circle delete-unidad-btn"
                                                            data-id="{{ $unidad->id }}">
                                                            <i class="ti ti-trash fs-lg"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            {{-- <tr>
                                                <td colspan="8" class="text-center text-muted py-3">
                                                    No hay unidades registradas
                                                </td>
                                            </tr> --}}
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
                            <h4 class="card-title mb-0">Unidades Eliminadas</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>Nro</th>
                                            <th>Nombre</th>
                                            <th>Sigla</th>
                                            <th>Eliminado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-papelera">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input
                                                        class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Nro" type="text" />
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
                                                        placeholder="Sigla" type="text" />
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
                                        @forelse ($unidadesEliminadas as $unidad)
                                            <tr data-id="{{ $unidad->id }}">
                                                <td>{{ $unidad->id }}</td>
                                                <td>{{ $unidad->nombre }}</td>
                                                <td>{{ $unidad->sigla }}</td>
                                                <td>{{ $unidad->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="#"
                                                            class="btn btn-default btn-icon btn-sm rounded-circle restore-unidad-btn"
                                                            data-id="{{ $unidad->id }}">
                                                            <i class="ti ti-rotate fs-lg"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            {{-- <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    La papelera está vacía
                                                </td>
                                            </tr> --}}
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
    @include('admin.unidades.modals.add')
    @include('admin.unidades.modals.edit')

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-unidades.js'])

    <script type="module">
        import {
            initTomSelect
        } from "{{ Vite::asset('resources/js/components/tom-select.js') }}";

        document.addEventListener('DOMContentLoaded', () => {
            initTomSelect('.js-tom-select', {
                placeholder: 'Seleccione responsables'
            });
        });

        document.getElementById('addUnidadModal')?.addEventListener('shown.bs.modal', () => {
            initTomSelect('.js-tom-select', {
                placeholder: 'Seleccione responsables'
            });
        });
    </script>

    <script>
        // ==================== FUNCIONES DE UTILIDAD ====================
        function refreshDataTables() {
            if (window.unidadesDataTables) {
                window.unidadesDataTables.refresh();
            }
        }

        function renderResponsables(responsables = []) {
            if (!responsables.length) {
                return `<span class="text-muted">—</span>`;
            }

            return `
                <div class="d-flex flex-wrap gap-1">
                    ${responsables.map(r => `
                                                        <span class="badge bg-soft-primary text-primary">
                                                            ${r.nombre}
                                                        </span>
                                                    `).join('')}
                </div>
            `;
        }

        function createActivoRow(unidad) {
            const fecha = new Date(unidad.created_at).toLocaleDateString('es-ES', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            const badgeClass = unidad.estado === 'activa' ? 'bg-success' : 'bg-secondary';

            return `
                <tr data-id="${unidad.id}">
                    <td>${unidad.id}</td>
                    <td>${unidad.nombre}</td>
                    <td>${unidad.sigla}</td>
                    <td>${unidad.celular ?? '-'}</td>
                    <td>${renderResponsables(unidad.responsables)}</td>
                    <td>
                        <span class="badge ${badgeClass}">
                            ${unidad.estado}
                        </span>
                    </td>
                    <td>${fecha}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-unidad-btn" data-id="${unidad.id}">
                                <i class="ti ti-edit fs-lg"></i>
                            </a>
                            <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-unidad-btn" data-id="${unidad.id}">
                                <i class="ti ti-trash fs-lg"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        }

        function createPapeleraRow(unidad) {
            const fecha = new Date(unidad.deleted_at).toLocaleDateString('es-ES', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            return `
                <tr data-id="${unidad.id}">
                    <td>${unidad.id}</td>
                    <td>${unidad.nombre}</td>
                    <td>${unidad.sigla}</td>
                    <td>${fecha}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-unidad-btn" data-id="${unidad.id}">
                                <i class="ti ti-rotate fs-lg"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        }

        // ==================== LÓGICA PRINCIPAL ====================
        document.addEventListener('DOMContentLoaded', function() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const tbodyActivos = document.getElementById('tbody-activos');
            const tbodyPapelera = document.getElementById('tbody-papelera');

            // ================= AGREGAR UNIDAD =================
            document.getElementById('addUnidadForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const formData = new FormData(form);
                let hasError = false;

                // Validaciones
                const nombreInput = form.querySelector('[name="nombre"]');
                if (!nombreInput.value.trim()) {
                    nombreInput.classList.add('is-invalid');
                    hasError = true;
                } else {
                    nombreInput.classList.remove('is-invalid');
                }

                const siglaInput = form.querySelector('[name="sigla"]');
                if (!siglaInput.value.trim()) {
                    siglaInput.classList.add('is-invalid');
                    hasError = true;
                } else {
                    siglaInput.classList.remove('is-invalid');
                }

                const select = form.querySelector('[name="responsables[]"]');
                const responsablesError = document.getElementById('responsables-error');

                if (!select.tomselect || select.tomselect.items.length === 0) {
                    responsablesError.classList.remove('d-none');
                    hasError = true;
                } else {
                    responsablesError.classList.add('d-none');
                }

                const estadoSeleccionado = form.querySelector('input[name="estado"]:checked');
                const estadoError = document.getElementById('estado-error');

                if (!estadoSeleccionado) {
                    estadoError.classList.remove('d-none');
                    hasError = true;
                } else {
                    estadoError.classList.add('d-none');
                }

                if (hasError) return;

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (!res.ok && data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) input.classList.add('is-invalid');
                        });
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addUnidadModal')).hide();
                    form.reset();



                    const newRow = createActivoRow(data.unidad);
                    const dt = window.unidadesDataTables.activos;

                    dt.row.add([
                        data.unidad.id,
                        data.unidad.nombre,
                        data.unidad.sigla,
                        data.unidad.celular ?? '-',
                        renderResponsables(data.unidad.responsables),
                        `<span class="badge ${data.unidad.estado === 'activa' ? 'bg-success' : 'bg-secondary'}">
                            ${data.unidad.estado}
                        </span>`,
                        new Date(data.unidad.created_at).toLocaleDateString('es-ES'),
                        `
                        <div class="d-flex justify-content-center gap-1">
                            <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-unidad-btn" data-id="${data.unidad.id}">
                                <i class="ti ti-edit fs-lg"></i>
                            </a>
                            <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-unidad-btn" data-id="${data.unidad.id}">
                                <i class="ti ti-trash fs-lg"></i>
                            </a>
                        </div>
                        `
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Unidad creada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    Swal.fire('Error', 'No se pudo crear la unidad', 'error');
                }
            });

            // ================= EDITAR UNIDAD =================
            document.getElementById('editUnidadForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editUnidadId').value;
                const formData = new FormData(form);
                let hasError = false;

                // Validaciones...
                const nombre = document.getElementById('editNombre');
                if (!nombre.value.trim()) {
                    nombre.classList.add('is-invalid');
                    hasError = true;
                } else {
                    nombre.classList.remove('is-invalid');
                }

                const sigla = document.getElementById('editSigla');
                if (!sigla.value.trim()) {
                    sigla.classList.add('is-invalid');
                    hasError = true;
                } else {
                    sigla.classList.remove('is-invalid');
                }

                const estadoSeleccionado = form.querySelector('input[name="estado"]:checked');
                const estadoError = document.getElementById('edit-estado-error');

                if (!estadoSeleccionado) {
                    estadoError.classList.remove('d-none');
                    hasError = true;
                } else {
                    estadoError.classList.add('d-none');
                }

                const responsablesSelect = document.getElementById('editResponsables');
                const responsablesError = document.getElementById('edit-responsables-error');

                if (!responsablesSelect || !responsablesSelect.tomselect || responsablesSelect.tomselect
                    .items.length === 0) {
                    responsablesError.classList.remove('d-none');
                    hasError = true;
                } else {
                    responsablesError.classList.add('d-none');
                }

                if (hasError) return;

                try {
                    const res = await fetch(`/admin/unidades/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (!res.ok && data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) input.classList.add('is-invalid');
                        });
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('editUnidadModal')).hide();

                    const dt = window.unidadesDataTables.activos;

                    dt.row(`#unidad-${id}`).data([
                        data.unidad.id,
                        data.unidad.nombre,
                        data.unidad.sigla,
                        data.unidad.celular ?? '-',
                        renderResponsables(data.unidad.responsables),
                        `<span class="badge ${data.unidad.estado === 'activa' ? 'bg-success' : 'bg-secondary'}">
                    ${data.unidad.estado}
                        </span>`,
                        new Date(data.unidad.created_at).toLocaleDateString('es-ES'),
                        `
                        <div class="d-flex justify-content-center gap-1">
                            <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-unidad-btn" data-id="${data.unidad.id}">
                                <i class="ti ti-edit fs-lg"></i>
                            </a>
                            <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-unidad-btn" data-id="${data.unidad.id}">
                                <i class="ti ti-trash fs-lg"></i>
                            </a>
                        </div>
                        `
                    ]).draw(false);


                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Unidad actualizada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    Swal.fire('Error', 'No se pudo actualizar la unidad', 'error');
                }
            });

            // ================= DELEGACIÓN DE EVENTOS =================
            document.addEventListener('click', async function(e) {

                // EDITAR
                const editBtn = e.target.closest('.edit-unidad-btn');
                if (editBtn) {
                    e.preventDefault();
                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/unidades/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editUnidadId').value = data.unidad.id;
                        document.getElementById('editNombre').value = data.unidad.nombre;
                        document.getElementById('editSigla').value = data.unidad.sigla;
                        document.getElementById('editCelular').value = data.unidad.celular ?? '';
                        document.getElementById('editDescripcion').value = data.unidad.descripcion ??
                            '';

                        document.getElementById('editEstadoActiva').checked = data.unidad.estado ===
                            'activa';
                        document.getElementById('editEstadoInactiva').checked = data.unidad.estado ===
                            'inactiva';

                        const select = document.getElementById('editResponsables');
                        if (select.tomselect) {
                            select.tomselect.destroy();
                        }

                        const ts = new TomSelect(select, {
                            plugins: ['remove_button'],
                            placeholder: 'Seleccione responsables'
                        });

                        ts.setValue((data.unidad.responsables || []).map(r => r.id));

                        document.getElementById('edit-estado-error')?.classList.add('d-none');
                        document.getElementById('edit-responsables-error')?.classList.add('d-none');

                        new bootstrap.Modal(document.getElementById('editUnidadModal')).show();

                    } catch (error) {
                        Swal.fire('Error', error.message, 'error');
                    }
                    return;
                }

                // ================= ELIMINAR =================
                const deleteBtn = e.target.closest('.delete-unidad-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;
                    const dtActivos = window.unidadesDataTables.activos;
                    const dtPapelera = window.unidadesDataTables.papelera;

                    Swal.fire({
                        title: '¿Eliminar Unidad?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then(async result => {
                        if (!result.isConfirmed) return;

                        try {
                            const res = await fetch(`/admin/unidades/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                }
                            });

                            const data = await res.json();

                            if (!res.ok || !data.success) {
                                Swal.fire('Error', 'No se pudo eliminar', 'error');
                                return;
                            }

                            //  Obtener datos antes de eliminar
                            const rowData = dtActivos.row(`#unidad-${id}`).data();

                            //  Eliminar de ACTIVOS
                            dtActivos.row(`#unidad-${id}`).remove().draw(false);

                            //  Agregar a PAPELERA
                            dtPapelera.row.add([
                                rowData[0], // id
                                rowData[1], // nombre
                                rowData[2], // sigla
                                new Date().toLocaleDateString('es-ES'),
                                `
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-unidad-btn" data-id="${id}">
                                        <i class="ti ti-rotate fs-lg"></i>
                                    </a>
                                </div>
                                `
                            ]).draw(false);

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Unidad enviada a la papelera',
                                showConfirmButton: false,
                                timer: 2000
                            });

                        } catch (error) {
                            Swal.fire('Error', 'No se pudo eliminar la unidad', 'error');
                        }
                    });

                    return;
                }


                // ================= RESTAURAR =================
                const restoreBtn = e.target.closest('.restore-unidad-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;
                    const dtActivos = window.unidadesDataTables.activos;
                    const dtPapelera = window.unidadesDataTables.papelera;

                    Swal.fire({
                        title: '¿Restaurar Unidad?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar'
                    }).then(async result => {
                        if (!result.isConfirmed) return;

                        try {
                            const res = await fetch(`/admin/unidades/${id}/restore`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                }
                            });

                            const data = await res.json();

                            if (!res.ok || !data.success) {
                                Swal.fire('Error', data.message || 'No se pudo restaurar',
                                    'error');
                                return;
                            }

                            // 🔹 Eliminar de PAPELERA
                            dtPapelera.row(`#unidad-${id}`).remove().draw(false);

                            // 🔹 Agregar nuevamente a ACTIVOS
                            dtActivos.row.add([
                                data.unidad.id,
                                data.unidad.nombre,
                                data.unidad.sigla,
                                data.unidad.celular ?? '-',
                                renderResponsables(data.unidad.responsables),
                                `<span class="badge bg-success">activa</span>`,
                                new Date(data.unidad.created_at).toLocaleDateString(
                                    'es-ES'),
                                `
                            <div class="d-flex justify-content-center gap-1">
                                <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-unidad-btn" data-id="${data.unidad.id}">
                                    <i class="ti ti-edit fs-lg"></i>
                                </a>
                                <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-unidad-btn" data-id="${data.unidad.id}">
                                    <i class="ti ti-trash fs-lg"></i>
                                </a>
                            </div>
                            `
                            ]).draw(false);

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Unidad restaurada',
                                showConfirmButton: false,
                                timer: 1500
                            });

                        } catch (error) {
                            Swal.fire('Error', 'No se pudo restaurar la unidad', 'error');
                        }
                    });

                    return;
                }

            });

            // ================= MARCAR "ACTIVA" AL ABRIR MODAL =================
            document.getElementById('addUnidadModal')?.addEventListener('shown.bs.modal', function() {
                const form = document.getElementById('addUnidadForm');
                if (!form) return;

                // Marcar "activa" por defecto si ningún radio está seleccionado
                const estadoChecked = form.querySelector('input[name="estado"]:checked');
                if (!estadoChecked) {
                    const estadoActivaRadio = form.querySelector('input[name="estado"][value="activa"]');
                    if (estadoActivaRadio) {
                        estadoActivaRadio.checked = true;
                    }
                }
            });

            // ================= LIMPIAR MODAL AL CERRAR =================
            document.getElementById('addUnidadModal')?.addEventListener('hidden.bs.modal', function() {

                const form = document.getElementById('addUnidadForm');
                if (!form) return;

                form.reset();


                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                document.getElementById('responsables-error')?.classList.add('d-none');
                document.getElementById('estado-error')?.classList.add('d-none');

                form.querySelectorAll('input[name="estado"]').forEach(radio => radio.checked = false);

                form.querySelectorAll('.js-tom-select').forEach(select => {
                    if (select.tomselect) {
                        select.tomselect.clear();
                    }
                });
            });

            // ================= LIMPIAR ERRORES AL ESCRIBIR =================
            document.querySelector('[name="nombre"]')?.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });

            document.querySelector('[name="sigla"]')?.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });

            document.querySelector('[name="responsables[]"]')?.addEventListener('change', function() {
                document.getElementById('responsables-error')?.classList.add('d-none');
            });

            document.querySelectorAll('input[name="estado"]').forEach(radio => {
                radio.addEventListener('change', () => {
                    document.getElementById('estado-error')?.classList.add('d-none');
                });
            });

            document.getElementById('editNombre')?.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });

            document.getElementById('editSigla')?.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });

            document.querySelectorAll('#editUnidadForm input[name="estado"]').forEach(radio => {
                radio.addEventListener('change', () => {
                    document.getElementById('edit-estado-error')?.classList.add('d-none');
                });
            });
        });
    </script>
@endsection
