<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        @if(session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <div>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Vérification de l\'email') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Veuillez entrer votre adresse email universitaire pour commencer l\'inscription.') }}
            </p>

            <form method="POST" action="{{ route('email.verify') }}">
                @csrf

                <div class="mt-4">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input
                        id="email"
                        name="email"
                        class="block mt-1 w-full"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="exemple@facmed.mg"
                        required
                        autofocus
                    />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        {{ __('Déjà inscrit?') }}
                    </a>
                    <button type="submit"
                    id="submitBtn"
                    class="ms-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <span class="flex items-center">
                            <span class="normal-state">{{ __('Continuer') }}</span>
                            <span class="loading-state hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Vérification...
                            </span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </x-authentication-card>

    <script>
        document.getElementById('emailVerificationForm').addEventListener('submit', function(e) {
            const button = this.querySelector('#submitBtn');
            const normalState = button.querySelector('.normal-state');
            const loadingState = button.querySelector('.loading-state');

            button.disabled = true;
            normalState.classList.add('hidden');
            loadingState.classList.remove('hidden');
        });
    </script>

</x-guest-layout>
