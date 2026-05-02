<!-- Modal Editar Unidad -->
<div class="modal fade" id="editUnidadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    Editar Unidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editUnidadForm" method="POST" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editUnidadId">

                <div class="modal-body">
                    <div class="col-12">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre de la unidad <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editNombre" name="nombre">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <!-- Sigla -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Sigla <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editSigla" name="sigla">
                            <div class="invalid-feedback">La sigla es obligatoria.</div>
                        </div>

                        <div class="col-md-3"> {{-- o el col que uses --}}
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" class="form-control" id="editCelular" name="celular"
                                placeholder="Ej. 75123456" maxlength="8" inputmode="numeric">
                            <div class="invalid-feedback">El teléfono debe tener 8 dígitos numéricos.</div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="estado" id="editEstadoActiva"
                                    value="activa">
                                <label class="btn btn-outline-success" for="editEstadoActiva">
                                    <i class="ti ti-check me-1"></i> Activa
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="editEstadoInactiva"
                                    value="inactiva">
                                <label class="btn btn-outline-secondary" for="editEstadoInactiva">
                                    <i class="ti ti-ban me-1"></i> Inactiva
                                </label>
                            </div>

                            <div id="edit-estado-error" class="invalid-feedback d-block d-none">
                                Seleccione el estado.
                            </div>
                        </div>

                        <!-- Responsables -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Responsables <span class="text-danger">*</span>
                            </label>

                            <select name="responsables[]" multiple class="form-control js-tom-select"
                                id="editResponsables" placeholder="Seleccione responsables">
                                @foreach ($responsables as $responsable)
                                    <option value="{{ $responsable->id }}">
                                        {{ $responsable->nombre }} – {{ $responsable->cargo }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="text-muted">
                                El campo responsable es opcional.
                            </small>
                            
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
