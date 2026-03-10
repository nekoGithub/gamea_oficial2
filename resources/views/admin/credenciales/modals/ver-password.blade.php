<!-- Modal Ver Contraseña -->
<div class="modal fade" id="verPasswordModal" tabindex="-1" aria-labelledby="verPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title" id="verPasswordModalLabel">
                    <i class="ti ti-shield-lock me-2"></i>
                    Verificación de Seguridad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="verPasswordCredencialId">

                <!-- Contenedor del formulario de verificación -->
                <div id="verPasswordFormContainer">
                    <div class="alert alert-warning d-flex align-items-start mb-3">
                        <i class="ti ti-alert-triangle fs-3 me-2 mt-1"></i>
                        <div>
                            <strong>Verificación Requerida</strong>
                            <p class="mb-0 mt-1">Por seguridad, ingresa tu contraseña actual para ver esta credencial.</p>
                        </div>
                    </div>

                    <form id="verPasswordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label fw-semibold">
                                Tu Contraseña Actual <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ti ti-key"></i>
                                </span>
                                <input type="password" class="form-control" id="currentPassword" 
                                       placeholder="Ingresa tu contraseña" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">La contraseña es incorrecta.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-lock-open me-1"></i>
                                Verificar y Ver Contraseña
                            </button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contenedor de contraseña revelada -->
                <div id="passwordReveladaContainer" class="d-none">
                    <div class="alert alert-success d-flex align-items-center mb-3">
                        <i class="ti ti-check fs-3 me-2"></i>
                        <div>
                            <strong>Contraseña Revelada</strong>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="ti ti-lock-open me-1"></i>
                                Contraseña:
                            </label>
                            <div class="d-flex align-items-center justify-content-between">
                                <code id="passwordRevelada" class="fs-5 text-dark user-select-all"></code>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="copyPasswordBtn" title="Copiar">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-start mt-3 mb-0">
                        <i class="ti ti-info-circle fs-4 me-2 mt-1"></i>
                        <small>
                            Por seguridad, esta contraseña solo se mostrará una vez. 
                            Cópiala ahora si la necesitas.
                        </small>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    // Toggle mostrar/ocultar contraseña actual
    document.getElementById('toggleCurrentPassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('currentPassword');
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