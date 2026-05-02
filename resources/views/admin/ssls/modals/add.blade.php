<!-- Modal Agregar SSL -->
<div class="modal fade" id="addSslModal" tabindex="-1" aria-labelledby="addSslModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addSslModalLabel">
                    <i class="ti ti-certificate me-2"></i>
                    Agregar Certificado SSL
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addSslForm" action="{{ route('admin.ssls.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Emisor -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Emisor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="emisor" id="addEmisor"
                                placeholder="Ej. Let's Encrypt, DigiCert, etc." required maxlength="50">
                            <div class="d-flex justify-content-between">
                                <div class="invalid-feedback">El emisor es obligatorio.</div>
                                <small class="text-muted ms-auto" id="addEmisorCounter">50 caracteres restantes</small>
                            </div>
                        </div>

                        <!-- Archivo SSL -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Archivo de Certificado</label>
                            <input type="file" class="form-control" name="archivo_ssl" accept=".rar,.zip">
                            <small class="text-muted">Formatos permitidos: .rar, .zip (Máx. 2MB)</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Emisión <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" name="fecha_emision" id="addFechaEmision"
                                required min="2020-01-01" max="{{ now()->format('Y-m-d') }}">
                            <small class="text-muted">Desde 2020 hasta hoy</small>
                            <div class="invalid-feedback">La fecha de emisión es obligatoria.</div>
                        </div>

                        <!-- Fecha de Expiración -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fecha de Expiración <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" name="fecha_expiracion" id="addFechaExpiracion"
                                required max="{{ now()->addYears(5)->format('Y-m-d') }}">
                            <small class="text-muted">Hasta {{ now()->addYears(5)->format('d/m/Y') }}</small>
                            <div class="invalid-feedback">Fecha de expiración inválida.</div>
                        </div>

                        <!-- Info -->
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                El estado del certificado se calculará automáticamente según las fechas ingresadas.
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar SSL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ========== CONTADOR EMISOR ==========
    const addEmisorInput   = document.getElementById('addEmisor');
    const addEmisorCounter = document.getElementById('addEmisorCounter');

    if (addEmisorInput && addEmisorCounter) {
        addEmisorInput.addEventListener('input', function () {
            const remaining = 50 - this.value.length;
            addEmisorCounter.textContent = remaining + ' caracteres restantes';
            addEmisorCounter.className   = remaining <= 10
                ? 'text-danger ms-auto'
                : remaining <= 20
                    ? 'text-warning ms-auto'
                    : 'text-muted ms-auto';
        });
    }

    // ========== VALIDACIÓN FECHAS EN TIEMPO REAL ==========
    const addFechaEmision    = document.getElementById('addFechaEmision');
    const addFechaExpiracion = document.getElementById('addFechaExpiracion');

    function validarFechasAdd() {
        if (!addFechaEmision.value || !addFechaExpiracion.value) return;

        const emision    = new Date(addFechaEmision.value);
        const expiracion = new Date(addFechaExpiracion.value);
        const dias       = Math.floor((expiracion - emision) / 86400000);
        const feedback   = addFechaExpiracion.nextElementSibling.nextElementSibling;

        if (expiracion <= emision) {
            addFechaExpiracion.classList.add('is-invalid');
            feedback.textContent = 'La fecha de expiración debe ser posterior a la fecha de emisión.';
        } else if (dias > 1826) {
            addFechaExpiracion.classList.add('is-invalid');
            feedback.textContent = 'Los certificados SSL no pueden tener una vigencia mayor a 5 años.';
        } else {
            addFechaExpiracion.classList.remove('is-invalid');
            feedback.textContent = 'Fecha de expiración inválida.';
        }
    }

    addFechaEmision?.addEventListener('change', validarFechasAdd);
    addFechaExpiracion?.addEventListener('change', validarFechasAdd);

    // ========== LIMPIAR AL CERRAR ==========
    document.getElementById('addSslModal')?.addEventListener('hidden.bs.modal', function () {
        addEmisorCounter.textContent = '50 caracteres restantes';
        addEmisorCounter.className   = 'text-muted ms-auto';
        addFechaExpiracion?.classList.remove('is-invalid');
    });

});
</script>