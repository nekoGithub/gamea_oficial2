<!-- Modal Editar SSL -->
<div class="modal fade" id="editSslModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-certificate me-2"></i>
                    Editar Certificado SSL
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editSslForm" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editSslId">

                <div class="modal-body">
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Emisor -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Emisor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editEmisor" name="emisor" required>
                            <div class="invalid-feedback">El emisor es obligatorio.</div>
                        </div>

                        <!-- Archivo SSL -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Archivo de Certificado
                            </label>
                            <div id="currentSslFile" class="mb-2"></div>
                            <input type="file" class="form-control" name="archivo_ssl" 
                                   accept=".rar,.zip">
                            <small class="text-muted">
                                Deja vacío para mantener el archivo actual. Formatos: .rar, .zip
                            </small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Emisión <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="editFechaEmision" name="fecha_emision" required>
                            <div class="invalid-feedback">La fecha de emisión es obligatoria.</div>
                        </div>

                        <!-- Fecha de Expiración -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Expiración <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="editFechaExpiracion" name="fecha_expiracion" required>
                            <div class="invalid-feedback">La fecha de expiración es obligatoria.</div>
                        </div>

                        <!-- Información adicional -->
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                El estado se recalculará automáticamente al guardar.
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top-0">
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