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

                        <!-- Usuario -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Usuario <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ti ti-user"></i>
                                </span>
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
                                <span class="input-group-text">
                                    <i class="ti ti-lock"></i>
                                </span>
                                <input type="password" class="form-control" name="password" id="addPassword"
                                       placeholder="Contraseña segura" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="toggleAddPassword">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            <small class="text-muted">Mínimo 6 caracteres</small>
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
                                <input type="url" class="form-control" name="url_acceso" 
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
                                <input type="radio" class="btn-check" name="estado" id="estado-activo" value="activo" checked>
                                <label class="btn btn-outline-success" for="estado-activo">
                                    <i class="ti ti-check me-1"></i> Activo
                                </label>

                                <input type="radio" class="btn-check" name="estado" id="estado-inactivo" value="inactivo">
                                <label class="btn btn-outline-secondary" for="estado-inactivo">
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
                        Guardar Credencial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle mostrar/ocultar contraseña en modal agregar
    document.getElementById('toggleAddPassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('addPassword');
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