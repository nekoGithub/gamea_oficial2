<!-- Modal Editar SSL -->
<div class="modal fade" id="editSslModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-certificate me-2"></i>
                    Editar Certificado SSL
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editSslForm" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editSslId">

                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Campos obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Emisor -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Emisor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editEmisor" name="emisor" required
                                maxlength="50">
                            <div class="d-flex justify-content-between">
                                <div class="invalid-feedback">El emisor es obligatorio.</div>
                                <small class="text-muted ms-auto" id="editEmisorCounter">50 caracteres restantes</small>
                            </div>
                        </div>

                        <!-- Archivo SSL -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Archivo de Certificado</label>
                            <div id="currentSslFile" class="mb-2"></div>
                            <input type="file" class="form-control" name="archivo_ssl" accept=".rar,.zip">
                            <small class="text-muted">Deja vacío para mantener el archivo actual. Formatos: .rar, .zip
                                (Máx. 2MB)</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Emisión <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="editFechaEmision" name="fecha_emision"
                                required min="2020-01-01" max="{{ now()->format('Y-m-d') }}">
                            <small class="text-muted">Desde 2020 hasta hoy</small>
                            <div class="invalid-feedback">La fecha de emisión es obligatoria.</div>
                        </div>

                        <!-- Fecha de Expiración -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Expiración <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="editFechaExpiracion" name="fecha_expiracion"
                                required max="{{ now()->addYears(5)->format('Y-m-d') }}">
                            <small class="text-muted">Hasta {{ now()->addYears(5)->format('d/m/Y') }}</small>
                            <div class="invalid-feedback">Fecha de expiración inválida.</div>
                        </div>

                        <!-- Info -->
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                El estado se recalculará automáticamente al guardar.
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ========== CONTADOR EMISOR EDITAR ==========
        const editEmisorInput = document.getElementById('editEmisor');
        const editEmisorCounter = document.getElementById('editEmisorCounter');

        if (editEmisorInput && editEmisorCounter) {
            editEmisorInput.addEventListener('input', function() {
                const remaining = 50 - this.value.length;
                editEmisorCounter.textContent = remaining + ' caracteres restantes';
                editEmisorCounter.className = remaining <= 10 ?
                    'text-danger ms-auto' :
                    remaining <= 20 ?
                    'text-warning ms-auto' :
                    'text-muted ms-auto';
            });
        }

        // ========== VALIDACIÓN FECHAS EN TIEMPO REAL ==========
        const editFechaEmision = document.getElementById('editFechaEmision');
        const editFechaExpiracion = document.getElementById('editFechaExpiracion');

        function validarFechasEdit() {
            if (!editFechaEmision.value || !editFechaExpiracion.value) return;

            const emision = new Date(editFechaEmision.value);
            const expiracion = new Date(editFechaExpiracion.value);
            const dias = Math.floor((expiracion - emision) / 86400000);

            const feedback = editFechaExpiracion.nextElementSibling.nextElementSibling;

            if (expiracion <= emision) {
                editFechaExpiracion.classList.add('is-invalid');
                feedback.textContent = 'La fecha de expiración debe ser posterior a la fecha de emisión.';
            } else if (dias > 1826) {
                editFechaExpiracion.classList.add('is-invalid');
                feedback.textContent = 'Los certificados SSL no pueden tener una vigencia mayor a 5 años.';
            } else {
                editFechaExpiracion.classList.remove('is-invalid');
                feedback.textContent = 'Fecha de expiración inválida.';
            }
        }

        editFechaEmision?.addEventListener('change', validarFechasEdit);
        editFechaExpiracion?.addEventListener('change', validarFechasEdit);

        // ========== ACTUALIZAR CONTADOR AL ABRIR MODAL ==========
        document.getElementById('editSslModal')?.addEventListener('shown.bs.modal', function() {
            if (editEmisorInput && editEmisorCounter) {
                const remaining = 50 - editEmisorInput.value.length;
                editEmisorCounter.textContent = remaining + ' caracteres restantes';
                editEmisorCounter.className = remaining <= 10 ?
                    'text-danger ms-auto' :
                    remaining <= 20 ?
                    'text-warning ms-auto' :
                    'text-muted ms-auto';
            }
        });

    });
</script>
