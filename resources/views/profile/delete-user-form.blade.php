<div>
    <h5 class="mb-3 text-uppercase bg-danger bg-opacity-10 p-1 border-dashed border rounded border-danger text-center text-danger">
        <i class="ti ti-alert-triangle me-1"></i> Delete Account
    </h5>

    <div class="alert alert-danger d-flex align-items-start">
        <i class="ti ti-alert-triangle fs-xl me-2 mt-1"></i>
        <div>
            <h6 class="alert-heading mb-2">Permanently delete your account</h6>
            <p class="mb-0">
                Once your account is deleted, all of its resources and data will be permanently deleted. 
                Before deleting your account, please download any data or information that you wish to retain.
            </p>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-danger" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
            <i class="ti ti-trash me-1"></i> Delete Account
        </button>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <x-dialog-modal wire:model.live="confirmingUserDeletion">
        <x-slot name="title">
            <h4 class="modal-title text-danger">
                <i class="ti ti-alert-triangle me-2"></i> Delete Account
            </h4>
        </x-slot>

        <x-slot name="content">
            <div class="alert alert-danger">
                <strong>Are you sure you want to delete your account?</strong>
            </div>

            <p>Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>

            <div class="mt-3" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                    placeholder="Password" autocomplete="current-password"
                    x-ref="password"
                    wire:model="password" 
                    wire:keydown.enter="deleteUser">

                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </x-slot>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary" wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                Cancel
            </button>

            <button type="button" class="btn btn-danger ms-2" wire:click="deleteUser" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="deleteUser">
                    <i class="ti ti-trash me-1"></i> Delete Account
                </span>
                <span wire:loading wire:target="deleteUser">
                    <span class="spinner-border spinner-border-sm me-1"></span> Deleting...
                </span>
            </button>
        </x-slot>
    </x-dialog-modal>
</div>