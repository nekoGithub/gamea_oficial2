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

    .btn-add-documento {
        border: 2px dashed #6366f1;
        transition: all 0.3s;
    }

    .btn-add-documento:hover {
        background: #6366f1;
        color: white;
        border-style: solid;
    }

    /* ========== MODO OSCURO ========== */
    [data-bs-theme="dark"] .documento-item {
        background: #2a2e3b;
        border-color: #3f4451;
        color: #e5e7eb;
    }

    [data-bs-theme="dark"] .btn-add-documento {
        border-color: #6366f1;
        color: #a5b4fc;
        background: transparent;
    }

    [data-bs-theme="dark"] .btn-add-documento:hover {
        background: #6366f1;
        color: white;
    }

    [data-bs-theme="dark"] .alert-info {
        background-color: #1e3a5f;
        border-color: #2563eb;
        color: #93c5fd;
    }

    /* ✅ MODAL HEADER */
    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-header {
        background-color: #2a2e3b;
        border-bottom-color: #3f4451;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-title {
        color: #e5e7eb;
    }

    /* ✅ MODAL BODY */
    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-body {
        background-color: #1a1d29;
    }

    /* ✅ MODAL FOOTER */
    [data-bs-theme="dark"] #documentosAdicionalesModal .modal-footer {
        background-color: #2a2e3b;
        border-top-color: #3f4451;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .text-muted,
    [data-bs-theme="dark"] #documentosAdicionalesModal small.text-muted {
        color: #9ca3af !important;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .form-select,
    [data-bs-theme="dark"] #documentosAdicionalesModal select.form-select {
        background-color: #2a2e3b;
        border-color: #3f4451;
        color: #e5e7eb;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .form-control {
        background-color: #2a2e3b;
        border-color: #3f4451;
        color: #e5e7eb;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .form-label {
        color: #e5e7eb;
    }

    /* ✅ BOTÓN CERRAR (X) */
    [data-bs-theme="dark"] #documentosAdicionalesModal .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* ✅ BOTÓN LIGHT */
    [data-bs-theme="dark"] #documentosAdicionalesModal .btn-light {
        background-color: #3f4451;
        border-color: #3f4451;
        color: #e5e7eb;
    }

    [data-bs-theme="dark"] #documentosAdicionalesModal .btn-light:hover {
        background-color: #4a5060;
    }
</style>
{{-- Modal Documentos Adicionales --}}


<div class="modal fade" id="documentosAdicionalesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-files me-2"></i>
                    Documentos Adicionales
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- Información --}}
                <div class="alert alert-info mb-3">
                    <i class="ti ti-info-circle me-2"></i>
                    <small>
                        <strong>Nota:</strong> Puede adjuntar documentos adicionales como certificados, licencias,
                        contratos, etc. Cada archivo no debe superar <strong>50MB</strong>.
                    </small>
                </div>

                {{-- Contenedor de documentos dinámicos --}}
                <div id="documentosContainer">
                    <!-- Los documentos se agregarán dinámicamente aquí -->
                </div>

                {{-- Botón agregar documento --}}
                <button type="button" class="btn btn-outline-primary w-100 btn-add-documento" id="addDocumentoBtn">
                    <i class="ti ti-plus me-1"></i>
                    Agregar Documento
                </button>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="guardarDocumentosBtn">
                    <i class="ti ti-check me-1"></i>
                    Guardar Documentos
                </button>
            </div>

        </div>
    </div>
</div>
