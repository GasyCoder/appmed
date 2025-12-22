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

        @if(session('error'))
        <div class="mb-4 px-4 py-2 border border-red-200 rounded-md bg-red-50 text-red-600">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6">
                <div class="px-4 py-2 border border-green-200 rounded-md bg-green-50 text-green-600 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                {{-- Message de bienvenue avec l'email --}}
                <div class="text-center">
                    <p class="mt-2 text-sm text-gray-600">
                        Vous utilisez l'adresse email :
                        <span class="font-medium text-indigo-600">{{ $email }}</span>
                    </p>
                </div>
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register.store', ['token' => $token]) }}" x-data="{ loading: false }" x-on:submit="loading = true">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-label for="name" value="{{ __('Nom complet') }}" />
                    <x-input
                        id="name"
                        name="name"
                        class="block mt-1 w-full"
                        type="text"
                        value="{{ old('name') }}"
                        required
                    />
                </div>
                <div>
                    <x-label for="telephone" value="{{ __('Téléphone') }}" />
                    <x-input
                        id="telephone"
                        name="telephone"
                        class="block mt-1 w-full"
                        type="tel"
                        value="{{ old('telephone') }}"
                        required
                    />
                </div>
                <x-input
                    id="email"
                    type="hidden"
                    value="{{ $email }}"
                    name="email"
                />

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Sexe') }}</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors duration-200 {{ old('sexe') == 'homme' ? 'bg-indigo-50 border-indigo-500' : '' }}">
                            <input type="radio"
                                   name="sexe"
                                   value="homme"
                                   {{ old('sexe') == 'homme' ? 'checked' : '' }}
                                   class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Homme</span>
                        </label>
                        <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors duration-200 {{ old('sexe') == 'femme' ? 'bg-indigo-50 border-indigo-500' : '' }}">
                            <input type="radio"
                                   name="sexe"
                                   value="femme"
                                   {{ old('sexe') == 'femme' ? 'checked' : '' }}
                                   class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Femme</span>
                        </label>
                    </div>
                    @error('sexe')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Niveau en Radio Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Niveau') }}</label>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($niveaux as $niveau)
                                <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors duration-200 {{ old('niveau_id') == $niveau->id ? 'bg-indigo-50 border-indigo-500' : '' }}">
                                    <input type="radio"
                                           name="niveau_id"
                                           value="{{ $niveau->id }}"
                                           {{ old('niveau_id') == $niveau->id ? 'checked' : '' }}
                                           class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $niveau->sigle }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('niveau_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parcours en Radio Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Parcours') }}</label>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($parcours as $parcour)
                                <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 transition-colors duration-200 {{ old('parcour_id') == $parcour->id ? 'bg-indigo-50 border-indigo-500' : '' }}">
                                    <input type="radio"
                                           name="parcour_id"
                                           value="{{ $parcour->id }}"
                                           {{ old('parcour_id') == $parcour->id ? 'checked' : '' }}
                                           class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $parcour->sigle }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('parcour_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section mot de passe avec icônes show/hide -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <x-label for="password" value="{{ __('Mot de passe') }}" />
                        <div class="relative">
                            <x-input
                                id="password"
                                name="password"
                                class="block mt-1 w-full pr-10"
                                type="password"
                                required
                            />
                            <button type="button"
                                onclick="togglePassword('password')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                                <svg class="show-password h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="hide-password h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <x-label for="password_confirmation" value="{{ __('Confirmer le mot de passe') }}" />
                        <div class="relative">
                            <x-input
                                id="password_confirmation"
                                name="password_confirmation"
                                class="block mt-1 w-full pr-10"
                                type="password"
                                required
                            />
                            <button type="button"
                                onclick="togglePassword('password_confirmation')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                                <svg class="show-password h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="hide-password h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <x-label for="terms">
                            <div class="flex items-center">
                                <x-checkbox name="terms" id="terms" required />
                                <div class="ms-2 text-sm text-gray-600">
                                    {!! __('J\'accepte les :terms_of_service et la :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-indigo-600 hover:text-indigo-900">'.__('conditions d\'utilisation').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-indigo-600 hover:text-indigo-900">'.__('politique de confidentialité').'</a>',
                                    ]) !!}
                                </div>
                            </div>
                        </x-label>
                    </div>
                @endif

                <div class="flex items-center justify-between mt-6">
                    <button type="submit"
                    id="registerBtn"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <span class="flex items-center">
                            <span class="normal-state">{{ __('S\'inscrire') }}</span>
                            <span class="loading-state hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Inscription en cours...
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </form>

    </x-authentication-card>


    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const parent = input.parentElement;
            const showIcon = parent.querySelector('.show-password');
            const hideIcon = parent.querySelector('.hide-password');

            if (input.type === 'password') {
                input.type = 'text';
                showIcon.classList.add('hidden');
                hideIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                showIcon.classList.remove('hidden');
                hideIcon.classList.add('hidden');
            }
        }
    </script>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const button = this.querySelector('#registerBtn');
            const normalState = button.querySelector('.normal-state');
            const loadingState = button.querySelector('.loading-state');

            // Vérifier si le formulaire est valide
            if (this.checkValidity()) {
                button.disabled = true;
                normalState.classList.add('hidden');
                loadingState.classList.remove('hidden');
            }
        });
        </script>
</x-guest-layout>
