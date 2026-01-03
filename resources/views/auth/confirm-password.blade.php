<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-lg">
            {{-- Back to Home --}}
            <div class="mb-4">
                <x-back-to-home />
            </div>

            {{-- Card with Skeleton --}}
            <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
                {{-- Skeleton --}}
                <div x-show="loading" x-cloak>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden p-6">
                        <x-skeleton.line width="w-full" height="h-4" class="mb-4" />
                        <x-skeleton.form :fields="1" />
                    </div>
                </div>

                {{-- Actual Content --}}
                <div x-show="!loading" x-cloak x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <x-authentication-card-logo />
                            </div>

                            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                            </div>

                            <x-validation-errors class="mb-4" />

                            <form method="POST" action="{{ route('password.confirm') }}">
                                @csrf

                                <div>
                                    <x-label for="password" value="{{ __('Password') }}" />
                                    <x-input id="password" class="block mt-1 w-full rounded-xl" type="password" name="password" required autocomplete="current-password" autofocus />
                                </div>

                                <div class="flex justify-end mt-4">
                                    <x-button class="ms-4 bg-indigo-600 hover:bg-indigo-700">
                                        {{ __('Confirm') }}
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
