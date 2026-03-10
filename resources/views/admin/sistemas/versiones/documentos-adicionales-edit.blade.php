{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: GESTIONAR DOCUMENTOS ADICIONALES (EDIT) --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<style>
    .documento-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
    }

    .documento-item:last-child {
        margin-bottom: 0;
    }

    .documento-existente {
        background: #e7f3ff;
        border-color: #6366f1;
    }

    .documento-nuevo {
        background: #f0fdf4;
        border-color: #10b981;
    }

    .btn-add-documento {
        border: 2px dashed #6366f1;
        transition: all 0.3s;
    }

    .btn-add-documento:hover {
        background: #6366f1;
        color: white;
        border-style: solid;
    }

    /* ========== MODO OSCURO (FORZADO CON !important) ========== */
    [data-bs-theme="dark"] .documento-item {
        background: #2a2e3b !important;
        border-color: #3f4451 !important;
        color: #e5e7eb !important;
    }

    [data-bs-theme="dark"] .documento-existente {
        background: #1e2939 !important;
        border-color: #6366f1 !important;
    }

    [data-bs-theme="dark"] .documento-nuevo {
        background: #1a2e23 !important;
        border-color: #10b981 !important;
    }

    [data-bs-theme="dark"] .btn-add-documento {
        border-color: #6366f1 !important;
        color: #a5b4fc !important;
        background: transparent !important;
    }

    [data-bs-theme="dark"] .btn-add-documento:hover {
        background: #6366f1 !important;
        color: white !important;
    }

    [data-bs-theme="dark"] .alert-info {
        background-color: #1e3a5f !important;
        border-color: #2563eb !important;
        color: #93c5fd !important;
    }

    [data-bs-theme="dark"] .alert-success {
        background-color: #1a2e23 !important;
        border-color: #10b981 !important;
        color: #86efac !important;
    }

    /* ✅ SELECTORES MÁS ESPECÍFICOS PARA EL MODAL */
    [data-bs-theme="dark"] #documentosAdicionalesModal.modal .modal-dialog .modal-content {
        background-color: #1a1d29 !important;
        color: #e5e7eb !important;
        border-color: #3f4451 !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-header.bg-light,
    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-header {
        background-color: #2a2e3b !important;
        border-bottom-color: #3f4451 !important;
        color: #e5e7eb !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-body {
        background-color: #1a1d29 !important;
        color: #e5e7eb !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-footer {
        background-color: #2a2e3b !important;
        border-top-color: #3f4451 !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-title,
    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-title i {
        color: #e5e7eb !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .text-primary {
        color: #818cf8 !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .text-muted,
    [data-bs-theme="dark"] #documentosAdicionalesModal small.text-muted {
        color: #9ca3af !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .fw-semibold,
    [data-bs-theme="dark"] #documentosAdicionalesModal h6 {
        color: #e5e7eb !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal strong {
        color: #e5e7eb !important;
    }

    /* ✅ SELECT DROPDOWN EN MODO OSCURO */
    [data-bs-theme="dark"] #documentosAdicionalesModal select.form-select,
    [data-bs-theme="dark"] #documentosAdicionalesModal .form-select {
        background-color: #2a2e3b !important;
        border-color: #3f4451 !important;
        color: #e5e7eb !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .form-control {
        background-color: #2a2e3b !important;
        border-color: #3f4451 !important;
        color: #e5e7eb !important;
    }

    /* ✅ BOTONES EN MODO OSCURO */
    [data-bs-theme="dark"] #documentosAdicionalesModal .btn-light {
        background-color: #3f4451 !important;
        border-color: #3f4451 !important;
        color: #e5e7eb !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .btn-light:hover {
        background-color: #4a5060 !important;
    }

    /* ✅ CLOSE BUTTON */
    [data-bs-theme="dark"] #documentosAdicionalesModal .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>

{{-- Toggle para activar documentos adicionales --}}
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="toggleDocumentos"
                    {{ $version->documentos->count() > 0 ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="toggleDocumentos">
                    <i class="ti ti-files me-1"></i>
                    Gestionar Documentos Adicionales
                    @if ($version->documentos->count() > 0)
                        <span class="badge bg-primary ms-2">{{ $version->documentos->count() }} actualmente</span>
                    @endif
                </label>
            </div>
            <small class="text-muted d-block mt-1">
                Administre documentos adicionales como certificados, licencias, contratos, etc.
            </small>
        </div>
    </div>
</div>

{{-- MODAL PRINCIPAL --}}
<div class="modal fade" id="documentosAdicionalesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="ti ti-files me-2"></i>
                    Gestionar Documentos Adicionales
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row">

                    {{-- COLUMNA IZQUIERDA: Documentos Existentes --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">
                            <i class="ti ti-file-check text-primary me-1"></i>
                            Documentos Actuales
                        </h6>

                        <div id="documentosExistentesContainer">
                            @if ($version->documentos->count() > 0)
                                @foreach ($version->documentos as $index => $doc)
                                    <div class="documento-item documento-existente"
                                        data-documento-id="{{ $doc->id }}">
                                        <div class="row align-items-center g-2">

                                            {{-- Tipo de documento --}}
                                            <div class="col-12">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <strong class="text-primary">{{ $doc->nombre }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="ti ti-file me-1"></i>
                                                            {{ basename($doc->pivot->archivo) }}
                                                        </small>
                                                    </div>
                                                    <div class="btn-group">
                                                        <a href="{{ asset('storage/' . $doc->pivot->archivo) }}"
                                                            class="btn btn-sm btn-info" download title="Descargar">
                                                            <i class="ti ti-download"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger btn-eliminar-existente"
                                                            data-documento-id="{{ $doc->id }}"
                                                            data-documento-nombre="{{ $doc->nombre }}"
                                                            title="Eliminar">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Checkbox oculto para marcar como eliminado --}}
                                            <input type="hidden" name="documentos_eliminar[]" value=""
                                                class="documento-eliminar-flag" disabled>

                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info" id="noDocumentosExistentes">
                                    <i class="ti ti-info-circle me-2"></i>
                                    No hay documentos adicionales actualmente.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: Agregar Nuevos --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">
                            <i class="ti ti-file-plus text-success me-1"></i>
                            Agregar Nuevos Documentos
                        </h6>

                        <div class="alert alert-success mb-3">
                            <i class="ti ti-info-circle me-2"></i>
                            <small>
                                Puede agregar nuevos documentos sin afectar los existentes.
                                Máximo <strong>50MB</strong> por archivo.
                            </small>
                        </div>

                        {{-- Contenedor de documentos nuevos --}}
                        <div id="documentosNuevosContainer">
                            <!-- Los nuevos documentos se agregarán aquí -->
                        </div>

                        {{-- Botón agregar --}}
                        <button type="button" class="btn btn-outline-success w-100 btn-add-documento"
                            id="addDocumentoBtn">
                            <i class="ti ti-plus me-1"></i>
                            Agregar Nuevo Documento
                        </button>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="guardarDocumentosBtn">
                    <i class="ti ti-check me-1"></i>
                    Guardar Cambios
                </button>
            </div>

        </div>
    </div>
</div>
