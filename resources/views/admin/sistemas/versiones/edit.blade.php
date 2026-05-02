@extends('layouts.vertical', ['title' => 'Editar Versión'])

@section('css')
    <style>
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .invalid-feedback,
        .valid-feedback {
            display: none;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback {
            color: #dc3545;
        }

        .valid-feedback {
            color: #28a745;
        }

        .form-control.is-invalid~.invalid-feedback,
        .form-select.is-invalid~.invalid-feedback {
            display: block;
        }

        .form-control.is-valid~.valid-feedback,
        .form-select.is-valid~.valid-feedback {
            display: block;
        }

        #tecnologias-error,
        #servidores-error,
        #bd-error,
        #creds-error {
            display: none !important;
            visibility: hidden;
            margin-top: 0.5rem;
        }

        #tecnologias-error.show,
        #servidores-error.show,
        #bd-error.show,
        #creds-error.show {
            display: block !important;
            visibility: visible;
        }

        .checkbox-horizontal-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            max-height: 250px;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        .checkbox-horizontal-item {
            display: flex;
            align-items: center;
            min-width: fit-content;
        }

        .checkbox-horizontal-item .form-check {
            margin: 0;
        }

        .checkbox-horizontal-item .form-check-label {
            white-space: nowrap;
            margin-left: 0.5rem;
        }

        .checkbox-horizontal-item.hidden {
            display: none !important;
        }

        .show-more-btn {
            cursor: pointer;
            color: #6366f1;
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }

        .show-more-btn:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        .selected-count {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Barras de progreso */
        .upload-progress-item {
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .upload-progress-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .upload-progress-item .progress {
            height: 28px;
            border-radius: 8px;
            background-color: #e5e7eb;
        }

        .upload-progress-item .progress-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .upload-progress-item .progress-label .file-info {
            display: flex;
            flex-direction: column;
        }

        .upload-progress-item .progress-label .file-name {
            font-size: 0.8rem;
            color: #6b7280;
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upload-progress-item .progress-label .file-size {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .upload-progress-item .progress-status {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 0.5rem;
            display: flex;
            justify-content: space-between;
        }
    </style>
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.sistemas.index') }}">Sistemas</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.sistemas.versiones.index', $sistema) }}">Versiones</a></li>
                        <li class="breadcrumb-item active">Editar v{{ $version->numero_version }}</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="ti ti-edit me-2"></i>
                    Editar Versión {{ $version->numero_version }} - {{ $sistema->nombre }}
                </h4>
            </div>
        </div>
    </div>

    <form id="editVersionForm" action="{{ route('admin.sistemas.versiones.update', [$sistema, $version]) }}" method="POST"
        enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">

                {{-- Información Básica --}}
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Información Básica</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Número de Versión --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Número de Versión <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="numero_version" id="numero_version" class="form-control"
                                    value="{{ old('numero_version', $version->numero_version) }}" placeholder="Ej. 1.0.0">
                                <div class="invalid-feedback">El número de versión es obligatorio</div>
                            </div>

                            {{-- Fecha de Lanzamiento --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de Lanzamiento <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="fecha_lanzamiento" id="fecha_lanzamiento" class="form-control"
                                    value="{{ old('fecha_lanzamiento', $version->fecha_lanzamiento instanceof \Carbon\Carbon ? $version->fecha_lanzamiento->format('Y-m-d') : $version->fecha_lanzamiento) }}">
                                <div class="invalid-feedback">La fecha de lanzamiento es obligatoria</div>
                            </div>

                            {{-- Estado --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="">Seleccionar estado...</option>
                                    <option value="estable"
                                        {{ old('estado', $version->estado) == 'estable' ? 'selected' : '' }}>Estable
                                    </option>
                                    <option value="beta"
                                        {{ old('estado', $version->estado) == 'beta' ? 'selected' : '' }}>Beta
                                    </option>
                                    <option value="deprecated"
                                        {{ old('estado', $version->estado) == 'deprecated' ? 'selected' : '' }}>Deprecated
                                    </option>
                                </select>
                                <div class="invalid-feedback">Debe seleccionar un estado</div>
                            </div>

                            {{-- Marcar como Actual --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold d-block">&nbsp;</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="es_actual" id="es_actual"
                                        value="1" {{ old('es_actual', $version->es_actual) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="es_actual">
                                        <strong>Marcar como Versión Actual</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Desmarcará automáticamente otras versiones</small>
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="4" class="form-control"
                                    placeholder="Describe las características y mejoras de esta versión...">{{ old('descripcion', $version->descripcion) }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Archivos --}}
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Archivos y Documentación</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Imagen --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Imagen de la Versión <small
                                        class="text-muted">(Opcional)</small></label>
                                @if ($version->imagen)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $version->imagen) }}" alt="Imagen actual"
                                            class="img-thumbnail" style="max-height: 150px;">
                                        <p class="text-muted small mb-0">Imagen actual</p>
                                    </div>
                                @endif
                                <input type="file" name="imagen" id="imagen" class="form-control"
                                    accept="image/*">
                                <small class="text-muted">Dejar vacío para mantener la imagen actual. Máx 2MB. JPG, PNG,
                                    GIF</small>
                                <div class="invalid-feedback">El archivo debe ser una imagen válida (máx. 2MB)</div>
                            </div>

                            {{-- Código Fuente --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Código Fuente <small
                                        class="text-muted">(Opcional)</small></label>
                                @if ($version->codigo_fuente)
                                    <div class="alert alert-info py-2 mb-2">
                                        <i class="ti ti-file-zip me-1"></i>
                                        Actual: <strong>{{ basename($version->codigo_fuente) }}</strong>
                                        <a href="{{ asset('storage/' . $version->codigo_fuente) }}"
                                            class="btn btn-sm btn-info ms-2" download>
                                            <i class="ti ti-download"></i>
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="codigo_fuente" id="codigo_fuente" class="form-control"
                                    accept=".zip,.rar">
                                <small class="text-muted">Dejar vacío para mantener el actual. Máx 10GB. ZIP, RAR</small>
                                <div class="invalid-feedback">Error en el código fuente</div>
                            </div>

                            {{-- Archivo Base de Datos --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Archivo de Base de Datos <small
                                        class="text-muted">(Opcional)</small></label>
                                @if ($version->archivo_bd)
                                    <div class="alert alert-warning py-2 mb-2">
                                        <i class="ti ti-database me-1"></i>
                                        Actual: <strong>{{ basename($version->archivo_bd) }}</strong>
                                        <a href="{{ asset('storage/' . $version->archivo_bd) }}"
                                            class="btn btn-sm btn-warning ms-2" download>
                                            <i class="ti ti-download"></i>
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="archivo_bd" id="archivo_bd" class="form-control"
                                    accept=".sql,.gz,.xbk,.dump,.backup,.tar,.bson,.json,.archive,.bak,.bz2,.zip">
                                <small class="text-muted">
                                    MySQL: .sql .gz .xbk &nbsp;|&nbsp;
                                    PostgreSQL: .dump .backup .tar &nbsp;|&nbsp;
                                    MongoDB: .bson .json &nbsp;|&nbsp;
                                    General: .bak .zip .bz2
                                </small>
                                <div class="invalid-feedback">Formato de archivo BD no permitido</div>
                            </div>

                            {{-- Manual Técnico --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manual Técnico <small
                                        class="text-muted">(Opcional)</small></label>
                                @if ($version->manual_tecnico)
                                    <div class="alert alert-info py-2 mb-2">
                                        <i class="ti ti-book me-1"></i>
                                        <strong>{{ basename($version->manual_tecnico) }}</strong>
                                        <a href="{{ asset('storage/' . $version->manual_tecnico) }}"
                                            class="btn btn-sm btn-info ms-2" download>
                                            <i class="ti ti-download"></i>
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="manual_tecnico" id="manual_tecnico" class="form-control"
                                    accept=".pdf">
                                <small class="text-muted">PDF • Máx 100MB • Dejar vacío para mantener el actual</small>
                                <div class="invalid-feedback">El manual técnico debe ser PDF (máx. 100MB)</div>
                            </div>

                            {{-- Manual de Usuario --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manual de Usuario <small
                                        class="text-muted">(Opcional)</small></label>
                                @if ($version->manual_usuario)
                                    <div class="alert alert-info py-2 mb-2">
                                        <i class="ti ti-book-2 me-1"></i>
                                        <strong>{{ basename($version->manual_usuario) }}</strong>
                                        <a href="{{ asset('storage/' . $version->manual_usuario) }}"
                                            class="btn btn-sm btn-info ms-2" download>
                                            <i class="ti ti-download"></i>
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="manual_usuario" id="manual_usuario" class="form-control"
                                    accept=".pdf">
                                <small class="text-muted">PDF • Máx 100MB • Dejar vacío para mantener el actual</small>
                                <div class="invalid-feedback">El manual de usuario debe ser PDF (máx. 100MB)</div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">

                {{-- Tecnologías --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-code me-1"></i>Tecnologías <span class="text-danger">*</span>
                        </h4>
                        <span class="selected-count" id="tecnologias-count">{{ $version->tecnologias->count() }}
                            seleccionadas</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" id="searchTecnologias"
                                placeholder="Buscar tecnología...">
                        </div>
                        <div class="checkbox-horizontal-container" id="tecnologiasContainer">
                            @php
                                $tecnologiasRecientes = $tecnologias->sortByDesc('created_at')->take(3);
                                $tecnologiasRestantes = $tecnologias->sortByDesc('created_at')->slice(3);
                            @endphp

                            @foreach ($tecnologiasRecientes as $tecnologia)
                                <div class="checkbox-horizontal-item">
                                    <div class="form-check">
                                        <input class="form-check-input tecnologia-checkbox" type="checkbox"
                                            name="tecnologias[]" value="{{ $tecnologia->id }}"
                                            id="tec_{{ $tecnologia->id }}"
                                            {{ $version->tecnologias->contains($tecnologia->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tec_{{ $tecnologia->id }}">
                                            {{ $tecnologia->nombre }}
                                            @if ($tecnologia->version)
                                                <span class="badge bg-primary-subtle text-primary ms-1"
                                                    style="font-size:0.65rem">
                                                    v{{ $tecnologia->version }}
                                                </span>
                                            @endif
                                            <small class="text-muted">({{ $tecnologia->tipo }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach

                            @foreach ($tecnologiasRestantes as $tecnologia)
                                <div class="checkbox-horizontal-item hidden tecnologia-extra">
                                    <div class="form-check">
                                        <input class="form-check-input tecnologia-checkbox" type="checkbox"
                                            name="tecnologias[]" value="{{ $tecnologia->id }}"
                                            id="tec_{{ $tecnologia->id }}"
                                            {{ $version->tecnologias->contains($tecnologia->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tec_{{ $tecnologia->id }}">
                                            {{ $tecnologia->nombre }}
                                            @if ($tecnologia->version)
                                                <span class="badge bg-primary-subtle text-primary ms-1"
                                                    style="font-size:0.65rem">
                                                    v{{ $tecnologia->version }}
                                                </span>
                                            @endif
                                            <small class="text-muted">({{ $tecnologia->tipo }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($tecnologias->count() > 3)
                            <a href="#" class="show-more-btn" id="showMoreTecnologias">
                                <i class="ti ti-chevron-down"></i> Ver todas ({{ $tecnologias->count() }})
                            </a>
                        @endif
                        <div class="invalid-feedback" id="tecnologias-error">Seleccione al menos una tecnología</div>
                    </div>
                </div>

                {{-- Servidores --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-server me-1"></i>Servidores <span class="text-danger">*</span>
                        </h4>
                        <span class="selected-count" id="servidores-count">{{ $version->servidores->count() }}
                            seleccionados</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" id="searchServidores"
                                placeholder="Buscar servidor...">
                        </div>
                        <div class="checkbox-horizontal-container" id="servidoresContainer">
                            @php
                                $servidoresRecientes = $servidores->sortByDesc('created_at')->take(3);
                                $servidoresRestantes = $servidores->sortByDesc('created_at')->slice(3);
                            @endphp
                            @foreach ($servidoresRecientes as $servidor)
                                <div class="checkbox-horizontal-item">
                                    <div class="form-check">
                                        <input class="form-check-input servidor-checkbox" type="checkbox"
                                            name="servidores[]" value="{{ $servidor->id }}"
                                            id="srv_{{ $servidor->id }}"
                                            {{ $version->servidores->contains($servidor->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="srv_{{ $servidor->id }}">
                                            {{ $servidor->nombre }}
                                            <small class="text-muted">({{ $servidor->ip }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($servidoresRestantes as $servidor)
                                <div class="checkbox-horizontal-item hidden servidor-extra">
                                    <div class="form-check">
                                        <input class="form-check-input servidor-checkbox" type="checkbox"
                                            name="servidores[]" value="{{ $servidor->id }}"
                                            id="srv_{{ $servidor->id }}"
                                            {{ $version->servidores->contains($servidor->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="srv_{{ $servidor->id }}">
                                            {{ $servidor->nombre }}
                                            <small class="text-muted">({{ $servidor->ip }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($servidores->count() > 3)
                            <a href="#" class="show-more-btn" id="showMoreServidores">
                                <i class="ti ti-chevron-down"></i> Ver todos ({{ $servidores->count() }})
                            </a>
                        @endif
                        <div class="invalid-feedback" id="servidores-error">Seleccione al menos un servidor</div>
                    </div>
                </div>

                {{-- Bases de Datos --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-database me-1"></i>Bases de Datos <span class="text-danger">*</span>
                        </h4>
                        <span class="selected-count" id="bd-count">{{ $version->basesDatos->count() }}
                            seleccionadas</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" id="searchBD"
                                placeholder="Buscar base de datos...">
                        </div>
                        <div class="checkbox-horizontal-container" id="bdContainer">
                            @php
                                $bdRecientes = $basesDatos->sortByDesc('created_at')->take(3);
                                $bdRestantes = $basesDatos->sortByDesc('created_at')->slice(3);
                            @endphp
                            @foreach ($bdRecientes as $bd)
                                <div class="checkbox-horizontal-item">
                                    <div class="form-check">
                                        <input class="form-check-input bd-checkbox" type="checkbox" name="bases_datos[]"
                                            value="{{ $bd->id }}" id="bd_{{ $bd->id }}"
                                            {{ $version->basesDatos->contains($bd->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bd_{{ $bd->id }}">
                                            {{ $bd->nombre }}
                                            <small class="text-muted">({{ $bd->gestor }})</small>
                                            @if ($bd->version)
                                                <span class="badge bg-success-subtle text-success ms-1"
                                                    style="font-size:0.65rem">
                                                    v{{ $bd->version }}
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($bdRestantes as $bd)
                                <div class="checkbox-horizontal-item hidden bd-extra">
                                    <div class="form-check">
                                        <input class="form-check-input bd-checkbox" type="checkbox" name="bases_datos[]"
                                            value="{{ $bd->id }}" id="bd_{{ $bd->id }}"
                                            {{ $version->basesDatos->contains($bd->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bd_{{ $bd->id }}">
                                            {{ $bd->nombre }}
                                            <small class="text-muted">({{ $bd->gestor }})</small>
                                            @if ($bd->version)
                                                <span class="badge bg-success-subtle text-success ms-1"
                                                    style="font-size:0.65rem">
                                                    v{{ $bd->version }}
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($basesDatos->count() > 3)
                            <a href="#" class="show-more-btn" id="showMoreBD">
                                <i class="ti ti-chevron-down"></i> Ver todas ({{ $basesDatos->count() }})
                            </a>
                        @endif
                        <div class="invalid-feedback" id="bd-error">Seleccione al menos una base de datos</div>
                    </div>
                </div>

                {{-- Credenciales (OPCIONAL) --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-key me-1"></i>Credenciales
                            <small class="text-muted ms-1">(Opcional)</small>
                        </h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="selected-count" id="creds-count">{{ $version->credenciales->count() }}
                                seleccionadas</span>
                            @can('admin.credenciales.store')
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addCredencialRapidaModal"
                                    title="Agregar nueva credencial para este sistema">
                                    <i class="ti ti-plus"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm" id="searchCreds"
                                placeholder="Buscar credencial...">
                        </div>
                        <div class="checkbox-horizontal-container" id="credsContainer">
                            @php
                                $credsRecientes = $credenciales->sortByDesc('created_at')->take(3);
                                $credsRestantes = $credenciales->sortByDesc('created_at')->slice(3);
                            @endphp
                            @foreach ($credsRecientes as $cred)
                                <div class="checkbox-horizontal-item">
                                    <div class="form-check">
                                        <input class="form-check-input cred-checkbox" type="checkbox"
                                            name="credenciales[]" value="{{ $cred->id }}"
                                            id="cred_{{ $cred->id }}"
                                            {{ $version->credenciales->contains($cred->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cred_{{ $cred->id }}">
                                            {{ $cred->titulo }}
                                            <small class="text-muted">({{ $cred->usuario }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($credsRestantes as $cred)
                                <div class="checkbox-horizontal-item hidden cred-extra">
                                    <div class="form-check">
                                        <input class="form-check-input cred-checkbox" type="checkbox"
                                            name="credenciales[]" value="{{ $cred->id }}"
                                            id="cred_{{ $cred->id }}"
                                            {{ $version->credenciales->contains($cred->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cred_{{ $cred->id }}">
                                            {{ $cred->titulo }}
                                            <small class="text-muted">({{ $cred->usuario }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($credenciales->count() > 3)
                            <a href="#" class="show-more-btn" id="showMoreCreds">
                                <i class="ti ti-chevron-down"></i> Ver todas ({{ $credenciales->count() }})
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Botones --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.sistemas.versiones.index', $sistema) }}" class="btn btn-light">
                                <i class="ti ti-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="ti ti-device-floppy me-1"></i>Actualizar Versión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

    @include('admin.sistemas.versiones.documentos-adicionales-edit')
    @include('admin.sistemas.versiones.add-credencial-rapida')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('editVersionForm');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const sistemaId = {{ $sistema->id }};
            const versionIdActual = {{ $version->id }};

            const CHUNK_SIZE_CODIGO = 5 * 1024 * 1024; // 5MB
            const CHUNK_SIZE_MANUAL = 2 * 1024 * 1024; // 2MB

            let versionCheckTimeout;
            let currentUploadId = null;

            // ── Validación número de versión ──────────────────────────────────
            const numeroVersionInput = document.getElementById('numero_version');
            const versionChecking = document.createElement('div');
            versionChecking.id = 'version-checking';
            versionChecking.className = 'text-primary small mt-1';
            versionChecking.style.display = 'none';
            versionChecking.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Verificando...';
            numeroVersionInput.parentNode.appendChild(versionChecking);

            if (numeroVersionInput) {
                numeroVersionInput.placeholder = '1.0.0';
                numeroVersionInput.maxLength = 5;

                function formatVersion(value) {
                    let n = value.replace(/[^\d]/g, '').substring(0, 3);
                    if (n.length === 0) return '';
                    if (n.length === 1) return n;
                    if (n.length === 2) return n[0] + '.' + n[1];
                    return n[0] + '.' + n[1] + '.' + n[2];
                }

                numeroVersionInput.addEventListener('input', function() {
                    const newValue = formatVersion(this.value);
                    this.value = newValue;

                    if (newValue.length === 5 && /^\d\.\d\.\d$/.test(newValue)) {
                        clearTimeout(versionCheckTimeout);
                        versionChecking.style.display = 'block';
                        this.classList.remove('is-valid', 'is-invalid');

                        versionCheckTimeout = setTimeout(async () => {
                            try {
                                const url =
                                    `/admin/sistemas/${sistemaId}/versiones/check-duplicate?numero=${encodeURIComponent(newValue)}&exclude=${versionIdActual}`;
                                const res = await fetch(url, {
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                versionChecking.style.display = 'none';
                                if (data.exists) {
                                    numeroVersionInput.classList.add('is-invalid');
                                    numeroVersionInput.classList.remove('is-valid');
                                } else {
                                    numeroVersionInput.classList.remove('is-invalid');
                                    numeroVersionInput.classList.add('is-valid');
                                }
                            } catch (e) {
                                versionChecking.style.display = 'none';
                            }
                        }, 500);
                    } else if (newValue.length > 0 && newValue.length < 5) {
                        versionChecking.style.display = 'none';
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    } else if (newValue.length === 0) {
                        versionChecking.style.display = 'none';
                        this.classList.remove('is-valid', 'is-invalid');
                    }
                });

                numeroVersionInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    this.value = formatVersion((e.clipboardData || window.clipboardData).getData('text'));
                    this.dispatchEvent(new Event('input'));
                });

                numeroVersionInput.addEventListener('keypress', function(e) {
                    if (!/^\d$/.test(e.key)) e.preventDefault();
                });
            }

            // ── Documentos adicionales (edit modal) ───────────────────────────
            const toggleDocumentos = document.getElementById('toggleDocumentos');
            const documentosModal = new bootstrap.Modal(document.getElementById('documentosAdicionalesModal'));
            const documentosNuevosContainer = document.getElementById('documentosNuevosContainer');
            const documentosExistContainer = document.getElementById('documentosExistentesContainer');
            const addDocumentoBtn = document.getElementById('addDocumentoBtn');
            const guardarDocumentosBtn = document.getElementById('guardarDocumentosBtn');

            let documentoNuevoCounter = 0;
            let documentosEliminados = [];
            let documentosTipos = @json($documentos ?? []);

            toggleDocumentos?.addEventListener('change', function() {
                if (this.checked) {
                    documentosModal.show();
                } else {
                    const hayNuevos = documentosNuevosContainer.querySelectorAll('.documento-item').length >
                        0;
                    const hayEliminar = documentosEliminados.length > 0;
                    if (hayNuevos || hayEliminar) {
                        Swal.fire({
                            title: '¿Descartar cambios?',
                            text: 'Hay cambios sin guardar',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, descartar',
                            cancelButtonText: 'Cancelar'
                        }).then(r => {
                            if (r.isConfirmed) resetearDocumentos();
                            else this.checked = true;
                        });
                    }
                }
            });

            function addDocumentoNuevo() {
                documentoNuevoCounter++;
                const docId = `doc_nuevo_${documentoNuevoCounter}`;
                documentosNuevosContainer.insertAdjacentHTML('beforeend', `
                <div class="documento-item documento-nuevo" id="${docId}">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm documento-nombre-nuevo" name="documentos_nuevos[${documentoNuevoCounter}][documento_id]" required>
                                <option value="">Seleccionar...</option>
                                ${documentosTipos.map(d => `<option value="${d.id}">${d.nombre}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1">Archivo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control form-control-sm documento-archivo-nuevo"
                                name="documentos_nuevos[${documentoNuevoCounter}][archivo]"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" required>
                            <small class="text-muted">PDF, Word, Excel, ZIP • Máx: 50MB</small>
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-sm btn-danger remove-documento-nuevo-btn" data-doc-id="${docId}">
                                <i class="ti ti-trash me-1"></i>Quitar
                            </button>
                        </div>
                    </div>
                </div>`);
            }

            addDocumentoBtn?.addEventListener('click', addDocumentoNuevo);

            documentosNuevosContainer?.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-documento-nuevo-btn');
                if (btn) {
                    document.getElementById(btn.dataset.docId)?.remove();
                    documentoNuevoCounter--;
                }
            });

            documentosExistContainer?.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-eliminar-existente');
                if (!btn) return;
                Swal.fire({
                    title: '¿Eliminar documento?',
                    text: btn.dataset.documentoNombre,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(r => {
                    if (r.isConfirmed) {
                        const el = btn.closest('.documento-item');
                        el.style.opacity = '0.5';
                        el.style.textDecoration = 'line-through';
                        el.querySelector('.documento-eliminar-flag').disabled = false;
                        el.querySelector('.documento-eliminar-flag').value = btn.dataset
                        .documentoId;
                        btn.disabled = true;
                        documentosEliminados.push(btn.dataset.documentoId);
                    }
                });
            });

            guardarDocumentosBtn?.addEventListener('click', function() {
                documentosModal.hide();
            });

            function resetearDocumentos() {
                documentosNuevosContainer.innerHTML = '';
                documentoNuevoCounter = 0;
                documentosEliminados.forEach(id => {
                    const el = documentosExistContainer?.querySelector(`[data-documento-id="${id}"]`);
                    if (el) {
                        el.style.opacity = '1';
                        el.style.textDecoration = 'none';
                        el.querySelector('.documento-eliminar-flag').disabled = true;
                        el.querySelector('.btn-eliminar-existente').disabled = false;
                    }
                });
                documentosEliminados = [];
            }

            // ── Validación archivo_bd ─────────────────────────────────────────
            document.getElementById('archivo_bd')?.addEventListener('change', function() {
                const extensiones = ['sql', 'gz', 'xbk', 'dump', 'backup', 'tar', 'bson', 'json', 'archive',
                    'bak', 'bz2', 'zip'
                ];
                if (this.files[0]) {
                    const ext = this.files[0].name.split('.').pop().toLowerCase();
                    if (!extensiones.includes(ext)) {
                        this.classList.add('is-invalid');
                        this.value = '';
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                }
            });

            // ── Validaciones básicas ──────────────────────────────────────────
            function validateField(field) {
                const v = field.value.trim() !== '';
                field.classList.toggle('is-invalid', !v);
                field.classList.toggle('is-valid', v);
                return v;
            }

            function validateFileOptional(input, maxSizeMB, allowedTypes) {
                if (!input.files || !input.files[0]) {
                    input.classList.remove('is-invalid', 'is-valid');
                    return true;
                }
                const file = input.files[0];
                if (file.size / 1024 / 1024 > maxSizeMB) {
                    input.classList.add('is-invalid');
                    return false;
                }
                if (allowedTypes) {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!allowedTypes.includes(ext)) {
                        input.classList.add('is-invalid');
                        return false;
                    }
                }
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }

            document.getElementById('fecha_lanzamiento')?.addEventListener('change', function() {
                validateField(this);
            });
            document.getElementById('estado')?.addEventListener('change', function() {
                validateField(this);
            });
            document.getElementById('imagen')?.addEventListener('change', function() {
                validateFileOptional(this, 2, ['jpg', 'jpeg', 'png', 'gif']);
            });
            document.getElementById('codigo_fuente')?.addEventListener('change', function() {
                validateFileOptional(this, 10240, ['zip', 'rar']);
            });
            document.getElementById('manual_tecnico')?.addEventListener('change', function() {
                validateFileOptional(this, 100, ['pdf']);
            });
            document.getElementById('manual_usuario')?.addEventListener('change', function() {
                validateFileOptional(this, 100, ['pdf']);
            });

            function validateCheckboxGroup(cls, errorId) {
                const checked = document.querySelectorAll(`.${cls}:checked`).length;
                const errorDiv = document.getElementById(errorId);
                if (!errorDiv) return checked > 0;
                errorDiv.style.display = checked > 0 ? 'none' : 'block';
                errorDiv.classList.toggle('show', checked === 0);
                return checked > 0;
            }

            ['tecnologia', 'servidor', 'bd'].forEach(t => {
                document.querySelectorAll(`.${t}-checkbox`).forEach(cb => {
                    cb.addEventListener('change', () => validateCheckboxGroup(`${t}-checkbox`,
                        `${t === 'bd' ? 'bd' : t + 's'}-error`));
                });
            });

            function validateForm() {
                let ok = true;
                const errors = [];

                const nv = document.getElementById('numero_version').value.trim();
                if (!nv || !/^\d\.\d\.\d$/.test(nv)) {
                    document.getElementById('numero_version').classList.add('is-invalid');
                    errors.push('Número de versión');
                    ok = false;
                }

                if (!validateField(document.getElementById('fecha_lanzamiento'))) {
                    errors.push('Fecha de lanzamiento');
                    ok = false;
                }
                if (!validateField(document.getElementById('estado'))) {
                    errors.push('Estado');
                    ok = false;
                }
                if (!validateCheckboxGroup('tecnologia-checkbox', 'tecnologias-error')) {
                    errors.push('Tecnologías');
                    ok = false;
                }
                if (!validateCheckboxGroup('servidor-checkbox', 'servidores-error')) {
                    errors.push('Servidores');
                    ok = false;
                }
                if (!validateCheckboxGroup('bd-checkbox', 'bd-error')) {
                    errors.push('Bases de Datos');
                    ok = false;
                }

                if (!ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formulario Incompleto',
                        html: `<p>Complete:</p><ul class="text-start">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                        confirmButtonColor: '#6366f1'
                    });
                }
                return ok;
            }

            // ── Chunks ────────────────────────────────────────────────────────
            function generateIdentifier() {
                return Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            }

            function formatBytes(bytes) {
                if (!bytes) return '0 Bytes';
                const k = 1024,
                    sizes = ['Bytes', 'KB', 'MB', 'GB'],
                    i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            async function uploadFileInChunks(file, uploadId, tipo, chunkSize, onProgress) {
                const totalChunks = Math.ceil(file.size / chunkSize);
                const identifier = generateIdentifier();
                const endpoint = tipo === 'codigo_fuente' ?
                    `/admin/sistemas/${sistemaId}/versiones/upload-chunk` :
                    `/admin/sistemas/${sistemaId}/versiones/upload-manual-chunk`;

                for (let i = 0; i < totalChunks; i++) {
                    const start = i * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const fd = new FormData();
                    fd.append('chunk', file.slice(start, end));
                    fd.append('chunkIndex', i);
                    fd.append('totalChunks', totalChunks);
                    fd.append('identifier', identifier);
                    fd.append('fileName', file.name);
                    fd.append('upload_id', uploadId);
                    if (tipo !== 'codigo_fuente') fd.append('tipo', tipo);

                    const res = await fetch(endpoint, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error(`Error en chunk ${i} de ${tipo}`);

                    if (onProgress) onProgress({
                        chunkIndex: i + 1,
                        totalChunks,
                        progress: Math.round(((i + 1) / totalChunks) * 100),
                        bytesUploaded: end,
                        totalBytes: file.size
                    });
                }
                return identifier;
            }

            const updateProgress = (id, progress, current, total, bytes, totalBytes) => {
                const bar = document.getElementById(`progress-${id}`);
                const status = document.getElementById(`status-${id}`);
                if (bar) {
                    bar.style.width = progress + '%';
                    bar.textContent = progress + '%';
                    if (progress >= 100) bar.classList.remove('progress-bar-striped', 'progress-bar-animated');
                }
                if (status) {
                    status.innerHTML = progress >= 100 ?
                        '<i class="ti ti-check text-success me-1"></i>Completado' :
                        `Chunk ${current}/${total} • ${formatBytes(bytes)} / ${formatBytes(totalBytes)}`;
                }
            };

            // ── Submit ────────────────────────────────────────────────────────
            form?.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (!validateForm()) return false;

                const codigoFuenteFile = document.getElementById('codigo_fuente').files[0];
                const manualTecnicoFile = document.getElementById('manual_tecnico').files[0];
                const manualUsuarioFile = document.getElementById('manual_usuario').files[0];
                const archivoBdFile = document.getElementById('archivo_bd')?.files[0];
                const imagenFile = document.getElementById('imagen').files[0];
                const numeroVersion = document.getElementById('numero_version').value;

                const hayCodigoNuevo = !!codigoFuenteFile;
                const hayTecnicoNuevo = !!manualTecnicoFile;
                const hayUsuarioNuevo = !!manualUsuarioFile;
                const hayArchivoBd = !!archivoBdFile;

                const hayArchivosGrandes = hayCodigoNuevo || hayTecnicoNuevo || hayUsuarioNuevo ||
                    hayArchivoBd;

                if (hayArchivosGrandes) {
                    // ── MODO CHUNKS ──
                    const formData = new FormData();
                    formData.append('numero_version', numeroVersion);
                    formData.append('fecha_lanzamiento', document.getElementById('fecha_lanzamiento')
                        .value);
                    formData.append('estado', document.getElementById('estado').value);
                    formData.append('descripcion', document.getElementById('descripcion').value);
                    formData.append('version_id', versionIdActual);
                    formData.append('es_actual', document.getElementById('es_actual')?.checked ? 1 : 0);

                    document.querySelectorAll('.tecnologia-checkbox:checked').forEach(cb => formData
                        .append('tecnologias[]', cb.value));
                    document.querySelectorAll('.servidor-checkbox:checked').forEach(cb => formData
                        .append('servidores[]', cb.value));
                    document.querySelectorAll('.bd-checkbox:checked').forEach(cb => formData.append(
                        'bases_datos[]', cb.value));
                    document.querySelectorAll('.cred-checkbox:checked').forEach(cb => formData.append(
                        'credenciales[]', cb.value));

                    if (hayCodigoNuevo) {
                        formData.append('codigo_fuente_nombre', codigoFuenteFile.name);
                        formData.append('codigo_fuente_tamano', codigoFuenteFile.size);
                        formData.append('codigo_fuente_tipo', codigoFuenteFile.type ||
                            'application/octet-stream');
                    }
                    if (hayTecnicoNuevo) {
                        formData.append('manual_tecnico_nombre', manualTecnicoFile.name);
                        formData.append('manual_tecnico_tamano', manualTecnicoFile.size);
                        formData.append('manual_tecnico_tipo', manualTecnicoFile.type ||
                            'application/octet-stream');
                    }
                    if (hayUsuarioNuevo) {
                        formData.append('manual_usuario_nombre', manualUsuarioFile.name);
                        formData.append('manual_usuario_tamano', manualUsuarioFile.size);
                        formData.append('manual_usuario_tipo', manualUsuarioFile.type ||
                            'application/octet-stream');
                    }
                    if (hayArchivoBd) {
                        formData.append('archivo_bd_nombre', archivoBdFile.name);
                        formData.append('archivo_bd_tamano', archivoBdFile.size);
                        formData.append('archivo_bd_tipo', archivoBdFile.type ||
                            'application/octet-stream');
                    }
                    if (imagenFile) {
                        formData.append('imagen', imagenFile);
                    }

                    // Documentos
                    if (toggleDocumentos?.checked) {
                        documentosNuevosContainer.querySelectorAll('.documento-item').forEach(doc => {
                            const sel = doc.querySelector('.documento-nombre-nuevo');
                            const file = doc.querySelector('.documento-archivo-nuevo');
                            if (sel?.value && file?.files?.length) {
                                formData.append('documentos_nuevos_ids[]', sel.value);
                                formData.append('documentos_nuevos_archivos[]', file.files[0],
                                    file.files[0].name);
                            }
                        });
                        documentosEliminados.forEach(id => formData.append('documentos_eliminar[]',
                        id));
                    }

                    try {
                        const initRes = await fetch(
                            `/admin/sistemas/${sistemaId}/versiones/iniciar-upload`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                }
                            });
                        const initData = await initRes.json();
                        if (!initData.success) throw new Error(initData.message ||
                            'Error al iniciar upload');
                        currentUploadId = initData.upload_id;

                        // Construir HTML barras
                        let htmlBars = '<div class="text-start">';
                        if (hayCodigoNuevo) htmlBars +=
                            `<div class="upload-progress-item"><div class="progress-label"><div class="file-info"><span><i class="ti ti-file-zip text-primary me-1"></i><strong>Código Fuente</strong></span><span class="file-name">${codigoFuenteFile.name}</span><span class="file-size">${formatBytes(codigoFuenteFile.size)}</span></div></div><div class="progress"><div id="progress-codigo" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width:0%">0%</div></div><div class="progress-status"><span id="status-codigo">Esperando...</span></div></div>`;
                        if (hayArchivoBd) htmlBars +=
                            `<div class="upload-progress-item"><div class="progress-label"><div class="file-info"><span><i class="ti ti-database text-warning me-1"></i><strong>Archivo Base de Datos</strong></span><span class="file-name">${archivoBdFile.name}</span><span class="file-size">${formatBytes(archivoBdFile.size)}</span></div></div><div class="progress"><div id="progress-archivoBd" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width:0%">0%</div></div><div class="progress-status"><span id="status-archivoBd">Esperando...</span></div></div>`;
                        if (hayTecnicoNuevo) htmlBars +=
                            `<div class="upload-progress-item"><div class="progress-label"><div class="file-info"><span><i class="ti ti-file-text text-success me-1"></i><strong>Manual Técnico</strong></span><span class="file-name">${manualTecnicoFile.name}</span><span class="file-size">${formatBytes(manualTecnicoFile.size)}</span></div></div><div class="progress"><div id="progress-tecnico" class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:0%">0%</div></div><div class="progress-status"><span id="status-tecnico">Esperando...</span></div></div>`;
                        if (hayUsuarioNuevo) htmlBars +=
                            `<div class="upload-progress-item"><div class="progress-label"><div class="file-info"><span><i class="ti ti-file-description text-info me-1"></i><strong>Manual Usuario</strong></span><span class="file-name">${manualUsuarioFile.name}</span><span class="file-size">${formatBytes(manualUsuarioFile.size)}</span></div></div><div class="progress"><div id="progress-usuario" class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width:0%">0%</div></div><div class="progress-status"><span id="status-usuario">Esperando...</span></div></div>`;
                        htmlBars += '</div>';

                        Swal.fire({
                            title: `<i class="ti ti-upload me-2"></i>Actualizando v${numeroVersion}`,
                            html: htmlBars,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            width: '600px'
                        });

                        // Subir en paralelo
                        const uploadPromises = [];
                        const identifiers = {};

                        if (hayCodigoNuevo) uploadPromises.push(uploadFileInChunks(codigoFuenteFile,
                            currentUploadId, 'codigo_fuente', CHUNK_SIZE_CODIGO, d =>
                            updateProgress('codigo', d.progress, d.chunkIndex, d.totalChunks, d
                                .bytesUploaded, d.totalBytes)).then(id => {
                            identifiers.codigo = id;
                        }));
                        if (hayArchivoBd) uploadPromises.push(uploadFileInChunks(archivoBdFile,
                            currentUploadId, 'archivo_bd', CHUNK_SIZE_MANUAL, d =>
                            updateProgress('archivoBd', d.progress, d.chunkIndex, d.totalChunks,
                                d.bytesUploaded, d.totalBytes)).then(id => {
                            identifiers.archivoBd = id;
                        }));
                        if (hayTecnicoNuevo) uploadPromises.push(uploadFileInChunks(manualTecnicoFile,
                            currentUploadId, 'manual_tecnico', CHUNK_SIZE_MANUAL, d =>
                            updateProgress('tecnico', d.progress, d.chunkIndex, d.totalChunks, d
                                .bytesUploaded, d.totalBytes)).then(id => {
                            identifiers.tecnico = id;
                        }));
                        if (hayUsuarioNuevo) uploadPromises.push(uploadFileInChunks(manualUsuarioFile,
                            currentUploadId, 'manual_usuario', CHUNK_SIZE_MANUAL, d =>
                            updateProgress('usuario', d.progress, d.chunkIndex, d.totalChunks, d
                                .bytesUploaded, d.totalBytes)).then(id => {
                            identifiers.usuario = id;
                        }));

                        await Promise.all(uploadPromises);

                        const completeRes = await fetch(
                            `/admin/sistemas/${sistemaId}/versiones/completar-upload`, {
                                method: 'POST',
                                body: JSON.stringify({
                                    upload_id: currentUploadId,
                                    codigo_identifier: identifiers.codigo || null,
                                    archivo_bd_identifier: identifiers.archivoBd || null,
                                    manual_tecnico_identifier: identifiers.tecnico || null,
                                    manual_usuario_identifier: identifiers.usuario || null,
                                }),
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            });

                        const completeData = await completeRes.json();
                        if (!completeData.success) throw new Error(completeData.message);

                        Swal.fire({
                                icon: 'success',
                                title: '¡Actualización Completada!',
                                html: `<p>La versión <strong>${numeroVersion}</strong> se está procesando.</p>`,
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            })
                            .then(() => {
                                window.location.href =
                                    "{{ route('admin.sistemas.versiones.index', $sistema) }}";
                            });

                    } catch (error) {
                        console.error('❌', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message,
                            confirmButtonColor: '#6366f1'
                        });
                    }

                } else {
                    // ── MODO DIRECTO (sin archivos grandes) ──
                    if (toggleDocumentos?.checked) {
                        form.querySelectorAll(
                                'input[name^="documentos_nuevos_"], input[name^="documentos_eliminar"]')
                            .forEach(i => i.remove());

                        documentosNuevosContainer.querySelectorAll('.documento-item').forEach(doc => {
                            const sel = doc.querySelector('.documento-nombre-nuevo');
                            const file = doc.querySelector('.documento-archivo-nuevo');
                            if (sel?.value && file?.files?.length) {
                                const inputId = document.createElement('input');
                                inputId.type = 'hidden';
                                inputId.name = 'documentos_nuevos_ids[]';
                                inputId.value = sel.value;
                                form.appendChild(inputId);

                                const inputFile = document.createElement('input');
                                inputFile.type = 'file';
                                inputFile.name = 'documentos_nuevos_archivos[]';
                                inputFile.style.display = 'none';
                                const dt = new DataTransfer();
                                dt.items.add(file.files[0]);
                                inputFile.files = dt.files;
                                form.appendChild(inputFile);
                            }
                        });

                        documentosEliminados.forEach(id => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = 'documentos_eliminar[]';
                            inp.value = id;
                            form.appendChild(inp);
                        });
                    }

                    form.submit();
                }
            });

            // ── Helpers UI ────────────────────────────────────────────────────
            function setupSearch(searchId, containerId) {
                const input = document.getElementById(searchId);
                const container = document.getElementById(containerId);
                if (!input || !container) return;
                input.addEventListener('input', function() {
                    const term = this.value.toLowerCase();
                    container.querySelectorAll('.checkbox-horizontal-item').forEach(item => {
                        const label = item.querySelector('label');
                        if (label) item.classList.toggle('hidden', !label.textContent.toLowerCase()
                            .includes(term));
                    });
                });
            }

            setupSearch('searchTecnologias', 'tecnologiasContainer');
            setupSearch('searchServidores', 'servidoresContainer');
            setupSearch('searchBD', 'bdContainer');
            setupSearch('searchCreds', 'credsContainer');

            function setupShowMore(btnId, extraClass) {
                const btn = document.getElementById(btnId);
                if (!btn) return;
                let expanded = false;
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    expanded = !expanded;
                    document.querySelectorAll(`.${extraClass}`).forEach(item => item.classList.toggle(
                        'hidden', !expanded));
                    this.innerHTML = expanded ? `<i class="ti ti-chevron-up"></i> Ver menos` :
                        `<i class="ti ti-chevron-down"></i> Ver todas`;
                });
            }

            setupShowMore('showMoreTecnologias', 'tecnologia-extra');
            setupShowMore('showMoreServidores', 'servidor-extra');
            setupShowMore('showMoreBD', 'bd-extra');
            setupShowMore('showMoreCreds', 'cred-extra');

            function updateCount(cls, countId) {
                const checked = document.querySelectorAll(`.${cls}:checked`).length;
                const counter = document.getElementById(countId);
                if (counter) {
                    const word = cls.includes('servidor') ? 'seleccionado' : 'seleccionada';
                    counter.textContent = `${checked} ${checked === 1 ? word : word + 's'}`;
                }
            }

            ['tecnologia', 'servidor', 'bd', 'cred'].forEach(t => {
                document.querySelectorAll(`.${t}-checkbox`).forEach(cb => {
                    const countId = t === 'bd' ? 'bd-count' : t === 'cred' ? 'creds-count' : t ===
                        'servidor' ? 'servidores-count' : 'tecnologias-count';
                    cb.addEventListener('change', () => updateCount(`${t}-checkbox`, countId));
                });
            });

            updateCount('tecnologia-checkbox', 'tecnologias-count');
            updateCount('servidor-checkbox', 'servidores-count');
            updateCount('bd-checkbox', 'bd-count');
            updateCount('cred-checkbox', 'creds-count');

        });
    </script>
@endsection
