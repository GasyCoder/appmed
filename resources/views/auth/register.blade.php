<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-lg">
            {{-- Back to Home Button --}}
            <div class="mb-4">
                <x-back-to-home />
            </div>

            {{-- Card with Skeleton Loading --}}
            <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
                {{-- Skeleton --}}
                <div x-show="loading" x-cloak>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
                        <div class="p-6 text-center">
                            <x-skeleton.avatar size="h-16 w-16" class="mx-auto mb-4" />
                            <x-skeleton.line width="w-48 mx-auto" height="h-6" />
                        </div>
                        <div class="px-6 pb-6">
                            <x-skeleton.form :fields="4" />
                        </div>
                    </div>
                </div>

                {{-- Actual Content --}}
                <div x-show="!loading" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
                        <div class="p-6 pb-0 text-center">
                             <x-authentication-card-logo />
                             <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Inscription</h2>
                        </div>

                        <div class="p-6">
                            <x-validation-errors class="mb-4" />

                            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <x-label for="name" value="{{ __('Name') }}" />
                                    <x-input id="name" class="block mt-1 w-full rounded-xl" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                </div>

                                <div>
                                    <x-label for="email" value="{{ __('Email') }}" />
                                    <x-input id="email" class="block mt-1 w-full rounded-xl" type="email" name="email" :value="old('email')" required autocomplete="username" />
                                </div>

                                <div>
                                    <x-label for="password" value="{{ __('Password') }}" />
                                    <x-input id="password" class="block mt-1 w-full rounded-xl" type="password" name="password" required autocomplete="new-password" />
                                </div>

                                <div>
                                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                                    <x-input id="password_confirmation" class="block mt-1 w-full rounded-xl" type="password" name="password_confirmation" required autocomplete="new-password" />
                                </div>

                                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                                    <div>
                                        <x-label for="terms">
                                            <div class="flex items-center">
                                                <x-checkbox name="terms" id="terms" required />

                                                <div class="ms-2">
                                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </x-label>
                                    </div>
                                @endif

                                <div class="flex items-center justify-end mt-4 pt-2">
                                    <a class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                                        {{ __('Already registered?') }}
                                    </a>

                                    <x-button class="ms-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800">
                                        {{ __('Register') }}
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
