<!-- Modal Agregar Base de Datos -->
<div class="modal fade" id="addBaseDatosModal" tabindex="-1" aria-labelledby="addBaseDatosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addBaseDatosModalLabel">
                    <i class="ti ti-database me-2"></i>
                    Agregar Gestor de Base de Datos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addBaseDatosForm" action="{{ route('admin.bases-datos.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Gestor -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Gestor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="gestor" 
                                   placeholder="Ej. MySQL, PostgreSQL, MongoDB" required maxlength="100">
                            <div class="invalid-feedback">El gestor es obligatorio.</div>
                        </div>

                        <!-- Versión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Versión <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="version" 
                                   placeholder="Ej. 8.0, 14.5, 6.0" required maxlength="10">
                            <div class="invalid-feedback">La versión es obligatoria.</div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Descripción
                            </label>
                            <textarea class="form-control" name="descripcion" rows="3" 
                                      placeholder="Breve descripción del gestor de base de datos (opcional)" maxlength="1000"></textarea>
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
                        Guardar Base de Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>