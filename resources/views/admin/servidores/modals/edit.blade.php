<!-- Modal Editar Servidor -->
<div class="modal fade" id="editServidorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-server me-2"></i>
                    Editar Servidor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editServidorForm" method="POST" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" id="editServidorId">
                <input type="hidden" id="editTieneVersionesActivas" value="0">

                <div class="modal-body">

                    <small class="text-muted mb-3 d-block">
                        <span class="text-danger">*</span> Campos obligatorios
                    </small>

                    <!-- Alerta si tiene versiones activas -->
                    <div class="alert alert-warning alert-dismissible fade" role="alert" id="alertVersionesActivas"
                        style="display: none;">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Atención:</strong> Este servidor tiene versiones activas en producción. No se puede
                        inactivar.
                    </div>

                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Nombre del Servidor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required
                                maxlength="45">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <!-- IP Interna -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                IP Interna <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-network"></i></span>
                                <input type="text" class="form-control ip-input-edit" id="editIpInterna"
                                    name="ip_interna" placeholder="192.168.1.1" required>
                            </div>
                            <small class="text-muted">Escribe los números, los puntos se agregan automáticamente</small>
                            <div class="invalid-feedback">La IP interna debe ser válida (0–255 por octeto).</div>
                        </div>

                        <!-- IP Externa -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IP Externa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-world"></i></span>
                                <input type="text" class="form-control ip-input-edit" id="editIpExterna"
                                    name="ip_externa" placeholder="200.158.14.1">
                            </div>
                            <small class="text-muted">IP pública (opcional)</small>
                            <div class="invalid-feedback">La IP externa debe ser válida (0–255 por octeto).</div>
                        </div>

                        <!-- MAC -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dirección MAC</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-device-desktop"></i></span>
                                <input type="text" class="form-control mac-input-edit" id="editMacAddress"
                                    name="mac_address" placeholder="00:1A:2B:3C:4D:5E">
                            </div>
                            <small class="text-muted">Escribe caracteres hexadecimales (0-9, A-F), formato
                                automático</small>
                            <div class="invalid-feedback">La dirección MAC debe ser válida (formato: XX:XX:XX:XX:XX:XX).
                            </div>
                        </div>

                        <!-- Sistema Operativo -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Sistema Operativo <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="editSistemaOperativoId" name="sistema_operativo_id"
                                required>
                                <option value="">Seleccione un sistema operativo</option>
                                @foreach ($sistemasOperativos as $so)
                                    <option value="{{ $so->id }}">
                                        {{ $so->nombre }} {{ $so->version }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Seleccione un sistema operativo.</div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Descripción <span class="text-muted">(Opcional)</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ti ti-align-left"></i>
                                </span>
                                <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"
                                    placeholder="Ej. Servidor principal para aplicaciones web..."></textarea>
                            </div>

                            <small class="text-muted">Descripción del servidor</small>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Tipo de Servidor <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100">
                                <input type="radio" class="btn-check" name="tipo_servidor" id="editTipoFisico"
                                    value="físico">
                                <label class="btn btn-outline-primary" for="editTipoFisico">
                                    <i class="ti ti-server me-1"></i> Físico
                                </label>

                                <input type="radio" class="btn-check" name="tipo_servidor" id="editTipoVirtual"
                                    value="virtual">
                                <label class="btn btn-outline-warning" for="editTipoVirtual">
                                    <i class="ti ti-cloud me-1"></i> Virtual
                                </label>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" id="editEstadoGroup">
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

<script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 🔥 CONFIGURACIÓN MEJORADA PARA IP
        const ipMaskEdit = {
            alias: "ip",
            greedy: false,
            clearIncomplete: false,
            showMaskOnHover: false,
            placeholder: "0",
            oncomplete: function() {
                this.classList.remove('is-invalid');
            }
        };

        // 🔥 CONFIGURACIÓN MEJORADA PARA MAC
        const macMaskEdit = {
            mask: "HH:HH:HH:HH:HH:HH",
            placeholder: "0",
            greedy: false,
            clearIncomplete: false,
            showMaskOnHover: false,
            definitions: {
                H: {
                    validator: "[0-9A-Fa-f]",
                    casing: "upper"
                }
            },
            oncomplete: function() {
                this.classList.remove('is-invalid');
            }
        };

        // 🔥 APLICAR MÁSCARAS AL ABRIR EL MODAL
        document.getElementById('editServidorModal')?.addEventListener('shown.bs.modal', function() {
            // Destruir y reaplicar máscaras
            document.querySelectorAll('#editServidorForm .ip-input-edit').forEach(input => {
                if (input.inputmask) input.inputmask.remove();
                Inputmask(ipMaskEdit).mask(input);
            });

            document.querySelectorAll('#editServidorForm .mac-input-edit').forEach(input => {
                if (input.inputmask) input.inputmask.remove();
                Inputmask(macMaskEdit).mask(input);
            });
        });

        // 🔥 LIMPIAR AL CERRAR EL MODAL
        document.getElementById('editServidorModal')?.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('editServidorForm');
            if (!form) return;

            // Limpiar valores y errores
            form.reset();
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Ocultar alerta
            document.getElementById('alertVersionesActivas').style.display = 'none';

            // Limpiar máscaras
            document.querySelectorAll(
                '#editServidorForm .ip-input-edit, #editServidorForm .mac-input-edit').forEach(
                input => {
                    if (input.inputmask) {
                        input.inputmask.remove();
                    }
                });
        });

        // 🔥 REMOVER ERRORES AL ESCRIBIR
        document.querySelectorAll('#editServidorForm input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>
