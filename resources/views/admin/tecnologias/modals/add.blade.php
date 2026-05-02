<!-- Modal Agregar Tecnología -->
<div class="modal fade" id="addTecnologiaModal" tabindex="-1" aria-labelledby="addTecnologiaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addTecnologiaModalLabel">
                    <i class="ti ti-code me-2"></i>
                    Agregar Tecnología
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addTecnologiaForm" action="{{ route('admin.tecnologias.store') }}" method="POST" novalidate>
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
                                placeholder="Ej. Laravel, React, Docker" required maxlength="100">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <!-- Versión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Versión <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="version" placeholder="Ej. 10.x, 18.2, 24.0"
                                required maxlength="50">
                            <div class="invalid-feedback">La versión es obligatoria.</div>
                        </div>

                        <!-- URL Documentación -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                URL de Documentación
                            </label>
                            <input type="url" class="form-control" name="url_documentacion"
                                placeholder="https://ejemplo.com/docs" maxlength="255">
                            <div class="invalid-feedback">Debe ingresar una URL válida.</div>
                        </div>

                        <!-- Fecha Lanzamiento -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Lanzamiento</label>
                            <input type="date" class="form-control" name="fecha_lanzamiento" id="addFechaLanzamiento"
                                min="2015-01-01">
                            <div class="invalid-feedback">Fecha inválida (2015 - año actual +5).</div>
                        </div>

                        <!-- Fecha Fin Soporte -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha Fin de Soporte</label>
                            <input type="date" class="form-control" name="fecha_fin_soporte" id="addFechaFinSoporte"
                                min="2015-01-01">
                            <div class="invalid-feedback">La fecha debe ser posterior a la de lanzamiento.</div>
                        </div>

                        <!-- Alert Info -->
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Vigencia Automática:</strong> Se calculará como:
                                <ul class="mb-0 mt-2">
                                    <li><span class="badge bg-success">Vigente</span>: Sin fecha de fin o más de 6 meses
                                        restantes</li>
                                    <li><span class="badge bg-warning">Desactualizada</span>: 6 meses o menos para fin
                                        de soporte</li>
                                    <li><span class="badge bg-danger">Obsoleta</span>: Fin de soporte ya pasó</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Descripción
                            </label>
                            <textarea class="form-control" name="descripcion" rows="3"
                                placeholder="Breve descripción de la tecnología (opcional)" maxlength="1000"></textarea>
                            <div class="invalid-feedback">La descripción no puede exceder 1000 caracteres.</div>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Tipo <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="tipo-backend" value="backend"
                                    checked>
                                <label class="btn btn-outline-primary" for="tipo-backend">
                                    <i class="ti ti-server me-1"></i> Backend
                                </label>

                                <input type="radio" class="btn-check" name="tipo" id="tipo-frontend"
                                    value="frontend">
                                <label class="btn btn-outline-success" for="tipo-frontend">
                                    <i class="ti ti-browser me-1"></i> Frontend
                                </label>

                                <input type="radio" class="btn-check" name="tipo" id="tipo-otros"
                                    value="otros/librerias">
                                <label class="btn btn-outline-warning" for="tipo-otros">
                                    <i class="ti ti-puzzle me-1"></i> Otros/Librerias
                                </label>
                            </div>
                            <div class="invalid-feedback">Seleccione el tipo.</div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="estado" id="estado-activo"
                                    value="activo" checked>
                                <label class="btn btn-outline-success" for="estado-activo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="estado-inactivo"
                                    value="inactivo">
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
                        Guardar Tecnología
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
