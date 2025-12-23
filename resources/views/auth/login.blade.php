<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <img
                                width="56"
                                height="56"
                                src="{{ asset('assets/image/logo_med.png') }}"
                                alt="Faculté de Médecine"
                                class="h-14 w-14 rounded-xl object-contain bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-1"
                            >
                        </div>

                        <div class="min-w-0">
                            <h1 class="text-lg font-semibold text-gray-900 dark:text-white leading-tight">
                                AppMed
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Plateforme de la Faculté de Médecine
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
                <div class="px-6 pt-4">
                    <x-validation-errors class="mb-4" />

                    @session('status')
                        <div class="mb-4 rounded-xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3">
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
                    class="p-6 space-y-5"
                    x-data="{ loading: false }"
                    x-on:submit="loading = true"
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
                                class="block w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:border-gray-900 dark:focus:border-white focus:ring-0"
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
                                class="block w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       focus:border-gray-900 dark:focus:border-white focus:ring-0"
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
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
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
                            :disabled="loading"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                   disabled:opacity-60 disabled:cursor-not-allowed transition"
                        >
                            <template x-if="!loading">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </template>

                            <template x-if="loading">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>

                            <span x-text="loading ? 'Connexion…' : 'Se connecter'"></span>
                        </button>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="px-6 py-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                    <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                        Vous n’avez pas de compte ?
                        <a href="/inscription"
                           class="font-semibold text-gray-900 dark:text-white hover:underline underline-offset-2">
                            S’inscrire
                        </a>
                    </p>

                    <p class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                        En cas de problème d’accès, contactez le service informatique.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (!passwordInput || !eyeIcon || !eyeOffIcon) return;

            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            eyeIcon.classList.toggle('hidden', isHidden);
            eyeOffIcon.classList.toggle('hidden', !isHidden);
        }
    </script>
</x-guest-layout>
