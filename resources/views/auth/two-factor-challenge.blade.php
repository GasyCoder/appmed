{{-- resources/views/auth/two-factor-challenge.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div
                x-data="twoFactorOtp()"
                x-init="init()"
                class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 bg-white/90 dark:bg-gray-950/50 shadow-xl shadow-gray-900/5 dark:shadow-black/30"
            >
                {{-- Header --}}
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center gap-3">
                        <img
                            src="{{ asset('assets/image/logo.png') }}"
                            alt="Faculté de Médecine"
                            class="w-14 h-14 rounded-xl object-contain bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10"
                        />
                        <div class="min-w-0">
                            <div class="text-base font-bold text-gray-900 dark:text-white truncate">
                                Faculté de Médecine
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                Université de Mahajanga
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Vérification en 2 étapes
                        </h1>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" x-show="!recovery">
                            Saisissez le code à 6 chiffres. Vous pouvez aussi coller le code directement.
                        </p>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" x-cloak x-show="recovery">
                            Saisissez un de vos codes de récupération.
                        </p>
                    </div>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="px-6">
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
                            <div class="font-semibold mb-1">Erreur</div>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form
                    x-ref="form"
                    method="POST"
                    action="{{ route('two-factor.login') }}"
                    class="px-6 pb-6 pt-5 space-y-5"
                    @submit.prevent="submit()"
                >
                    @csrf

                    {{-- Champ réellement envoyé à Laravel --}}
                    <input type="hidden" name="code" x-model="code" :disabled="recovery">

                    {{-- OTP --}}
                    <div x-show="!recovery" class="space-y-3">
                        <div class="grid grid-cols-6 gap-2 sm:gap-3">
                            @for ($i = 0; $i < 6; $i++)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    class="h-12 sm:h-14 w-full rounded-xl
                                           border border-gray-300 dark:border-gray-700
                                           bg-white dark:bg-gray-900
                                           text-center text-lg font-semibold tracking-widest
                                           text-gray-900 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-gray-900/60 dark:focus:ring-white/20
                                           focus:border-gray-900 dark:focus:border-white/20
                                           transition"
                                    x-ref="otp{{ $i }}"
                                    @focus="onFocus({{ $i }})"
                                    @input="onInput($event, {{ $i }})"
                                    @keydown="onKeydown($event, {{ $i }})"
                                    @paste.prevent="onPaste($event, {{ $i }})"
                                />
                            @endfor
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span x-text="code.length ? ('Code saisi : ' + code.length + '/6') : 'Code : 0/6'"></span>

                            <button
                                type="button"
                                class="font-semibold underline underline-offset-4 hover:text-gray-900 dark:hover:text-white"
                                @click="clear(); focus(0)"
                            >
                                Effacer
                            </button>
                        </div>
                    </div>

                    {{-- Recovery code --}}
                    <div x-cloak x-show="recovery" class="space-y-2">
                        <label class="text-sm font-semibold text-gray-900 dark:text-white" for="recovery_code">
                            Code de récupération
                        </label>

                        <input
                            id="recovery_code"
                            name="recovery_code"
                            type="text"
                            class="w-full h-11 rounded-xl
                                   border border-gray-300 dark:border-gray-700
                                   bg-white dark:bg-gray-900
                                   px-3 text-sm
                                   text-gray-900 dark:text-white
                                   focus:outline-none focus:ring-2 focus:ring-gray-900/60 dark:focus:ring-white/20
                                   focus:border-gray-900 dark:focus:border-white/20
                                   transition"
                            x-ref="recovery"
                            :disabled="!recovery"
                        />
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                        <div class="text-sm">
                            <button
                                type="button"
                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white underline underline-offset-4"
                                x-show="!recovery"
                                @click="switchToRecovery()"
                            >
                                Utiliser un code de récupération
                            </button>

                            <button
                                type="button"
                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white underline underline-offset-4"
                                x-cloak
                                x-show="recovery"
                                @click="switchToOtp()"
                            >
                                Utiliser le code d’authentification
                            </button>
                        </div>

                        {{-- Bouton NOIR (comme demandé) --}}
                        <button
                            type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                                   rounded-xl px-4 py-2.5 text-sm font-semibold
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                   transition disabled:opacity-60 disabled:cursor-not-allowed"
                            :disabled="loading || (!recovery && code.length !== 6)"
                        >
                            <svg x-cloak x-show="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loading ? 'Vérification…' : 'Se connecter'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                Astuce : collez “123456” dans la première case, ça se répartit automatiquement.
            </div>
        </div>
    </div>

    <script>
        function twoFactorOtp() {
            return {
                recovery: false,
                loading: false,

                digits: ['', '', '', '', '', ''],
                code: '',

                init() {
                    this.loading = false;
                    this.recovery = false;
                    this.clear();
                    this.$nextTick(() => this.focus(0));
                },

                // ------- utils -------
                el(i) { return this.$refs['otp' + i]; },

                sync() {
                    this.code = this.digits.join('');
                },

                focus(i) {
                    this.$nextTick(() => {
                        const input = this.el(i);
                        if (input) input.focus();
                    });
                },

                clear() {
                    this.digits = ['', '', '', '', '', ''];
                    this.sync();
                    this.$nextTick(() => {
                        for (let i = 0; i < 6; i++) {
                            const input = this.el(i);
                            if (input) input.value = '';
                        }
                    });
                },

                // Remplit à partir d'une position (paste, autofill, input multi-chiffres)
                fillFrom(index, value) {
                    const nums = String(value || '').replace(/\D/g, '');
                    if (!nums) return;

                    const arr = nums.split('');
                    for (let k = 0; k < arr.length; k++) {
                        const pos = index + k;
                        if (pos > 5) break;
                        this.digits[pos] = arr[k];
                    }

                    this.$nextTick(() => {
                        for (let i = 0; i < 6; i++) {
                            const input = this.el(i);
                            if (input) input.value = this.digits[i] || '';
                        }
                        this.sync();

                        // focus sur prochaine case vide, sinon dernière
                        const nextEmpty = this.digits.findIndex(d => !d);
                        if (nextEmpty !== -1) this.focus(nextEmpty);
                        else this.focus(5);
                    });
                },

                // ------- events -------
                onFocus(i) {
                    // évite les comportements bizarres sur mobile
                    this.$nextTick(() => {
                        const input = this.el(i);
                        if (input) input.select();
                    });
                },

                onInput(e, i) {
                    if (this.recovery) return;

                    const raw = (e.target.value || '');
                    const nums = raw.replace(/\D/g, '');

                    if (!nums) {
                        this.digits[i] = '';
                        e.target.value = '';
                        this.sync();
                        return;
                    }

                    // 1 chiffre normal
                    if (nums.length === 1) {
                        this.digits[i] = nums;
                        e.target.value = nums;
                        this.sync();

                        // avance uniquement si pas dernière case
                        if (i < 5) this.focus(i + 1);
                        return;
                    }

                    // Plusieurs chiffres (paste mobile / autofill / collage)
                    this.fillFrom(i, nums);
                },

                onPaste(e, i) {
                    if (this.recovery) return;
                    const text = (e.clipboardData && e.clipboardData.getData)
                        ? e.clipboardData.getData('text')
                        : '';
                    this.fillFrom(i, text);
                },

                onKeydown(e, i) {
                    if (this.recovery) return;

                    if (e.key === 'Backspace') {
                        e.preventDefault();

                        // si la case a déjà un chiffre: on efface seulement
                        if (this.digits[i]) {
                            this.digits[i] = '';
                            const input = this.el(i);
                            if (input) input.value = '';
                            this.sync();
                            return;
                        }

                        // sinon revenir à la précédente
                        if (i > 0) {
                            this.digits[i - 1] = '';
                            this.$nextTick(() => {
                                const prev = this.el(i - 1);
                                if (prev) {
                                    prev.value = '';
                                    prev.focus();
                                }
                                this.sync();
                            });
                        }
                        return;
                    }

                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        if (i > 0) this.focus(i - 1);
                        return;
                    }

                    if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        if (i < 5) this.focus(i + 1);
                        return;
                    }
                },

                // ------- toggles -------
                switchToRecovery() {
                    this.recovery = true;
                    this.loading = false;
                    this.clear();
                    this.$nextTick(() => {
                        if (this.$refs.recovery) this.$refs.recovery.focus();
                    });
                },

                switchToOtp() {
                    this.recovery = false;
                    this.loading = false;
                    this.$nextTick(() => this.focus(0));
                },

                // ------- submit -------
                submit() {
                    if (this.loading) return;

                    // sécurité: si OTP, doit être 6 chiffres
                    if (!this.recovery && this.code.length !== 6) {
                        this.focus(this.digits.findIndex(d => !d) !== -1 ? this.digits.findIndex(d => !d) : 0);
                        return;
                    }

                    this.loading = true;
                    this.$nextTick(() => this.$refs.form.submit());
                }
            }
        }
    </script>
</x-guest-layout>
