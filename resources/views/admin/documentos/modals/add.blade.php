<!-- Modal Agregar Documento -->
<div class="modal fade" id="addDocumentoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-file-text me-2"></i>
                    Agregar Tipo de Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addDocumentoForm" action="{{ route('admin.documentos.store') }}" method="POST" novalidate>
                @csrf
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
                            name="nombre"
                            placeholder="Ej. Certificado SSL, Licencia de Software"
                            required 
                            maxlength="100"
                        >
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                        <small class="text-muted">Máximo 45 caracteres</small>
                    </div>

                    <!-- Info -->
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        <small>
                            <strong>Nota:</strong> Este es solo el <em>tipo</em> de documento. 
                            Los archivos se adjuntarán a cada versión de sistema por separado.
                        </small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>