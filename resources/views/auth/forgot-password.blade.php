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
                            Mot de passe oublié
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Entrez votre email pour recevoir un lien de réinitialisation.
                        </p>
                    </div>
                </div>

                {{-- Flash / Errors --}}
                <div class="px-6">
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

                    <x-validation-errors class="mb-4" />
                </div>

                {{-- Form --}}
                <form
                    method="POST"
                    action="{{ route('password.email') }}"
                    id="forgotForm"
                    class="px-6 pb-6 space-y-5"
                >
                    @csrf

                    {{-- Info --}}
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-800/40 px-4 py-3 ring-1 ring-black/5 dark:ring-white/10">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 text-gray-500 dark:text-gray-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ __("Aucun souci. Indiquez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.") }}
                            </p>
                        </div>
                    </div>

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

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Vérifiez l’orthographe de votre email avant de valider.
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between gap-3 pt-1">
                        <a href="{{ route('login') }}" wire:navigate
                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                  bg-gray-100 text-gray-900 hover:bg-gray-200
                                  dark:bg-gray-900/40 dark:text-white dark:hover:bg-gray-900/70
                                  ring-1 ring-black/5 dark:ring-white/10
                                  transition">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 19l-7-7 7-7" />
                            </svg>
                            Retour
                        </a>

                        <button
                            type="submit"
                            id="submitBtn"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                   disabled:opacity-60 disabled:cursor-not-allowed transition"
                        >
                            {{-- Icon normal --}}
                            <svg id="normalIcon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 11c.53 0 1.04.21 1.41.59.38.37.59.88.59 1.41v3H10v-3c0-.53.21-1.04.59-1.41.37-.38.88-.59 1.41-.59z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 11V8a5 5 0 00-10 0v3m-2 0h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z"/>
                            </svg>

                            {{-- Spinner --}}
                            <svg id="spinnerIcon" class="h-5 w-5 hidden animate-spin"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <span id="btnText">Envoyer le lien</span>
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

    {{-- ✅ JS loading state --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotForm');
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
                if (btnText) btnText.textContent = 'Envoi…';
            });
        });
    </script>
</x-guest-layout>
