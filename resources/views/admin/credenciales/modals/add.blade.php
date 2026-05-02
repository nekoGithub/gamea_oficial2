<!-- Modal Agregar Credencial -->
<div class="modal fade" id="addCredencialModal" tabindex="-1" aria-labelledby="addCredencialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addCredencialModalLabel">
                    <i class="ti ti-key me-2"></i>
                    Agregar Credencial de Acceso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addCredencialForm" action="{{ route('admin.credenciales.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="alert alert-warning d-flex align-items-center mb-3">
                        <i class="ti ti-alert-circle fs-4 me-2"></i>
                        <div>
                            <strong>Importante:</strong> La contraseña será encriptada y almacenada de forma segura.
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        {{-- SISTEMA con Tom Select (igual que responsables en unidades) --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Sistema <span class="text-danger">*</span>
                            </label>

                            <select name="sistema_id" id="add_sistema_id" class="form-control js-tom-select"
                                placeholder="Busca por sigla o dominio..." data-required="true">
                                <option value="">— Selecciona un sistema —</option>
                                @foreach ($sistemas as $sistema)
                                    <option value="{{ $sistema->id }}">
                                        {{ ($sistema->sigla ? $sistema->sigla . ' — ' : '') . ($sistema->dominio ?? $sistema->nombre) }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="invalid-feedback d-block d-none" id="add_sistema_error">
                                Debes seleccionar un sistema.
                            </div>
                        </div>

                        <!-- Usuario -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Usuario <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-user"></i></span>
                                <input type="text" class="form-control" name="usuario"
                                    placeholder="Nombre de usuario" required maxlength="150">
                            </div>
                            <div class="invalid-feedback">El usuario es obligatorio.</div>
                        </div>

                        <!-- Contraseña -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="addPassword"
                                    placeholder="Contraseña segura" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="toggleAddPassword">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="estado" id="add_estado_activo"
                                    value="activo" checked>
                                <label class="btn btn-outline-success" for="add_estado_activo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>
                                <input type="radio" class="btn-check" name="estado" id="add_estado_inactivo"
                                    value="inactivo">
                                <label class="btn btn-outline-secondary" for="add_estado_inactivo">
                                    <i class="ti ti-ban me-1"></i> Inactivo
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Credencial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Toggle contraseña ──────────────────────────────────────────────────────
        document.getElementById('toggleAddPassword')?.addEventListener('click', function() {
            const inp = document.getElementById('addPassword');
            const icon = this.querySelector('i');
            const isPwd = inp.type === 'password';
            inp.type = isPwd ? 'text' : 'password';
            icon.classList.toggle('ti-eye', !isPwd);
            icon.classList.toggle('ti-eye-off', isPwd);
        });

        // ── Inicializar Tom Select en el select de sistema ─────────────────────────
        // UBold inicializa automáticamente todos los .js-tom-select en DOMContentLoaded,
        // pero como este modal se incluye en la página, ya está disponible.
        // Si Tom Select ya lo inicializó automáticamente no hacemos nada más.
        // Si necesitas forzarlo manualmente descomenta lo siguiente:
        /*
        const selEl = document.getElementById('add_sistema_id');
        if (typeof TomSelect !== 'undefined' && selEl && !selEl.tomselect) {
            new TomSelect(selEl, {
                placeholder: 'Busca por sigla o dominio...',
                allowEmptyOption: true,
                maxItems: 1,
            });
        }
        */

        // ── Validar sistema en submit ──────────────────────────────────────────────
        document.getElementById('addCredencialForm')?.addEventListener('submit', function(e) {
            const val = document.getElementById('add_sistema_id').value;
            const err = document.getElementById('add_sistema_error');
            if (!val) {
                e.preventDefault();
                err.classList.remove('d-none');
                return false;
            }
            err.classList.add('d-none');
        });

        // ── Quitar error al seleccionar sistema ───────────────────────────────────
        document.getElementById('add_sistema_id')?.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('add_sistema_error').classList.add('d-none');
            }
        });

        // ── Limpiar al cerrar modal ────────────────────────────────────────────────
        document.getElementById('addCredencialModal')?.addEventListener('hidden.bs.modal', function() {
            document.getElementById('addCredencialForm').reset();

            // Resetear Tom Select si fue inicializado
            const sel = document.getElementById('add_sistema_id');
            if (sel && sel.tomselect) {
                sel.tomselect.clear();
            }

            document.querySelectorAll('#addCredencialForm .is-invalid')
                .forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('add_sistema_error').classList.add('d-none');
        });
    });
</script>
