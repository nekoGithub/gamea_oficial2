<!-- Modal Agregar Servidor -->
<div class="modal fade" id="addServidorModal" tabindex="-1" aria-labelledby="addServidorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addServidorModalLabel">
                    <i class="ti ti-server me-2"></i>
                    Agregar Servidor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addServidorForm" action="{{ route('admin.servidores.store') }}" method="POST" novalidate>
                @csrf

                <div class="modal-body">

                    <small class="text-muted mb-3 d-block">
                        <span class="text-danger">*</span> Los campos marcados son obligatorios
                    </small>

                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Nombre del Servidor <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="nombre"
                                placeholder="Ej. Servidor Web Principal" required maxlength="45">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>

                        <!-- IP Interna -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                IP Interna <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-network"></i></span>
                                <input type="text" class="form-control ip-input" id="ipInterna" name="ip_interna"
                                    placeholder="192.168.1.1" value="{{ old('ip_interna') }}" required>
                            </div>
                            <div class="invalid-feedback">
                                La IP interna debe ser válida (0–255 por octeto).
                            </div>
                            <small class="text-muted">Escribe los números, los puntos se agregan automáticamente</small>
                        </div>

                        <!-- IP Externa -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IP Externa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-world"></i></span>
                                <input type="text" class="form-control ip-input" id="ipExterna" name="ip_externa"
                                    placeholder="200.158.14.1" value="{{ old('ip_externa') }}">
                            </div>
                            <small class="text-muted">IP pública (opcional)</small>
                            <div class="invalid-feedback">La IP externa debe ser válida (0–255 por octeto).</div>
                        </div>

                        <!-- MAC -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dirección MAC</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-device-desktop"></i></span>
                                <input type="text" class="form-control mac-input" id="macAddress" name="mac_address"
                                    placeholder="00:1A:2B:3C:4D:5E" value="{{ old('mac_address') }}">
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
                            <select class="form-select" name="sistema_operativo_id" required>
                                <option value="">Seleccione un sistema operativo</option>
                                @foreach ($sistemasOperativos as $so)
                                    <option value="{{ $so->id }}"
                                        {{ old('sistema_operativo_id') == $so->id ? 'selected' : '' }}>
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
                                <textarea class="form-control" name="descripcion" rows="3"
                                    placeholder="Ej. Servidor principal para aplicaciones web..."></textarea>
                            </div>

                            <small class="text-muted">Descripción del servidor (opcional)</small>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Tipo de Servidor <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100">
                                <input type="radio" class="btn-check" name="tipo_servidor" id="tipo-fisico"
                                    value="físico" {{ old('tipo_servidor') == 'físico' ? 'checked' : '' }}>

                                <label class="btn btn-outline-primary" for="tipo-fisico">
                                    <i class="ti ti-server me-1"></i> Físico
                                </label>
                                <input type="radio" class="btn-check" name="tipo_servidor" id="tipo-virtual"
                                    value="virtual"
                                    {{ old('tipo_servidor', 'virtual') == 'virtual' ? 'checked' : '' }}>

                                <label class="btn btn-outline-warning" for="tipo-virtual">
                                    <i class="ti ti-cloud me-1"></i> Virtual
                                </label>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100">
                                <input type="radio" class="btn-check" name="estado" id="estado-activo"
                                    value="activo" {{ old('estado', 'activo') == 'activo' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success" for="estado-activo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="estado-inactivo"
                                    value="inactivo" {{ old('estado') == 'inactivo' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary" for="estado-inactivo">
                                    <i class="ti ti-ban me-1"></i> Inactivo
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Servidor
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
        const ipMask = {
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
        const macMask = {
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
        document.getElementById('addServidorModal')?.addEventListener('shown.bs.modal', function() {
            // Destruir y reaplicar máscaras
            document.querySelectorAll('#addServidorForm .ip-input').forEach(input => {
                if (input.inputmask) input.inputmask.remove();
                Inputmask(ipMask).mask(input);
            });

            document.querySelectorAll('#addServidorForm .mac-input').forEach(input => {
                if (input.inputmask) input.inputmask.remove();
                Inputmask(macMask).mask(input);
            });
        });

        // 🔥 LIMPIAR AL CERRAR EL MODAL
        document.getElementById('addServidorModal')?.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('addServidorForm');
            if (!form) return;

            // Limpiar valores y errores
            form.reset();
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Limpiar máscaras
            document.querySelectorAll('#addServidorForm .ip-input, #addServidorForm .mac-input')
                .forEach(input => {
                    if (input.inputmask) {
                        input.inputmask.remove();
                    }
                });
        });

        // 🔥 REMOVER ERRORES AL ESCRIBIR
        document.querySelectorAll('#addServidorForm input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>
