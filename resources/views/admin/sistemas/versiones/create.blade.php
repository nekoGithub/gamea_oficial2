@extends('layouts.vertical', ['title' => 'Nueva Versión'])

@section('css')
    <style>
        /* Estilos para validación personalizada */
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

        /* Mensajes de error de checkboxes */
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

        /* Checkboxes horizontales con diseño normal */
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

        /* Estilos para barras de progreso en modal */
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

        /* ========== TOGGLE DOCUMENTOS ADICIONALES (INLINE) ========== */

        #toggleDocumentosGroup {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
        }

        #toggleDocumentosGroup .btn-outline-primary {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
            border-color: #dee2e6;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        #toggleDocumentosGroup .btn-outline-primary:hover {
            background-color: #f8f9fa;
            border-color: #6366f1;
            color: #6366f1;
        }

        /* Estado activo (checked) */
        #toggleDocumentos:checked+.btn-outline-primary {
            background-color: #6366f1;
            border-color: #6366f1;
            color: white;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
        }

        #toggleDocumentos:checked+.btn-outline-primary:hover {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        /* Icono */
        #toggleDocumentosGroup .btn-outline-primary i {
            font-size: 0.9rem;
        }

        /* ========== MODAL DE DOCUMENTOS ========== */

        #documentosAdicionalesModal .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        #documentosAdicionalesModal .modal-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            padding: 1.25rem 1.5rem;
        }

        #documentosAdicionalesModal .modal-header .modal-title {
            color: #212529;
            font-weight: 600;
        }

        #documentosAdicionalesModal .modal-body {
            padding: 1.5rem;
            max-height: 500px;
        }

        #documentosAdicionalesModal .modal-footer {
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        /* ========== ITEMS DE DOCUMENTOS ========== */

        .documento-item {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .documento-item:hover {
            background-color: #e9ecef;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .documento-item .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .documento-item .form-select,
        .documento-item .form-control {
            font-size: 0.875rem;
        }

        .documento-item .btn-danger {
            height: 38px;
            width: 38px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .documento-item .btn-danger i {
            font-size: 1.1rem;
        }

        .documento-item small.text-muted {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.75rem;
        }

        /* Botón agregar documento */
        .btn-add-documento {
            border: 2px dashed #6366f1;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-documento:hover {
            background-color: #6366f1;
            color: white;
            border-style: solid;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.2);
        }

        /* Responsive para documentos */
        @media (max-width: 768px) {

            .documento-item .col-md-5,
            .documento-item .col-md-6,
            .documento-item .col-md-1 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 0.5rem;
            }

            .documento-item .btn-danger {
                width: 100%;
            }
        }

        /* Responsive toggle */
        @media (max-width: 576px) {
            #toggleDocumentosGroup .btn-outline-primary {
                font-size: 0.75rem;
                padding: 0.3rem 0.6rem;
            }

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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.sistemas.versiones.index', $sistema) }}">Versiones</a>
                        </li>
                        <li class="breadcrumb-item active">Nueva Versión</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="ti ti-plus me-2"></i>
                    Nueva Versión para: {{ $sistema->nombre }}
                </h4>
            </div>
        </div>
    </div>

    <form id="createVersionForm" action="{{ route('admin.sistemas.versiones.store', $sistema) }}" method="POST"
        enctype="multipart/form-data" novalidate>
        @csrf

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
                                    value="{{ old('numero_version') }}" placeholder="Ej. 1.0.0">
                                <div class="invalid-feedback">El número de versión es obligatorio</div>
                                <div class="valid-feedback">Versión disponible</div>
                                <div id="version-checking" class="text-primary small mt-1" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Verificando disponibilidad...
                                </div>
                            </div>

                            {{-- Fecha de Lanzamiento --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de Lanzamiento <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="fecha_lanzamiento" id="fecha_lanzamiento" class="form-control"
                                    value="{{ old('fecha_lanzamiento', date('Y-m-d')) }}">
                                <div class="invalid-feedback">La fecha de lanzamiento es obligatoria</div>
                            </div>

                            {{-- Estado --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="">Seleccionar estado...</option>
                                    <option value="estable" {{ old('estado') == 'estable' ? 'selected' : '' }}>Estable
                                    </option>
                                    <option value="beta" {{ old('estado') == 'beta' ? 'selected' : '' }}>Beta</option>
                                    <option value="deprecated" {{ old('estado') == 'deprecated' ? 'selected' : '' }}>
                                        Deprecated</option>
                                </select>
                                <div class="invalid-feedback">Debe seleccionar un estado</div>
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="4" class="form-control"
                                    placeholder="Describe las características y mejoras de esta versión...">{{ old('descripcion') }}</textarea>
                                <small class="text-muted">
                                    <i class="ti ti-info-circle"></i>
                                    Esta versión se marcará automáticamente como <strong>Versión Actual</strong> al crearla
                                </small>
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

                            {{-- Imagen con Toggle de Documentos Adicionales --}}
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">
                                        Imagen de la Versión <small class="text-muted">(Opcional)</small>
                                    </label>

                                    {{-- Toggle Documentos Adicionales --}}
                                    <div class="btn-group btn-group-sm" role="group" id="toggleDocumentosGroup">
                                        <input type="checkbox" class="btn-check" id="toggleDocumentos" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="toggleDocumentos">
                                            <i class="ti ti-files me-1"></i>
                                            <span class="d-none d-sm-inline">Documentos Adicionales</span>
                                            <span class="d-inline d-sm-none">Doc. Adic.</span>
                                        </label>
                                    </div>
                                </div>

                                <input type="file" name="imagen" id="imagen" class="form-control"
                                    accept="image/*">
                                <small class="text-muted">Máximo 2MB. Formatos: JPG, PNG, GIF</small>
                                <div class="invalid-feedback">El archivo debe ser una imagen válida (máx. 2MB)</div>
                            </div>

                            {{-- Código Fuente --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Código Fuente <span
                                        class="text-danger">*</span></label>
                                <input type="file" name="codigo_fuente" id="codigo_fuente" class="form-control"
                                    accept=".zip,.rar">
                                <small class="text-muted">Máximo 10GB. Formatos permitidos: ZIP, RAR</small>
                                <div class="invalid-feedback">El código fuente es obligatorio</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Archivo de Base de Datos <small class="text-muted">(Opcional)</small>
                                </label>
                                <input type="file" name="archivo_bd" id="archivo_bd" class="form-control"
                                    accept=".sql,.gz,.xbk,.dump,.backup,.tar,.bson,.json,.archive,.bak,.bz2,.zip">
                                <small class="text-muted">
                                    MySQL: .sql .gz .xbk &nbsp;|&nbsp;
                                    PostgreSQL: .dump .backup .tar .sql &nbsp;|&nbsp;
                                    MongoDB: .bson .json .archive &nbsp;|&nbsp;
                                    General: .bak .zip .gz .bz2
                                </small>
                            </div>

                            {{-- Manual Técnico --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manual Técnico <small
                                        class="text-muted">(Opcional)</small></label>
                                <input type="file" name="manual_tecnico" id="manual_tecnico" class="form-control"
                                    accept=".pdf">
                                <div class="invalid-feedback">
                                    El manual técnico debe ser un archivo PDF (máximo 100MB)
                                </div>
                                <small class="text-muted">Formato: PDF • Tamaño máximo: 100MB</small>
                            </div>

                            {{-- Manual de Usuario --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manual de Usuario <small
                                        class="text-muted">(Opcional)</small></label>
                                <input type="file" name="manual_usuario" id="manual_usuario" class="form-control"
                                    accept=".pdf">
                                <div class="invalid-feedback">
                                    El manual de usuario debe ser un archivo PDF (máximo 100MB)
                                </div>
                                <small class="text-muted">Formato: PDF • Tamaño máximo: 100MB</small>
                            </div>

                        </div>
                    </div>
                </div>



            </div>

            <!-- Columna Lateral - Relaciones -->
            <div class="col-lg-4">

                {{-- Tecnologías --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-code me-1"></i>Tecnologías <span class="text-danger">*</span>
                        </h4>
                        <span class="selected-count" id="tecnologias-count">0 seleccionadas</span>
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
                                            id="tec_{{ $tecnologia->id }}">
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
                                            id="tec_{{ $tecnologia->id }}">
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
                                <i class="ti ti-chevron-down"></i>
                                Ver todas ({{ $tecnologias->count() }})
                            </a>
                        @endif
                        <div class="invalid-feedback" id="tecnologias-error">
                            Seleccione al menos una tecnología
                        </div>
                    </div>
                </div>

                {{-- Servidores --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-server me-1"></i>Servidores <span class="text-danger">*</span>
                        </h4>
                        <span class="selected-count" id="servidores-count">0 seleccionados</span>
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
                                            id="srv_{{ $servidor->id }}">
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
                                            id="srv_{{ $servidor->id }}">
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
                                <i class="ti ti-chevron-down"></i>
                                Ver todos ({{ $servidores->count() }})
                            </a>
                        @endif
                        <div class="invalid-feedback" id="servidores-error">
                            Seleccione al menos un servidor
                        </div>
                    </div>
                </div>

                {{-- Bases de Datos --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-database me-1"></i>Bases de Datos <span class="text-danger">*</span>
                        </h4>
                        <span class="selected-count" id="bd-count">0 seleccionadas</span>
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
                                            value="{{ $bd->id }}" id="bd_{{ $bd->id }}">
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
                                            value="{{ $bd->id }}" id="bd_{{ $bd->id }}">
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
                                <i class="ti ti-chevron-down"></i>
                                Ver todas ({{ $basesDatos->count() }})
                            </a>
                        @endif
                        <div class="invalid-feedback" id="bd-error">
                            Seleccione al menos una base de datos
                        </div>
                    </div>
                </div>

                {{-- Credenciales --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="ti ti-key me-1"></i>Credenciales
                            <small class="text-muted ms-1">(Opcional)</small>
                        </h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="selected-count" id="creds-count">0 seleccionadas</span>
                            {{-- ✅ Botón + agregar credencial rápida --}}
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
                                            id="cred_{{ $cred->id }}">
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
                                            id="cred_{{ $cred->id }}">
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
                                <i class="ti ti-chevron-down"></i>
                                Ver todas ({{ $credenciales->count() }})
                            </a>
                        @endif
                        <div class="invalid-feedback" id="creds-error">
                            Seleccione al menos una credencial
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.sistemas.versiones.index', $sistema) }}" class="btn btn-light">
                                <i class="ti ti-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="ti ti-device-floppy me-1"></i>Guardar Versión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

    @include('admin.sistemas.versiones.documentos-adicionales')
    @include('admin.sistemas.versiones.add-credencial-rapida')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {




            const form = document.getElementById('createVersionForm');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const sistemaId = {{ $sistema->id }};

            // Tamaños de chunk
            const CHUNK_SIZE_CODIGO = 5 * 1024 * 1024; // 5MB para código fuente
            const CHUNK_SIZE_MANUAL = 2 * 1024 * 1024; // 2MB para manuales

            let versionCheckTimeout;
            let currentUploadId = null;

            // ========== DETECTAR REANUDACIÓN ==========
            @if (isset($resumeUpload))
                currentUploadId = {{ $resumeUpload->id }};

                // Pre-llenar formulario con datos guardados
                const uploadData = @json($resumeUpload->data);

                document.getElementById('numero_version').value = '{{ $resumeUpload->numero_version }}';
                document.getElementById('numero_version').disabled = true;
                document.getElementById('fecha_lanzamiento').value = uploadData.fecha_lanzamiento;
                document.getElementById('estado').value = uploadData.estado;
                document.getElementById('descripcion').value = uploadData.descripcion || '';

                // Pre-seleccionar tecnologías
                if (uploadData.tecnologias) {
                    uploadData.tecnologias.forEach(id => {
                        const checkbox = document.getElementById(`tec_${id}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                // Pre-seleccionar servidores
                if (uploadData.servidores) {
                    uploadData.servidores.forEach(id => {
                        const checkbox = document.getElementById(`srv_${id}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                // Pre-seleccionar bases de datos
                if (uploadData.bases_datos) {
                    uploadData.bases_datos.forEach(id => {
                        const checkbox = document.getElementById(`bd_${id}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                // Pre-seleccionar credenciales
                if (uploadData.credenciales) {
                    uploadData.credenciales.forEach(id => {
                        const checkbox = document.getElementById(`cred_${id}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                setTimeout(() => {
                    updateCount('tecnologia-checkbox', 'tecnologias-count');
                    updateCount('servidor-checkbox', 'servidores-count');
                    updateCount('bd-checkbox', 'bd-count');
                    updateCount('cred-checkbox', 'creds-count');
                }, 100);

                Swal.fire({
                    icon: 'info',
                    title: 'Reanudando Upload',
                    html: `
                        <p>Continúa subiendo los archivos de la <strong>versión {{ $resumeUpload->numero_version }}</strong></p>
                        <p class="text-muted">Progreso código: {{ $resumeUpload->progreso }}%</p>
                    `,
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#6366f1'
                });
            @endif


            // ========== DOCUMENTOS ADICIONALES CON MODAL ==========

            const toggleDocumentos = document.getElementById('toggleDocumentos');
            const documentosModal = new bootstrap.Modal(document.getElementById('documentosAdicionalesModal'));
            const documentosContainer = document.getElementById('documentosContainer');
            const addDocumentoBtn = document.getElementById('addDocumentoBtn');
            const guardarDocumentosBtn = document.getElementById('guardarDocumentosBtn');

            let documentoCounter = 0;

            // ========== CARGAR TIPOS DE DOCUMENTOS ==========
            // Documentos pasados desde el backend
            let documentosTipos = @json($documentos ?? []);

            console.log('📄 Documentos disponibles:', documentosTipos);

            // Si no hay documentos disponibles, mostrar advertencia
            if (documentosTipos.length === 0) {
                console.warn('⚠️ No hay tipos de documentos disponibles');

                // Deshabilitar el toggle si no hay documentos
                if (toggleDocumentos) {
                    toggleDocumentos.disabled = true;
                    const label = toggleDocumentos.nextElementSibling;
                    if (label) {
                        label.style.opacity = '0.5';
                        label.title = 'No hay tipos de documentos disponibles. Cree tipos de documentos primero.';
                    }
                }
            } else {
                console.log(`✅ ${documentosTipos.length} tipos de documentos cargados`);
            }

            // Toggle: abrir modal al activar
            toggleDocumentos?.addEventListener('change', function() {
                if (this.checked) {
                    documentosModal.show();
                    // Agregar un documento por defecto si está vacío
                    if (documentoCounter === 0) {
                        addDocumento();
                    }
                } else {
                    // Limpiar documentos al desactivar
                    if (documentoCounter > 0) {
                        Swal.fire({
                            title: '¿Eliminar documentos?',
                            text: '¿Desea eliminar todos los documentos adicionales agregados?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                documentosContainer.innerHTML = '';
                                documentoCounter = 0;
                            } else {
                                this.checked = true;
                            }
                        });
                    }
                }
            });

            // Cerrar modal: desactivar toggle si no hay documentos
            document.getElementById('documentosAdicionalesModal')?.addEventListener('hidden.bs.modal', function() {
                if (documentoCounter === 0) {
                    toggleDocumentos.checked = false;
                }
            });

            // Botón "Guardar" del modal
            guardarDocumentosBtn?.addEventListener('click', function() {
                if (documentoCounter > 0) {
                    const allValid = validateDocumentosAdicionales();
                    if (allValid) {
                        documentosModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Documentos guardados',
                            text: `${documentoCounter} documento(s) agregado(s)`,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sin documentos',
                        text: 'Debe agregar al menos un documento o desactivar la opción',
                        confirmButtonColor: '#6366f1'
                    });
                }
            });

            // Función para agregar un nuevo documento
            function addDocumento() {
                documentoCounter++;
                const docId = `doc_${documentoCounter}`;

                const docHtml = `
        <div class="documento-item" id="${docId}" data-doc-id="${documentoCounter}">
            <div class="row g-3">
                
                <!-- Selector de Tipo de Documento -->
                <div class="col-md-5">
                    <label class="form-label">
                        Tipo de Documento <span class="text-danger">*</span>
                    </label>
                    <select 
                        class="form-select documento-nombre" 
                        name="documentos[${documentoCounter}][documento_id]"
                        required
                    >
                        <option value="">Seleccionar...</option>
                        ${documentosTipos.map(doc => `
                                                                                                                            <option value="${doc.id}">${doc.nombre}</option>
                                                                                                                        `).join('')}
                    </select>
                    <div class="invalid-feedback">Debe seleccionar un tipo de documento</div>
                </div>

                <!-- Input de Archivo -->
                <div class="col-md-6">
                    <label class="form-label">
                        Archivo <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="file" 
                        class="form-control documento-archivo" 
                        name="documentos[${documentoCounter}][archivo]"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                        required
                    >
                    <div class="invalid-feedback">El archivo es obligatorio (máx. 50MB)</div>
                    <small class="text-muted">PDF, Word, Excel, ZIP • Máx: 50MB</small>
                </div>

                <!-- Botón Eliminar -->
                <div class="col-md-1">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button 
                        type="button" 
                        class="btn btn-danger remove-documento-btn" 
                        data-doc-id="${docId}"
                        title="Eliminar documento"
                    >
                        <i class="ti ti-trash"></i>
                    </button>
                </div>

            </div>
        </div>
    `;

                documentosContainer.insertAdjacentHTML('beforeend', docHtml);

                // Agregar validación al nuevo archivo
                const newFileInput = document.querySelector(`#${docId} .documento-archivo`);
                newFileInput.addEventListener('change', function() {
                    validateDocumentoFile(this);
                });

                // Agregar validación al selector
                const newSelect = document.querySelector(`#${docId} .documento-nombre`);
                newSelect.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                    if (this.value) {
                        this.classList.add('is-valid');
                    }
                });
            }

            // Validar archivo de documento
            function validateDocumentoFile(input) {
                const maxSize = 50; // MB

                if (!input.files || !input.files[0]) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                    return false;
                }

                const file = input.files[0];
                const fileSize = file.size / 1024 / 1024;

                if (fileSize > maxSize) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');

                    const feedback = input.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = `El archivo supera los ${maxSize}MB`;
                    }
                    return false;
                }

                const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'];
                const fileExt = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExt)) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');

                    const feedback = input.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Formato no permitido';
                    }
                    return false;
                }

                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }

            // Botón para agregar documento
            addDocumentoBtn?.addEventListener('click', function() {
                addDocumento();
            });

            // Delegación de eventos para eliminar documentos
            documentosContainer?.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-documento-btn');
                if (removeBtn) {
                    const docId = removeBtn.dataset.docId;
                    const docElement = document.getElementById(docId);

                    if (docElement) {
                        Swal.fire({
                            title: '¿Eliminar documento?',
                            text: 'Esta acción no se puede deshacer',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                docElement.remove();
                                documentoCounter--;

                                if (documentoCounter === 0) {
                                    toggleDocumentos.checked = false;
                                    documentosModal.hide();
                                }
                            }
                        });
                    }
                }
            });

            // Validar documentos adicionales
            function validateDocumentosAdicionales() {
                if (!toggleDocumentos.checked) {
                    return true;
                }

                const documentos = documentosContainer.querySelectorAll('.documento-item');
                let isValid = true;

                documentos.forEach(doc => {
                    const select = doc.querySelector('.documento-nombre');
                    const fileInput = doc.querySelector('.documento-archivo');

                    if (!select.value) {
                        select.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!validateDocumentoFile(fileInput)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Documentos incompletos',
                        text: 'Complete todos los campos de los documentos adicionales',
                        confirmButtonColor: '#6366f1'
                    });
                }

                return isValid;
            }


            // ========== VALIDACIÓN DE VERSIÓN CON FORMATO OBLIGATORIO X.X.X ==========
            const numeroVersionInput = document.getElementById('numero_version');
            const versionChecking = document.getElementById('version-checking');

            if (numeroVersionInput && !numeroVersionInput.disabled) {
                numeroVersionInput.placeholder = '1.0.0';
                numeroVersionInput.maxLength = 5;

                function formatVersion(value) {
                    let numbers = value.replace(/[^\d]/g, '');
                    numbers = numbers.substring(0, 3);

                    if (numbers.length === 0) return '';
                    else if (numbers.length === 1) return numbers;
                    else if (numbers.length === 2) return numbers[0] + '.' + numbers[1];
                    else return numbers[0] + '.' + numbers[1] + '.' + numbers[2];
                }

                numeroVersionInput.addEventListener('input', function(e) {
                    const cursorPosition = this.selectionStart;
                    const oldValue = this.value;
                    const newValue = formatVersion(this.value);
                    this.value = newValue;

                    if (newValue.length > oldValue.length && newValue[cursorPosition] === '.') {
                        this.setSelectionRange(cursorPosition + 1, cursorPosition + 1);
                    }

                    if (newValue.length === 5 && /^\d\.\d\.\d$/.test(newValue)) {
                        clearTimeout(versionCheckTimeout);
                        versionChecking.style.display = 'block';
                        this.classList.remove('is-valid', 'is-invalid');

                        versionCheckTimeout = setTimeout(async () => {
                            try {
                                const url =
                                    `/admin/sistemas/${sistemaId}/versiones/check-duplicate?numero=${encodeURIComponent(newValue)}`;
                                const response = await fetch(url, {
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'Accept': 'application/json'
                                    }
                                });

                                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                                const data = await response.json();
                                versionChecking.style.display = 'none';

                                if (data.exists) {
                                    numeroVersionInput.classList.remove('is-valid');
                                    numeroVersionInput.classList.add('is-invalid');
                                    const feedback = numeroVersionInput.nextElementSibling;
                                    if (feedback && feedback.classList.contains(
                                            'invalid-feedback')) {
                                        feedback.textContent =
                                            'Esta versión ya existe para este sistema';
                                    }
                                } else {
                                    numeroVersionInput.classList.remove('is-invalid');
                                    numeroVersionInput.classList.add('is-valid');
                                }
                            } catch (error) {
                                console.error('Error verificando versión:', error);
                                versionChecking.style.display = 'none';
                            }
                        }, 500);
                    } else if (newValue.length > 0 && newValue.length < 5) {
                        versionChecking.style.display = 'none';
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                        const feedback = this.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = 'Debe completar el formato X.X.X (3 números)';
                        }
                    } else if (newValue.length === 0) {
                        versionChecking.style.display = 'none';
                        this.classList.remove('is-valid', 'is-invalid');
                    }
                });

                numeroVersionInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value.length > 0 && value.length < 5) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                        const feedback = this.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = 'Debe completar el formato 1.0.0 (3 números)';
                        }
                    }
                });

                numeroVersionInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const formatted = formatVersion(pastedText);
                    this.value = formatted;
                    this.dispatchEvent(new Event('input'));
                });

                numeroVersionInput.addEventListener('keypress', function(e) {
                    if (!/^\d$/.test(e.key)) {
                        e.preventDefault();
                    }
                });
            }

            // ========== VALIDACIÓN DE CAMPOS OBLIGATORIOS ==========
            function validateField(field) {
                const value = field.value.trim();
                const isValid = value !== '';

                if (isValid) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                }

                return isValid;
            }

            document.getElementById('fecha_lanzamiento')?.addEventListener('change', function() {
                validateField(this);
            });

            document.getElementById('estado')?.addEventListener('change', function() {
                validateField(this);
            });

            // ========== VALIDACIÓN DE ARCHIVOS OBLIGATORIOS ==========
            function validateFileRequired(input, maxSize, allowedTypes = null, isRequired = true) {
                if (!input.files || !input.files[0]) {
                    if (isRequired) {
                        input.classList.add('is-invalid');
                        return false;
                    }
                    input.classList.remove('is-invalid', 'is-valid');
                    return true;
                }

                const file = input.files[0];
                const fileSize = file.size / 1024 / 1024;

                if (fileSize > maxSize) {
                    input.classList.add('is-invalid');
                    return false;
                }

                if (allowedTypes) {
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    if (!allowedTypes.includes(fileExt)) {
                        input.classList.add('is-invalid');
                        return false;
                    }
                }

                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }

            document.getElementById('imagen')?.addEventListener('change', function() {
                validateFileRequired(this, 2, ['jpg', 'jpeg', 'png', 'gif'], false);
            });

            document.getElementById('codigo_fuente')?.addEventListener('change', function() {
                validateFileRequired(this, 10240, ['zip', 'rar'], true);
            });

            document.getElementById('manual_tecnico')?.addEventListener('change', function() {
                validateFileRequired(this, 100, ['pdf'], true);
            });

            document.getElementById('manual_usuario')?.addEventListener('change', function() {
                validateFileRequired(this, 100, ['pdf'], true);
            });
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

            // ========== VALIDACIÓN DE CHECKBOXES OBLIGATORIOS ==========
            function validateCheckboxGroup(checkboxClass, errorId) {
                const checked = document.querySelectorAll(`.${checkboxClass}:checked`).length;
                const errorDiv = document.getElementById(errorId);

                if (!errorDiv) return checked > 0;

                if (checked > 0) {
                    errorDiv.style.display = 'none';
                    errorDiv.classList.remove('show');
                    return true;
                } else {
                    errorDiv.style.display = 'block';
                    errorDiv.classList.add('show');
                    return false;
                }
            }

            document.querySelectorAll('.tecnologia-checkbox').forEach(cb => {
                cb.addEventListener('change', () => validateCheckboxGroup('tecnologia-checkbox',
                    'tecnologias-error'));
            });

            document.querySelectorAll('.servidor-checkbox').forEach(cb => {
                cb.addEventListener('change', () => validateCheckboxGroup('servidor-checkbox',
                    'servidores-error'));
            });

            document.querySelectorAll('.bd-checkbox').forEach(cb => {
                cb.addEventListener('change', () => validateCheckboxGroup('bd-checkbox', 'bd-error'));
            });

            // ========== FUNCIÓN DE VALIDACIÓN COMPLETA ==========
            function validateForm() {
                let isFormValid = true;
                const errors = [];

                const numeroVersion = document.getElementById('numero_version');
                if (!numeroVersion.value.trim()) {
                    numeroVersion.classList.add('is-invalid');
                    errors.push('Número de versión');
                    isFormValid = false;
                } else if (numeroVersion.classList.contains('is-invalid') && !numeroVersion.disabled) {
                    errors.push('Número de versión (duplicado o formato inválido)');
                    isFormValid = false;
                }

                if (!validateField(document.getElementById('fecha_lanzamiento'))) {
                    errors.push('Fecha de lanzamiento');
                    isFormValid = false;
                }

                if (!validateField(document.getElementById('estado'))) {
                    errors.push('Estado');
                    isFormValid = false;
                }

                if (!validateFileRequired(document.getElementById('codigo_fuente'), 10240, ['zip', 'rar'], true)) {
                    errors.push('Código fuente');
                    isFormValid = false;
                }

                /* if (!validateFileRequired(document.getElementById('manual_tecnico'), 100, ['pdf'],
                        true)) {
                    errors.push('Manual técnico');
                    isFormValid = false;
                }

                if (!validateFileRequired(document.getElementById('manual_usuario'), 100, ['pdf'],
                        true)) {
                    errors.push('Manual de usuario');
                    isFormValid = false;
                } */

                if (!validateCheckboxGroup('tecnologia-checkbox', 'tecnologias-error')) {
                    errors.push('Tecnologías');
                    isFormValid = false;
                }

                if (!validateCheckboxGroup('servidor-checkbox', 'servidores-error')) {
                    errors.push('Servidores');
                    isFormValid = false;
                }

                if (!validateCheckboxGroup('bd-checkbox', 'bd-error')) {
                    errors.push('Bases de Datos');
                    isFormValid = false;
                }

                if (toggleDocumentos.checked && !validateDocumentosAdicionales()) {
                    errors.push('Documentos adicionales (complete todos los campos)');
                    isFormValid = false;
                }

                if (!isFormValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formulario Incompleto',
                        html: `<p>Complete los siguientes campos:</p><ul class="text-start">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                        confirmButtonColor: '#6366f1'
                    });
                }

                return isFormValid;
            }

            // ========== GENERAR IDENTIFICADOR ÚNICO ==========
            function generateIdentifier() {
                return Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            }

            // ========== FORMATEAR BYTES ==========
            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // ========== SUBIR ARCHIVO EN CHUNKS (GENÉRICO) ==========
            async function uploadFileInChunks(file, uploadId, tipo, chunkSize, onProgress) {
                const totalChunks = Math.ceil(file.size / chunkSize);
                const identifier = generateIdentifier();

                // Determinar endpoint según tipo
                const endpoint = tipo === 'codigo_fuente' ?
                    `/admin/sistemas/${sistemaId}/versiones/upload-chunk` :
                    `/admin/sistemas/${sistemaId}/versiones/upload-manual-chunk`;

                console.log(`📦 Subiendo ${file.name} (${tipo}) en ${totalChunks} chunks`);

                for (let i = 0; i < totalChunks; i++) {
                    const start = i * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const chunk = file.slice(start, end);

                    const formData = new FormData();
                    formData.append('chunk', chunk);
                    formData.append('chunkIndex', i);
                    formData.append('totalChunks', totalChunks);
                    formData.append('identifier', identifier);
                    formData.append('fileName', file.name);
                    formData.append('upload_id', uploadId);

                    // Para manuales, agregar el tipo
                    if (tipo !== 'codigo_fuente') {
                        formData.append('tipo', tipo);
                    }

                    const response = await fetch(endpoint, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Error en chunk ${i} de ${tipo}`);
                    }

                    const progress = Math.round(((i + 1) / totalChunks) * 100);

                    if (onProgress) {
                        onProgress({
                            chunkIndex: i + 1,
                            totalChunks,
                            progress,
                            bytesUploaded: Math.min(end, file.size),
                            totalBytes: file.size
                        });
                    }
                }

                return identifier;
            }

            // ========== SUBMIT DEL FORMULARIO ==========
            form?.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return false;
                }

                const codigoFuenteFile = document.getElementById('codigo_fuente').files[0];
                const manualTecnicoFile = document.getElementById('manual_tecnico').files[0];
                const manualUsuarioFile = document.getElementById('manual_usuario').files[0];
                const archivoBdFile = document.getElementById('archivo_bd')?.files[0];
                const imagenFile = document.getElementById('imagen').files[0];

                const numeroVersion = document.getElementById('numero_version').value;

                // Mostrar modal con 3 barras de progreso
                Swal.fire({
                    title: `<i class="ti ti-upload me-2"></i>Subiendo Versión ${numeroVersion}`,
                    html: `
                        <div class="text-start">
                            <!-- Código Fuente -->
                            <div class="upload-progress-item">
                                <div class="progress-label">
                                    <div class="file-info">
                                        <span><i class="ti ti-file-zip text-primary me-1"></i><strong>Código Fuente</strong></span>
                                        <span class="file-name">${codigoFuenteFile.name}</span>
                                        <span class="file-size">${formatBytes(codigoFuenteFile.size)}</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div id="progress-codigo" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 0%">0%</div>
                                </div>
                                <div class="progress-status">
                                    <span id="status-codigo">Esperando...</span>
                                    <span id="speed-codigo"></span>
                                </div>
                            </div>

                            ${archivoBdFile ? `
                                        <div class="upload-progress-item">
                                            <div class="progress-label">
                                                <div class="file-info">
                                                    <span><i class="ti ti-database text-warning me-1"></i><strong>Archivo Base de Datos</strong></span>
                                                    <span class="file-name">${archivoBdFile.name}</span>
                                                    <span class="file-size">${formatBytes(archivoBdFile.size)}</span>
                                                </div>
                                            </div>
                                            <div class="progress">
                                                <div id="progress-archivoBd" class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width: 0%">0%</div>
                                            </div>
                                            <div class="progress-status"><span id="status-archivoBd">Esperando...</span></div>
                                        </div>` : ''}

                            <!-- Manual Técnico -->
                            ${manualTecnicoFile ? `
                                            <div class="upload-progress-item">
                                                <div class="progress-label">
                                                    <div class="file-info">
                                                        <span><i class="ti ti-file-text text-success me-1"></i><strong>Manual Técnico</strong></span>
                                                        <span class="file-name">${manualTecnicoFile.name}</span>
                                                        <span class="file-size">${formatBytes(manualTecnicoFile.size)}</span>
                                                    </div>
                                                </div>
                                                <div class="progress">
                                                    <div id="progress-tecnico" class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 0%">0%</div>
                                                </div>
                                                <div class="progress-status"><span id="status-tecnico">Esperando...</span></div>
                                            </div>` : ''}

                            <!-- Manual Usuario -->
                            ${manualUsuarioFile ? `
                                            <div class="upload-progress-item">
                                                <div class="progress-label">
                                                    <div class="file-info">
                                                        <span><i class="ti ti-file-description text-info me-1"></i><strong>Manual Usuario</strong></span>
                                                        <span class="file-name">${manualUsuarioFile.name}</span>
                                                        <span class="file-size">${formatBytes(manualUsuarioFile.size)}</span>
                                                    </div>
                                                </div>
                                                <div class="progress">
                                                    <div id="progress-usuario" class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: 0%">0%</div>
                                                </div>
                                                <div class="progress-status"><span id="status-usuario">Esperando...</span></div>
                                            </div>` : ''}
                        </div>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    width: '600px'
                });

                try {
                    // PASO 1: Iniciar upload con metadata
                    const initFormData = new FormData();
                    initFormData.append('numero_version', numeroVersion);
                    initFormData.append('fecha_lanzamiento', document.getElementById(
                        'fecha_lanzamiento').value);
                    initFormData.append('estado', document.getElementById('estado').value);
                    initFormData.append('descripcion', document.getElementById('descripcion').value);

                    // Checkboxes
                    document.querySelectorAll('.tecnologia-checkbox:checked').forEach(cb => {
                        initFormData.append('tecnologias[]', cb.value);
                    });
                    document.querySelectorAll('.servidor-checkbox:checked').forEach(cb => {
                        initFormData.append('servidores[]', cb.value);
                    });
                    document.querySelectorAll('.bd-checkbox:checked').forEach(cb => {
                        initFormData.append('bases_datos[]', cb.value);
                    });
                    document.querySelectorAll('.cred-checkbox:checked').forEach(cb => {
                        initFormData.append('credenciales[]', cb.value);
                    });

                    // Metadata de archivos
                    initFormData.append('codigo_fuente_nombre', codigoFuenteFile.name);
                    initFormData.append('codigo_fuente_tamano', codigoFuenteFile.size);
                    initFormData.append('codigo_fuente_tipo', codigoFuenteFile.type ||
                        'application/octet-stream');

                    if (manualTecnicoFile) {
                        initFormData.append('manual_tecnico_nombre', manualTecnicoFile.name);
                        initFormData.append('manual_tecnico_tamano', manualTecnicoFile.size);
                        initFormData.append('manual_tecnico_tipo', manualTecnicoFile.type ||
                            'application/octet-stream');
                    }
                    if (manualUsuarioFile) {
                        initFormData.append('manual_usuario_nombre', manualUsuarioFile.name);
                        initFormData.append('manual_usuario_tamano', manualUsuarioFile.size);
                        initFormData.append('manual_usuario_tipo', manualUsuarioFile.type ||
                            'application/octet-stream');
                    }

                    if (archivoBdFile) {
                        initFormData.append('archivo_bd_nombre', archivoBdFile.name);
                        initFormData.append('archivo_bd_tamano', archivoBdFile.size);
                        initFormData.append('archivo_bd_tipo', archivoBdFile.type ||
                            'application/octet-stream');
                    }

                    // Imagen (pequeña, se sube directo)
                    if (imagenFile) {
                        initFormData.append('imagen', imagenFile);
                    }

                    // ✅ DOCUMENTOS ADICIONALES - VERSIÓN ALTERNATIVA
                    if (toggleDocumentos && toggleDocumentos.checked) {
                        const documentos = documentosContainer.querySelectorAll('.documento-item');

                        console.log('═══════════════════════════════════════════════');
                        console.log('🔍 PREPARANDO DOCUMENTOS ADICIONALES');
                        console.log('═══════════════════════════════════════════════');
                        console.log('Total elementos .documento-item:', documentos.length);

                        const documentosIds = [];
                        const documentosArchivos = [];

                        documentos.forEach((doc, idx) => {
                            const select = doc.querySelector('.documento-nombre');
                            const fileInput = doc.querySelector('.documento-archivo');

                            console.log(`\n--- Documento #${idx} ---`);
                            console.log('SELECT value:', select ? select.value : 'N/A');
                            console.log('FILE INPUT files:', fileInput ? fileInput.files
                                .length : 0);

                            if (select && select.value && fileInput && fileInput.files &&
                                fileInput.files.length > 0) {
                                const file = fileInput.files[0];

                                console.log('✅ VÁLIDO');
                                console.log('   - documento_id:', select.value);
                                console.log('   - archivo:', file.name);
                                console.log('   - size:', file.size);

                                documentosIds.push(select.value);
                                documentosArchivos.push(file);
                            } else {
                                console.warn('⚠️ INVÁLIDO - Saltado');
                            }
                        });

                        console.log('\n📊 RESUMEN:');
                        console.log('   IDs:', documentosIds);
                        console.log('   Archivos:', documentosArchivos.map(f => f.name));

                        // ✅ ENVIAR IDS COMO ARRAY SIMPLE
                        documentosIds.forEach((id, index) => {
                            initFormData.append(`documentos_ids[]`, id);
                        });

                        // ✅ ENVIAR ARCHIVOS COMO ARRAY SIMPLE
                        documentosArchivos.forEach((file, index) => {
                            initFormData.append(`documentos_archivos[]`, file, file.name);
                        });

                        console.log('\n📋 Agregado al FormData:');
                        console.log('   documentos_ids[]:', documentosIds);
                        console.log('   documentos_archivos[]:', documentosArchivos.length +
                            ' archivos');
                        console.log('═══════════════════════════════════════════════\n');
                    }


                    const initResponse = await fetch(
                        `/admin/sistemas/${sistemaId}/versiones/iniciar-upload`, {
                            method: 'POST',
                            body: initFormData,
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                    const initData = await initResponse.json();

                    if (!initData.success) {
                        throw new Error(initData.message || 'Error al iniciar upload');
                    }

                    const uploadId = initData.upload_id;
                    console.log('✅ Upload iniciado:', uploadId);

                    // PASO 2: Función para actualizar progreso
                    const updateProgress = (id, progress, current, total, bytes, totalBytes) => {
                        const bar = document.getElementById(`progress-${id}`);
                        const status = document.getElementById(`status-${id}`);

                        if (bar) {
                            bar.style.width = progress + '%';
                            bar.textContent = progress + '%';

                            if (progress >= 100) {
                                bar.classList.remove('progress-bar-striped',
                                    'progress-bar-animated');
                            }
                        }

                        if (status) {
                            if (progress >= 100) {
                                status.innerHTML =
                                    '<i class="ti ti-check text-success me-1"></i>Completado';
                            } else {
                                status.textContent =
                                    `Chunk ${current}/${total} • ${formatBytes(bytes)} / ${formatBytes(totalBytes)}`;
                            }
                        }
                    };

                    // PASO 3: Subir archivos en paralelo (manuales opcionales)
                    const uploadPromises = [
                        uploadFileInChunks(
                            codigoFuenteFile,
                            uploadId,
                            'codigo_fuente',
                            CHUNK_SIZE_CODIGO,
                            (data) => updateProgress('codigo', data.progress, data.chunkIndex,
                                data.totalChunks, data.bytesUploaded, data.totalBytes)
                        )
                    ];

                    if (manualTecnicoFile) {
                        uploadPromises.push(uploadFileInChunks(
                            manualTecnicoFile,
                            uploadId,
                            'manual_tecnico',
                            CHUNK_SIZE_MANUAL,
                            (data) => updateProgress('tecnico', data.progress, data.chunkIndex,
                                data.totalChunks, data.bytesUploaded, data.totalBytes)
                        ));
                    }

                    if (manualUsuarioFile) {
                        uploadPromises.push(uploadFileInChunks(
                            manualUsuarioFile,
                            uploadId,
                            'manual_usuario',
                            CHUNK_SIZE_MANUAL,
                            (data) => updateProgress('usuario', data.progress, data.chunkIndex,
                                data.totalChunks, data.bytesUploaded, data.totalBytes)
                        ));
                    }

                    if (archivoBdFile) {
                        uploadPromises.push(uploadFileInChunks(
                            archivoBdFile,
                            uploadId,
                            'archivo_bd',
                            CHUNK_SIZE_MANUAL, // 2MB por chunk
                            (data) => updateProgress('archivoBd', data.progress, data
                                .chunkIndex,
                                data.totalChunks, data.bytesUploaded, data.totalBytes)
                        ));
                    }

                    const results = await Promise.all(uploadPromises);
                    const codigoIdentifier = results[0];
                    let idx = 1;
                    const tecnicoIdentifier = manualTecnicoFile ? results[idx++] : null;
                    const usuarioIdentifier = manualUsuarioFile ? results[idx++] : null;
                    const archivoBdIdentifier = archivoBdFile ? results[idx++] : null;

                    console.log('✅ Todos los archivos subidos');
                    console.log('  - Código:', codigoIdentifier);
                    console.log('  - Manual Técnico:', tecnicoIdentifier);
                    console.log('  - Manual Usuario:', usuarioIdentifier);

                    // PASO 4: Completar upload
                    const completeResponse = await fetch(
                        `/admin/sistemas/${sistemaId}/versiones/completar-upload`, {
                            method: 'POST',
                            body: JSON.stringify({
                                upload_id: uploadId,
                                codigo_identifier: codigoIdentifier,
                                manual_tecnico_identifier: tecnicoIdentifier,
                                manual_usuario_identifier: usuarioIdentifier,
                                archivo_bd_identifier: archivoBdIdentifier,
                            }),
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });

                    const completeData = await completeResponse.json();

                    if (!completeData.success) {
                        throw new Error(completeData.message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Archivos Subidos!',
                        html: `
                            <p>La versión <strong>${numeroVersion}</strong> se está procesando en segundo plano.</p>
                            <p class="text-muted">Serás redirigido en unos segundos...</p>
                        `,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href =
                            "{{ route('admin.sistemas.versiones.index', $sistema) }}";
                    });

                } catch (error) {
                    console.error('❌ Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                        confirmButtonColor: '#6366f1'
                    });
                }
            });

            // ========== HELPERS DE UI ==========
            function setupSearch(searchId, containerId) {
                const searchInput = document.getElementById(searchId);
                const container = document.getElementById(containerId);
                if (!searchInput || !container) return;

                searchInput.addEventListener('input', function() {
                    const term = this.value.toLowerCase();
                    container.querySelectorAll('.checkbox-horizontal-item').forEach(item => {
                        const label = item.querySelector('label');
                        if (label) {
                            item.classList.toggle('hidden', !label.textContent.toLowerCase()
                                .includes(term));
                        }
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

                    document.querySelectorAll(`.${extraClass}`).forEach(item => {
                        item.classList.toggle('hidden', !expanded);
                    });

                    this.querySelector('i').className = expanded ? 'ti ti-chevron-up' :
                        'ti ti-chevron-down';
                    this.innerHTML = expanded ?
                        `<i class="ti ti-chevron-up"></i> Ver menos` :
                        `<i class="ti ti-chevron-down"></i> Ver todas`;
                });
            }

            setupShowMore('showMoreTecnologias', 'tecnologia-extra');
            setupShowMore('showMoreServidores', 'servidor-extra');
            setupShowMore('showMoreBD', 'bd-extra');
            setupShowMore('showMoreCreds', 'cred-extra');

            function updateCount(checkboxClass, countId) {
                const checked = document.querySelectorAll(`.${checkboxClass}:checked`).length;
                const counter = document.getElementById(countId);
                if (counter) {
                    const word = checkboxClass.includes('servidor') ? 'seleccionado' : 'seleccionada';
                    counter.textContent = `${checked} ${checked === 1 ? word : word + 's'}`;
                }
            }

            ['tecnologia', 'servidor', 'bd', 'cred'].forEach(type => {
                document.querySelectorAll(`.${type}-checkbox`).forEach(cb => {
                    const countId = type === 'bd' ? 'bd-count' :
                        type === 'cred' ? 'creds-count' :
                        type === 'servidor' ? 'servidores-count' :
                        'tecnologias-count';

                    cb.addEventListener('change', () => updateCount(`${type}-checkbox`, countId));
                });
            });

            // Inicializar contadores
            updateCount('tecnologia-checkbox', 'tecnologias-count');
            updateCount('servidor-checkbox', 'servidores-count');
            updateCount('bd-checkbox', 'bd-count');
            updateCount('cred-checkbox', 'creds-count');

        });
    </script>
@endsection
