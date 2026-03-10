<!-- Modal Editar Credencial -->
<div class="modal fade" id="editCredencialModal" tabindex="-1" aria-labelledby="editCredencialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editCredencialModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    Editar Credencial de Acceso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="editCredencialForm" action="#" method="POST" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" id="editCredencialId" name="id">

                <div class="modal-body">

                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="ti ti-info-circle fs-4 me-2"></i>
                        <div>
                            <strong>Nota:</strong> Si no deseas cambiar la contraseña, deja el campo vacío.
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex align-items-start mb-3">
                        <i class="ti ti-shield-lock fs-4 me-2 mt-1"></i>
                        <div>
                            <strong>Verificación de Seguridad:</strong> Debes ingresar tu contraseña actual para actualizar esta credencial.
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row g-3">

                        <!-- Contraseña Actual (Verificación) -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Tu Contraseña Actual <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ti ti-shield-check"></i>
                                </span>
                                <input type="password" class="form-control" name="current_password" id="editCurrentPassword"
                                       placeholder="Verifica tu identidad" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditCurrentPassword">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Debes ingresar tu contraseña actual.</div>
                            <small class="text-muted">Requerido por seguridad</small>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                        </div>

                        <!-- Usuario -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Usuario <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ti ti-user"></i>
                                </span>
                                <input type="text" class="form-control" name="usuario" id="editUsuario"
                                       placeholder="Nombre de usuario" required maxlength="150">
                            </div>
                            <div class="invalid-feedback">El usuario es obligatorio.</div>
                        </div>

                        <!-- Nueva Contraseña (opcional) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nueva Contraseña
                                <span class="text-muted">(Opcional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ti ti-lock"></i>
                                </span>
                                <input type="password" class="form-control" name="password" id="editPassword"
                                       placeholder="Dejar vacío para mantener" minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditPassword">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            <small class="text-muted">Dejar vacío para no cambiar</small>
                        </div>

                        <!-- URL de Acceso -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                URL de Acceso <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ti ti-link"></i>
                                </span>
                                <input type="url" class="form-control" name="url_acceso" id="editUrlAcceso"
                                       placeholder="https://ejemplo.com/login" required maxlength="255">
                            </div>
                            <div class="invalid-feedback">La URL debe ser válida.</div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold d-block">
                                Estado <span class="text-danger">*</span>
                            </label>

                            <div class="btn-group w-100" role="group" aria-label="Estado">
                                <input type="radio" class="btn-check" name="estado" id="editEstadoActivo" value="activo">
                                <label class="btn btn-outline-success" for="editEstadoActivo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="editEstadoInactivo" value="inactivo">
                                <label class="btn btn-outline-secondary" for="editEstadoInactivo">
                                    <i class="ti ti-ban me-1"></i> Inactivo
                                </label>
                            </div>
                            <div class="invalid-feedback">Seleccione un estado.</div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Actualizar Credencial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle mostrar/ocultar contraseña actual en modal editar
    document.getElementById('toggleEditCurrentPassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('editCurrentPassword');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    });

    // Toggle mostrar/ocultar nueva contraseña en modal editar
    document.getElementById('toggleEditPassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('editPassword');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    });
</script>