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
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-5">
                                <x-skeleton.avatar size="h-[120px] w-[120px]" />
                                <div class="flex-1">
                                    <x-skeleton.line width="w-3/4" height="h-6" class="mb-2" />
                                    <x-skeleton.line width="w-1/2" height="h-4" />
                                </div>
                            </div>
                            <x-skeleton.line width="w-32" height="h-6" class="mb-2" />
                            <x-skeleton.line width="w-48" height="h-4" class="mb-5" />
                        </div>
                        <div class="px-6 pb-6">
                            <x-skeleton.form :fields="2" />
                        </div>
                        <div class="px-6 py-5 bg-gray-50 dark:bg-gray-800/40">
                            <x-skeleton.line width="w-64 mx-auto" height="h-4" />
                        </div>
                    </div>
                </div>

                {{-- Actual Content --}}
                <div x-show="!loading" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">

                        {{-- Header --}}
                        <div class="p-6">
                            <div class="flex items-center gap-4">
                                <img
                                    src="{{ asset('assets/image/logo.png') }}"
                                    alt="Faculté de Médecine"
                                    class="w-[120px] h-[120px] lg:w-[140px] lg:h-[140px] object-contain"
                                />

                                <div class="min-w-0">
                                    <h1 class="lg:text-xl md:text-sm font-semibold text-gray-900 dark:text-white leading-tight">
                                      <span class="font-bold text-blue-500">EpiRC</span> - Faculté de Médecine
                                    </h1>
                                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400">
                                        Université de Mahajanga
                                    </p>
                                </div>
                            </div>
                            <div class="mt-5">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Connexion
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Accédez à votre espace en toute sécurité.
                                </p>
                            </div>
                        </div>

                        {{-- Flash / Errors --}}
                        <div class="px-6">
                            <x-validation-errors class="mb-4" />

                            @session('status')
                                <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 ring-1 ring-emerald-600/15 dark:ring-emerald-400/15">
                                    <div class="flex items-start gap-2">
                                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm text-emerald-800 dark:text-emerald-200">
                                            {{ $value }}
                                        </p>
                                    </div>
                                </div>
                            @endsession
                        </div>

                        {{-- Form --}}
                        <form
                            method="POST"
                            action="{{ route('login') }}"
                            id="loginForm"
                            class="px-6 pb-6 space-y-5"
                        >
                            @csrf

                            {{-- Email --}}
                            <div class="space-y-1.5">
                                <x-label for="email" value="Adresse email" class="text-sm text-gray-700 dark:text-gray-300" />

                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </span>

                                    <x-input
                                        id="email"
                                        name="email"
                                        type="email"
                                        :value="old('email')"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        placeholder="ex: prenom.nom@umg.mg"
                                        class="block w-full pl-10 pr-3 py-2.5 rounded-xl border-0
                                               ring-1 ring-gray-300/70 dark:ring-white/10
                                               bg-white dark:bg-gray-900/40
                                               text-gray-900 dark:text-white
                                               placeholder:text-gray-400 dark:placeholder:text-gray-500
                                               focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                    />
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between gap-3">
                                    <x-label for="password" value="Mot de passe" class="text-sm text-gray-700 dark:text-gray-300" />
                                    <a href="{{ route('password.request') }}"
                                       class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline-offset-2 hover:underline">
                                        Mot de passe oublié ?
                                    </a>
                                </div>

                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </span>

                                    <x-input
                                        id="password"
                                        name="password"
                                        type="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                        class="block w-full pl-10 pr-12 py-2.5 rounded-xl border-0
                                               ring-1 ring-gray-300/70 dark:ring-white/10
                                               bg-white dark:bg-gray-900/40
                                               text-gray-900 dark:text-white
                                               placeholder:text-gray-400 dark:placeholder:text-gray-500
                                               focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                    />

                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
                                        aria-label="Afficher / masquer le mot de passe"
                                    >
                                        <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>

                                        <svg id="eye-off-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Remember --}}
                            <div class="flex items-center justify-between gap-3">
                                <label class="inline-flex items-center gap-2">
                                    <x-checkbox id="remember_me" name="remember"
                                        class="rounded border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-0" />
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Se souvenir de moi</span>
                                </label>

                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ now()->format('d/m/Y H:i') }}
                                </span>
                            </div>

                            {{-- Submit --}}
                            <div class="pt-1">
                                <button
                                    type="submit"
                                    id="submitBtn"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                           bg-gray-900 text-white hover:bg-gray-800
                                           dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                           disabled:opacity-60 disabled:cursor-not-allowed 
                                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-900 dark:focus-visible:ring-white
                                           transition"
                                >
                                    {{-- Icône normale --}}
                                    <svg id="normalIcon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>

                                    {{-- Spinner --}}
                                    <svg id="spinnerIcon" class="h-5 w-5 hidden animate-spin"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>

                                    <span id="btnText">Se connecter</span>
                                </button>
                            </div>
                        </form>

                        {{-- Footer --}}
                        <div class="px-6 py-5 bg-gray-50 dark:bg-gray-800/40">
                            <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                                Vous n'avez pas de compte ?
                                <a href="/inscription"
                                   class="font-semibold text-gray-900 dark:text-white hover:underline underline-offset-2">
                                    S'inscrire
                                </a>
                            </p>

                            <p class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                                En cas de problème d'accès, contactez le service informatique.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript vanilla pour le loading --}}
    <script>
        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (!passwordInput || !eyeIcon || !eyeOffIcon) return;

            const toText = passwordInput.type === 'password';
            passwordInput.type = toText ? 'text' : 'password';
            eyeIcon.classList.toggle('hidden', toText);
            eyeOffIcon.classList.toggle('hidden', !toText);
        }

        // Gestion du loading state
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const normalIcon = document.getElementById('normalIcon');
            const spinnerIcon = document.getElementById('spinnerIcon');
            const btnText = document.getElementById('btnText');

            if (!form || !submitBtn) return;

            form.addEventListener('submit', function(e) {
                // Vérifier la validation HTML5
                if (!form.checkValidity()) {
                    return; // Laisser le navigateur gérer l'affichage des erreurs
                }

                // Empêcher les doubles soumissions
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return;
                }

                // Activer l'état de chargement
                submitBtn.disabled = true;
                
                if (normalIcon) normalIcon.classList.add('hidden');
                if (spinnerIcon) spinnerIcon.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Connexion…';
            });
        });
    </script>
</x-guest-layout>