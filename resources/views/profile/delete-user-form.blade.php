<x-action-section>
    <x-slot name="title">
        <h2 class="dark:text-gray-100 claire:text-gray-900">{{ __('Delete Account') }}</h2>
    </x-slot>

    <x-slot name="description">
        <p class="dark:text-gray-300 claire:text-gray-700">{{ __('Permanently delete your account.') }}</p>
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm dark:text-gray-300 claire:text-gray-700">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled" class="dark:bg-red-600 dark:hover:bg-red-700 claire:bg-red-500 claire:hover:bg-red-600">
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion" class="dark:bg-gray-800 dark:text-gray-200 claire:bg-white claire:text-gray-900">
            <x-slot name="title">
                <h2 class="dark:text-gray-100 claire:text-gray-900">{{ __('Delete Account') }}</h2>
            </x-slot>

            <x-slot name="content">
                <p class="dark:text-gray-300 claire:text-gray-700">
                    {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4 dark:bg-gray-700 dark:text-gray-200 claire:bg-white claire:text-gray-900"
                                autocomplete="current-password"
                                placeholder="{{ __('Password') }}"
                                x-ref="password"
                                wire:model="password"
                                wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2 dark:text-red-400 claire:text-red-600" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled" class="dark:border-gray-600 dark:hover:border-gray-400 claire:border-gray-300 claire:hover:border-gray-500">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="deleteUser" wire:loading.attr="disabled" class="dark:bg-red-600 dark:hover:bg-red-700 claire:bg-red-500 claire:hover:bg-red-600">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
