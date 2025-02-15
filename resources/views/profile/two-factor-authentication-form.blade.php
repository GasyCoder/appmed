<x-action-section>
    <x-slot name="title">
        <h2 class="dark:text-gray-100 claire:text-gray-900">{{ __('Two Factor Authentication') }}</h2>
    </x-slot>

    <x-slot name="description">
        <p class="dark:text-gray-300 claire:text-gray-700">
            {{ __('Add additional security to your account using two factor authentication.') }}
        </p>
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium dark:text-gray-100 claire:text-gray-900">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('Finish enabling two factor authentication.') }}
                @else
                    {{ __('You have enabled two factor authentication.') }}
                @endif
            @else
                {{ __('You have not enabled two factor authentication.') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm dark:text-gray-300 claire:text-gray-700">
            <p>
                {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
            </p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm dark:text-gray-300 claire:text-gray-700">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                        @else
                            {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-2 inline-block dark:bg-gray-800 claire:bg-white">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 max-w-xl text-sm dark:text-gray-300 claire:text-gray-700">
                    <p class="font-semibold">
                        {{ __('Setup Key') }}: {{ decrypt($this->user->two_factor_secret) }}
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4">
                        <x-label for="code" value="{{ __('Code') }}" class="dark:text-gray-300 claire:text-gray-700" />

                        <x-input id="code" type="text" name="code" class="block mt-1 w-1/2 dark:bg-gray-700 dark:text-gray-200 claire:bg-white claire:text-gray-900" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />

                        <x-input-error for="code" class="mt-2 dark:text-red-400 claire:text-red-600" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm dark:text-gray-300 claire:text-gray-700">
                    <p class="font-semibold">
                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                    </p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm dark:bg-gray-700 claire:bg-gray-100 rounded-lg">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div class="dark:text-gray-200 claire:text-gray-800">{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled" class="dark:bg-blue-600 dark:hover:bg-blue-700 claire:bg-blue-500 claire:hover:bg-blue-600">
                        {{ __('Enable') }}
                    </x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button class="me-3 dark:border-gray-600 dark:hover:border-gray-400 claire:border-gray-300 claire:hover:border-gray-500">
                            {{ __('Regenerate Recovery Codes') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <x-button type="button" class="me-3 dark:bg-gray-600 dark:hover:bg-gray-700 claire:bg-blue-500 claire:hover:bg-blue-600" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </x-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button class="me-3 dark:border-gray-600 dark:hover:border-gray-400 claire:border-gray-300 claire:hover:border-gray-500">
                            {{ __('Show Recovery Codes') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @endif
        
                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-secondary-button wire:loading.attr="disabled" class="dark:border-gray-600 dark:hover:border-gray-400 claire:border-gray-300 claire:hover:border-gray-500">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-danger-button wire:loading.attr="disabled" class="dark:bg-red-600 dark:hover:bg-red-700 claire:bg-red-500 claire:hover:bg-red-600">
                            {{ __('Disable') }}
                        </x-danger-button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
        
    </x-slot>
</x-action-section>
