@extends('layouts.vertical', ['title' => 'Servidores'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Servidores', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0">Servidores</h4>
                            @can('admin.servidores.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServidorModal"
                                    href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar Servidor
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
                                            <th>IP Interna</th>
                                            <th>IP Externa</th>
                                            <th>MAC</th>
                                            <th>Sistema Operativo</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
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
                                                        placeholder="IP Interna" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="IP Externa" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="MAC" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="S.O." type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Tipo" type="text">
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

                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-activos">
                                        @forelse ($servidores as $servidor)
                                            <tr data-id="{{ $servidor->id }}">
                                                <td>{{ $servidor->id }}</td>
                                                <td>
                                                    <strong>{{ $servidor->nombre }}</strong>

                                                    @if ($servidor->descripcion)
                                                        <div class="text-muted small mt-1" data-bs-toggle="tooltip"
                                                            title="{{ $servidor->descripcion }}">
                                                            {{ Str::limit($servidor->descripcion, 60) }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td><code>{{ $servidor->ip_interna }}</code></td>
                                                <td>
                                                    @if ($servidor->ip_externa)
                                                        <code class="text-success">{{ $servidor->ip_externa }}</code>
                                                    @else
                                                        <span class="badge bg-soft-secondary text-secondary">No
                                                            asignada</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($servidor->mac_address)
                                                        <code class="text-muted">{{ $servidor->mac_address }}</code>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info">
                                                        {{ $servidor->sistemaOperativo->nombre }}
                                                        {{ $servidor->sistemaOperativo->version }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $servidor->tipo_servidor === 'físico' ? 'bg-soft-primary text-primary' : 'bg-soft-warning text-warning' }}">
                                                        {{ ucfirst($servidor->tipo_servidor) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $servidor->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($servidor->estado) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.servidores.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-servidor-btn"
                                                                data-id="{{ $servidor->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.servidores.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-servidor-btn"
                                                                data-id="{{ $servidor->id }}">
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
                            <h4 class="card-title mb-0">Servidores Eliminados</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100"
                                    id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>IP Interna</th>
                                            <th>Sistema Operativo</th>
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
                                                        placeholder="IP" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="S.O." type="text">
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
                                        @forelse ($servidoresEliminados as $servidor)
                                            <tr data-id="{{ $servidor->id }}">
                                                <td>{{ $servidor->id }}</td>
                                                <td>{{ $servidor->nombre }}</td>
                                                <td><code>{{ $servidor->ip_interna }}</code></td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info">
                                                        {{ $servidor->sistemaOperativo->nombre }}
                                                        {{ $servidor->sistemaOperativo->version }}
                                                    </span>
                                                </td>
                                                <td>{{ $servidor->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.servidores.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-servidor-btn"
                                                                data-id="{{ $servidor->id }}">
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
    @include('admin.servidores.modals.add')
    @include('admin.servidores.modals.edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-servidores.js'])

    <script>
        // Permisos 
        const canEdit = @json(auth()->user()?->can('admin.servidores.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.servidores.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.servidores.restore') ?? false);
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

        function getSistemaOperativoBadge(so) {
            return `<span class="badge bg-soft-info text-info">${so.nombre} ${so.version}</span>`;
        }

        function getTipoBadge(tipo) {
            return tipo === 'físico' ?
                '<span class="badge bg-soft-primary text-primary">Físico</span>' :
                '<span class="badge bg-soft-warning text-warning">Virtual</span>';
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

        function accionesActivos(id) {
            let btns = '<div class="d-flex justify-content-center gap-1">';
            if (canEdit) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-servidor-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-servidor-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-servidor-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= AGREGAR SERVIDOR ================= */
            document.getElementById('addServidorForm')?.addEventListener('submit', async function(e) {
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

                                // 🔥 BUSCAR EL .invalid-feedback CORRECTAMENTE
                                let feedback = null;

                                // Si el input está dentro de un input-group
                                const inputGroup = input.closest('.input-group');
                                if (inputGroup) {
                                    // Buscar después del input-group
                                    feedback = inputGroup.nextElementSibling;
                                } else {
                                    // Buscar después del input directamente
                                    feedback = input.nextElementSibling;
                                }

                                if (feedback && feedback.classList.contains(
                                        'invalid-feedback')) {
                                    feedback.textContent = data.errors[field][0];
                                    feedback.style.display = 'block';
                                }
                            }
                        });
                        return;
                    }

                    if (!res.ok || !data.success) {
                        Swal.fire('Error', 'No se pudo crear el servidor', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addServidorModal')).hide();

                    window.servidoresDataTables.activos.row.add([
                        data.servidor.id,
                        `<div>
                            <strong>${data.servidor.nombre}</strong>
                            ${data.servidor.descripcion 
                                ? `<div class="text-muted small mt-1">${data.servidor.descripcion.substring(0,60)}</div>` 
                                : ''
                            }
                        </div>`,
                        `<code>${data.servidor.ip_interna}</code>`,
                        data.servidor.ip_externa ?
                        `<code class="text-success">${data.servidor.ip_externa}</code>` :
                        '<span class="badge bg-soft-secondary text-secondary">No asignada</span>',
                        data.servidor.mac_address ?
                        `<code class="text-muted">${data.servidor.mac_address}</code>` :
                        '<span class="text-muted">—</span>',
                        getSistemaOperativoBadge(data.servidor.sistema_operativo),
                        getTipoBadge(data.servidor.tipo_servidor),
                        getEstadoBadge(data.servidor.estado),
                        accionesActivos(data.servidor.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Servidor creado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear servidor:', error);
                    Swal.fire('Error', 'No se pudo crear el servidor', 'error');
                }
            });

            /* ================= EDITAR SERVIDOR ================= */
            document.getElementById('editServidorForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editServidorId').value;

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/servidores/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    const data = await res.json();

                    if (res.status === 422) {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');

                                    // 🔥 BUSCAR EL .invalid-feedback CORRECTAMENTE
                                    let feedback = null;

                                    const inputGroup = input.closest('.input-group');
                                    if (inputGroup) {
                                        feedback = inputGroup.nextElementSibling;
                                    } else {
                                        feedback = input.nextElementSibling;
                                    }

                                    if (feedback && feedback.classList.contains(
                                            'invalid-feedback')) {
                                        feedback.textContent = data.errors[field][0];
                                        feedback.style.display = 'block';
                                    }
                                }
                            });
                        } else if (data.message) {
                            // Error de versiones activas
                            Swal.fire({
                                icon: 'error',
                                title: 'No se puede inactivar',
                                text: data.message,
                                confirmButtonColor: '#6366f1'
                            });
                        }
                        return;
                    }

                    if (!res.ok || !data.success) {
                        Swal.fire('Error', 'No se pudo actualizar', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('editServidorModal')).hide();

                    window.servidoresDataTables.activos.row(`#servidor-${id}`).data([
                        data.servidor.id,
                        `<div>
                            <strong>${data.servidor.nombre}</strong>
                            ${data.servidor.descripcion 
                                ? `<div class="text-muted small mt-1">${data.servidor.descripcion.substring(0,60)}</div>` 
                                : ''
                            }
                        </div>`,
                        `<code>${data.servidor.ip_interna}</code>`,
                        data.servidor.ip_externa ?
                        `<code class="text-success">${data.servidor.ip_externa}</code>` :
                        '<span class="badge bg-soft-secondary text-secondary">No asignada</span>',
                        data.servidor.mac_address ?
                        `<code class="text-muted">${data.servidor.mac_address}</code>` :
                        '<span class="text-muted">—</span>',
                        getSistemaOperativoBadge(data.servidor.sistema_operativo),
                        getTipoBadge(data.servidor.tipo_servidor),
                        getEstadoBadge(data.servidor.estado),
                        accionesActivos(id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Servidor actualizado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar servidor:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-servidor-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/servidores/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editServidorId').value = data.servidor.id;
                        document.getElementById('editNombre').value = data.servidor.nombre;
                        document.getElementById('editIpInterna').value = data.servidor.ip_interna;
                        document.getElementById('editIpExterna').value = data.servidor.ip_externa || '';
                        document.getElementById('editMacAddress').value = data.servidor.mac_address ||
                            '';

                        document.getElementById('editSistemaOperativoId').value = data.servidor
                            .sistema_operativo_id;

                        document.getElementById('editDescripcion').value = data.servidor.descripcion ??
                            '';

                        // Tipo servidor
                        if (data.servidor.tipo_servidor === 'físico') {
                            document.getElementById('editTipoFisico').checked = true;
                        } else {
                            document.getElementById('editTipoVirtual').checked = true;
                        }

                        // ✅ VALIDAR SI TIENE VERSIONES ACTIVAS
                        const tieneVersionesActivas = data.tiene_versiones_activas;
                        document.getElementById('editTieneVersionesActivas').value =
                            tieneVersionesActivas ? '1' : '0';

                        const alertVersiones = document.getElementById('alertVersionesActivas');
                        const estadoInactivoRadio = document.getElementById('editEstadoInactivo');
                        const estadoInactivoLabel = document.querySelector(
                            'label[for="editEstadoInactivo"]');

                        if (tieneVersionesActivas) {
                            // Mostrar alerta
                            alertVersiones.style.display = 'block';
                            alertVersiones.classList.add('show');

                            // Deshabilitar opción "Inactivo"
                            estadoInactivoRadio.disabled = true;
                            estadoInactivoLabel.classList.add('disabled');
                            estadoInactivoLabel.style.opacity = '0.5';
                            estadoInactivoLabel.style.cursor = 'not-allowed';

                            // Forzar estado activo
                            document.getElementById('editEstadoActivo').checked = true;
                        } else {
                            // Ocultar alerta
                            alertVersiones.style.display = 'none';
                            alertVersiones.classList.remove('show');

                            // Habilitar opción "Inactivo"
                            estadoInactivoRadio.disabled = false;
                            estadoInactivoLabel.classList.remove('disabled');
                            estadoInactivoLabel.style.opacity = '1';
                            estadoInactivoLabel.style.cursor = 'pointer';

                            // Estado
                            if (data.servidor.estado === 'activo') {
                                document.getElementById('editEstadoActivo').checked = true;
                            } else {
                                document.getElementById('editEstadoInactivo').checked = true;
                            }
                        }

                        new bootstrap.Modal(document.getElementById('editServidorModal')).show();

                    } catch (error) {
                        console.error('Error al cargar servidor:', error);
                        Swal.fire('Error', 'No se pudo cargar el servidor', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-servidor-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Servidor?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/servidores/${id}`, {
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

                        const dtA = window.servidoresDataTables.activos;
                        const dtP = window.servidoresDataTables.papelera;

                        const rowData = dtA.row(`#servidor-${id}`).data();

                        dtA.row(`#servidor-${id}`).remove().draw(false);

                        dtP.row.add([
                            rowData[0], // ID
                            rowData[1], // Nombre
                            rowData[2], // IP Interna
                            rowData[5], // Sistema Operativo
                            formatearFecha(new Date()),
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Servidor enviado a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar servidor:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-servidor-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Servidor?',
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
                        const res = await fetch(`/admin/servidores/${id}/restore`, {
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

                        const dtA = window.servidoresDataTables.activos;
                        const dtP = window.servidoresDataTables.papelera;

                        dtP.row(`#servidor-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.servidor.id,
                            `<div>
                                <strong>${data.servidor.nombre}</strong>
                                ${data.servidor.descripcion 
                                    ? `<div class="text-muted small mt-1">${data.servidor.descripcion.substring(0,60)}</div>` 
                                    : ''
                                }
                            </div>`,
                            `<code>${data.servidor.ip_interna}</code>`,
                            data.servidor.ip_externa ?
                            `<code class="text-success">${data.servidor.ip_externa}</code>` :
                            '<span class="badge bg-soft-secondary text-secondary">No asignada</span>',
                            data.servidor.mac_address ?
                            `<code class="text-muted">${data.servidor.mac_address}</code>` :
                            '<span class="text-muted">—</span>',
                            getSistemaOperativoBadge(data.servidor.sistema_operativo),
                            getTipoBadge(data.servidor.tipo_servidor),
                            getEstadoBadge(data.servidor.estado),
                            accionesActivos(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Servidor restaurado correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar servidor:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll(
                '#addServidorForm input, #addServidorForm select, #editServidorForm input, #editServidorForm select'
            ).forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');

                    // Ocultar mensaje de error también
                    const inputGroup = this.closest('.input-group');
                    let feedback = null;

                    if (inputGroup) {
                        feedback = inputGroup.nextElementSibling;
                    } else {
                        feedback = this.nextElementSibling;
                    }

                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'none';
                    }
                });
                input.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                });
            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addServidorModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addServidorForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
            });

            document.getElementById('editServidorModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editServidorForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

                // Limpiar alerta
                document.getElementById('alertVersionesActivas').style.display = 'none';
                document.getElementById('alertVersionesActivas').classList.remove('show');
            });

        });
    </script>
@endsection
