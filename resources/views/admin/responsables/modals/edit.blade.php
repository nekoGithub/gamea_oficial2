<!-- Modal Editar Responsable -->
<div class="modal fade" id="editResponsableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Editar Responsable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editResponsableForm" method="POST" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editResponsableId">

                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Cargo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editCargo" name="cargo" required>
                            <div class="invalid-feedback">El cargo es obligatorio.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Email <span>(Opcional)</span>
                            </label>
                            <input type="email" class="form-control" id="editEmail" name="email">
                            <div class="invalid-feedback">El email es obligatorio y debe ser válido.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Celular <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editCelular" name="celular" required>
                            <div class="invalid-feedback">El celular es obligatorio.</div>
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
