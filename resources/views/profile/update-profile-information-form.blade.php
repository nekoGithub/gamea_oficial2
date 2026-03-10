<form wire:submit="updateProfileInformation">
    <h5 class="mb-3 text-uppercase bg-light-subtle p-1 border-dashed border rounded border-light text-center">
        <i class="ti ti-user-circle me-1"></i> Información personal
    </h5>

    <!-- Photo -->
    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
    <div class="mb-4" x-data="{photoName: null, photoPreview: null}">
        <label class="form-label">Foto de perfil</label>
        
        <!-- Profile Photo File Input -->
        <input type="file" id="photo" class="d-none"
            wire:model.live="photo"
            x-ref="photo"
            x-on:change="
                photoName = $refs.photo.files[0].name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreview = e.target.result;
                };
                reader.readAsDataURL($refs.photo.files[0]);
            " 
            accept="image/*">
        
        <!-- Current Profile Photo -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <div x-show="! photoPreview">
                <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" 
                    class="rounded-circle" width="80" height="80">
            </div>
            
            <!-- New Photo Preview -->
            <div x-show="photoPreview" style="display: none;">
                <span class="d-block rounded-circle" 
                    style="width: 80px; height: 80px; background-size: cover; background-repeat: no-repeat; background-position: center;"
                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                </span>
            </div>

            <div>
                <button type="button" class="btn btn-secondary btn-sm" x-on:click.prevent="$refs.photo.click()">
                    <i class="ti ti-upload me-1"></i> Seleccionar una nueva foto
                </button>
                
                @if ($this->user->profile_photo_path)
                    <button type="button" class="btn btn-danger btn-sm ms-2" wire:click="deleteProfilePhoto">
                        <i class="ti ti-trash me-1"></i> Remover foto
                    </button>
                @endif
            </div>
        </div>

        @error('photo')
            <div class="text-danger fs-sm">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="name">Nombre Completo</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    id="name" wire:model="state.name" placeholder="Enter your full name" required autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="email">Correo Electronico</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                    id="email" wire:model="state.email" placeholder="Enter your email" required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                
                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-2 d-flex align-items-start">
                        <i class="ti ti-alert-triangle me-2 mt-1"></i>
                        <div>
                            <p class="mb-1 fs-sm">Tu dirección de correo electrónico no está verificada.</p>
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-sm" 
                                wire:click.prevent="sendEmailVerification">
                                Haz clic aquí para reenviar el correo electrónico de verificación.
                            </button>
                        </div>
                    </div>

                    @if ($this->verificationLinkSent)
                        <div class="alert alert-success mt-2">
                            <i class="ti ti-check me-2"></i>
                            <span class="fs-sm">Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.</span>
                        </div>
                    @endif
                @endif
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
        <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="photo">
            <span wire:loading.remove wire:target="updateProfileInformation">
                <i class="ti ti-device-floppy me-1"></i> Guardando cambios
            </span>
            <span wire:loading wire:target="updateProfileInformation">
                <span class="spinner-border spinner-border-sm me-1"></span> Guardando...
            </span>
        </button>
    </div>
</form>