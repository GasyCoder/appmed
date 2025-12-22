<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="w-full max-w-md">
            <!-- Card principal -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <!-- En-tête avec dégradé -->
                <div class="shadow-xl bg-gradient-to-r from-blue-500/50 to-indigo-500/10 dark:from-blue-900/50 dark:to-indigo-900/50 p-2 text-center">
                    <img width="100" height="90" src="{{ asset('assets/image/logo_med.png') }}"
                         alt="Faculté de Médecine"
                         class="mx-auto transform transition-transform hover:scale-105">
                    <h2 class="mt-4 text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">
                        Bienvenue sur AppMed
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Plateforme officielle de la Faculté de Médecine
                    </p>
                </div>

                <!-- Messages d'erreur/succès -->
                <div class="px-6 pt-2">
                    <x-validation-errors class="mb-4" />
                    @session('status')
                        <div class="p-4 mb-4 rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 flex items-center space-x-2">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ $value }}</p>
                        </div>
                    @endsession
                </div>

                <!-- Formulaire -->
                <form method="POST" action="{{ route('login') }}" class="p-6 space-y-6" x-data="{ loading: false }" x-on:submit="loading = true">
                    @csrf
                    <!-- Email -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <x-label for="email" value="Adresse Email" class="text-gray-700 dark:text-gray-300 font-medium" />
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <x-input id="email"
                                    class="block w-full pl-10 pr-3 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-opacity-30"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    placeholder="votre@email.com"
                                    required />
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <x-label for="password" value="Mot de passe" class="text-gray-700 dark:text-gray-300 font-medium" />
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Mot de passe oublié ?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <x-input id="password"
                                    class="block w-full pl-10 pr-10 py-2 border rounded-xl dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-opacity-30"
                                    type="password"
                                    name="password"
                                    placeholder="••••••••"
                                    required />
                            <button type="button"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-off-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Se souvenir de moi -->
                    <div class="flex items-center">
                        <label class="flex items-center space-x-2">
                            <x-checkbox id="remember_me" name="remember" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:border-blue-500 focus:ring-blue-500" />
                            <span class="text-sm text-gray-600 dark:text-gray-400">Se souvenir de moi</span>
                        </label>
                    </div>

                     <!-- Bouton avec état de chargement -->
                    <div class="mt-6">
                        <button type="submit"
                                class="w-full flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 focus:ring-4 focus:ring-blue-500/50 disabled:opacity-50"
                                :disabled="loading">
                            <template x-if="!loading">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </template>
                            <template x-if="loading">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Chargement...' : 'Connexion'"></span>
                        </button>
                    </div>
                </form>

                <!-- Aide -->
                <div class="p-6 bg-gray-50 dark:bg-gray-800/50 border-t dark:border-gray-700">
                    <p class="text-center text-gray-600 dark:text-gray-400 text-sm">
                        Vous n'avez pas encore de compte ?
                        <a href="/inscription" class="text-blue-600 dark:text-blue-400 hover:underline hover:text-blue-700 transition-colors duration-200">
                           S'inscrire
                        </a>
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
</x-guest-layout>
