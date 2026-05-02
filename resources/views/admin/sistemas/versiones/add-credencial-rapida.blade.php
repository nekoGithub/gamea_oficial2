{{-- Modal Agregar Credencial Rápida (desde formulario de versiones) --}}
{{-- El sistema_id se toma automáticamente de $sistema->id --}}

<div class="modal fade" id="addCredencialRapidaModal" tabindex="-1" aria-labelledby="addCredencialRapidaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addCredencialRapidaLabel">
                    <i class="ti ti-key me-2"></i>
                    Nueva Credencial
                    <small class="text-muted fs-6 ms-1">— {{ $sistema->sigla ?? $sistema->nombre }}</small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addCredencialRapidaForm" novalidate>
                @csrf
                {{-- sistema_id oculto — siempre es el sistema actual --}}
                <input type="hidden" name="sistema_id" value="{{ $sistema->id }}">

                <div class="modal-body">

                    <div class="alert alert-info d-flex align-items-center py-2 mb-3">
                        <i class="ti ti-info-circle me-2"></i>
                        <small>La credencial se asociará automáticamente a
                            <strong>{{ $sistema->nombre }}</strong>.</small>
                    </div>

                    <div class="row g-3">

                        {{-- Usuario --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Usuario <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-user"></i></span>
                                <input type="text" class="form-control" id="rapida_usuario" name="usuario"
                                    placeholder="Nombre de usuario" required maxlength="150">
                            </div>
                            <div class="invalid-feedback" id="rapida_usuario_error">
                                El usuario es obligatorio.
                            </div>
                        </div>

                        {{-- Contraseña --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <input type="password" class="form-control" id="rapida_password" name="password"
                                    placeholder="Contraseña segura" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="toggleRapidaPassword">
                                    <i class="ti ti-eye" id="iconRapidaPassword"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="rapida_password_error">
                                La contraseña debe tener al menos 6 caracteres.
                            </div>
                            <small class="text-muted">Mínimo 6 caracteres — será encriptada</small>
                        </div>

                        {{-- Estado --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="estado" id="rapida_estado_activo"
                                    value="activo" checked>
                                <label class="btn btn-outline-success" for="rapida_estado_activo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>
                                <input type="radio" class="btn-check" name="estado" id="rapida_estado_inactivo"
                                    value="inactivo">
                                <label class="btn btn-outline-secondary" for="rapida_estado_inactivo">
                                    <i class="ti ti-ban me-1"></i> Inactivo
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarCredencialRapida">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Credencial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('addCredencialRapidaForm');
        const modal = document.getElementById('addCredencialRapidaModal');
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const sistemaId = {{ $sistema->id }};

        // ── Toggle contraseña ──────────────────────────────────────────────────
        document.getElementById('toggleRapidaPassword')?.addEventListener('click', function() {
            const inp = document.getElementById('rapida_password');
            const icon = document.getElementById('iconRapidaPassword');
            const isPwd = inp.type === 'password';
            inp.type = isPwd ? 'text' : 'password';
            icon.classList.toggle('ti-eye', !isPwd);
            icon.classList.toggle('ti-eye-off', isPwd);
        });

        // ── Limpiar al cerrar ──────────────────────────────────────────────────
        modal?.addEventListener('hidden.bs.modal', function() {
            form.reset();
            document.getElementById('rapida_estado_activo').checked = true;
            document.getElementById('rapida_usuario').classList.remove('is-invalid');
            document.getElementById('rapida_password').classList.remove('is-invalid');
            document.getElementById('rapida_password').setAttribute('type', 'password');
            document.getElementById('iconRapidaPassword').classList.remove('ti-eye-off');
            document.getElementById('iconRapidaPassword').classList.add('ti-eye');
        });

        // ── Submit ─────────────────────────────────────────────────────────────
        form?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const usuario = document.getElementById('rapida_usuario');
            const password = document.getElementById('rapida_password');
            let isValid = true;

            // Validar usuario
            if (!usuario.value.trim()) {
                usuario.classList.add('is-invalid');
                isValid = false;
            } else {
                usuario.classList.remove('is-invalid');
            }

            // Validar contraseña
            if (!password.value || password.value.length < 6) {
                password.classList.add('is-invalid');
                isValid = false;
            } else {
                password.classList.remove('is-invalid');
            }

            if (!isValid) return;

            // Deshabilitar botón
            const btn = document.getElementById('btnGuardarCredencialRapida');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

            const formData = new FormData(form);

            try {
                const res = await fetch('{{ route('admin.credenciales.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.status === 422 && data.errors) {
                    // Mostrar errores del servidor inline
                    if (data.errors.usuario) {
                        usuario.classList.add('is-invalid');
                        document.getElementById('rapida_usuario_error').textContent = data.errors
                            .usuario[0];
                    }
                    if (data.errors.password) {
                        password.classList.add('is-invalid');
                        document.getElementById('rapida_password_error').textContent = data.errors
                            .password[0];
                    }
                    return;
                }

                if (!res.ok || !data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo guardar la credencial.',
                        confirmButtonColor: '#6366f1'
                    });
                    return;
                }

                // ✅ Éxito — cerrar modal y agregar checkbox al listado
                bootstrap.Modal.getInstance(modal).hide();

                // Agregar el nuevo checkbox dinámicamente al contenedor de credenciales
                const cred = data.credencial;
                const container = document.getElementById('credsContainer');
                const newItem = document.createElement('div');
                newItem.className = 'checkbox-horizontal-item';
                newItem.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input cred-checkbox" type="checkbox"
                           name="credenciales[]"
                           value="${cred.id}"
                           id="cred_${cred.id}"
                           checked>
                    <label class="form-check-label" for="cred_${cred.id}">
                        ${cred.usuario}
                        <small class="text-muted">(nuevo)</small>
                    </label>
                </div>`;
                container.appendChild(newItem);

                // Agregar listener al nuevo checkbox para el contador
                newItem.querySelector('.cred-checkbox').addEventListener('change', () => {
                    updateCredsCount();
                    validateCheckboxGroup('cred-checkbox', 'creds-error');
                });

                updateCredsCount();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Credencial "${cred.usuario}" creada y seleccionada`,
                    showConfirmButton: false,
                    timer: 2500
                });

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    confirmButtonColor: '#6366f1'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Guardar Credencial';
            }
        });

        // ── Helper contador (reutiliza la función del padre si existe) ─────────
        function updateCredsCount() {
            const checked = document.querySelectorAll('.cred-checkbox:checked').length;
            const counter = document.getElementById('creds-count');
            if (counter) counter.textContent = `${checked} seleccionada${checked !== 1 ? 's' : ''}`;
        }

        function validateCheckboxGroup(cls, errId) {
            const checked = document.querySelectorAll(`.${cls}:checked`).length;
            const err = document.getElementById(errId);
            if (!err) return;
            if (checked > 0) {
                err.style.display = 'none';
                err.classList.remove('show');
            } else {
                err.style.display = 'block';
                err.classList.add('show');
            }
        }
    });
</script>
