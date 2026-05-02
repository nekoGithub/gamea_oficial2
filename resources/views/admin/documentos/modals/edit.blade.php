<!-- Modal Editar Documento -->
<div class="modal fade" id="editDocumentoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-file-text me-2"></i>
                    Editar Tipo de Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editDocumentoForm" method="POST" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editDocumentoId">

                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios
                        </small>
                    </div>

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nombre del Documento <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="editNombre"
                            name="nombre"
                            required 
                            maxlength="45"
                        >
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>

                    <!-- Estado -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">
                            Estado <span class="text-danger">*</span>
                        </label>

                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="activo" id="editEstadoActivo" value="1">
                            <label class="btn btn-outline-success" for="editEstadoActivo">
                                <i class="ti ti-check me-1"></i> Activo
                            </label>

                            <input type="radio" class="btn-check" name="activo" id="editEstadoInactivo" value="0">
                            <label class="btn btn-outline-secondary" for="editEstadoInactivo">
                                <i class="ti ti-ban me-1"></i> Inactivo
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>