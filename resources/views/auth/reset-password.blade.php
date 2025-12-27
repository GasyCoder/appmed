<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-lg">

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">

                {{-- Header --}}
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <img
                            src="{{ asset('assets/image/logo.png') }}"
                            alt="Faculté de Médecine"
                            class="w-[110px] h-[110px] lg:w-[130px] lg:h-[130px] object-contain"
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
                            Définir un nouveau mot de passe
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Compte : <span class="font-semibold text-gray-900 dark:text-white">{{ $request->email }}</span>
                        </p>
                    </div>

                    {{-- Info banner (simple + utile) --}}
                    <div class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 ring-1 ring-gray-200/70 dark:ring-white/10 px-4 py-3">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-gray-700 dark:text-gray-200 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                Choisissez un mot de passe robuste (8 caractères minimum).
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Errors --}}
                <div class="px-6">
                    <x-validation-errors class="mb-4" />
                </div>

                {{-- Form --}}
                <form
                    method="POST"
                    action="{{ route('password.update') }}"
                    id="resetForm"
                    class="px-6 pb-6 space-y-5"
                >
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    {{-- On garde l’email envoyé par le lien, mais SANS champ visible --}}
                    <input type="hidden" name="email" value="{{ $request->email }}">

                    {{-- New Password --}}
                    <div class="space-y-1.5">
                        <label for="password" class="text-sm text-gray-700 dark:text-gray-300">
                            Nouveau mot de passe
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
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
                                data-toggle-password="password"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
                                aria-label="Afficher / masquer le mot de passe"
                            >
                                <svg data-eye="password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>

                                <svg data-eye-slash="password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            8 caractères minimum, mélange recommandé (lettres + chiffres).
                        </p>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="text-sm text-gray-700 dark:text-gray-300">
                            Confirmer le mot de passe
                        </label>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
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
                                data-toggle-password="password_confirmation"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
                                aria-label="Afficher / masquer la confirmation"
                            >
                                <svg data-eye="password_confirmation" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>

                                <svg data-eye-slash="password_confirmation" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-1">
                        <button
                            type="submit"
                            id="submitBtn"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                   disabled:opacity-60 disabled:cursor-not-allowed transition"
                        >
                            <svg id="normalIcon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>

                            <svg id="spinnerIcon" class="h-5 w-5 hidden animate-spin"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <span id="btnText">Valider</span>
                        </button>

                        <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400">
                            Si vous n’êtes pas à l’origine de cette demande, vous pouvez fermer cette page.
                        </p>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="px-6 py-5 bg-gray-50 dark:bg-gray-800/40">
                    <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} EpiRC — Faculté de Médecine, Université de Mahajanga
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            function toggle(inputId) {
                const input = document.getElementById(inputId);
                if (!input) return;

                const eye = document.querySelector(`[data-eye="${inputId}"]`);
                const eyeSlash = document.querySelector(`[data-eye-slash="${inputId}"]`);

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                if (eye && eyeSlash) {
                    eye.classList.toggle('hidden', isPassword);
                    eyeSlash.classList.toggle('hidden', !isPassword);
                }
            }

            document.querySelectorAll('[data-toggle-password]').forEach(btn => {
                btn.addEventListener('click', () => toggle(btn.getAttribute('data-toggle-password')));
            });

            // Loading state
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('resetForm');
                const submitBtn = document.getElementById('submitBtn');
                const normalIcon = document.getElementById('normalIcon');
                const spinnerIcon = document.getElementById('spinnerIcon');
                const btnText = document.getElementById('btnText');

                if (!form || !submitBtn) return;

                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) return;

                    if (submitBtn.disabled) {
                        e.preventDefault();
                        return;
                    }

                    submitBtn.disabled = true;
                    if (normalIcon) normalIcon.classList.add('hidden');
                    if (spinnerIcon) spinnerIcon.classList.remove('hidden');
                    if (btnText) btnText.textContent = 'Validation…';
                });
            });
        })();
    </script>
</x-guest-layout>
