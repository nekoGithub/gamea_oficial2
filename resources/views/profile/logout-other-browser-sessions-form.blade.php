<div>
    <h5 class="mb-3 text-uppercase bg-light-subtle p-1 border-dashed border rounded border-light text-center">
        <i class="ti ti-devices me-1"></i> Browser Sessions
    </h5>

    <p class="text-muted mb-4">
        Manage and log out your active sessions on other browsers and devices. If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.
    </p>

    @if (count($this->sessions) > 0)
        <div class="mb-4">
            @foreach ($this->sessions as $session)
                <div class="d-flex align-items-center gap-3 border-bottom border-light pb-3 mb-3">
                    <div>
                        @if ($session->agent->isDesktop())
                            <i class="ti ti-device-desktop fs-xl text-primary"></i>
                        @else
                            <i class="ti ti-device-mobile fs-xl text-primary"></i>
                        @endif
                    </div>

                    <div class="flex-grow-1">
                        <div class="fw-semibold">
                            {{ $session->agent->platform() ? $session->agent->platform() : __('Unknown') }} - 
                            {{ $session->agent->browser() ? $session->agent->browser() : __('Unknown') }}
                        </div>
                        <div class="text-muted fs-sm">
                            {{ $session->ip_address }},
                            
                            @if ($session->is_current_device)
                                <span class="text-success fw-semibold">{{ __('This device') }}</span>
                            @else
                                {{ __('Last active') }} {{ $session->last_active }}
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Success Message -->
    <x-action-message class="d-inline-block me-3" on="loggedOut">
        <div class="alert alert-success mb-0 py-2 px-3">
            <i class="ti ti-check me-1"></i>
            <span class="fs-sm">{{ __('Done.') }}</span>
        </div>
    </x-action-message>

    <div class="d-flex gap-2 justify-content-end">
        <button type="button" class="btn btn-danger" wire:click="confirmLogout" wire:loading.attr="disabled">
            <i class="ti ti-logout me-1"></i> Log Out Other Browser Sessions
        </button>
    </div>

    <!-- Log Out Other Devices Confirmation Modal -->
    <x-dialog-modal wire:model.live="confirmingLogout">
        <x-slot name="title">
            <h4 class="modal-title">Log Out Other Browser Sessions</h4>
        </x-slot>

        <x-slot name="content">
            <p>Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.</p>

            <div class="mt-3" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                    placeholder="Password" autocomplete="current-password"
                    x-ref="password"
                    wire:model="password" 
                    wire:keydown.enter="logoutOtherBrowserSessions">

                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </x-slot>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                Cancel
            </button>

            <button type="button" class="btn btn-danger ms-2" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="logoutOtherBrowserSessions">
                    Log Out Other Browser Sessions
                </span>
                <span wire:loading wire:target="logoutOtherBrowserSessions">
                    <span class="spinner-border spinner-border-sm me-1"></span> Logging out...
                </span>
            </button>
        </x-slot>
    </x-dialog-modal>
</div>