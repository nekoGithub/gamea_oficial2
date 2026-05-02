@extends('layouts.vertical', ['title' => 'Tecnologías'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Tecnologías', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0">Tecnologías</h4>
                            @can('admin.tecnologias.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTecnologiaModal"
                                    href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar Tecnología
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
                                            <th>Lanzamiento</th>
                                            <th>Fin Soporte</th>
                                            <th>Vigencia</th>
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
                                                        placeholder="Versión" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Lanzamiento" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Fin Soporte" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Vigencia" type="text">
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
                                        @forelse ($tecnologias as $tec)
                                            <tr data-id="{{ $tec->id }}">
                                                <td>{{ $tec->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-code fs-4 text-info me-2"></i>
                                                        <strong>{{ $tec->nombre }}</strong>
                                                    </div>
                                                </td>
                                                <td><code>{{ $tec->version }}</code></td>
                                                <td>{{ $tec->fecha_lanzamiento ? $tec->fecha_lanzamiento->format('d M, Y') : '—' }}
                                                </td>
                                                <td>{{ $tec->fecha_fin_soporte ? $tec->fecha_fin_soporte->format('d M, Y') : '—' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $vigenciaClasses = [
                                                            'vigente' => 'bg-success',
                                                            'desactualizada' => 'bg-warning',
                                                            'obsoleta' => 'bg-danger',
                                                        ];
                                                        $vigenciaTextos = [
                                                            'vigente' => 'Vigente',
                                                            'desactualizada' => 'Desactualizada',
                                                            'obsoleta' => 'Obsoleta',
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $vigenciaClasses[$tec->vigencia] }}">
                                                        {{ $vigenciaTextos[$tec->vigencia] }}
                                                    </span>
                                                    @if ($tec->dias_restante_soporte !== null && $tec->dias_restante_soporte >= 0)
                                                        <br><small class="text-muted">{{ $tec->dias_restante_soporte }}
                                                            días</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $tipoClasses = [
                                                            'backend' => 'bg-soft-primary text-primary',
                                                            'frontend' => 'bg-soft-success text-success',
                                                            'otros/librerias' => 'bg-soft-warning text-warning',
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $tipoClasses[$tec->tipo] }}">
                                                        {{ ucfirst($tec->tipo) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $tec->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($tec->estado) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @if ($tec->url_documentacion)
                                                            <a href="#"
                                                                class="btn btn-info btn-icon btn-sm rounded-circle scrape-tec-btn"
                                                                data-id="{{ $tec->id }}" title="Scraping asistido">
                                                                <i class="ti ti-robot fs-lg"></i>
                                                            </a>
                                                        @endif
                                                        @can('admin.tecnologias.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-tec-btn"
                                                                data-id="{{ $tec->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.tecnologias.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-tec-btn"
                                                                data-id="{{ $tec->id }}">
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
                            <h4 class="card-title mb-0">Tecnologías Eliminadas</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100"
                                    id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Versión</th>
                                            <th>Tipo</th>
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
                                                        placeholder="Tipo" type="text">
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
                                        @forelse ($tecnologiasEliminadas as $tec)
                                            <tr data-id="{{ $tec->id }}">
                                                <td>{{ $tec->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-code fs-4 text-muted me-2"></i>
                                                        <strong>{{ $tec->nombre }}</strong>
                                                    </div>
                                                </td>
                                                <td><code>{{ $tec->version }}</code></td>
                                                <td>
                                                    @php
                                                        $tipoClasses = [
                                                            'backend' => 'bg-soft-primary text-primary',
                                                            'frontend' => 'bg-soft-success text-success',
                                                            'otros/librerias' => 'bg-soft-warning text-warning',
                                                        ];
                                                        $tipoTextos = [
                                                            'backend' => 'Backend',
                                                            'frontend' => 'Frontend',
                                                            'otros/librerias' => 'otros/librerias',
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $tipoClasses[$tec->tipo] }}">
                                                        {{ $tipoTextos[$tec->tipo] }}
                                                    </span>
                                                </td>
                                                <td>{{ $tec->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.tecnologias.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-tec-btn"
                                                                data-id="{{ $tec->id }}">
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

    {{-- Modal Scraping Resultados --}}
    <div class="modal fade" id="scrapingResultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-robot me-2"></i>
                        Resultados del Scraping
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="scrapingResultContent">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="applyScrapingData" style="display:none;">
                        <i class="ti ti-check me-1"></i>
                        Aplicar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- MODALES --}}
    @include('admin.tecnologias.modals.add')
    @include('admin.tecnologias.modals.edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-tecnologias.js'])

    <script>
        const canEdit = @json(auth()->user()?->can('admin.tecnologias.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.tecnologias.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.tecnologias.restore') ?? false);
    </script>

    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

        function getNombreHtml(nombre) {
            return `
                <div class="d-flex align-items-center">
                    <i class="ti ti-code fs-4 text-info me-2"></i>
                    <strong>${nombre}</strong>
                </div>`;
        }

        function getDocsHtml(url) {
            return url ?
                `<a href="${url}" target="_blank" class="text-primary">
                     <i class="ti ti-external-link me-1"></i>Ver docs
                   </a>` :
                '<span class="text-muted">—</span>';
        }

        function getTipoBadge(tipo) {
            const badges = {
                'backend': '<span class="badge bg-soft-primary text-primary">Backend</span>',
                'frontend': '<span class="badge bg-soft-success text-success">Frontend</span>',
                'otros/librerias': '<span class="badge bg-soft-warning text-warning">otros/librerias</span>'
            };
            return badges[tipo] || '';
        }

        function getEstadoBadge(estado) {
            return estado === 'activo' ?
                '<span class="badge bg-success">Activo</span>' :
                '<span class="badge bg-secondary">Inactivo</span>';
        }

        function formatearFecha(fecha) {
            if (!fecha) return '—';

            // fecha esperada: YYYY-MM-DD
            const [y, m, d] = fecha.substring(0, 10).split('-');

            const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun',
                'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
            ];

            return `${parseInt(d)} ${meses[parseInt(m) - 1]}, ${y}`;
        }


        function truncarTexto(texto, limite = 40) {
            if (!texto || texto === '—') return '—';
            return texto.length > limite ? texto.substring(0, limite) + '...' : texto;
        }

        function accionesActivos(id, tieneUrl = false) {
            let btns = '<div class="d-flex justify-content-center gap-1">';
            if (tieneUrl) btns +=
                `<a href="#" class="btn btn-info btn-icon btn-sm rounded-circle scrape-tec-btn" data-id="${id}" title="Scraping asistido"><i class="ti ti-robot fs-lg"></i></a>`;
            if (canEdit) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-tec-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-tec-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-tec-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function getVigenciaBadge(vigencia, diasRestantes = null) {
            const badges = {
                'vigente': '<span class="badge bg-success">Vigente</span>',
                'desactualizada': '<span class="badge bg-warning">Desactualizada</span>',
                'obsoleta': '<span class="badge bg-danger">Obsoleta</span>'
            };

            let html = badges[vigencia] || '';

            if (diasRestantes !== null && diasRestantes >= 0) {
                html += `<br><small class="text-muted">${diasRestantes} días</small>`;
            }

            return html;
        }

        function calcularVigencia(fechaFinSoporte) {
            if (!fechaFinSoporte) return 'vigente';

            const hoy = new Date();
            const fin = new Date(fechaFinSoporte);
            const meses = (fin - hoy) / (1000 * 60 * 60 * 24 * 30);

            if (fin < hoy) return 'obsoleta';
            if (meses <= 6) return 'desactualizada';
            return 'vigente';
        }

        function calcularDiasRestantes(fechaFin) {
            if (!fechaFin) return null;

            const hoy = new Date();
            const fin = new Date(fechaFin + 'T00:00:00');

            // Normalizar ambas fechas a medianoche local
            hoy.setHours(0, 0, 0, 0);

            const diffMs = fin - hoy;
            const diffDias = Math.ceil(diffMs / (1000 * 60 * 60 * 24));

            return diffDias;
        }


        function normalizarFecha(fecha) {
            if (!fecha) return '';

            // Si ya viene en formato YYYY-MM-DD
            if (typeof fecha === 'string' && fecha.length >= 10) {
                return fecha.substring(0, 10);
            }

            // Si por alguna razón llega un objeto Date
            if (Object.prototype.toString.call(fecha) === '[object Date]') {
                if (isNaN(fecha.getTime())) return '';
                const y = fecha.getFullYear();
                const m = String(fecha.getMonth() + 1).padStart(2, '0');
                const d = String(fecha.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            return '';
        }


        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            const maxFecha = `${new Date().getFullYear() + 5}-12-31`;
            ['addFechaLanzamiento', 'addFechaFinSoporte',
                'editFechaLanzamiento', 'editFechaFinSoporte'
            ].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.setAttribute('min', '2015-01-01');
                    el.setAttribute('max', maxFecha);
                }
            });

            /* ================= AGREGAR TECNOLOGÍA ================= */
            document.getElementById('addTecnologiaForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();



                document.getElementById('addFechaLanzamiento')?.addEventListener('change', function() {
                    document.getElementById('addFechaFinSoporte').min = this.value ||
                        '2015-01-01';
                });
                document.getElementById('editFechaLanzamiento')?.addEventListener('change', function() {
                    document.getElementById('editFechaFinSoporte').min = this.value ||
                        '2015-01-01';
                });

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
                        Swal.fire('Error', 'No se pudo crear la tecnología', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addTecnologiaModal')).hide();

                    window.tecnologiasDataTables.activos.row.add([
                        data.tecnologia.id,
                        getNombreHtml(data.tecnologia.nombre),
                        `<code>${data.tecnologia.version}</code>`,
                        data.tecnologia.fecha_lanzamiento ? formatearFecha(data.tecnologia
                            .fecha_lanzamiento) : '—',
                        data.tecnologia.fecha_fin_soporte ? formatearFecha(data.tecnologia
                            .fecha_fin_soporte) : '—',
                        getVigenciaBadge(
                            calcularVigencia(data.tecnologia.fecha_fin_soporte),
                            calcularDiasRestantes(data.tecnologia.fecha_fin_soporte)
                        ),
                        getTipoBadge(data.tecnologia.tipo),
                        getEstadoBadge(data.tecnologia.estado),
                        accionesActivos(data.tecnologia.id, data.tecnologia.url_documentacion ?
                            true : false)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tecnología creada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear tecnología:', error);
                    Swal.fire('Error', 'No se pudo crear la tecnología', 'error');
                }
            });

            /* ================= EDITAR TECNOLOGÍA ================= */
            document.getElementById('editTecnologiaForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editTecnologiaId').value;

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/tecnologias/${id}`, {
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

                    bootstrap.Modal.getInstance(document.getElementById('editTecnologiaModal')).hide();

                    window.tecnologiasDataTables.activos.row(`#tecnologia-${id}`).data([
                        data.tecnologia.id,
                        getNombreHtml(data.tecnologia.nombre),
                        `<code>${data.tecnologia.version}</code>`,
                        data.tecnologia.fecha_lanzamiento ? formatearFecha(data.tecnologia
                            .fecha_lanzamiento) : '—',
                        data.tecnologia.fecha_fin_soporte ? formatearFecha(data.tecnologia
                            .fecha_fin_soporte) : '—',
                        getVigenciaBadge(
                            data.tecnologia.vigencia,
                            data.tecnologia.dias_restante_soporte
                        ),
                        getTipoBadge(data.tecnologia.tipo),
                        getEstadoBadge(data.tecnologia.estado),
                        accionesActivos(data.tecnologia.id, data.tecnologia.url_documentacion ?
                            true : false)
                    ]).draw(false);


                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tecnología actualizada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar tecnología:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-tec-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/tecnologias/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editTecnologiaId').value = data.tecnologia.id;
                        document.getElementById('editNombre').value = data.tecnologia.nombre;
                        document.getElementById('editVersion').value = data.tecnologia.version;
                        document.getElementById('editDescripcion').value = data.tecnologia
                            .descripcion || '';
                        document.getElementById('editUrlDocumentacion').value = data.tecnologia
                            .url_documentacion || '';

                        document.getElementById('editFechaLanzamiento').value =
                            normalizarFecha(data.tecnologia.fecha_lanzamiento);

                        const fechaLanz = normalizarFecha(data.tecnologia.fecha_lanzamiento);
                        if (fechaLanz) {
                            document.getElementById('editFechaFinSoporte').setAttribute('min',
                                fechaLanz);
                        }

                        document.getElementById('editFechaFinSoporte').value =
                            normalizarFecha(data.tecnologia.fecha_fin_soporte);
                        // Tipo
                        if (data.tecnologia.tipo === 'backend') {
                            document.getElementById('editTipoBackend').checked = true;
                        } else if (data.tecnologia.tipo === 'frontend') {
                            document.getElementById('editTipoFrontend').checked = true;
                        } else {
                            document.getElementById('editTipoOtros').checked = true;
                        }

                        // Estado
                        if (data.tecnologia.estado === 'activo') {
                            document.getElementById('editEstadoActivo').checked = true;
                        } else {
                            document.getElementById('editEstadoInactivo').checked = true;
                        }

                        new bootstrap.Modal(document.getElementById('editTecnologiaModal')).show();

                    } catch (error) {
                        console.error('Error al cargar tecnología:', error);
                        Swal.fire('Error', 'No se pudo cargar la tecnología', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-tec-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Tecnología?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/tecnologias/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        if (!res.ok) {
                            throw new Error('Error HTTP');
                        }

                        const data = await res.json();

                        if (!data.success) {
                            Swal.fire('Error', 'No se pudo eliminar', 'error');
                            return;
                        }

                        const dtA = window.tecnologiasDataTables.activos;
                        const dtP = window.tecnologiasDataTables.papelera;

                        // obtener la fila REAL desde el botón
                        const row = deleteBtn.closest('tr');

                        // si es child row, subir al padre
                        const realRow = row.classList.contains('child') ?
                            row.previousSibling :
                            row;

                        const rowData = dtA.row(realRow).data();

                        // eliminar de activos
                        dtA.row(realRow).remove().draw(false);

                        // agregar a papelera
                        dtP.row.add([
                            rowData[0], // ID
                            rowData[1], // Nombre
                            rowData[2], // Versión
                            rowData[5], // Estado / Vigencia (ajusta si cambia)
                            formatearFecha(new Date().toISOString()),
                            accionesPapelera(id)
                        ]).draw(false);


                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Tecnología enviada a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar tecnología:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-tec-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Tecnología?',
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
                        const res = await fetch(`/admin/tecnologias/${id}/restore`, {
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

                        const dtA = window.tecnologiasDataTables.activos;
                        const dtP = window.tecnologiasDataTables.papelera;

                        dtP.row(`#tecnologia-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.tecnologia.id,
                            getNombreHtml(data.tecnologia.nombre),
                            `<code>${data.tecnologia.version}</code>`,
                            data.tecnologia.fecha_lanzamiento ?
                            formatearFecha(data.tecnologia.fecha_lanzamiento) :
                            '—',
                            data.tecnologia.fecha_fin_soporte ?
                            formatearFecha(data.tecnologia.fecha_fin_soporte) :
                            '—',
                            getVigenciaBadge(
                                data.tecnologia.vigencia,
                                data.tecnologia.dias_restante_soporte
                            ),

                            getTipoBadge(data.tecnologia.tipo),
                            getEstadoBadge(data.tecnologia.estado),
                            accionesActivos(data.tecnologia.id, data.tecnologia
                                .url_documentacion ?
                                true : false)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Tecnología restaurada correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar tecnología:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

                /* ===== SCRAPING ASISTIDO ===== */
                const scrapeBtn = e.target.closest('.scrape-tec-btn');
                if (scrapeBtn) {
                    e.preventDefault();
                    const id = scrapeBtn.dataset.id;

                    // Mostrar loading
                    Swal.fire({
                        title: 'Analizando página...',
                        html: 'Buscando información de fechas<br><small class="text-muted">Esto puede tardar unos segundos...</small>',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/admin/tecnologias/${id}/scrape`)
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();

                            if (!data.success) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sin resultados',
                                    html: `<p>${data.message}</p><small class="text-muted">Verifica que la URL tenga información de fechas estructurada.</small>`
                                });
                                return;
                            }

                            // Mostrar resultados en modal
                            const content = document.getElementById('scrapingResultContent');

                            const tieneAlgunDato = data.data.fecha_lanzamiento || data.data
                                .fecha_fin_soporte;

                            content.innerHTML = `
                            ${tieneAlgunDato ? `
                                                                                            <div class="alert alert-success">
                                                                                                <i class="ti ti-check-circle me-2"></i>
                                                                                                <strong>Datos encontrados exitosamente</strong>
                                                                                            </div>
                                                                                        ` : `
                                                                                            <div class="alert alert-warning">
                                                                                                <i class="ti ti-alert-triangle me-2"></i>
                                                                                                <strong>Datos parciales encontrados</strong>
                                                                                            </div>
                                                                                        `}
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ti ti-calendar-event me-1"></i>
                                    Fecha de Lanzamiento
                                </label>
                                <input type="date" class="form-control ${data.data.fecha_lanzamiento ? 'border-success' : ''}" 
                                    id="scrapedFechaLanzamiento" 
                                    value="${data.data.fecha_lanzamiento || ''}" 
                                    ${data.data.fecha_lanzamiento ? '' : 'disabled'}>
                                ${!data.data.fecha_lanzamiento ? 
                                    '<small class="text-muted"><i class="ti ti-x me-1"></i>No encontrada</small>' : 
                                    '<small class="text-success"><i class="ti ti-check me-1"></i>Encontrada</small>'
                                }
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ti ti-calendar-x me-1"></i>
                                    Fecha Fin de Soporte
                                </label>
                                <input type="date" class="form-control ${data.data.fecha_fin_soporte ? 'border-success' : ''}" 
                                    id="scrapedFechaFinSoporte" 
                                    value="${data.data.fecha_fin_soporte || ''}"
                                    ${data.data.fecha_fin_soporte ? '' : 'disabled'}>
                                ${!data.data.fecha_fin_soporte ? 
                                    '<small class="text-muted"><i class="ti ti-x me-1"></i>No encontrada</small>' : 
                                    '<small class="text-success"><i class="ti ti-check me-1"></i>Encontrada</small>'
                                }
                            </div>
                            
                            <div class="alert alert-info mb-0">
                                <small>
                                    <i class="ti ti-info-circle me-1"></i>
                                    <strong>Fuente:</strong> ${data.data.fuente || 'Desconocida'}
                                </small>
                            </div>
                        `;

                            // Guardar ID para aplicar después
                            document.getElementById('applyScrapingData').dataset.tecnologiaId = id;

                            // Mostrar botón "Aplicar" solo si hay datos
                            if (tieneAlgunDato) {
                                document.getElementById('applyScrapingData').style.display =
                                    'inline-block';
                            } else {
                                document.getElementById('applyScrapingData').style.display = 'none';
                            }

                            new bootstrap.Modal(document.getElementById('scrapingResultModal'))
                                .show();
                        })
                        .catch(error => {
                            Swal.close();
                            console.error('Error en scraping:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo realizar el scraping. Verifica la conexión.',
                                footer: '<small class="text-muted">Revisa la consola para más detalles</small>'
                            });
                        });

                    return;
                }

            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll(
                '#addTecnologiaForm input, #addTecnologiaForm textarea, #editTecnologiaForm input, #editTecnologiaForm textarea'
            ).forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addTecnologiaModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addTecnologiaForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

            document.getElementById('editTecnologiaModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editTecnologiaForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });

        });
    </script>

    <script>
        /* ===== APLICAR DATOS DEL SCRAPING ===== */
        document.getElementById('applyScrapingData')?.addEventListener('click', async function() {
            const id = this.dataset.tecnologiaId;
            const fechaLanzamiento = document.getElementById('scrapedFechaLanzamiento').value;
            const fechaFinSoporte = document.getElementById('scrapedFechaFinSoporte').value;

            // Abrir modal de edición con los datos
            try {
                const res = await fetch(`/admin/tecnologias/${id}/edit`);
                const data = await res.json();

                // Llenar formulario de edición
                document.getElementById('editTecnologiaId').value = data.tecnologia.id;
                document.getElementById('editNombre').value = data.tecnologia.nombre;
                document.getElementById('editVersion').value = data.tecnologia.version;
                document.getElementById('editDescripcion').value = data.tecnologia.descripcion || '';
                document.getElementById('editUrlDocumentacion').value = data.tecnologia.url_documentacion || '';

                // APLICAR FECHAS DEL SCRAPING
                document.getElementById('editFechaLanzamiento').value = fechaLanzamiento || data.tecnologia
                    .fecha_lanzamiento || '';
                document.getElementById('editFechaFinSoporte').value = fechaFinSoporte || data.tecnologia
                    .fecha_fin_soporte || '';

                // Tipo y Estado
                if (data.tecnologia.tipo === 'backend') {
                    document.getElementById('editTipoBackend').checked = true;
                } else if (data.tecnologia.tipo === 'frontend') {
                    document.getElementById('editTipoFrontend').checked = true;
                } else {
                    document.getElementById('editTipoOtros').checked = true;
                }

                if (data.tecnologia.estado === 'activo') {
                    document.getElementById('editEstadoActivo').checked = true;
                } else {
                    document.getElementById('editEstadoInactivo').checked = true;
                }

                // Cerrar modal de scraping
                bootstrap.Modal.getInstance(document.getElementById('scrapingResultModal')).hide();

                // Abrir modal de edición
                new bootstrap.Modal(document.getElementById('editTecnologiaModal')).show();

                // Highlight de los campos modificados
                if (fechaLanzamiento) {
                    document.getElementById('editFechaLanzamiento').classList.add('border-success', 'border-2');
                }
                if (fechaFinSoporte) {
                    document.getElementById('editFechaFinSoporte').classList.add('border-success', 'border-2');
                }

            } catch (error) {
                Swal.fire('Error', 'No se pudo cargar la tecnología', 'error');
            }
        });
    </script>
@endsection
