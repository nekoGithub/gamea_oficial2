<!-- Modal Agregar SSL -->
<div class="modal fade" id="addSslModal" tabindex="-1" aria-labelledby="addSslModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addSslModalLabel">
                    <i class="ti ti-certificate me-2"></i>
                    Agregar Certificado SSL
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addSslForm" action="{{ route('admin.ssls.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Emisor -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Emisor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="emisor" 
                                   placeholder="Ej. Let's Encrypt, DigiCert, etc." required>
                            <div class="invalid-feedback">El emisor es obligatorio.</div>
                        </div>

                        <!-- Archivo SSL -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Archivo de Certificado
                            </label>
                            <input type="file" class="form-control" name="archivo_ssl" 
                                   accept=".rar,.zip">
                            <small class="text-muted">
                                Formatos permitidos: .rar, .zip (Máx. 2MB)
                            </small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Emisión <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" name="fecha_emision" required>
                            <div class="invalid-feedback">La fecha de emisión es obligatoria.</div>
                        </div>

                        <!-- Fecha de Expiración -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Expiración <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" name="fecha_expiracion" required>
                            <div class="invalid-feedback">La fecha de expiración es obligatoria.</div>
                        </div>

                        <!-- Información adicional -->
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                El estado del certificado se calculará automáticamente según las fechas ingresadas.
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar SSL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>