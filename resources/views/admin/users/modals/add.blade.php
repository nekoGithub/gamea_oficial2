<!-- Modal Agregar Usuario -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Agregar Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Nombre completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Ej. Juan Pérez" required>
                            <div class="invalid-feedback">Por favor ingresa el nombre completo</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Ej. juan@example.com" required>
                            {{-- ✅ id para poder mostrar el error de duplicado --}}
                            <div class="invalid-feedback" id="emailFeedback">Por favor ingresa un correo válido</div>
                        </div>

                        <!-- Contraseña con ojito -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Ingresa una contraseña segura" required autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="ti ti-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            {{-- ✅ Quitado d-block para que no aparezca siempre --}}
                            <div class="invalid-feedback">La contraseña no cumple los requisitos</div>

                            <!-- Requisitos de contraseña -->
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1">La contraseña debe contener:</small>
                                <div class="password-requirements">
                                    <div class="requirement" id="req-length">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Mínimo 8 caracteres</small>
                                    </div>
                                    <div class="requirement" id="req-uppercase">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Una letra mayúscula</small>
                                    </div>
                                    <div class="requirement" id="req-lowercase">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Una letra minúscula</small>
                                    </div>
                                    <div class="requirement" id="req-number">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Un número</small>
                                    </div>
                                    <div class="requirement" id="req-special">
                                        <i class="ti ti-circle-x text-muted"></i>
                                        <small>Un carácter especial (@$!%*?&)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Avatar con Preview Circular -->
                        <div class="col-md-6">
                            <label class="form-label">Avatar (opcional)</label>
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-preview" id="avatarPreview">
                                        <img src="https://ui-avatars.com/api/?name=BS&background=random&size=200"
                                            alt="Avatar" id="avatarImage">
                                        <div class="avatar-drag-overlay" id="avatarDragOverlay">
                                            <i class="ti ti-upload"></i>
                                            <p>Suelta aquí</p>
                                        </div>
                                    </div>
                                    <label for="avatar" class="avatar-upload-btn">
                                        <i class="ti ti-camera"></i>
                                    </label>
                                    <button type="button" class="avatar-remove-btn d-none" id="removeAvatar">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="file" class="d-none" id="avatar" name="avatar"
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
                                <div class="row g-2" id="rolesContainer">
                                    @foreach ($roles as $role)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input role-checkbox" type="checkbox"
                                                    name="role" value="{{ $role->name }}"
                                                    id="role_{{ $role->id }}">
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-danger mt-2 d-none" id="roleError">
                                    <small>Por favor selecciona un rol</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="ti ti-device-floppy me-1"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Progreso (sin cambios) -->
<div class="modal fade" id="progressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                <h5 class="mb-3" id="progressTitle">Guardando usuario...</h5>
                <p class="text-muted mb-3" id="progressMessage">Por favor espera mientras procesamos la información
                </p>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar"
                        id="progressBar" style="width: 0%">
                        <span id="progressPercent">0%</span>
                    </div>
                </div>
                <small class="text-muted d-block mt-2" id="progressStep">Validando datos...</small>
            </div>
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
    }

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

    #progressModal .modal-content {
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    #progressModal .progress {
        border-radius: 10px;
    }

    #progressModal .progress-bar {
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                if (type === 'text') {
                    toggleIcon.classList.remove('ti-eye');
                    toggleIcon.classList.add('ti-eye-off');
                } else {
                    toggleIcon.classList.remove('ti-eye-off');
                    toggleIcon.classList.add('ti-eye');
                }
            });
        }

        const avatarInput = document.getElementById('avatar');
        const avatarImage = document.getElementById('avatarImage');
        const avatarPreview = document.getElementById('avatarPreview');
        const removeAvatarBtn = document.getElementById('removeAvatar');

        avatarPreview.addEventListener('click', function() {
            avatarInput.click();
        });

        function processAvatarFile(file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('La imagen no debe superar 2MB');
                return false;
            }
            const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Solo se permiten imágenes PNG, JPG o GIF');
                return false;
            }
            const reader = new FileReader();
            reader.onload = function(event) {
                avatarImage.src = event.target.result;
                removeAvatarBtn.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
            return true;
        }

        avatarInput.addEventListener('change', function(e) {
            if (e.target.files[0]) processAvatarFile(e.target.files[0]);
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            avatarPreview.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        ['dragenter', 'dragover'].forEach(ev => avatarPreview.addEventListener(ev, () => avatarPreview.classList
            .add('dragging')));
        ['dragleave', 'drop'].forEach(ev => avatarPreview.addEventListener(ev, () => avatarPreview.classList
            .remove('dragging')));
        avatarPreview.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                avatarInput.files = dataTransfer.files;
                processAvatarFile(file);
            }
        });

        removeAvatarBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            avatarInput.value = '';
            avatarImage.src = 'https://ui-avatars.com/api/?name=BS&background=random&size=200';
            removeAvatarBtn.classList.add('d-none');
        });

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                document.getElementById('req-length').classList.toggle('valid', password.length >= 8);
                document.getElementById('req-uppercase').classList.toggle('valid', /[A-Z]/.test(
                    password));
                document.getElementById('req-lowercase').classList.toggle('valid', /[a-z]/.test(
                    password));
                document.getElementById('req-number').classList.toggle('valid', /[0-9]/.test(password));
                document.getElementById('req-special').classList.toggle('valid', /[@$!%*?&]/.test(
                    password));
            });
        }

        const roleCheckboxes = document.querySelectorAll('.role-checkbox');
        roleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    roleCheckboxes.forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                    document.getElementById('roleError').classList.add('d-none');
                }
            });
        });

        const formInputs = document.querySelectorAll('#addUserForm input');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const parent = this.closest('.col-md-6, .col-12');
                if (parent) {
                    const feedback = parent.querySelector('.invalid-feedback');
                    if (feedback) feedback.style.display = 'none';
                }
            });
        });

        // ── SUBMIT ─────────────────────────────────────────────────────────────
        const addUserForm = document.getElementById('addUserForm');
        const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));
        const addUserModalEl = document.getElementById('addUserModal');
        const addUserModalInstance = bootstrap.Modal.getInstance(addUserModalEl) || new bootstrap.Modal(
            addUserModalEl);

        // ✅ CAMBIO 1: async para poder usar await dentro
        addUserForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            let isValid = true;
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');

            // Validar nombre
            if (!nameInput.value.trim()) {
                nameInput.classList.add('is-invalid');
                nameInput.nextElementSibling.style.display = 'block';
                isValid = false;
            }

            // Validar email formato
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value)) {
                emailInput.classList.add('is-invalid');
                document.getElementById('emailFeedback').textContent =
                    'Por favor ingresa un correo válido';
                document.getElementById('emailFeedback').style.display = 'block';
                isValid = false;
            }

            // Validar contraseña (inline, sin Swal)
            const password = passwordInput.value;
            if (!password || password.length < 8 || !/[A-Z]/.test(password) || !/[a-z]/.test(
                    password) ||
                !/[0-9]/.test(password) || !/[@$!%*?&]/.test(password)) {
                passwordInput.classList.add('is-invalid');
                isValid = false;
            }

            // Validar rol (inline, sin Swal)
            const roleSelected = Array.from(roleCheckboxes).some(cb => cb.checked);
            if (!roleSelected) {
                document.getElementById('roleError').classList.remove('d-none');
                isValid = false;
            }

            // ✅ CAMBIO 2: verificar email duplicado ANTES de la animación
            if (isValid && emailInput.value.trim()) {
                try {
                    const checkRes = await fetch('{{ route('admin.users.checkEmail') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            email: emailInput.value.trim()
                        })
                    });
                    const checkData = await checkRes.json();
                    if (checkData.exists) {
                        // ✅ CAMBIO 3: error inline en el campo email
                        emailInput.classList.add('is-invalid');
                        document.getElementById('emailFeedback').textContent =
                            'Este correo ya está registrado en el sistema';
                        document.getElementById('emailFeedback').style.display = 'block';
                        isValid = false;
                    }
                } catch (err) {
                    // si falla el check, dejamos pasar (el servidor lo atrapará)
                }
            }

            if (!isValid) return; // se queda en el modal con los errores inline

            // ── A partir de aquí todo igual que antes ─────────────────────────
            addUserModalInstance.hide();
            progressModal.show();

            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const progressMessage = document.getElementById('progressMessage');
            const progressStep = document.getElementById('progressStep');

            const steps = [{
                    percent: 20,
                    title: 'Validando datos...',
                    message: 'Verificando información'
                },
                {
                    percent: 40,
                    title: 'Procesando avatar...',
                    message: 'Guardando imagen'
                },
                {
                    percent: 60,
                    title: 'Creando usuario...',
                    message: 'Registrando en BD'
                },
                {
                    percent: 80,
                    title: 'Enviando correo...',
                    message: 'Enviando verificación'
                },
                {
                    percent: 100,
                    title: '¡Completado!',
                    message: 'Usuario registrado'
                }
            ];

            let currentStep = 0;
            const progressInterval = setInterval(() => {
                if (currentStep < steps.length) {
                    const step = steps[currentStep];
                    progressBar.style.width = step.percent + '%';
                    progressPercent.textContent = step.percent + '%';
                    progressStep.textContent = step.title;
                    progressMessage.textContent = step.message;
                    currentStep++;
                }
            }, 1000);

            const formData = new FormData(addUserForm);
            fetch(addUserForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(() => {}).catch(() => {});

            setTimeout(() => {
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                progressPercent.textContent = '100%';

                setTimeout(() => {
                    progressModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Usuario Creado!',
                        html: `<p>El usuario <strong>${formData.get('name')}</strong> ha sido registrado.</p>
                               <p class="text-muted mb-0">Se envió un correo a <strong>${formData.get('email')}</strong></p>`,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#5156be',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => window.location.reload());
                }, 500);
            }, 5000);
        });

        // Limpiar modal (sin cambios)
        if (addUserModalEl) {
            addUserModalEl.addEventListener('hidden.bs.modal', function() {
                addUserForm.reset();
                addUserForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                    'is-invalid'));
                addUserForm.querySelectorAll('.invalid-feedback').forEach(el => el.style.display =
                    'none');
                document.querySelectorAll('.requirement').forEach(req => req.classList.remove('valid'));
                passwordInput.setAttribute('type', 'password');
                toggleIcon.classList.remove('ti-eye-off');
                toggleIcon.classList.add('ti-eye');
                document.getElementById('roleError').classList.add('d-none');
                avatarInput.value = '';
                avatarImage.src = 'https://ui-avatars.com/api/?name=BS&background=random&size=200';
                removeAvatarBtn.classList.add('d-none');
            });
        }
    });
</script>
