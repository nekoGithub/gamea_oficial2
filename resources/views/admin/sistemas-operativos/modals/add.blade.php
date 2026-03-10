<!-- Modal Agregar Sistema Operativo -->
<div class="modal fade" id="addSistemaOperativoModal" tabindex="-1" aria-labelledby="addSistemaOperativoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addSistemaOperativoModalLabel">
                    <i class="ti ti-device-desktop me-2"></i>
                    Agregar Sistema Operativo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addSistemaOperativoForm" action="{{ route('admin.sistemas-operativos.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="nombre" 
                                   placeholder="Ej. Ubuntu Server" required maxlength="100">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <!-- Versión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Versión <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="version" 
                                   placeholder="Ej. 22.04 LTS" required maxlength="50">
                            <div class="invalid-feedback">La versión es obligatoria.</div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Descripción
                            </label>
                            <textarea class="form-control" name="descripcion" rows="3" 
                                      placeholder="Breve descripción del sistema operativo (opcional)" maxlength="1000"></textarea>
                            <div class="invalid-feedback">La descripción no puede exceder 1000 caracteres.</div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group" aria-label="Estado">
                                <input type="radio" class="btn-check" name="estado" id="estado-activo" value="activo" checked>
                                <label class="btn btn-outline-success" for="estado-activo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="estado-inactivo" value="inactivo">
                                <label class="btn btn-outline-secondary" for="estado-inactivo">
                                    <i class="ti ti-ban me-1"></i> Inactivo
                                </label>
                            </div>
                            <div class="invalid-feedback">Seleccione un estado.</div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar Sistema Operativo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>