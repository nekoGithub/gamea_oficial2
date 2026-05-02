@extends('layouts.vertical', ['title' => 'Certificados SSL'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Certificados SSL', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0">Certificados SSL</h4>
                            @can('admin.ssls.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSslModal" href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar SSL
                                </a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-activos">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Emisor</th>
                                            <th>Archivo</th>
                                            <th>Fecha Emisión</th>
                                            <th>Fecha Expiración</th>
                                            <th>Días Restantes</th>
                                            <th>Estado</th>
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
                                                        placeholder="Emisor" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Archivo" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="F. Emisión" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="F. Expiración" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="Días" type="text" />
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
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-activos">
                                        @forelse ($ssls as $ssl)
                                            <tr data-id="{{ $ssl->id }}">
                                                <td>{{ $ssl->id }}</td>
                                                <td>{{ $ssl->emisor }}</td>
                                                <td>
                                                    @if ($ssl->archivo_ssl)
                                                        <a href="{{ $ssl->archivo_ssl_url }}" target="_blank"
                                                            class="text-primary">
                                                            <i class="ti ti-file-certificate me-1"></i>
                                                            Ver certificado
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $ssl->fecha_emision->format('d M, Y') }}</td>
                                                <td>{{ $ssl->fecha_expiracion->format('d M, Y') }}</td>
                                                <td>
                                                    @php
                                                        $dias = $ssl->dias_restantes;
                                                        $badgeClass =
                                                            $dias < 0
                                                                ? 'text-danger'
                                                                : ($dias <= 30
                                                                    ? 'text-warning'
                                                                    : 'text-success');
                                                    @endphp
                                                    <span class="fw-semibold {{ $badgeClass }}">
                                                        {{ $dias }} días
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $estadoClasses = [
                                                            'valido' => 'bg-success',
                                                            'proximo_vencer' => 'bg-warning',
                                                            'vencido' => 'bg-danger',
                                                        ];
                                                        $estadoTextos = [
                                                            'valido' => 'Válido',
                                                            'proximo_vencer' => 'Próximo a vencer',
                                                            'vencido' => 'Vencido',
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $estadoClasses[$ssl->estado] }}">
                                                        {{ $estadoTextos[$ssl->estado] }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.ssls.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-ssl-btn"
                                                                data-id="{{ $ssl->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.ssls.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-ssl-btn"
                                                                data-id="{{ $ssl->id }}">
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
                            <h4 class="card-title mb-0">Certificados SSL Eliminados</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100"
                                    id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Emisor</th>
                                            <th>Fecha Expiración</th>
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
                                                        placeholder="Emisor" type="text" />
                                                </div>
                                            </th>
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input
                                                        class="form-control form-control-sm bg-light-subtle border-light"
                                                        placeholder="F. Expiración" type="text" />
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
                                        @forelse ($sslsEliminados as $ssl)
                                            <tr data-id="{{ $ssl->id }}">
                                                <td>{{ $ssl->id }}</td>
                                                <td>{{ $ssl->emisor }}</td>
                                                <td>{{ $ssl->fecha_expiracion->format('d M, Y') }}</td>
                                                <td>{{ $ssl->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.ssls.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-ssl-btn"
                                                                data-id="{{ $ssl->id }}">
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
    @include('admin.ssls.modals.add')
    @include('admin.ssls.modals.edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-ssls.js'])

    <script>
        const canEdit = @json(auth()->user()?->can('admin.ssls.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.ssls.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.ssls.restore') ?? false);
    </script>

    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

        /**
         * Normaliza cualquier formato de fecha a YYYY-MM-DD
         * Acepta: "2024-12-22", "2024-12-22T00:00:00.000000Z", Date object
         */
        function normalizarFecha(fecha) {
            if (!fecha) return null;

            // Si es un objeto Date
            if (fecha instanceof Date) {
                return fecha.toISOString().substring(0, 10);
            }

            // Si es string, extraer solo YYYY-MM-DD
            if (typeof fecha === 'string') {
                return fecha.substring(0, 10);
            }

            return null;
        }

        /**
         * Formatea fecha para mostrar (DD/MM/YYYY o DD Mes, YYYY)
         */
        function formatearFechaDisplay(fecha) {
            const fechaNorm = normalizarFecha(fecha);
            if (!fechaNorm) return '—';

            const [y, m, d] = fechaNorm.split('-');
            const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

            return `${d} ${meses[parseInt(m) - 1]}, ${y}`;
        }

        /**
         * Calcula días restantes hasta la fecha de expiración
         */
        function calcularDiasRestantes(fechaExpiracion) {
            const fechaNorm = normalizarFecha(fechaExpiracion);

            if (!fechaNorm || !/^\d{4}-\d{2}-\d{2}$/.test(fechaNorm)) {
                return '<span class="text-muted">—</span>';
            }

            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            const [y, m, d] = fechaNorm.split('-');
            const expiracion = new Date(parseInt(y), parseInt(m) - 1, parseInt(d));

            if (isNaN(expiracion.getTime())) {
                return '<span class="text-muted">—</span>';
            }

            const diff = Math.floor((expiracion - hoy) / 86400000);

            const badgeClass = diff < 0 ? 'text-danger' : (diff <= 30 ? 'text-warning' : 'text-success');

            return `<span class="fw-semibold ${badgeClass}">${diff} días</span>`;
        }

        /**
         * Retorna badge HTML según el estado
         */
        function getEstadoBadge(estado) {
            const badges = {
                'valido': '<span class="badge bg-success">Válido</span>',
                'proximo_vencer': '<span class="badge bg-warning">Próximo a vencer</span>',
                'vencido': '<span class="badge bg-danger">Vencido</span>'
            };
            return badges[estado] || '<span class="badge bg-secondary">Desconocido</span>';
        }

        /**
         * Retorna HTML para mostrar el archivo SSL
         */
        function getArchivoHtml(ssl) {
            if (!ssl.archivo_ssl) {
                return '<span class="text-muted">—</span>';
            }

            return `<a href="/storage/${ssl.archivo_ssl}" target="_blank" class="text-primary">
                <i class="ti ti-file-certificate me-1"></i>Ver certificado
            </a>`;
        }

        /**
         * HTML de acciones para tabla activos
         */
        function accionesActivos(id) {
            let btns = '<div class="d-flex justify-content-center gap-1">';
            if (canEdit) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-ssl-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-ssl-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-ssl-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        document.querySelectorAll('#addSslForm input, #editSslForm input').forEach(input => {
            input.addEventListener('input', () => {
                input.classList.remove('is-invalid');
            });
        });


        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= AGREGAR SSL ================= */
            document.getElementById('addSslForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // 🔥 VALIDACIÓN DE TAMAÑO DE ARCHIVO
                const archivoInput = form.querySelector('[name="archivo_ssl"]');
                if (archivoInput && archivoInput.files.length > 0) {
                    const archivo = archivoInput.files[0];
                    const maxSize = 2 * 1024 * 1024; // 2 MB en bytes

                    if (archivo.size > maxSize) {
                        archivoInput.classList.add('is-invalid');
                        const feedback = archivoInput.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = 'El archivo no debe ser mayor a 2 MB.';
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'Archivo muy grande',
                            text: `El archivo pesa ${(archivo.size / 1024 / 1024).toFixed(2)} MB. El tamaño máximo permitido es 2 MB.`,
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }
                }

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
                        Swal.fire('Error', 'No se pudo crear el SSL', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addSslModal')).hide();

                    window.sslsDataTables.activos.row.add([
                        data.ssl.id,
                        data.ssl.emisor,
                        getArchivoHtml(data.ssl),
                        formatearFechaDisplay(data.ssl.fecha_emision),
                        formatearFechaDisplay(data.ssl.fecha_expiracion),
                        calcularDiasRestantes(data.ssl.fecha_expiracion),
                        getEstadoBadge(data.ssl.estado),
                        accionesActivos(data.ssl.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'SSL creado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear SSL:', error);
                    Swal.fire('Error', 'No se pudo crear el SSL', 'error');
                }
            });

            /* ================= EDITAR SSL ================= */
            document.getElementById('editSslForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editSslId').value;
                const formData = new FormData(form);

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // 🔥 VALIDACIÓN DE TAMAÑO DE ARCHIVO EN EDICIÓN
                const archivoInput = form.querySelector('[name="archivo_ssl"]');
                if (archivoInput && archivoInput.files.length > 0) {
                    const archivo = archivoInput.files[0];
                    const maxSize = 2 * 1024 * 1024; // 2 MB en bytes

                    if (archivo.size > maxSize) {
                        archivoInput.classList.add('is-invalid');
                        const feedback = archivoInput.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = 'El archivo no debe ser mayor a 2 MB.';
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'Archivo muy grande',
                            text: `El archivo pesa ${(archivo.size / 1024 / 1024).toFixed(2)} MB. El tamaño máximo permitido es 2 MB.`,
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }
                }

                try {
                    const res = await fetch(`/admin/ssls/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    // 🔥 VALIDACIONES
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

                    bootstrap.Modal.getInstance(document.getElementById('editSslModal')).hide();

                    window.sslsDataTables.activos.row(`#ssl-${id}`).data([
                        data.ssl.id,
                        data.ssl.emisor,
                        getArchivoHtml(data.ssl),
                        formatearFechaDisplay(data.ssl.fecha_emision),
                        formatearFechaDisplay(data.ssl.fecha_expiracion),
                        calcularDiasRestantes(data.ssl.fecha_expiracion),
                        getEstadoBadge(data.ssl.estado),
                        accionesActivos(id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'SSL actualizado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-ssl-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/ssls/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editSslId').value = data.ssl.id;
                        document.getElementById('editEmisor').value = data.ssl.emisor;

                        // Normalizar fechas para input type="date"
                        document.getElementById('editFechaEmision').value = normalizarFecha(data.ssl
                            .fecha_emision);
                        document.getElementById('editFechaExpiracion').value = normalizarFecha(data.ssl
                            .fecha_expiracion);

                        // Mostrar archivo actual
                        const currentFileDiv = document.getElementById('currentSslFile');
                        if (data.ssl.archivo_ssl) {
                            currentFileDiv.innerHTML = `
                                <small class="text-muted">
                                    Archivo actual:
                                    <a href="/storage/${data.ssl.archivo_ssl}" target="_blank">
                                        ${data.ssl.archivo_ssl.split('/').pop()}
                                    </a>
                                </small>`;
                        } else {
                            currentFileDiv.innerHTML = '';
                        }

                        new bootstrap.Modal(document.getElementById('editSslModal')).show();

                    } catch (error) {
                        console.error('Error al cargar SSL:', error);
                        Swal.fire('Error', 'No se pudo cargar el SSL', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-ssl-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar SSL?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/ssls/${id}`, {
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

                        const dtA = window.sslsDataTables.activos;
                        const dtP = window.sslsDataTables.papelera;

                        // Obtener datos de la fila antes de eliminar
                        const rowData = dtA.row(`#ssl-${id}`).data();

                        // Eliminar de activos
                        dtA.row(`#ssl-${id}`).remove().draw(false);

                        // Agregar a papelera
                        dtP.row.add([
                            rowData[0], // ID
                            rowData[1], // Emisor
                            rowData[4], // Fecha Expiración
                            formatearFechaDisplay(new Date()), // Fecha eliminado
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'SSL enviado a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar SSL:', error);
                        Swal.fire('Error', 'No se pudo eliminar el SSL', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-ssl-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    // 🔥 PREGUNTA DE CONFIRMACIÓN
                    const confirm = await Swal.fire({
                        title: '¿Restaurar SSL?',
                        text: 'El certificado volverá a la lista activa',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/ssls/${id}/restore`, {
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

                        const dtA = window.sslsDataTables.activos;
                        const dtP = window.sslsDataTables.papelera;

                        // Eliminar de papelera
                        dtP.row(`#ssl-${id}`).remove().draw(false);

                        // 🔥 CLAVE: Agregar a activos con datos FRESCOS del servidor
                        dtA.row.add([
                            data.ssl.id,
                            data.ssl.emisor,
                            getArchivoHtml(data.ssl),
                            formatearFechaDisplay(data.ssl.fecha_emision),
                            formatearFechaDisplay(data.ssl.fecha_expiracion),
                            calcularDiasRestantes(data.ssl.fecha_expiracion), // 🔥 Recalcular
                            getEstadoBadge(data.ssl.estado),
                            accionesActivos(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'SSL restaurado correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar SSL:', error);
                        Swal.fire('Error', 'No se pudo restaurar el SSL', 'error');
                    }

                    return;
                }

            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addSslModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addSslForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            document.getElementById('editSslModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editSslForm');
                if (!form) return;

                form.reset();
                document.getElementById('currentSslFile').innerHTML = '';
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

        });
    </script>
@endsection
