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
                            Définir le mot de passe
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Finalisez l’accès à votre compte en toute sécurité.
                        </p>
                    </div>
                </div>

                {{-- Info + Email affiché (sans input) --}}
                <div class="px-6">
                    {{-- Email mentionné en haut --}}
                    <div class="mb-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3 ring-1 ring-gray-200 dark:ring-white/10">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-600 dark:text-gray-400">Compte associé :</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white break-all">
                                    {{ request()->email }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Info banner --}}
                    <div class="mb-5 rounded-xl bg-blue-50 dark:bg-blue-900/20 px-4 py-3 ring-1 ring-blue-600/15 dark:ring-blue-400/15">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-sm text-blue-900 dark:text-blue-200 font-medium">
                                    {{ __('Veuillez créer votre mot de passe pour accéder à votre compte.') }}
                                </p>
                                <p class="mt-1 text-xs text-blue-800/80 dark:text-blue-200/80">
                                    {{ __('Choisissez un mot de passe robuste et gardez-le confidentiel.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <x-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 ring-1 ring-emerald-600/15 dark:ring-emerald-400/15">
                            <p class="text-sm text-emerald-800 dark:text-emerald-200">
                                {{ session('status') }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Form : seulement 2 inputs --}}
                <form
                    method="POST"
                    action="{{ route('password.update') }}"
                    id="setPasswordForm"
                    class="px-6 pb-6 space-y-5"
                >
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request()->email }}">

                    {{-- New password --}}
                    <div class="space-y-1.5">
                        <x-label for="password" value="Nouveau mot de passe" class="text-sm text-gray-700 dark:text-gray-300" />

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
                                onclick="togglePwd('password', 'eye-password', 'eyeoff-password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
                                aria-label="Afficher / masquer le mot de passe"
                            >
                                <svg id="eye-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>

                                <svg id="eyeoff-password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Minimum recommandé : 8 caractères, avec lettres et chiffres.
                        </p>
                    </div>

                    {{-- Confirm password --}}
                    <div class="space-y-1.5">
                        <x-label for="password_confirmation" value="Confirmer le mot de passe" class="text-sm text-gray-700 dark:text-gray-300" />

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>

                            <x-input
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
                                onclick="togglePwd('password_confirmation', 'eye-password_confirmation', 'eyeoff-password_confirmation')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
                                aria-label="Afficher / masquer la confirmation"
                            >
                                <svg id="eye-password_confirmation" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>

                                <svg id="eyeoff-password_confirmation" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
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
                            {{-- Icon --}}
                            <svg id="normalIcon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>

                            {{-- Spinner --}}
                            <svg id="spinnerIcon" class="h-5 w-5 hidden animate-spin"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <span id="btnText">Définir le mot de passe</span>
                        </button>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="px-6 py-5 bg-gray-50 dark:bg-gray-800/40">
                    <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} EpiRC — Université de Mahajanga
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePwd(inputId, eyeId, eyeOffId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeOff = document.getElementById(eyeOffId);

            if (!input || !eye || !eyeOff) return;

            const toText = input.type === 'password';
            input.type = toText ? 'text' : 'password';

            eye.classList.toggle('hidden', toText);
            eyeOff.classList.toggle('hidden', !toText);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('setPasswordForm');
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
    </script>
</x-guest-layout>
