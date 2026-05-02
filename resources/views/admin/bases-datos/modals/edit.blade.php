<!-- Modal Editar Base de Datos -->
<div class="modal fade" id="editBaseDatosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-database me-2"></i>
                    Editar Gestor de Base de Datos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editBaseDatosForm" method="POST" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editBaseDatosId">

                <div class="modal-body">
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Gestor -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Gestor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editGestor" name="gestor" 
                                   required maxlength="100">
                            <div class="invalid-feedback">El gestor es obligatorio.</div>
                        </div>

                        <!-- Versión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Versión <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editVersion" name="version" 
                                   required maxlength="10">
                            <div class="invalid-feedback">La versión es obligatoria.</div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Descripción
                            </label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" 
                                      rows="3" maxlength="1000"></textarea>
                            <div class="invalid-feedback">La descripción no puede exceder 1000 caracteres.</div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="estado" id="editEstadoActivo" value="activo">
                                <label class="btn btn-outline-success" for="editEstadoActivo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="editEstadoInactivo" value="inactivo">
                                <label class="btn btn-outline-secondary" for="editEstadoInactivo">
                                    <i class="ti ti-ban me-1"></i> Inactivo
                                </label>
                            </div>
                            <div class="invalid-feedback">Seleccione un estado.</div>
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