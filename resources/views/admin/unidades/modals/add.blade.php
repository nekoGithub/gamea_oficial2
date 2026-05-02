<!-- Modal Agregar Unidad -->
<div class="modal fade" id="addUnidadModal" tabindex="-1" aria-labelledby="addUnidadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addUnidadModalLabel">Agregar Nueva Unidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addUnidadForm" action="{{ route('admin.unidades.store') }}" method="POST" novalidate>
                @csrf

                <div class="modal-body">

                    <!-- Indicador campos obligatorios -->
                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre de la unidad <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="nombre"
                                placeholder="Ej. Dirección de Sistemas" required>
                            <div class="invalid-feedback">
                                El nombre es obligatorio.
                            </div>
                        </div>

                        <!-- Sigla -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Sigla <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="sigla" placeholder="DSI" required>
                            <div class="invalid-feedback">
                                La sigla es obligatoria.
                            </div>
                        </div>
                        
                        <!-- Celular -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" class="form-control" id="celular" name="celular"
                                placeholder="Ej. 75123456" maxlength="8" inputmode="numeric">
                            <div class="invalid-feedback">El teléfono debe tener 8 dígitos numéricos.</div>                            
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Descripción
                            </label>
                            <textarea class="form-control" name="descripcion" rows="3" placeholder="Breve descripción de la unidad"></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group" aria-label="Estado">
                                <input type="radio" class="btn-check" name="estado" id="estado-activa"
                                    value="activa">
                                <label class="btn btn-outline-success" for="estado-activa">
                                    <i class="ti ti-check me-1"></i> Activa
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="estado-inactiva"
                                    value="inactiva">
                                <label class="btn btn-outline-secondary" for="estado-inactiva">
                                    <i class="ti ti-ban me-1"></i> Inactiva
                                </label>
                            </div>

                            <!-- Error -->
                            <div id="estado-error" class="invalid-feedback d-block d-none">
                                Seleccione el estado.
                            </div>
                        </div>

                        <!-- Responsables -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Responsables <span>(Opcional)</span>
                            </label>

                            <select name="responsables[]" multiple class="form-control js-tom-select"
                                placeholder="Seleccione responsables" >
                                @foreach ($responsables as $responsable)
                                    <option value="{{ $responsable->id }}">
                                        {{ $responsable->nombre }} – {{ $responsable->cargo }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="text-muted">
                                Este campo es opcional.
                            </small>
                            
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar Unidad
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
