<form action="{{ route('profile.update.info') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <h5 class="mb-3 text-uppercase bg-light-subtle p-1 border-dashed border rounded border-light text-center">
        <i class="ti ti-user-circle me-1"></i> Información personal
    </h5>

    @if (session('success_info'))
        <div class="alert alert-success">
            <i class="ti ti-check me-1"></i> {{ session('success_info') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="name">Nombre Completo</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name', Auth::user()->name) }}" maxlength="50" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="email">Correo Electrónico</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email', Auth::user()->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Avatar (opcional)</label>
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ Auth::user()->profile_photo_path
                        ? asset('storage/avatars/' . Auth::user()->profile_photo_path)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                        class="rounded-circle" width="60" height="60" id="avatarPreviewImg">
                    <input type="file" class="form-control" name="avatar"
                        accept="image/png,image/jpeg,image/jpg,image/gif" id="avatarFileInput">
                </div>
                @error('avatar')
                    <div class="text-danger fs-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="text-end mt-2">
        <button type="submit" class="btn btn-success">
            <i class="ti ti-device-floppy me-1"></i> Guardar cambios
        </button>
    </div>
</form>

<script>
    document.getElementById('avatarFileInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = evt => document.getElementById('avatarPreviewImg').src = evt.target.result;
            reader.readAsDataURL(file);
        }
    });
</script>
