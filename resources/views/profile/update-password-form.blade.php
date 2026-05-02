<div id="passwordFormWrapper">
    <h5 class="mb-3 text-uppercase bg-light-subtle p-1 border-dashed border rounded border-light text-center">
        <i class="ti ti-lock me-1"></i> Actualizar contraseña
    </h5>

    <p class="text-muted mb-4">
        Asegúrate de que tu cuenta utilice una contraseña larga y aleatoria para mantener la seguridad.
    </p>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">Contraseña actual</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="current_password"
                        name="current_password" placeholder="Introduce tu contraseña actual" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('current_password', 'icon_current')">
                        <i class="ti ti-eye" id="icon_current"></i>
                    </button>
                </div>
                <div class="text-danger fs-sm mt-1 d-none" id="error_current_password"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="new_password"
                        name="password" placeholder="Introduce la nueva contraseña" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('new_password', 'icon_new')">
                        <i class="ti ti-eye" id="icon_new"></i>
                    </button>
                </div>
                <div class="text-danger fs-sm mt-1 d-none" id="error_password"></div>
                <small class="text-muted fst-italic">Mínimo 8 caracteres, mayúscula, minúscula, número y carácter especial (@$!%*?&)</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Confirmar nueva contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password_confirmation"
                        name="password_confirmation" placeholder="Confirma la nueva contraseña" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password_confirmation', 'icon_confirm')">
                        <i class="ti ti-eye" id="icon_confirm"></i>
                    </button>
                </div>
                <div class="text-danger fs-sm mt-1 d-none" id="error_password_confirmation"></div>
            </div>
        </div>
    </div>

    <div class="text-end mt-4">
        <button type="button" class="btn btn-success" id="btnUpdatePassword">
            <i class="ti ti-device-floppy me-1"></i> Actualizar contraseña
        </button>
    </div>
</div>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ti-eye', 'ti-eye-off');
    } else {
        input.type = 'password';
        icon.classList.replace('ti-eye-off', 'ti-eye');
    }
}

function clearPasswordErrors() {
    ['current_password', 'password', 'password_confirmation'].forEach(field => {
        const el = document.getElementById('error_' + field);
        if (el) { el.textContent = ''; el.classList.add('d-none'); }
        const input = document.getElementById(field === 'current_password' ? 'current_password' : (field === 'password' ? 'new_password' : 'password_confirmation'));
        if (input) input.classList.remove('is-invalid');
    });
}

document.getElementById('btnUpdatePassword').addEventListener('click', function() {
    clearPasswordErrors();

    const current  = document.getElementById('current_password').value;
    const password = document.getElementById('new_password').value;
    const confirm  = document.getElementById('password_confirmation').value;

    // Validación frontend
    let hasError = false;

    if (!current) {
        document.getElementById('error_current_password').textContent = 'La contraseña actual es obligatoria';
        document.getElementById('error_current_password').classList.remove('d-none');
        document.getElementById('current_password').classList.add('is-invalid');
        hasError = true;
    }

    if (!password) {
        document.getElementById('error_password').textContent = 'La nueva contraseña es obligatoria';
        document.getElementById('error_password').classList.remove('d-none');
        document.getElementById('new_password').classList.add('is-invalid');
        hasError = true;
    } else if (password.length < 8 || !/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password) || !/[@$!%*?&]/.test(password)) {
        document.getElementById('error_password').textContent = 'Debe contener mayúscula, minúscula, número y carácter especial (@$!%*?&)';
        document.getElementById('error_password').classList.remove('d-none');
        document.getElementById('new_password').classList.add('is-invalid');
        hasError = true;
    }

    if (password !== confirm) {
        document.getElementById('error_password_confirmation').textContent = 'Las contraseñas no coinciden';
        document.getElementById('error_password_confirmation').classList.remove('d-none');
        document.getElementById('password_confirmation').classList.add('is-invalid');
        hasError = true;
    }

    if (hasError) return;

    // Deshabilitar botón
    const btn = document.getElementById('btnUpdatePassword');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Actualizando...';

    fetch('{{ route("profile.update.password") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            current_password:      current,
            password:              password,
            password_confirmation: confirm
        })
    })
    .then(async res => {
        const data = await res.json().catch(() => null);

        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Actualizar contraseña';

        if (!res.ok) {
            if (data && data.errors) {
                // Mostrar errores específicos bajo cada campo
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById('error_' + field);
                    if (errorEl) {
                        errorEl.textContent = data.errors[field][0];
                        errorEl.classList.remove('d-none');
                    }
                });

                // SweetAlert con el primer error
                const firstError = Object.values(data.errors)[0][0];
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    text: firstError,
                    confirmButtonColor: '#5156be',
                    toast: false
                });
            }
            return;
        }

        // Éxito
        document.getElementById('current_password').value      = '';
        document.getElementById('new_password').value          = '';
        document.getElementById('password_confirmation').value = '';

        Swal.fire({
            icon: 'success',
            title: '¡Contraseña actualizada!',
            text: 'Tu contraseña fue cambiada correctamente',
            confirmButtonColor: '#5156be',
            timer: 3000,
            timerProgressBar: true
        });
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Actualizar contraseña';
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor', confirmButtonColor: '#5156be' });
    });
});
</script>