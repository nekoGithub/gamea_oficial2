<!-- Modal Agregar Responsable -->
<div class="modal fade" id="addResponsableModal" tabindex="-1" aria-labelledby="addResponsableModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addResponsableModalLabel">Agregar Nuevo Responsable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addResponsableForm" action="{{ route('admin.responsables.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej. Juan Pérez"
                                required>
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Cargo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="cargo" placeholder="Ej. Director"
                                required>
                            <div class="invalid-feedback">El cargo es obligatorio.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Email <span>(Opcional)</span>
                            </label>
                            <input type="email" class="form-control" name="email" placeholder="ejemplo@correo.com">
                            <div class="invalid-feedback">El email debe ser válido.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Celular <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="celular" placeholder="Ej. 77712345" required>
                            <div class="invalid-feedback">El celular no es válido.</div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar Responsable
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
