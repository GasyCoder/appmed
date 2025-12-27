<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-md">

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <img
                            src="{{ asset('assets/image/logo.png') }}"
                            alt="Faculté de Médecine"
                            class="w-[120px] h-[120px] lg:w-[140px] lg:h-[140px] object-contain shrink-0"
                        />

                        <div class="min-w-0">
                            <h2 class="text-sm lg:text-xl font-semibold text-gray-900 dark:text-white leading-tight truncate">
                                Faculté de Médecine
                            </h2>
                            <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 truncate">
                                Université de Mahajanga
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-1">
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white leading-tight">
                            Vérification de l’email
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Pour des raisons de sécurité, seuls les emails universitaires déjà enregistrés sont autorisés à continuer l’inscription.
                        </p>
                    </div>
                </div>

                {{-- Alerts --}}
                <div class="px-6 pt-4 space-y-3" aria-live="polite">
                    @if(session('error'))
                        <div class="p-3 rounded-xl border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-900/20">
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.38A2 2 0 003.72 21h16.56a2 2 0 001.73-2.76l-8.3-14.38a2 2 0 00-3.42 0z"/>
                                </svg>
                                <div class="text-sm text-red-700 dark:text-red-300">
                                    {{ session('error') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <x-validation-errors class="mb-0" />
                </div>

                {{-- Form --}}
                <form
                    id="emailVerificationForm"
                    method="POST"
                    action="{{ route('email.verify') }}"
                    class="p-6 space-y-5"
                >
                    @csrf

                    <div class="space-y-1.5">
                        <x-label
                            for="email"
                            value="Email universitaire"
                            class="text-sm text-gray-700 dark:text-gray-300"
                        />

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>

                            <x-input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="ex: prenom.nom@facmed.mg"
                                required
                                autofocus
                                autocomplete="email"
                                inputmode="email"
                                class="block w-full pl-10 pr-3 py-2.5 rounded-xl
                                       border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                       placeholder-gray-400 dark:placeholder-gray-500
                                       focus:outline-none focus:ring-2 focus:ring-gray-900/15
                                       dark:focus:ring-white/15 focus:border-gray-900 dark:focus:border-white"
                            />
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Astuce : utilisez l’email officiel transmis par la Faculté.
                        </p>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-1">
                        <a
                            href="{{ route('login') }}"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:underline underline-offset-2"
                        >
                            Déjà inscrit ?
                        </a>

                        <button
                            type="submit"
                            id="submitBtn"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                   transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <span class="normal-state">Continuer</span>

                            <span class="loading-state hidden items-center gap-2" aria-hidden="true">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Vérification…
                            </span>
                        </button>
                    </div>
                </form>

                {{-- Footer --}}
                <div class="px-6 py-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Si votre email n’est pas reconnu, contactez le secrétariat pour l’ajout dans la liste autorisée.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('emailVerificationForm');
            const button = document.getElementById('submitBtn');
            if (!form || !button) return;

            form.addEventListener('submit', function () {
                // évite double-submit (clic multiple / enter)
                if (button.disabled) return;

                const normalState = button.querySelector('.normal-state');
                const loadingState = button.querySelector('.loading-state');

                button.disabled = true;
                button.setAttribute('aria-busy', 'true');

                if (normalState) normalState.classList.add('hidden');
                if (loadingState) {
                    loadingState.classList.remove('hidden');
                    loadingState.classList.add('inline-flex');
                }
            });
        })();
    </script>
</x-guest-layout>
