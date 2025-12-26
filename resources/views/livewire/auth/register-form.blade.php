<x-guest-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 px-4 py-10 flex items-center">
        <div class="mx-auto w-full max-w-2xl">

            <div class="rounded-2xl bg-white dark:bg-gray-900 shadow-sm border border-gray-200/70 dark:border-gray-800/70 overflow-hidden">

                {{-- Header --}}
                <div class="px-6 sm:px-8 pt-7 pb-6 border-b border-gray-200/70 dark:border-gray-800/70">
                    <div class="flex items-center gap-3">
                        <img
                            src="{{ asset('assets/image/logo.png') }}"
                            alt="Faculté de Médecine"
                            class="w-[120px] h-[120px] lg:w-[140px] lg:h-[140px] rounded-lg object-contain shrink-0"
                        />

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">Faculté de Médecine</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">Inscription</div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                            Créer votre compte
                        </h1>

                        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <span>Email autorisé :</span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                                         bg-green-100 text-green-900
                                         dark:bg-green-900/30 dark:text-green-100">
                                {{ $email }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                <div class="px-6 sm:px-8 pt-5 space-y-3" aria-live="polite">
                    @if(session('error'))
                        <div class="rounded-xl border border-red-200 dark:border-red-900/40 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="rounded-xl border border-green-200 dark:border-green-900/40 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    <x-validation-errors class="mb-0" />
                </div>

                {{-- Form --}}
                <form
                    method="POST"
                    action="{{ route('register.store', ['token' => $token]) }}"
                    class="px-6 sm:px-8 pt-4 pb-8 space-y-6"
                    x-data="{ loading:false }"
                    x-on:submit="if(loading) { $event.preventDefault(); return } loading = true"
                    x-bind:aria-busy="loading ? 'true' : 'false'">
                    @csrf

                    {{-- Email envoyé au serveur (hidden) --}}
                    <input type="hidden" name="email" value="{{ $email }}" />

                    @isset($defaultParcourId)
                        <input type="hidden" name="parcour_id" value="{{ $defaultParcourId }}" />
                    @endisset

                    {{-- Infos perso --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="name" value="Nom complet" class="text-sm text-gray-700 dark:text-gray-300" />
                            <x-input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                placeholder="Nom et Prénom(s)"
                                required
                                autocomplete="name"
                                class="mt-1 block w-full rounded-xl
                                       border border-gray-300 dark:border-gray-700
                                       bg-white dark:bg-gray-950 text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-gray-900/15 dark:focus:ring-white/15
                                       focus:border-gray-900 dark:focus:border-white"
                            />
                            @error('name')
                                <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="telephone" value="Téléphone" class="text-sm text-gray-700 dark:text-gray-300" />
                            <x-input
                                id="telephone"
                                name="telephone"
                                type="tel"
                                inputmode="tel"
                                autocomplete="tel"
                                placeholder="Ex : 034 12 345 67"
                                value="{{ old('telephone') }}"
                                required
                                class="mt-1 block w-full rounded-xl
                                       border border-gray-300 dark:border-gray-700
                                       bg-white dark:bg-gray-950 text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-gray-900/15 dark:focus:ring-white/15
                                       focus:border-gray-900 dark:focus:border-white"
                            />
                            @error('telephone')
                                <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Sexe --}}
                    @php $sexeOld = old('sexe'); @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-200">Sexe</label>

                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach([['homme','Homme'],['femme','Femme']] as [$value, $label])
                                <label class="cursor-pointer">
                                    <input type="radio" name="sexe" value="{{ $value }}" class="peer sr-only" {{ $sexeOld === $value ? 'checked' : '' }} required>
                                    <div class="rounded-xl px-3 py-2 text-sm font-semibold text-center transition
                                                border border-gray-200 dark:border-gray-800
                                                bg-gray-50 text-gray-800 hover:bg-gray-100
                                                dark:bg-gray-950 dark:text-gray-200 dark:hover:bg-gray-900
                                                peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900
                                                dark:peer-checked:bg-white dark:peer-checked:text-gray-900 dark:peer-checked:border-white">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('sexe')
                            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Niveau --}}
                    @php $niveauOld = (string) old('niveau_id'); @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-200">Niveau</label>

                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach($niveaux as $niveau)
                                @php $isChecked = $niveauOld === (string) $niveau->id; @endphp

                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="niveau_id"
                                        value="{{ $niveau->id }}"
                                        class="peer sr-only"
                                        {{ $isChecked ? 'checked' : '' }}
                                        required
                                    >
                                    <div class="rounded-xl px-3 py-2 text-sm font-semibold text-center transition
                                                border border-gray-200 dark:border-gray-800
                                                bg-gray-50 text-gray-800 hover:bg-gray-100
                                                dark:bg-gray-950 dark:text-gray-200 dark:hover:bg-gray-900
                                                peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900
                                                dark:peer-checked:bg-white dark:peer-checked:text-gray-900 dark:peer-checked:border-white">
                                        {{ $niveau->sigle }}
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('niveau_id')
                            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Passwords --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="password" value="Mot de passe" class="text-sm text-gray-700 dark:text-gray-300" />
                            <div class="relative mt-1">
                                <x-input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="*********"
                                    required
                                    autocomplete="new-password"
                                    class="block w-full pr-10 rounded-xl
                                           border border-gray-300 dark:border-gray-700
                                           bg-white dark:bg-gray-950 text-gray-900 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-gray-900/15 dark:focus:ring-white/15
                                           focus:border-gray-900 dark:focus:border-white"
                                />
                                <button type="button"
                                        onclick="togglePassword('password', this)"
                                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-800 dark:hover:text-gray-200"
                                        aria-label="Afficher/Masquer le mot de passe">
                                    <svg class="show-password h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg class="hide-password h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="password_confirmation" value="Confirmation" class="text-sm text-gray-700 dark:text-gray-300" />
                            <div class="relative mt-1">
                                <x-input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    placeholder="*********"
                                    required
                                    autocomplete="new-password"
                                    class="block w-full pr-10 rounded-xl
                                           border border-gray-300 dark:border-gray-700
                                           bg-white dark:bg-gray-950 text-gray-900 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-gray-900/15 dark:focus:ring-white/15
                                           focus:border-gray-900 dark:focus:border-white"
                                />
                                <button type="button"
                                        onclick="togglePassword('password_confirmation', this)"
                                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-800 dark:hover:text-gray-200"
                                        aria-label="Afficher/Masquer la confirmation">
                                    <svg class="show-password h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg class="hide-password h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Terms --}}
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <x-checkbox name="terms" id="terms" required class="mt-1" />
                            <span>
                                {!! __('J\'accepte les :terms_of_service et la :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-gray-900 dark:text-gray-200">'.__('conditions').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-gray-900 dark:text-gray-200">'.__('politique').'</a>',
                                ]) !!}
                            </span>
                        </label>
                    @endif

                    {{-- Actions --}}
                    <div class="pt-1">
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-gray-900 text-white py-2.5 text-sm font-semibold
                                   hover:bg-gray-800 transition disabled:opacity-60 disabled:cursor-not-allowed
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                            :disabled="loading"
                        >
                            <span x-show="!loading">Créer le compte</span>
                            <span x-show="loading" class="inline-flex items-center justify-center gap-2" style="display:none;">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Enregistrement…
                            </span>
                        </button>

                        <div class="mt-3 text-center">
                            <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:underline underline-offset-2">
                                Déjà inscrit ? Se connecter
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-600">
                Si l’inscription échoue malgré un email autorisé, contactez l’administration.
            </p>

        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const showIcon = btn.querySelector('.show-password');
            const hideIcon = btn.querySelector('.hide-password');

            if (input.type === 'password') {
                input.type = 'text';
                showIcon?.classList.add('hidden');
                hideIcon?.classList.remove('hidden');
            } else {
                input.type = 'password';
                showIcon?.classList.remove('hidden');
                hideIcon?.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>
