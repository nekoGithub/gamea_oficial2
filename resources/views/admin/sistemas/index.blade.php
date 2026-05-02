@extends('layouts.vertical', ['title' => 'Sistemas'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .border-purple {
            border-color: #6f42c1 !important;
        }

        .btn-outline-purple {
            color: #6f42c1;
            border-color: #6f42c1;
        }

        .btn-outline-purple:hover {
            color: #fff;
            background-color: #6f42c1;
            border-color: #6f42c1;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        code.text-primary:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Sistemas', 'title' => 'Listado'])

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
                            <h4 class="card-title mb-0 ">Sistemas Registrados</h4>
                            @can('admin.sistemas.store')
                                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSistemaModal"
                                    href="#">
                                    <i class="ti ti-plus me-1"></i> Agregar Sistema
                                </a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-activos">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Sistema</th>
                                            <th>Dominio</th>
                                            <th>Tipo</th>
                                            <th>Unidad Organizacional</th>
                                            <th>SSL</th>
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
                                                        placeholder="Sistema" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Dominio" type="text">
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
                                                        placeholder="Unidad Organizacional" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="SSL" type="text">
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
                                        @forelse ($sistemas as $sistema)
                                            <tr data-id="{{ $sistema->id }}">
                                                <td>{{ $sistema->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-server fs-4 text-primary me-2"></i>
                                                        <div>
                                                            <strong>{{ $sistema->nombre }}</strong>
                                                            @if ($sistema->descripcion)
                                                                <div class="text-muted small mt-1" data-bs-toggle="tooltip"
                                                                    title="{{ $sistema->descripcion }}">
                                                                    {{ Str::limit($sistema->descripcion, 50) }}
                                                                </div>
                                                            @endif
                                                            @if ($sistema->sigla)
                                                                <div>
                                                                    <span
                                                                        class="badge bg-secondary-subtle text-secondary mt-1">
                                                                        {{ $sistema->sigla }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ $sistema->ssl ? 'https' : 'http' }}://{{ $sistema->dominio }}"
                                                        target="_blank" class="text-decoration-none">
                                                        <code class="text-primary cursor-pointer">
                                                            {{ $sistema->dominio }}
                                                            <i class="ti ti-external-link ms-1 fs-xs"></i>
                                                        </code>
                                                    </a>
                                                </td>
                                                <td>
                                                    @foreach ($sistema->tipo as $tipo)
                                                        <span
                                                            class="badge {{ $tipo === 'interno' ? 'bg-info' : 'bg-warning' }} {{ !$loop->last ? 'me-1' : '' }}">
                                                            {{ ucfirst($tipo) }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if ($sistema->unidad)
                                                        <div class="position-relative">
                                                            <button class="btn btn-sm btn-outline-purple ver-unidad-btn"
                                                                data-bs-toggle="modal" data-bs-target="#unidadDetailModal"
                                                                data-unidad-id="{{ $sistema->unidad->id }}"
                                                                data-unidad-nombre="{{ $sistema->unidad->nombre }}">
                                                                <i class="ti ti-building me-1"></i>
                                                                {{ Str::limit($sistema->unidad->nombre, 20) }}
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sistema->ssl)
                                                        @php
                                                            $diasRestantes = $sistema->ssl->dias_restantes;
                                                            $estadoSsl =
                                                                $diasRestantes < 0
                                                                    ? 'vencido'
                                                                    : ($diasRestantes <= 30
                                                                        ? 'por-vencer'
                                                                        : 'valido');
                                                        @endphp
                                                        <button
                                                            class="btn btn-sm {{ $estadoSsl === 'vencido' ? 'btn-outline-danger' : ($estadoSsl === 'por-vencer' ? 'btn-outline-warning' : 'btn-outline-success') }} ver-ssl-btn"
                                                            data-bs-toggle="modal" data-bs-target="#sslDetailModal"
                                                            data-ssl-id="{{ $sistema->ssl->id }}"
                                                            data-ssl-emisor="{{ $sistema->ssl->emisor }}"
                                                            data-ssl-expiracion="{{ $sistema->ssl->fecha_expiracion }}"
                                                            data-ssl-dias="{{ $diasRestantes }}">
                                                            <i class="ti ti-shield-check me-1"></i>
                                                            @if ($estadoSsl === 'vencido')
                                                                Vencido
                                                            @elseif($estadoSsl === 'por-vencer')
                                                                {{ $diasRestantes }}d restantes
                                                            @else
                                                                Válido ({{ $diasRestantes }}d)
                                                            @endif
                                                        </button>
                                                    @else
                                                        <span class="text-muted">Sin SSL</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $sistema->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($sistema->estado) }}
                                                    </span>
                                                </td>
                                                <td>{{ $sistema->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.versiones.index')
                                                            <a href="{{ route('admin.sistemas.versiones.index', $sistema) }}"
                                                                class="btn btn-info btn-icon btn-sm rounded-circle"
                                                                title="Ver versiones">
                                                                <i class="ti ti-versions fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.sistemas.edit')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle edit-sistema-btn"
                                                                data-id="{{ $sistema->id }}">
                                                                <i class="ti ti-edit fs-lg"></i>
                                                            </a>
                                                        @endcan
                                                        @can('admin.sistemas.destroy')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle delete-sistema-btn"
                                                                data-id="{{ $sistema->id }}">
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
                            <h4 class="card-title mb-0">Sistemas Eliminados</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100"
                                    id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Sistema</th>
                                            <th>Dominio</th>
                                            <th>Tipo</th>
                                            <th>Unidad Organizacional</th>
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
                                                        placeholder="Sistema" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Dominio" type="text">
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
                                                        placeholder="Unidad Organizacional" type="text">
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
                                        @forelse ($sistemasEliminados as $sistema)
                                            <tr data-id="{{ $sistema->id }}">
                                                <td>{{ $sistema->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-server fs-4 text-muted me-2"></i>
                                                        <strong>{{ $sistema->nombre }}</strong>
                                                    </div>
                                                </td>
                                                <td><code class="text-muted">{{ $sistema->dominio }}</code></td>
                                                <td>
                                                <td>
                                                    @foreach ($sistema->tipo as $tipo)
                                                        <span
                                                            class="badge {{ $tipo === 'interno' ? 'bg-info' : 'bg-warning' }} {{ !$loop->last ? 'me-1' : '' }}">
                                                            {{ ucfirst($tipo) }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                </td>
                                                <td>{{ $sistema->unidad->nombre ?? '—' }}</td>
                                                <td>{{ $sistema->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('admin.sistemas.restore')
                                                            <a href="#"
                                                                class="btn btn-default btn-icon btn-sm rounded-circle restore-sistema-btn"
                                                                data-id="{{ $sistema->id }}">
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
    @include('admin.sistemas.modals.add')
    @include('admin.sistemas.modals.edit')
    @include('admin.sistemas.modals.unidad-detail')
    @include('admin.sistemas.modals.ssl-detail')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-sistemas.js'])

    <script>
        const canVersiones = @json(auth()->user()?->can('admin.versiones.index') ?? false);
        const canEdit = @json(auth()->user()?->can('admin.sistemas.edit') ?? false);
        const canDestroy = @json(auth()->user()?->can('admin.sistemas.destroy') ?? false);
        const canRestore = @json(auth()->user()?->can('admin.sistemas.restore') ?? false);
    </script>

    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */
        function getSistemaConSiglaHtml(nombre, sigla, descripcion = '') {
            let html = `
        <div class="d-flex align-items-center">
            <i class="ti ti-server fs-4 text-primary me-2"></i>
            <div>
                <strong>${nombre}</strong>`;

            if (sigla) {
                html += `
            <div>
                <span class="badge bg-secondary-subtle text-secondary mt-1">${sigla}</span>
            </div>`;
            }

            if (descripcion) {
                html += `
            <div class="text-muted small mt-1">
                ${descripcion.substring(0, 60)}
            </div>`;
            }

            html += `</div></div>`;
            return html;
        }

        function getDominioHtml(dominio, tieneSsl = false) {
            const protocolo = tieneSsl ? 'https' : 'http';
            return `
        <a href="${protocolo}://${dominio}" target="_blank" class="text-decoration-none">
            <code class="text-primary cursor-pointer">
                ${dominio}
                <i class="ti ti-external-link ms-1 fs-xs"></i>
            </code>
        </a>`;
        }



        function getTipoBadge(tipo) {
            if (Array.isArray(tipo)) {
                const esInterno = tipo.includes('interno');
                const esExterno = tipo.includes('externo');

                if (esInterno && esExterno) {
                    return `
                <span class="badge bg-info me-1">Interno</span>
                <span class="badge bg-warning">Externo</span>
            `;
                }

                if (esInterno) {
                    return '<span class="badge bg-info">Interno</span>';
                }

                if (esExterno) {
                    return '<span class="badge bg-warning">Externo</span>';
                }
            }

            return tipo === 'interno' ?
                '<span class="badge bg-info">Interno</span>' :
                '<span class="badge bg-warning">Externo</span>';
        }

        function getUnidadHtml(unidad, unidadId) {
            if (!unidad || !unidadId) {
                return '<span class="text-muted">—</span>';
            }
            const nombreCorto = unidad.length > 20 ? unidad.substring(0, 20) + '...' : unidad;
            return `
                <button class="btn btn-sm btn-outline-purple ver-unidad-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#unidadDetailModal"
                        data-unidad-id="${unidadId}"
                        data-unidad-nombre="${unidad}">
                    <i class="ti ti-building me-1"></i>
                    ${nombreCorto}
                </button>`;
        }

        function getSslBadge(sslEmisor, sslId, fechaExpiracion, diasRestantes) {
            if (!sslEmisor || !sslId) {
                return '<span class="text-muted">Sin SSL</span>';
            }

            diasRestantes = parseInt(diasRestantes) || 0;

            let btnClass, textoEstado;
            if (diasRestantes < 0) {
                btnClass = 'btn-outline-danger';
                textoEstado = 'Vencido';
            } else if (diasRestantes <= 30) {
                btnClass = 'btn-outline-warning';
                textoEstado = `${diasRestantes}d restantes`;
            } else {
                btnClass = 'btn-outline-success';
                textoEstado = `Válido (${diasRestantes}d)`;
            }

            let fechaFormateada = fechaExpiracion;
            if (typeof fechaExpiracion === 'object' && fechaExpiracion !== null) {
                fechaFormateada = new Date(fechaExpiracion).toISOString().split('T')[0];
            }

            return `
                <button class="btn btn-sm ${btnClass} ver-ssl-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#sslDetailModal"
                        data-ssl-id="${sslId}"
                        data-ssl-emisor="${sslEmisor}"
                        data-ssl-expiracion="${fechaFormateada}"
                        data-ssl-dias="${diasRestantes}">
                    <i class="ti ti-shield-check me-1"></i>
                    ${textoEstado}
                </button>`;
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
            if (canVersiones) btns +=
                `<a href="/admin/sistemas/${id}/versiones" class="btn btn-info btn-icon btn-sm rounded-circle" title="Ver versiones"><i class="ti ti-versions fs-lg"></i></a>`;
            if (canEdit) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-sistema-btn" data-id="${id}"><i class="ti ti-edit fs-lg"></i></a>`;
            if (canDestroy) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-sistema-btn" data-id="${id}"><i class="ti ti-trash fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        function accionesPapelera(id) {
            let btns = '<div class="d-flex justify-content-center">';
            if (canRestore) btns +=
                `<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-sistema-btn" data-id="${id}"><i class="ti ti-rotate fs-lg"></i></a>`;
            btns += '</div>';
            return btns;
        }

        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= VALIDACIÓN DE CHECKBOXES TIPO ================= */

            document.querySelectorAll('.tipo-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.tipo-checkbox:checked');
                    const errorDiv = document.getElementById('tipoError');
                    const container = document.getElementById('tipoCheckboxContainer');

                    if (checkboxes.length === 0) {
                        // Mostrar error si NO hay ninguno marcado
                        errorDiv.style.display = 'block';
                        container.classList.add('is-invalid');
                    } else {
                        // OCULTAR error si hay AL MENOS UNO marcado
                        errorDiv.style.display = 'none';
                        container.classList.remove('is-invalid');
                    }
                });
            });

            document.querySelectorAll('.edit-tipo-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.edit-tipo-checkbox:checked');
                    const errorDiv = document.getElementById('editTipoError');
                    const container = document.getElementById('editTipoCheckboxContainer');

                    if (checkboxes.length === 0) {
                        // Mostrar error si NO hay ninguno marcado
                        errorDiv.style.display = 'block';
                        container.classList.add('is-invalid');
                    } else {
                        // OCULTAR error si hay AL MENOS UNO marcado
                        errorDiv.style.display = 'none';
                        container.classList.remove('is-invalid');
                    }
                });
            });

            /* ================= AGREGAR SISTEMA ================= */
            document.getElementById('addSistemaForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                // ✅ VALIDAR CHECKBOXES ANTES DE ENVIAR
                const checkboxes = document.querySelectorAll('.tipo-checkbox:checked');
                const errorDiv = document.getElementById('tipoError');
                const container = document.getElementById('tipoCheckboxContainer');

                if (checkboxes.length === 0) {
                    errorDiv.style.display = 'block';
                    container.classList.add('is-invalid');
                    container.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return false;
                }

                const form = this;
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                    const feedback = el.parentElement.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'none';
                    }
                });

                try {
                    const formData = new FormData(form);

                    console.log('📤 Datos que se envían:');
                    for (let [key, value] of formData.entries()) {
                        console.log(`  ${key}: ${value}`);
                    }

                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    console.log('📥 Respuesta del servidor:', data);
                    console.log('Status:', res.status);

                    if (res.status === 422 && data.errors) {
                        console.error('❌ Errores de validación:', data.errors);

                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`) || form
                                .querySelector(`[name="${field}[]"]`);
                            if (input) {
                                input.classList.add('is-invalid');

                                let feedback = input.parentElement?.nextElementSibling;
                                if (!feedback || !feedback.classList.contains(
                                        'invalid-feedback')) {
                                    feedback = input.nextElementSibling;
                                }

                                if (feedback && feedback.classList.contains(
                                        'invalid-feedback')) {
                                    feedback.textContent = data.errors[field][0];
                                    feedback.style.display = 'block';
                                }
                            }
                        });

                        const errorsText = Object.values(data.errors).flat().join('\n');
                        Swal.fire('Error de Validación', errorsText, 'error');
                        return;
                    }

                    if (!res.ok || !data.success) {
                        console.error('❌ Error del servidor:', data);
                        Swal.fire('Error', data.message || 'No se pudo crear el sistema', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addSistemaModal')).hide();

                    window.sistemasDataTables.activos.row.add([
                        data.sistema.id,
                        getSistemaConSiglaHtml(data.sistema.nombre, data.sistema.sigla, data.sistema.descripcion),
                        getDominioHtml(data.sistema.dominio, !!data.sistema.ssl_id),
                        getTipoBadge(data.sistema.tipo),
                        getUnidadHtml(data.sistema.unidad?.nombre, data.sistema.unidad?.id),
                        getSslBadge(
                            data.sistema.ssl?.emisor,
                            data.sistema.ssl?.id,
                            data.sistema.ssl?.fecha_expiracion,
                            data.sistema.ssl?.dias_restantes
                        ),
                        getEstadoBadge(data.sistema.estado),
                        formatearFecha(data.sistema.created_at),
                        accionesActivos(data.sistema.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Sistema creado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('❌ Error al crear sistema:', error);
                    Swal.fire('Error', 'No se pudo crear el sistema: ' + error.message, 'error');
                }
            });

            /* ================= EDITAR SISTEMA (SUBMIT) ================= */
            document.getElementById('editSistemaForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                // ✅ VALIDAR CHECKBOXES ANTES DE ENVIAR
                const checkboxes = document.querySelectorAll('.edit-tipo-checkbox:checked');
                const errorDiv = document.getElementById('editTipoError');
                const container = document.getElementById('editTipoCheckboxContainer');

                if (checkboxes.length === 0) {
                    errorDiv.style.display = 'block';
                    container.classList.add('is-invalid');
                    container.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return false;
                }

                const form = this;
                const id = document.getElementById('editSistemaId').value;

                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                    const feedback = el.parentElement.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'none';
                    }
                });

                try {
                    const res = await fetch(`/admin/sistemas/${id}`, {
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

                                let feedback = input.parentElement.nextElementSibling;
                                if (!feedback || !feedback.classList.contains(
                                        'invalid-feedback')) {
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
                        Swal.fire('Error', 'No se pudo actualizar', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('editSistemaModal')).hide();

                    window.sistemasDataTables.activos.row(`#sistema-${id}`).data([
                        data.sistema.id,
                        getSistemaConSiglaHtml(data.sistema.nombre, data.sistema.sigla, data.sistema.descripcion),
                        getDominioHtml(data.sistema.dominio, !!data.sistema.ssl_id),
                        getTipoBadge(data.sistema.tipo),
                        getUnidadHtml(data.sistema.unidad?.nombre, data.sistema.unidad?.id),
                        getSslBadge(
                            data.sistema.ssl?.emisor,
                            data.sistema.ssl?.id,
                            data.sistema.ssl?.fecha_expiracion,
                            data.sistema.ssl?.dias_restantes || 0
                        ),
                        getEstadoBadge(data.sistema.estado),
                        formatearFecha(data.sistema.created_at),
                        accionesActivos(id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Sistema actualizado correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar sistema:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== EDITAR (CARGAR DATOS) ===== */
                const editBtn = e.target.closest('.edit-sistema-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/sistemas/${id}/edit`);
                        const data = await res.json();

                        console.log('✅ Datos del sistema:', data.sistema);

                        document.getElementById('editSistemaId').value = data.sistema.id;
                        document.getElementById('editNombre').value = data.sistema.nombre;
                        document.getElementById('editSigla').value = data.sistema.sigla || '';
                        document.getElementById('editDominio').value = data.sistema.dominio;
                        document.getElementById('editUnidadId').value = data.sistema.unidad_id;
                        document.getElementById('editSslId').value = data.sistema.ssl_id || '';

                        document.getElementById('editDescripcion').value = data.sistema.descripcion ?? '';
                        

                        // Tipo de sistema
                        document.getElementById('editTipoInterno').checked = false;
                        document.getElementById('editTipoExterno').checked = false;

                        if (Array.isArray(data.sistema.tipo)) {
                            if (data.sistema.tipo.includes('interno')) {
                                document.getElementById('editTipoInterno').checked = true;
                            }
                            if (data.sistema.tipo.includes('externo')) {
                                document.getElementById('editTipoExterno').checked = true;
                            }
                        } else {
                            if (data.sistema.tipo === 'interno') {
                                document.getElementById('editTipoInterno').checked = true;
                            } else if (data.sistema.tipo === 'externo') {
                                document.getElementById('editTipoExterno').checked = true;
                            }
                        }

                        // Estado
                        if (data.sistema.estado === 'activo') {
                            document.getElementById('editEstadoActivo').checked = true;
                        } else {
                            document.getElementById('editEstadoInactivo').checked = true;
                        }

                        setTimeout(() => {
                            const unidadSelect = document.getElementById('editUnidadId');
                            if (unidadSelect && unidadSelect.value) {
                                unidadSelect.dispatchEvent(new Event('change', {
                                    bubbles: true
                                }));
                            }

                            if (data.sistema.ssl_id) {
                                const sslSelect = document.getElementById('editSslId');
                                if (sslSelect && sslSelect.value) {
                                    sslSelect.dispatchEvent(new Event('change', {
                                        bubbles: true
                                    }));
                                }
                            }
                        }, 150);

                        new bootstrap.Modal(document.getElementById('editSistemaModal')).show();

                    } catch (error) {
                        console.error('❌ Error al cargar sistema:', error);
                        Swal.fire('Error', 'No se pudo cargar el sistema', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-sistema-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Sistema?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/sistemas/${id}`, {
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

                        const dtA = window.sistemasDataTables.activos;
                        const dtP = window.sistemasDataTables.papelera;

                        const rowData = dtA.row(`#sistema-${id}`).data();

                        dtA.row(`#sistema-${id}`).remove().draw(false);

                        dtP.row.add([
                            rowData[0],
                            rowData[1],
                            rowData[2],
                            rowData[3],
                            rowData[4].replace(/<i.*?>(.*?)<\/i>/, '$1'),
                            formatearFecha(new Date()),
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Sistema enviado a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar sistema:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-sistema-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Sistema?',
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
                        const res = await fetch(`/admin/sistemas/${id}/restore`, {
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

                        const dtA = window.sistemasDataTables.activos;
                        const dtP = window.sistemasDataTables.papelera;

                        dtP.row(`#sistema-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.sistema.id,
                            getSistemaConSiglaHtml(data.sistema.nombre, data.sistema.sigla, data.sistema.descripcion),
                            getDominioHtml(data.sistema.dominio, !!data.sistema.ssl_id),
                            getTipoBadge(data.sistema.tipo),
                            getUnidadHtml(data.sistema.unidad?.nombre, data.sistema.unidad?.id),
                            getSslBadge(
                                data.sistema.ssl?.emisor,
                                data.sistema.ssl?.id,
                                data.sistema.ssl?.fecha_expiracion,
                                data.sistema.ssl?.dias_restantes || 0
                            ),
                            getEstadoBadge(data.sistema.estado),
                            formatearFecha(data.sistema.created_at),
                            accionesActivos(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Sistema restaurado correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar sistema:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll(
                '#addSistemaForm input, #addSistemaForm select, #editSistemaForm input, #editSistemaForm select'
            ).forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');

                        let feedback = this.parentElement.nextElementSibling;
                        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                            feedback = this.nextElementSibling;
                        }

                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.style.display = 'none';
                        }
                    }
                });

                input.addEventListener('change', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');

                        let feedback = this.parentElement.nextElementSibling;
                        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                            feedback = this.nextElementSibling;
                        }

                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.style.display = 'none';
                        }
                    }
                });
            });

            /* ================= LIMPIAR MODAL AL CERRAR ================= */
            document.getElementById('addSistemaModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addSistemaForm');
                if (!form) return;

                // 1. Resetear formulario
                form.reset();

                // 2. Remover clases is-invalid de todos los campos
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // 3. Ocultar todos los mensajes de error
                form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

                // 4. LIMPIAR ERRORES DE CHECKBOXES TIPO
                const tipoError = document.getElementById('tipoError');
                const tipoContainer = document.getElementById('tipoCheckboxContainer');

                if (tipoError) tipoError.style.display = 'none';
                if (tipoContainer) tipoContainer.classList.remove('is-invalid');

                // 5. RESTAURAR CHECKBOX "INTERNO" MARCADO POR DEFECTO
                document.getElementById('tipo-interno').checked = true;
                document.getElementById('tipo-externo').checked = false;

                // 6. Limpiar previews
                const unidadPreview = document.getElementById('addUnidadPreview');
                const sslPreview = document.getElementById('addSslPreview');

                if (unidadPreview) {
                    unidadPreview.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="ti ti-building-off fs-1 opacity-50"></i>
                            <p class="mb-0 mt-2 small">Selecciona una unidad Organizacional para ver sus responsables</p>
                        </div>
                    `;
                }

                if (sslPreview) {
                    sslPreview.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="ti ti-shield-off fs-1 opacity-50"></i>
                            <p class="mb-0 mt-2 small">Selecciona un SSL para ver su estado</p>
                        </div>
                    `;
                }
            });

            document.getElementById('editSistemaModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editSistemaForm');
                if (!form) return;

                // 1. Resetear formulario
                form.reset();

                // 2. Remover clases is-invalid de todos los campos
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // 3. Ocultar todos los mensajes de error
                form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

                // 4. LIMPIAR ERRORES DE CHECKBOXES TIPO
                const editTipoError = document.getElementById('editTipoError');
                const editTipoContainer = document.getElementById('editTipoCheckboxContainer');

                if (editTipoError) editTipoError.style.display = 'none';
                if (editTipoContainer) editTipoContainer.classList.remove('is-invalid');

                // 5. Desmarcar todos los checkboxes
                document.getElementById('editTipoInterno').checked = false;
                document.getElementById('editTipoExterno').checked = false;

                // 6. Limpiar previews
                const unidadPreview = document.getElementById('editUnidadPreview');
                const sslPreview = document.getElementById('editSslPreview');

                if (unidadPreview) {
                    unidadPreview.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="ti ti-building-off fs-1 opacity-50"></i>
                            <p class="mb-0 mt-2 small">Selecciona una unidad organizacional para ver sus responsables</p>
                        </div>
                    `;
                }

                if (sslPreview) {
                    sslPreview.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="ti ti-shield-off fs-1 opacity-50"></i>
                            <p class="mb-0 mt-2 small">Selecciona un SSL para ver su estado</p>
                        </div>
                    `;
                }
            });

        });
    </script>
@endsection
