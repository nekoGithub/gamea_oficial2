<div>
    <h5 class="mb-3 text-uppercase bg-light-subtle p-1 border-dashed border rounded border-light text-center">
        <i class="ti ti-shield-lock me-1"></i> Autenticación de dos factores
    </h5>

    <h6 class="mb-3">
        @if ($this->enabled)
            @if ($showingConfirmation)
                {{ __('Finalice la habilitación de la autenticación de dos factores.') }}
            @else
                {{ __('Has habilitado la autenticación de dos factores..') }}
            @endif
        @else
            {{ __('No has habilitado la autenticación de dos factores.') }}
        @endif
    </h6>

    <p class="text-muted mb-4">
        Cuando la autenticación de dos factores esté habilitada, se le solicitará un token seguro y aleatorio durante el proceso de autenticación. Puede obtener este token desde la aplicación Google Authenticator de su teléfono.
    </p>

    @if ($this->enabled)
        @if ($showingQrCode)
            <div class="alert alert-success">
                <p class="mb-0 fw-semibold">
                    @if ($showingConfirmation)
                        {{ __('Para finalizar la activación de la autenticación de dos factores, escanee el siguiente código QR con la aplicación de autenticación de su teléfono o ingrese la clave de configuración y proporcione el código OTP generado.') }}
                    @else
                        {{ __('La autenticación de dos factores ya está habilitada. Escanea el siguiente código QR con la aplicación de autenticación de tu teléfono o introduce la clave de configuración.') }}
                    @endif
                </p>
            </div>

            <div class="bg-white p-3 rounded text-center mb-3 border">
                {!! $this->user->twoFactorQrCodeSvg() !!}
            </div>

            <div class="alert alert-info">
                <h6 class="alert-heading">Setup Key</h6>
                <p class="mb-0 font-monospace">{{ decrypt($this->user->two_factor_secret) }}</p>
            </div>

            @if ($showingConfirmation)
                <div class="mb-3">
                    <label class="form-label" for="code">Code</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                        id="code" name="code" inputmode="numeric" autofocus autocomplete="one-time-code"
                        wire:model="code"
                        wire:keydown.enter="confirmTwoFactorAuthentication"
                        placeholder="Enter 6-digit code">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        @endif

        @if ($showingRecoveryCodes)
            <div class="alert alert-warning">
                <p class="mb-0 fw-semibold">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                </p>
            </div>

            <div class="bg-light p-3 rounded mb-3 font-monospace">
                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                    <div class="mb-1">{{ $code }}</div>
                @endforeach
            </div>
        @endif

        <div class="d-flex gap-2 justify-content-end flex-wrap">
            @if ($showingRecoveryCodes)
                <button type="button" class="btn btn-secondary" wire:click="showQrCode">
                    <i class="ti ti-qrcode me-1"></i> Show QR Code
                </button>

                <x-confirms-password wire:then="regenerateRecoveryCodes">
                    <button type="button" class="btn btn-warning">
                        <i class="ti ti-refresh me-1"></i> Regenerate Recovery Codes
                    </button>
                </x-confirms-password>
            @elseif ($showingConfirmation)
                <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                    <button type="button" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmTwoFactorAuthentication">
                            <i class="ti ti-check me-1"></i> Confirm
                        </span>
                        <span wire:loading wire:target="confirmTwoFactorAuthentication">
                            <span class="spinner-border spinner-border-sm me-1"></span> Confirming...
                        </span>
                    </button>
                </x-confirms-password>

                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <button type="button" class="btn btn-secondary" wire:loading.attr="disabled">
                        Cancel
                    </button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="showRecoveryCodes">
                    <button type="button" class="btn btn-secondary">
                        <i class="ti ti-key me-1"></i> Show Recovery Codes
                    </button>
                </x-confirms-password>

                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <button type="button" class="btn btn-danger" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="disableTwoFactorAuthentication">
                            <i class="ti ti-shield-off me-1"></i> Disable
                        </span>
                        <span wire:loading wire:target="disableTwoFactorAuthentication">
                            <span class="spinner-border spinner-border-sm me-1"></span> Disabling...
                        </span>
                    </button>
                </x-confirms-password>
            @endif
        </div>
    @else
        <div class="alert alert-info d-flex align-items-start">
            <i class="ti ti-info-circle fs-xl me-2 mt-1"></i>
            <div>
                <p class="mb-0">
                    You have not enabled two factor authentication. When two factor authentication is enabled, 
                    you will be prompted for a secure, random token during authentication.
                </p>
            </div>
        </div>

        <div class="text-end">
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <button type="button" class="btn btn-success" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="enableTwoFactorAuthentication">
                        <i class="ti ti-shield-check me-1"></i> Enable Two Factor Authentication
                    </span>
                    <span wire:loading wire:target="enableTwoFactorAuthentication">
                        <span class="spinner-border spinner-border-sm me-1"></span> Enabling...
                    </span>
                </button>
            </x-confirms-password>
        </div>
    @endif
</div>