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
                    <x-skeleton.card :lines="4" :hasHeader="false" />
                </div>

                {{-- Actual Content --}}
                <div x-show="!loading" x-cloak x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <x-authentication-card-logo />
                            </div>

                            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                            </div>

                            @if (session('status') == 'verification-link-sent')
                                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                                    {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
                                </div>
                            @endif

                            <div class="mt-4 flex items-center justify-between">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf

                                    <div>
                                        <x-button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white">
                                            {{ __('Resend Verification Email') }}
                                        </x-button>
                                    </div>
                                </form>

                                <div>
                                    <a
                                        href="{{ route('profile.show') }}"
                                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        {{ __('Edit Profile') }}</a>

                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf

                                        <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2">
                                            {{ __('Log Out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
