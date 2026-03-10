@extends('layouts.vertical', ['title' => 'Editar Versión'])

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
                                        {{ old('estado', $version->estado) == 'beta' ? 'selected' : '' }}>Beta</option>
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
                                <small class="text-muted">Desmarcará automáticamente otras versiones como actuales</small>
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

                            {{-- Imagen (OPCIONAL) --}}
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
                                <small class="text-muted">Dejar vacío para mantener la imagen actual. Máximo 2MB. Formatos:
                                    JPG, PNG, GIF</small>
                                <div class="invalid-feedback">El archivo debe ser una imagen válida (máx. 2MB)</div>
                            </div>

                            {{-- Código Fuente --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Código Fuente</label>

                                @if ($version->codigo_fuente)
                                    <div class="alert alert-info py-2 mb-2">
                                        <i class="ti ti-file-zip me-1"></i>
                                        Archivo actual: <strong>{{ basename($version->codigo_fuente) }}</strong>
                                        <a href="{{ asset('storage/' . $version->codigo_fuente) }}"
                                            class="btn btn-sm btn-info ms-2" download>
                                            <i class="ti ti-download"></i> Descargar
                                        </a>
                                    </div>
                                @endif

                                <input type="file" name="codigo_fuente" id="codigo_fuente" class="form-control"
                                    accept=".zip,.rar">
                                <small class="text-muted">Dejar vacío para mantener el archivo actual. Máximo 10GB.
                                    Formatos: ZIP, RAR</small>
                                <div class="invalid-feedback">Error en el código fuente</div>
                            </div>

                            {{-- Manual Técnico --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manual Técnico</label>

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
                                @if ($version->manual_tecnico)
                                    <small class="text-muted d-block mt-1">
                                        Actual: <a href="{{ Storage::url($version->manual_tecnico) }}"
                                            target="_blank">{{ basename($version->manual_tecnico) }}</a>
                                    </small>
                                @endif
                                <small class="text-muted">Formato: PDF • Tamaño máximo: 100MB • Dejar vacío para mantener
                                    el actual</small>
                                <div class="invalid-feedback"> El manual técnico debe ser un archivo PDF (máximo 100MB)
                                </div>
                            </div>

                            {{-- Manual de Usuario --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Manual de Usuario</label>

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
                                @if ($version->manual_usuario)
                                    <small class="text-muted d-block mt-1">
                                        Actual: <a href="{{ Storage::url($version->manual_usuario) }}"
                                            target="_blank">{{ basename($version->manual_usuario) }}</a>
                                    </small>
                                @endif
                                <small class="text-muted">Formato: PDF • Tamaño máximo: 100MB • Dejar vacío para mantener
                                    el actual</small>
                                <div class="invalid-feedback">El manual de usuario debe ser un archivo PDF (máximo 100MB)
                                </div>
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
                            <i class="ti ti-key me-1"></i>Credenciales <span class="text-danger">*</span>
                        </h4>
                        <span class="selected-count" id="creds-count">{{ $version->credenciales->count() }}
                            seleccionadas</span>
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
                                <i class="ti ti-device-floppy me-1"></i>Actualizar Versión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    @include('admin.sistemas.versiones.documentos-adicionales-edit')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- edit.blade.php --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('editVersionForm');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const sistemaId = {{ $sistema->id }};
            const versionIdActual = {{ $version->id }};

            // Tamaños de chunk
            const CHUNK_SIZE_CODIGO = 5 * 1024 * 1024; // 5MB para código fuente
            const CHUNK_SIZE_MANUAL = 2 * 1024 * 1024; // 2MB para manuales

            let versionCheckTimeout;
            let currentUploadId = null;

            // ========== VALIDACIÓN DE VERSIÓN CON FORMATO OBLIGATORIO X.X.X ==========
            const numeroVersionInput = document.getElementById('numero_version');
            const versionChecking = document.createElement('div');
            versionChecking.id = 'version-checking';
            versionChecking.className = 'text-primary small mt-1';
            versionChecking.style.display = 'none';
            versionChecking.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span>Verificando disponibilidad...';
            numeroVersionInput.parentNode.appendChild(versionChecking);

            if (numeroVersionInput) {
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
                                    `/admin/sistemas/${sistemaId}/versiones/check-duplicate?numero=${encodeURIComponent(newValue)}&exclude=${versionIdActual}`;
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

            const toggleDocumentos = document.getElementById('toggleDocumentos');
            const documentosModal = new bootstrap.Modal(document.getElementById('documentosAdicionalesModal'));
            const documentosNuevosContainer = document.getElementById('documentosNuevosContainer');
            const documentosExistentesContainer = document.getElementById('documentosExistentesContainer');
            const addDocumentoBtn = document.getElementById('addDocumentoBtn');
            const guardarDocumentosBtn = document.getElementById('guardarDocumentosBtn');

            let documentoNuevoCounter = 0;
            let documentosEliminados = []; // IDs de documentos marcados para eliminar

            // Documentos disponibles (pasados desde el backend)
            let documentosTipos = @json($documentos ?? []);

            console.log('📄 Documentos disponibles:', documentosTipos);

            // ═══════════════════════════════════════════════════════════════
            // TOGGLE: Abrir modal
            // ═══════════════════════════════════════════════════════════════

            toggleDocumentos?.addEventListener('change', function() {
                if (this.checked) {
                    documentosModal.show();
                } else {
                    // Confirmar si hay cambios pendientes
                    const hayNuevos = documentosNuevosContainer.querySelectorAll('.documento-item').length >
                        0;
                    const hayEliminados = documentosEliminados.length > 0;

                    if (hayNuevos || hayEliminados) {
                        Swal.fire({
                            title: '¿Descartar cambios?',
                            text: 'Hay cambios sin guardar en los documentos',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, descartar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                resetearDocumentos();
                            } else {
                                this.checked = true;
                            }
                        });
                    }
                }
            });

            // ═══════════════════════════════════════════════════════════════
            // FUNCIÓN: Agregar nuevo documento
            // ═══════════════════════════════════════════════════════════════

            function addDocumentoNuevo() {
                documentoNuevoCounter++;
                const docId = `doc_nuevo_${documentoNuevoCounter}`;

                const docHtml = `
        <div class="documento-item documento-nuevo" id="${docId}" data-doc-id="${documentoNuevoCounter}">
            <div class="row g-2">
                
                <!-- Selector de Tipo -->
                <div class="col-12">
                    <label class="form-label fw-semibold mb-1">
                        Tipo de Documento <span class="text-danger">*</span>
                    </label>
                    <select 
                        class="form-select form-select-sm documento-nombre-nuevo" 
                        name="documentos_nuevos[${documentoNuevoCounter}][documento_id]"
                        required
                    >
                        <option value="">Seleccionar...</option>
                        ${documentosTipos.map(doc => `
                                        <option value="${doc.id}">${doc.nombre}</option>
                                    `).join('')}
                    </select>
                    <div class="invalid-feedback">Seleccione un tipo</div>
                </div>

                <!-- Input de Archivo -->
                <div class="col-12">
                    <label class="form-label fw-semibold mb-1">
                        Archivo <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="file" 
                        class="form-control form-control-sm documento-archivo-nuevo" 
                        name="documentos_nuevos[${documentoNuevoCounter}][archivo]"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                        required
                    >
                    <div class="invalid-feedback">El archivo es obligatorio</div>
                    <small class="text-muted">PDF, Word, Excel, ZIP • Máx: 50MB</small>
                </div>

                <!-- Botón Eliminar -->
                <div class="col-12 text-end">
                    <button 
                        type="button" 
                        class="btn btn-sm btn-danger remove-documento-nuevo-btn" 
                        data-doc-id="${docId}"
                    >
                        <i class="ti ti-trash me-1"></i>
                        Quitar
                    </button>
                </div>

            </div>
        </div>
    `;

                documentosNuevosContainer.insertAdjacentHTML('beforeend', docHtml);

                // Validación del archivo
                const newFileInput = document.querySelector(`#${docId} .documento-archivo-nuevo`);
                newFileInput.addEventListener('change', function() {
                    validateDocumentoFile(this);
                });

                // Validación del selector
                const newSelect = document.querySelector(`#${docId} .documento-nombre-nuevo`);
                newSelect.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                    if (this.value) {
                        this.classList.add('is-valid');
                    }
                });
            }

            // ═══════════════════════════════════════════════════════════════
            // FUNCIÓN: Validar archivo
            // ═══════════════════════════════════════════════════════════════

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
                    if (feedback?.classList.contains('invalid-feedback')) {
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
                    if (feedback?.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Formato no permitido';
                    }
                    return false;
                }

                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }

            // ═══════════════════════════════════════════════════════════════
            // BOTÓN: Agregar documento nuevo
            // ═══════════════════════════════════════════════════════════════

            addDocumentoBtn?.addEventListener('click', function() {
                addDocumentoNuevo();
            });

            // ═══════════════════════════════════════════════════════════════
            // DELEGACIÓN: Eliminar documento NUEVO
            // ═══════════════════════════════════════════════════════════════

            documentosNuevosContainer?.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-documento-nuevo-btn');
                if (removeBtn) {
                    const docId = removeBtn.dataset.docId;
                    document.getElementById(docId)?.remove();
                    documentoNuevoCounter--;
                }
            });

            // ═══════════════════════════════════════════════════════════════
            // DELEGACIÓN: Eliminar documento EXISTENTE
            // ═══════════════════════════════════════════════════════════════

            documentosExistentesContainer?.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.btn-eliminar-existente');
                if (removeBtn) {
                    const documentoId = removeBtn.dataset.documentoId;
                    const documentoNombre = removeBtn.dataset.documentoNombre;

                    Swal.fire({
                        title: '¿Eliminar documento?',
                        html: `¿Está seguro de eliminar <strong>${documentoNombre}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Marcar como eliminado
                            const docElement = removeBtn.closest('.documento-item');
                            docElement.style.opacity = '0.5';
                            docElement.style.textDecoration = 'line-through';

                            // Habilitar el input hidden para enviar al servidor
                            const hiddenInput = docElement.querySelector(
                                '.documento-eliminar-flag');
                            hiddenInput.value = documentoId;
                            hiddenInput.disabled = false;

                            // Deshabilitar botones
                            removeBtn.disabled = true;
                            docElement.querySelector('.btn-info').disabled = true;

                            // Agregar a la lista de eliminados
                            documentosEliminados.push(documentoId);

                            console.log('📝 Documento marcado para eliminar:', documentoId);
                        }
                    });
                }
            });

            // ═══════════════════════════════════════════════════════════════
            // BOTÓN: Guardar cambios
            // ═══════════════════════════════════════════════════════════════

            guardarDocumentosBtn?.addEventListener('click', function() {
                // Validar documentos nuevos
                const documentosNuevos = documentosNuevosContainer.querySelectorAll('.documento-item');
                let todosValidos = true;

                documentosNuevos.forEach(doc => {
                    const select = doc.querySelector('.documento-nombre-nuevo');
                    const fileInput = doc.querySelector('.documento-archivo-nuevo');

                    if (!select.value) {
                        select.classList.add('is-invalid');
                        todosValidos = false;
                    }

                    if (!validateDocumentoFile(fileInput)) {
                        todosValidos = false;
                    }
                });

                if (!todosValidos) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Documentos incompletos',
                        text: 'Complete todos los campos de los nuevos documentos',
                        confirmButtonColor: '#6366f1'
                    });
                    return;
                }

                // Cerrar modal
                documentosModal.hide();

                // Mostrar resumen
                const totalNuevos = documentosNuevos.length;
                const totalEliminados = documentosEliminados.length;

                if (totalNuevos > 0 || totalEliminados > 0) {
                    let mensaje = '<ul class="text-start mb-0">';
                    if (totalNuevos > 0) {
                        mensaje +=
                            `<li class="text-success"><i class="ti ti-plus me-1"></i>${totalNuevos} documento(s) nuevo(s)</li>`;
                    }
                    if (totalEliminados > 0) {
                        mensaje +=
                            `<li class="text-danger"><i class="ti ti-trash me-1"></i>${totalEliminados} documento(s) a eliminar</li>`;
                    }
                    mensaje += '</ul>';

                    Swal.fire({
                        icon: 'success',
                        title: 'Cambios guardados',
                        html: mensaje,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });

            // ═══════════════════════════════════════════════════════════════
            // FUNCIÓN: Resetear documentos
            // ═══════════════════════════════════════════════════════════════

            function resetearDocumentos() {
                // Limpiar nuevos
                documentosNuevosContainer.innerHTML = '';
                documentoNuevoCounter = 0;

                // Restaurar existentes marcados como eliminados
                documentosEliminados.forEach(id => {
                    const docElement = documentosExistentesContainer.querySelector(
                        `[data-documento-id="${id}"]`);
                    if (docElement) {
                        docElement.style.opacity = '1';
                        docElement.style.textDecoration = 'none';
                        docElement.querySelector('.documento-eliminar-flag').disabled = true;
                        docElement.querySelector('.btn-eliminar-existente').disabled = false;
                        docElement.querySelector('.btn-info').disabled = false;
                    }
                });

                documentosEliminados = [];
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

            // ========== VALIDACIÓN DE ARCHIVOS OPCIONALES (EN EDIT) ==========
            function validateFileRequired(input, maxSize, allowedTypes = null, isRequired = false) {
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
                validateFileRequired(this, 10240, ['zip', 'rar'], false);
            });

            document.getElementById('manual_tecnico')?.addEventListener('change', function() {
                validateFileRequired(this, 100, ['pdf'], false);
            });

            document.getElementById('manual_usuario')?.addEventListener('change', function() {
                validateFileRequired(this, 100, ['pdf'], false);
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

            document.querySelectorAll('.cred-checkbox').forEach(cb => {
                cb.addEventListener('change', () => validateCheckboxGroup('cred-checkbox', 'creds-error'));
            });

            // ========== FUNCIÓN DE VALIDACIÓN COMPLETA ==========
            function validateForm() {
                let isFormValid = true;
                const errors = [];

                const numeroVersion = document.getElementById('numero_version');
                const versionValue = numeroVersion.value.trim();

                if (!versionValue) {
                    numeroVersion.classList.add('is-invalid');
                    errors.push('Número de versión');
                    isFormValid = false;
                } else if (versionValue.length < 5 || !/^\d\.\d\.\d$/.test(versionValue)) {
                    numeroVersion.classList.add('is-invalid');
                    numeroVersion.classList.remove('is-valid');
                    errors.push('Número de versión (formato incompleto)');
                    isFormValid = false;
                } else if (numeroVersion.classList.contains('is-invalid')) {
                    errors.push('Número de versión (duplicado)');
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

                if (!validateCheckboxGroup('cred-checkbox', 'creds-error')) {
                    errors.push('Credenciales');
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
                const imagenFile = document.getElementById('imagen').files[0];

                const numeroVersion = document.getElementById('numero_version').value;

                // ✅ VERIFICAR SI HAY ARCHIVOS GRANDES NUEVOS
                const hayCodigoNuevo = codigoFuenteFile && codigoFuenteFile.size > 0;
                const hayTecnicoNuevo = manualTecnicoFile && manualTecnicoFile.size > 0;
                const hayUsuarioNuevo = manualUsuarioFile && manualUsuarioFile.size > 0;

                const hayArchivosGrandes = hayCodigoNuevo || hayTecnicoNuevo || hayUsuarioNuevo;

                if (hayArchivosGrandes) {
                    // ========== MODO CHUNKS: Subir archivos grandes ==========

                    const formData = new FormData();
                    formData.append('numero_version', numeroVersion);
                    formData.append('fecha_lanzamiento', document.getElementById('fecha_lanzamiento')
                        .value);
                    formData.append('estado', document.getElementById('estado').value);
                    formData.append('descripcion', document.getElementById('descripcion').value);
                    formData.append('version_id', versionIdActual);
                    formData.append('es_actual', document.getElementById('es_actual')?.checked ? 1 : 0);

                    // Checkboxes
                    document.querySelectorAll('.tecnologia-checkbox:checked').forEach(cb => {
                        formData.append('tecnologias[]', cb.value);
                    });
                    document.querySelectorAll('.servidor-checkbox:checked').forEach(cb => {
                        formData.append('servidores[]', cb.value);
                    });
                    document.querySelectorAll('.bd-checkbox:checked').forEach(cb => {
                        formData.append('bases_datos[]', cb.value);
                    });
                    document.querySelectorAll('.cred-checkbox:checked').forEach(cb => {
                        formData.append('credenciales[]', cb.value);
                    });


                    // ✅ DOCUMENTOS ADICIONALES - NUEVOS
                    if (toggleDocumentos && toggleDocumentos.checked) {
                        const documentosNuevos = documentosNuevosContainer.querySelectorAll(
                            '.documento-item');

                        console.log('═══════════════════════════════════════════════');
                        console.log('📄 DOCUMENTOS ADICIONALES - EDIT MODE');
                        console.log('═══════════════════════════════════════════════');
                        console.log('Total documentos nuevos:', documentosNuevos.length);
                        console.log('Total documentos a eliminar:', documentosEliminados.length);

                        // Arrays separados para enviar
                        const documentosNuevosIds = [];
                        const documentosNuevosArchivos = [];

                        documentosNuevos.forEach((doc, idx) => {
                            const select = doc.querySelector('.documento-nombre-nuevo');
                            const fileInput = doc.querySelector('.documento-archivo-nuevo');

                            console.log(`Documento nuevo #${idx}:`, {
                                tiene_select: !!select,
                                select_value: select ? select.value : 'N/A',
                                tiene_file: fileInput ? fileInput.files.length : 0
                            });

                            if (select && select.value && fileInput && fileInput.files &&
                                fileInput.files.length > 0) {
                                const file = fileInput.files[0];

                                console.log(`✅ Documento nuevo #${idx} VÁLIDO:`, {
                                    documento_id: select.value,
                                    archivo: file.name,
                                    size: file.size
                                });

                                documentosNuevosIds.push(select.value);
                                documentosNuevosArchivos.push(file);
                            }
                        });

                        // Enviar IDs y archivos
                        documentosNuevosIds.forEach(id => {
                            formData.append('documentos_nuevos_ids[]', id);
                        });

                        documentosNuevosArchivos.forEach(file => {
                            formData.append('documentos_nuevos_archivos[]', file, file.name);
                        });

                        // Enviar IDs de documentos a eliminar
                        documentosEliminados.forEach(id => {
                            formData.append('documentos_eliminar[]', id);
                        });

                        console.log('📊 RESUMEN:');
                        console.log('   Documentos nuevos:', documentosNuevosIds.length);
                        console.log('   Documentos a eliminar:', documentosEliminados.length);
                        console.log('═══════════════════════════════════════════════\n');
                    }

                    // Metadata de archivos grandes
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

                    // Imagen (pequeña, se sube directo)
                    if (imagenFile) {
                        formData.append('imagen', imagenFile);
                    }

                    try {
                        console.log('📝 Creando registro de upload...');

                        const initResponse = await fetch(
                            `/admin/sistemas/${sistemaId}/versiones/iniciar-upload`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                }
                            });

                        const initData = await initResponse.json();

                        if (!initData.success) {
                            throw new Error(initData.message || 'Error al iniciar upload');
                        }

                        currentUploadId = initData.upload_id;
                        console.log('✅ Upload iniciado:', currentUploadId);

                        // Mostrar modal con barras de progreso
                        let htmlBars = '<div class="text-start">';

                        if (hayCodigoNuevo) {
                            htmlBars += `
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
                            </div>
                        </div>
                    `;
                        }

                        if (hayTecnicoNuevo) {
                            htmlBars += `
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
                            <div class="progress-status">
                                <span id="status-tecnico">Esperando...</span>
                            </div>
                        </div>
                    `;
                        }

                        if (hayUsuarioNuevo) {
                            htmlBars += `
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
                            <div class="progress-status">
                                <span id="status-usuario">Esperando...</span>
                            </div>
                        </div>
                    `;
                        }

                        htmlBars += '</div>';

                        Swal.fire({
                            title: `<i class="ti ti-upload me-2"></i>Actualizando Versión ${numeroVersion}`,
                            html: htmlBars,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            width: '600px'
                        });

                        // Función para actualizar progreso
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

                        // Subir archivos en paralelo
                        const uploadPromises = [];
                        const identifiers = {};

                        if (hayCodigoNuevo) {
                            uploadPromises.push(
                                uploadFileInChunks(
                                    codigoFuenteFile,
                                    currentUploadId,
                                    'codigo_fuente',
                                    CHUNK_SIZE_CODIGO,
                                    (data) => updateProgress('codigo', data.progress, data
                                        .chunkIndex, data.totalChunks, data.bytesUploaded, data
                                        .totalBytes)
                                ).then(id => {
                                    identifiers.codigo = id;
                                })
                            );
                        }

                        if (hayTecnicoNuevo) {
                            uploadPromises.push(
                                uploadFileInChunks(
                                    manualTecnicoFile,
                                    currentUploadId,
                                    'manual_tecnico',
                                    CHUNK_SIZE_MANUAL,
                                    (data) => updateProgress('tecnico', data.progress, data
                                        .chunkIndex, data.totalChunks, data.bytesUploaded, data
                                        .totalBytes)
                                ).then(id => {
                                    identifiers.tecnico = id;
                                })
                            );
                        }

                        if (hayUsuarioNuevo) {
                            uploadPromises.push(
                                uploadFileInChunks(
                                    manualUsuarioFile,
                                    currentUploadId,
                                    'manual_usuario',
                                    CHUNK_SIZE_MANUAL,
                                    (data) => updateProgress('usuario', data.progress, data
                                        .chunkIndex, data.totalChunks, data.bytesUploaded, data
                                        .totalBytes)
                                ).then(id => {
                                    identifiers.usuario = id;
                                })
                            );
                        }

                        await Promise.all(uploadPromises);

                        console.log('✅ Todos los archivos subidos:', identifiers);

                        // Completar upload
                        const completeResponse = await fetch(
                            `/admin/sistemas/${sistemaId}/versiones/completar-upload`, {
                                method: 'POST',
                                body: JSON.stringify({
                                    upload_id: currentUploadId,
                                    codigo_identifier: identifiers.codigo || null,
                                    manual_tecnico_identifier: identifiers.tecnico || null,
                                    manual_usuario_identifier: identifiers.usuario || null
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
                            title: '¡Actualización Completada!',
                            html: `<p>La versión <strong>${numeroVersion}</strong> se está procesando.</p>`,
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

                } else {
                    // ========== MODO NORMAL: Sin archivos grandes, submit directo ==========
                    // ✅ VERIFICAR SI HAY DOCUMENTOS ADICIONALES
                    if (toggleDocumentos && toggleDocumentos.checked) {
                        const documentosNuevos = documentosNuevosContainer.querySelectorAll(
                            '.documento-item');

                        console.log('═══════════════════════════════════════════════');
                        console.log('📄 DOCUMENTOS ADICIONALES - MODO DIRECTO');
                        console.log('═══════════════════════════════════════════════');
                        console.log('Total documentos nuevos:', documentosNuevos.length);
                        console.log('Total documentos a eliminar:', documentosEliminados.length);

                        // ✅ CREAR INPUTS OCULTOS PARA DOCUMENTOS
                        // Limpiar inputs previos si existen
                        form.querySelectorAll('input[name^="documentos_nuevos_"]').forEach(input =>
                            input.remove());
                        form.querySelectorAll('input[name^="documentos_eliminar"]').forEach(input =>
                            input.remove());

                        // Arrays para los documentos
                        let docIndex = 0;

                        documentosNuevos.forEach((doc, idx) => {
                            const select = doc.querySelector('.documento-nombre-nuevo');
                            const fileInput = doc.querySelector('.documento-archivo-nuevo');

                            console.log(`Documento nuevo #${idx}:`, {
                                tiene_select: !!select,
                                select_value: select ? select.value : 'N/A',
                                tiene_file: fileInput ? fileInput.files.length : 0
                            });

                            if (select && select.value && fileInput && fileInput.files &&
                                fileInput.files.length > 0) {
                                console.log(
                                    `✅ Documento nuevo #${idx} VÁLIDO - Agregando al formulario`
                                    );

                                // ✅ CREAR INPUT PARA ID
                                const inputId = document.createElement('input');
                                inputId.type = 'hidden';
                                inputId.name = `documentos_nuevos_ids[]`;
                                inputId.value = select.value;
                                form.appendChild(inputId);

                                // ✅ CREAR INPUT PARA ARCHIVO
                                const inputFile = document.createElement('input');
                                inputFile.type = 'file';
                                inputFile.name = `documentos_nuevos_archivos[]`;
                                inputFile.style.display = 'none';

                                // Transferir el archivo
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(fileInput.files[0]);
                                inputFile.files = dataTransfer.files;

                                form.appendChild(inputFile);

                                console.log(
                                    `   ✅ Agregado: ID=${select.value}, Archivo=${fileInput.files[0].name}`
                                    );
                                docIndex++;
                            }
                        });

                        // ✅ AGREGAR IDS DE DOCUMENTOS A ELIMINAR
                        documentosEliminados.forEach((id, idx) => {
                            const inputEliminar = document.createElement('input');
                            inputEliminar.type = 'hidden';
                            inputEliminar.name = `documentos_eliminar[]`;
                            inputEliminar.value = id;
                            form.appendChild(inputEliminar);

                            console.log(`🗑️ Marcado para eliminar: ID=${id}`);
                        });

                        console.log('📊 RESUMEN:');
                        console.log('   Documentos nuevos agregados:', docIndex);
                        console.log('   Documentos marcados para eliminar:', documentosEliminados
                            .length);
                        console.log('═══════════════════════════════════════════════\n');
                    }

                    // ✅ SUBMIT DIRECTO
                    console.log('📤 Enviando formulario...');
                    form.submit();
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
                    this.innerHTML = expanded ? `<i class="ti ti-chevron-up"></i> Ver menos` :
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

            updateCount('tecnologia-checkbox', 'tecnologias-count');
            updateCount('servidor-checkbox', 'servidores-count');
            updateCount('bd-checkbox', 'bd-count');
            updateCount('cred-checkbox', 'creds-count');

        });
    </script>
@endsection
