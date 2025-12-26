<!DOCTYPE html>
<html lang="fr" x-data="landingTheme()" x-init="init()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>EpiRC — Plateforme Épidémiologie & Recherche Clinique</title>
    <meta name="description" content="Plateforme en ligne du parcours Épidémiologie et Recherche Clinique (EpiRC) — Faculté de Médecine, Université de Mahajanga">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak]{display:none!important}
        :root { --landing-header-h: 64px; }
        .landing-main { padding-top: calc(var(--landing-header-h) + 16px); }
    </style>
</head>

<body class="bg-white text-gray-900 dark:bg-gray-950 dark:text-white antialiased">

    {{-- Header fixed --}}
    <header id="landingHeader"
            class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200/70 dark:border-gray-800/70
                   bg-white/70 dark:bg-gray-950/70 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">
            {{-- Brand --}}
            <a href="#top" class="flex items-center gap-4 min-w-0">
                <img
                    src="{{ asset('assets/image/logo.png') }}"
                    alt="EpiRC"
                    class="h-12 w-12 sm:h-14 sm:w-14 lg:h-16 lg:w-16 object-contain shrink-0"
                >

                <div class="min-w-0 leading-tight">
                    <div class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white truncate">
                        EpiRC
                    </div>
                    <div class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 truncate">
                        Faculté de Médecine — UMG
                    </div>
                </div>
            </a>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- Dark toggle --}}
                <button type="button"
                        @click="toggleDark()"
                        class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-900 transition"
                        aria-label="Basculer le mode sombre">
                    <svg x-show="!darkMode" class="h-5 w-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                {{-- Auth buttons --}}
                @auth
                    @php
                        $accountUrl = match(true) {
                            auth()->user()->hasRole('admin')   => route('adminEspace'),
                            auth()->user()->hasRole('teacher') => route('teacherEspace'),
                            auth()->user()->hasRole('student') => route('studentEspace'),
                            default => route('dashboard'),
                        };
                    @endphp

                    <a href="{{ $accountUrl }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold
                              bg-gray-900 text-white hover:bg-gray-800 transition
                              dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        Mon compte
                    </a>
                @endauth

                @guest
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold
                              border border-gray-200 dark:border-gray-800
                              bg-white/70 dark:bg-gray-950/40
                              text-gray-800 dark:text-gray-200
                              hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                        Connexion
                    </a>

                    {{-- Chez toi l’inscription passe par /inscription --}}
                    <a href="/inscription"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold
                              bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        S’inscrire
                    </a>
                @endguest
            </div>
        </div>
    </header>

    <main id="top" class="landing-main">
        @yield('content')
    </main>

    {{-- Footer --}}
        <footer id="landingFooter" class="border-t border-gray-200/70 dark:border-gray-800/70 bg-gray-50 dark:bg-gray-950"> 
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/image/logo.png') }}" alt="EpiRC" class="h-9 w-9 object-contain">
                    <div class="leading-tight">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">EpiRC</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Faculté de Médecine — Université de Mahajanga</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('faq') }}" class="px-3 py-2 rounded-xl text-sm font-medium hover:bg-white/70 dark:hover:bg-gray-900 transition">FAQ</a>
                    <a href="{{ route('help') }}" class="px-3 py-2 rounded-xl text-sm font-medium hover:bg-white/70 dark:hover:bg-gray-900 transition">Aide</a>

                    @auth
                        @php
                            $accountUrl = match(true) {
                                auth()->user()->hasRole('admin')   => route('adminEspace'),
                                auth()->user()->hasRole('teacher') => route('teacherEspace'),
                                auth()->user()->hasRole('student') => route('studentEspace'),
                                default => route('dashboard'),
                            };
                        @endphp
                        <a href="{{ $accountUrl }}" class="px-3 py-2 rounded-xl text-sm font-medium hover:bg-white/70 dark:hover:bg-gray-900 transition">
                            Mon compte
                        </a>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-xl text-sm font-medium hover:bg-white/70 dark:hover:bg-gray-900 transition">
                            Connexion
                        </a>
                    @endguest
                </div>
            </div>

            <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} Faculté de Médecine — Université de Mahajanga. Tous droits réservés.
            </div>
        </div>
    </footer>

    <script>
        function landingTheme(){
            return {
                darkMode: false,
                init(){
                    const saved = localStorage.getItem('darkMode');
                    // compat: ton app.js utilise "true/false", ici on accepte aussi "1/0"
                    if (saved === '1' || saved === '0') {
                        this.darkMode = saved === '1';
                    } else if (saved === 'true' || saved === 'false') {
                        this.darkMode = saved === 'true';
                    } else {
                        this.darkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    const applyHeaderOffset = () => {
                        const header = document.getElementById('landingHeader');
                        if (!header) return;
                        const h = Math.ceil(header.getBoundingClientRect().height || 64);
                        document.documentElement.style.setProperty('--landing-header-h', h + 'px');
                    };

                    applyHeaderOffset();
                    window.addEventListener('resize', applyHeaderOffset, { passive: true });
                    setTimeout(applyHeaderOffset, 60);
                    setTimeout(applyHeaderOffset, 250);

                    // force la classe dès le chargement
                    document.documentElement.classList.toggle('dark', this.darkMode);
                },
                toggleDark(){
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode ? 'true' : 'false');
                    document.documentElement.classList.toggle('dark', this.darkMode);
                }
            }
        }
    </script>
</body>
</html>
