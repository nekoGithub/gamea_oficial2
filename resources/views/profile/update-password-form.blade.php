<form wire:submit="updatePassword">
    <h5 class="mb-3 text-uppercase bg-light-subtle p-1 border-dashed border rounded border-light text-center">
        <i class="ti ti-lock me-1"></i> Actualizar contraseña
    </h5>

    <p class="text-muted mb-4">
        Asegúrate de que tu cuenta utilice una contraseña larga y aleatoria para mantener la seguridad.
    </p>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label" for="current_password">Contraseña actual</label>
                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                    id="current_password" wire:model="state.current_password" 
                    placeholder="Introduce tu contraseña actual" autocomplete="current-password" required>
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="password">Nueva contraseña</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                    id="password" wire:model="state.password" 
                    placeholder="Introduce la nueva contraseña" autocomplete="new-password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <span class="form-text fs-xs fst-italic text-muted">
                    La contraseña debe tener al menos 8 caracteres.
                </span>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                    id="password_confirmation" wire:model="state.password_confirmation" 
                    placeholder="Confirma la nueva contraseña" autocomplete="new-password" required>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <x-action-message class="d-inline-block me-3" on="saved">
        <div class="alert alert-success mb-0 py-2 px-3">
            <i class="ti ti-check me-1"></i>
            <span class="fs-sm">{{ __('Saved.') }}</span>
        </div>
    </x-action-message>

    <!-- Submit Button -->
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="updatePassword">
                <i class="ti ti-device-floppy me-1"></i> Actualizar contraseña
            </span>
            <span wire:loading wire:target="updatePassword">
                <span class="spinner-border spinner-border-sm me-1"></span> Actualizando...
            </span>
        </button>
    </div>
</form>