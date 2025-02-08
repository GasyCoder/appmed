<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4 dark:bg-gray-800 dark:text-gray-200 claire:bg-gray-100 claire:text-gray-800">
            <x-label for="current_password" value="{{ __('Current Password') }}" />
            <div class="relative">
                <x-input id="current_password" type="password" class="mt-1 block w-full dark:bg-gray-700 dark:text-gray-200 claire:bg-white claire:text-gray-900" wire:model="state.current_password" autocomplete="current-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5" onclick="togglePasswordVisibility('current_password', 'eye-icon-current', 'eye-off-icon-current')">
                    <svg id="eye-icon-current" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-off-icon-current" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error for="current_password" class="mt-2 dark:text-red-400 claire:text-red-600" />
        </div>

        <div class="col-span-6 sm:col-span-4 dark:bg-gray-800 dark:text-gray-200 claire:bg-gray-100 claire:text-gray-800">
            <x-label for="password" value="{{ __('New Password') }}" />
            <div class="relative">
                <x-input id="password" type="password" class="mt-1 block w-full dark:bg-gray-700 dark:text-gray-200 claire:bg-white claire:text-gray-900" wire:model="state.password" autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5" onclick="togglePasswordVisibility('password', 'eye-icon-password', 'eye-off-icon-password')">
                    <svg id="eye-icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-off-icon-password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error for="password" class="mt-2 dark:text-red-400 claire:text-red-600" />
        </div>

        <div class="col-span-6 sm:col-span-4 dark:bg-gray-800 dark:text-gray-200 claire:bg-gray-100 claire:text-gray-800">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <div class="relative">
                <x-input id="password_confirmation" type="password" class="mt-1 block w-full dark:bg-gray-700 dark:text-gray-200 claire:bg-white claire:text-gray-900" wire:model="state.password_confirmation" autocomplete="new-password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5" onclick="togglePasswordVisibility('password_confirmation', 'eye-icon-confirm', 'eye-off-icon-confirm')">
                    <svg id="eye-icon-confirm" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-off-icon-confirm" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error for="password_confirmation" class="mt-2 dark:text-red-400 claire:text-red-600" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3 dark:bg-blue-700 dark:text-blue-300 claire:bg-blue-200 claire:text-blue-700" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button class="dark:bg-blue-600 dark:hover:bg-blue-700 claire:bg-blue-500 claire:hover:bg-blue-600">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>

<script>
    function togglePasswordVisibility(inputId, eyeIconId, eyeOffIconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeIconId);
        const eyeOffIcon = document.getElementById(eyeOffIconId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }
</script>
