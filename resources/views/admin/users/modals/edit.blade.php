<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editUserForm" method="POST" action="{{ route('admin.users.update', ['user' => 0]) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">

                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label for="editName" class="form-label">
                                Nombre completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editName" name="name" required
                                maxlength="50">
                            <div class="d-flex justify-content-between">
                                <div class="invalid-feedback">Por favor ingresa el nombre completo</div>
                                <small class="text-muted ms-auto" id="editNameCounter">50 caracteres restantes</small>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="editEmail" class="form-label">
                                Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                            <div class="invalid-feedback">Por favor ingresa un correo válido</div>
                        </div>

                        <!-- Contraseña (opcional) -->
                        <div class="col-md-6">
                            <label for="editPassword" class="form-label">
                                Nueva Contraseña (opcional)
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="editPassword" name="password"
                                    placeholder="Dejar en blanco para no cambiar" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditPassword">
                                    <i class="ti ti-eye" id="toggleEditPasswordIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback d-block" style="display: none;">La contraseña debe cumplir con
                                los requisitos</div>
                            <small class="text-muted">Dejar en blanco si no deseas cambiar la contraseña</small>

                            <!-- Requisitos de contraseña (solo se muestran si empieza a escribir) -->
                            <div class="mt-2 d-none" id="editPasswordRequirements">
                                <small class="text-muted d-block mb-1">La contraseña debe contener:</small>
                                <div class="password-requirements">
                                    <div class="requirement" id="edit-req-length">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Mínimo 8 caracteres</small>
                                    </div>
                                    <div class="requirement" id="edit-req-uppercase">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Una letra mayúscula</small>
                                    </div>
                                    <div class="requirement" id="edit-req-lowercase">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Una letra minúscula</small>
                                    </div>
                                    <div class="requirement" id="edit-req-number">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Un número</small>
                                    </div>
                                    <div class="requirement" id="edit-req-special">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Un carácter especial (@$!%*?&)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Avatar con Preview Circular -->
                        <div class="col-md-6">
                            <label class="form-label">Cambiar Avatar (opcional)</label>
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-preview" id="editAvatarPreview">
                                        <img src="https://ui-avatars.com/api/?name=BS&background=random&size=200"
                                            alt="Avatar" id="editAvatarImage">
                                        <div class="avatar-drag-overlay" id="editAvatarDragOverlay">
                                            <i class="ti ti-upload"></i>
                                            <p>Suelta aquí</p>
                                        </div>
                                    </div>
                                    <label for="editAvatar" class="avatar-upload-btn">
                                        <i class="ti ti-camera"></i>
                                    </label>
                                    <button type="button" class="avatar-remove-btn d-none" id="removeEditAvatar">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="file" class="d-none" id="editAvatar" name="avatar"
                                accept="image/png,image/jpeg,image/jpg,image/gif">
                            <small class="text-muted d-block text-center mt-2">Arrastra tu imagen o haz click. Máx:
                                2MB</small>
                            <div class="invalid-feedback text-center">Formato de imagen no válido</div>
                        </div>

                        <!-- Roles -->
                        <div class="col-12">
                            <label class="form-label">
                                Asignar Rol <span class="text-danger">*</span>
                            </label>
                            <div class="border rounded p-3 bg-light">
                                <div class="row g-2" id="editRolesContainer">
                                    <!-- Se llena con JavaScript -->
                                </div>
                                <div class="text-danger mt-2 d-none" id="editRoleError">
                                    <small>Por favor selecciona un rol</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .password-requirements {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .requirement {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
    }

    .requirement.valid i {
        color: #28a745 !important;
    }

    .requirement.valid i:before {
        content: "\eb7a";
        /* ti-circle-check */
    }

    /* Avatar Preview Styles */
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #e0e0e0;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .avatar-preview:hover {
        border-color: #5156be;
        transform: scale(1.05);
    }

    .avatar-preview.dragging {
        border-color: #5156be;
        border-style: dashed;
        border-width: 3px;
        background: rgba(81, 86, 190, 0.1);
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-drag-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(81, 86, 190, 0.95);
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 10;
    }

    .avatar-preview.dragging .avatar-drag-overlay {
        display: flex;
    }

    .avatar-drag-overlay i {
        font-size: 40px;
        margin-bottom: 8px;
    }

    .avatar-drag-overlay p {
        margin: 0;
        font-weight: 600;
        font-size: 14px;
    }

    .avatar-upload-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 35px;
        height: 35px;
        background: #5156be;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #fff;
        z-index: 5;
    }

    .avatar-upload-btn:hover {
        background: #3d42a0;
        transform: scale(1.1);
    }

    .avatar-upload-btn i {
        color: #fff;
        font-size: 18px;
    }

    .avatar-remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 30px;
        height: 30px;
        background: #dc3545;
        border: 2px solid #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 5;
    }

    .avatar-remove-btn:hover {
        background: #c82333;
        transform: scale(1.1);
    }

    .avatar-remove-btn i {
        color: #fff;
        font-size: 16px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========== TOGGLE PASSWORD EN EDITAR ==========
        const toggleEditPassword = document.getElementById('toggleEditPassword');
        const editPasswordInput = document.getElementById('editPassword');
        const toggleEditIcon = document.getElementById('toggleEditPasswordIcon');

        if (toggleEditPassword) {
            toggleEditPassword.addEventListener('click', function() {
                const type = editPasswordInput.getAttribute('type') === 'password' ? 'text' :
                    'password';
                editPasswordInput.setAttribute('type', type);

                if (type === 'text') {
                    toggleEditIcon.classList.remove('ti-eye');
                    toggleEditIcon.classList.add('ti-eye-off');
                } else {
                    toggleEditIcon.classList.remove('ti-eye-off');
                    toggleEditIcon.classList.add('ti-eye');
                }
            });
        }

        // ========== CONTADOR NOMBRE EDITAR ==========
        const editNameInput = document.getElementById('editName');
        const editNameCounter = document.getElementById('editNameCounter');
        if (editNameInput && editNameCounter) {
            editNameInput.addEventListener('input', function() {
                const remaining = 50 - this.value.length;
                editNameCounter.textContent = remaining + ' caracteres restantes';
                editNameCounter.className = remaining <= 10 ?
                    'text-danger ms-auto' :
                    remaining <= 20 ?
                    'text-warning ms-auto' :
                    'text-muted ms-auto';
            });
        }

        // Actualizar contador al cargar
        if (editNameCounter) {
            const remaining = 50 - data.name.length;
            editNameCounter.textContent = remaining + ' caracteres restantes';
            editNameCounter.className = remaining <= 10 ?
                'text-danger ms-auto' :
                remaining <= 20 ?
                'text-warning ms-auto' :
                'text-muted ms-auto';
        }

        // ========== AVATAR PREVIEW EN EDITAR ==========
        const editAvatarInput = document.getElementById('editAvatar');
        const editAvatarImage = document.getElementById('editAvatarImage');
        const editAvatarPreview = document.getElementById('editAvatarPreview');
        const removeEditAvatarBtn = document.getElementById('removeEditAvatar');

        // Click en preview abre selector
        editAvatarPreview.addEventListener('click', function() {
            editAvatarInput.click();
        });

        // Función para procesar archivo (reutilizable)
        function processEditAvatarFile(file) {
            // Validar tamaño (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('La imagen no debe superar 2MB');
                return false;
            }

            // Validar tipo
            const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Solo se permiten imágenes PNG, JPG o GIF');
                return false;
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(event) {
                editAvatarImage.src = event.target.result;
                removeEditAvatarBtn.classList.remove('d-none');
            };
            reader.readAsDataURL(file);

            return true;
        }

        // Preview cuando selecciona imagen
        editAvatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                processEditAvatarFile(file);
            }
        });

        // ========== DRAG & DROP EN EDITAR ==========
        const editAvatarDragOverlay = document.getElementById('editAvatarDragOverlay');

        // Prevenir comportamiento por defecto
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            editAvatarPreview.addEventListener(eventName, preventEditDefaults, false);
        });

        function preventEditDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight cuando arrastra sobre el área
        ['dragenter', 'dragover'].forEach(eventName => {
            editAvatarPreview.addEventListener(eventName, function() {
                editAvatarPreview.classList.add('dragging');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            editAvatarPreview.addEventListener(eventName, function() {
                editAvatarPreview.classList.remove('dragging');
            });
        });

        // Manejar drop
        editAvatarPreview.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                const file = files[0];

                // Asignar al input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                editAvatarInput.files = dataTransfer.files;

                // Procesar
                processEditAvatarFile(file);
            }
        });

        // Remover imagen
        removeEditAvatarBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            editAvatarInput.value = '';
            editAvatarImage.src = 'https://ui-avatars.com/api/?name=BS&background=random&size=200';
            removeEditAvatarBtn.classList.add('d-none');
        });

        // ========== VALIDACIÓN DE CONTRASEÑA EN EDITAR ==========
        if (editPasswordInput) {
            editPasswordInput.addEventListener('input', function() {
                const password = this.value;
                const requirementsDiv = document.getElementById('editPasswordRequirements');

                // Mostrar requisitos si empieza a escribir
                if (password.length > 0) {
                    requirementsDiv.classList.remove('d-none');

                    // Mínimo 8 caracteres
                    const lengthReq = document.getElementById('edit-req-length');
                    if (password.length >= 8) {
                        lengthReq.classList.add('valid');
                    } else {
                        lengthReq.classList.remove('valid');
                    }

                    // Mayúscula
                    const uppercaseReq = document.getElementById('edit-req-uppercase');
                    if (/[A-Z]/.test(password)) {
                        uppercaseReq.classList.add('valid');
                    } else {
                        uppercaseReq.classList.remove('valid');
                    }

                    // Minúscula
                    const lowercaseReq = document.getElementById('edit-req-lowercase');
                    if (/[a-z]/.test(password)) {
                        lowercaseReq.classList.add('valid');
                    } else {
                        lowercaseReq.classList.remove('valid');
                    }

                    // Número
                    const numberReq = document.getElementById('edit-req-number');
                    if (/[0-9]/.test(password)) {
                        numberReq.classList.add('valid');
                    } else {
                        numberReq.classList.remove('valid');
                    }

                    // Carácter especial
                    const specialReq = document.getElementById('edit-req-special');
                    if (/[@$!%*?&]/.test(password)) {
                        specialReq.classList.add('valid');
                    } else {
                        specialReq.classList.remove('valid');
                    }
                } else {
                    requirementsDiv.classList.add('d-none');
                }
            });
        }

        // ========== SOLO UN ROL SELECCIONADO EN EDITAR ==========
        // Se maneja dinámicamente cuando se cargan los roles

        // ========== LIMPIAR ERRORES AL ESCRIBIR ==========
        const editFormInputs = document.querySelectorAll('#editUserForm input');
        editFormInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                // Buscar y ocultar el mensaje de error asociado
                const parent = this.closest('.col-md-6, .col-12');
                if (parent) {
                    const feedback = parent.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                }
            });
        });

        // ========== LIMPIAR MODAL AL CERRAR ==========
        const editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editUserForm');
                if (form) {
                    form.reset();
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                        'is-invalid'));
                    form.querySelectorAll('.invalid-feedback').forEach(el => {
                        el.style.display = 'none';
                    });

                    // Reset password requirements
                    document.getElementById('editPasswordRequirements').classList.add('d-none');
                    document.querySelectorAll('#editPasswordRequirements .requirement').forEach(req =>
                        req.classList.remove('valid'));

                    // Reset password type
                    editPasswordInput.setAttribute('type', 'password');
                    toggleEditIcon.classList.remove('ti-eye-off');
                    toggleEditIcon.classList.add('ti-eye');

                    // Ocultar error de rol
                    document.getElementById('editRoleError').classList.add('d-none');

                    // Reset avatar
                    editAvatarInput.value = '';
                    editAvatarImage.src =
                        'https://ui-avatars.com/api/?name=BS&background=random&size=200';
                    removeEditAvatarBtn.classList.add('d-none');
                }
            });
        }
    });
</script>
