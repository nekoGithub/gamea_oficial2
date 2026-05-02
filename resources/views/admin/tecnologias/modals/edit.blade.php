<!-- Modal Editar Tecnología -->
<div class="modal fade" id="editTecnologiaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-code me-2"></i>
                    Editar Tecnología
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editTecnologiaForm" method="POST" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editTecnologiaId">

                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required
                                maxlength="100">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <!-- Versión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Versión <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editVersion" name="version" required
                                maxlength="50">
                            <div class="invalid-feedback">La versión es obligatoria.</div>
                        </div>

                        <!-- URL Documentación -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                URL de Documentación
                            </label>
                            <input type="url" class="form-control" id="editUrlDocumentacion"
                                name="url_documentacion" maxlength="255">
                            <div class="invalid-feedback">Debe ingresar una URL válida.</div>
                        </div>

                        <!-- Fecha Lanzamiento -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Lanzamiento</label>
                            <input type="date" class="form-control" id="editFechaLanzamiento"
                                name="fecha_lanzamiento" min="2015-01-01">
                            <div class="invalid-feedback">Fecha inválida (2015 - año actual +5).</div>
                        </div>

                        <!-- Fecha Fin Soporte -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha Fin de Soporte</label>
                            <input type="date" class="form-control" id="editFechaFinSoporte" name="fecha_fin_soporte"
                                min="2015-01-01">
                            <div class="invalid-feedback">La fecha debe ser posterior a la de lanzamiento.</div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Descripción
                            </label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3" maxlength="1000"></textarea>
                            <div class="invalid-feedback">La descripción no puede exceder 1000 caracteres.</div>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Tipo <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="editTipoBackend"
                                    value="backend">
                                <label class="btn btn-outline-primary" for="editTipoBackend">
                                    <i class="ti ti-server me-1"></i> Backend
                                </label>

                                <input type="radio" class="btn-check" name="tipo" id="editTipoFrontend"
                                    value="frontend">
                                <label class="btn btn-outline-success" for="editTipoFrontend">
                                    <i class="ti ti-browser me-1"></i> Frontend
                                </label>

                                <input type="radio" class="btn-check" name="tipo" id="editTipoOtros"
                                    value="otros/librerias">
                                <label class="btn btn-outline-warning" for="editTipoOtros">
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
                                <input type="radio" class="btn-check" name="estado" id="editEstadoActivo"
                                    value="activo">
                                <label class="btn btn-outline-success" for="editEstadoActivo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="editEstadoInactivo"
                                    value="inactivo">
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
