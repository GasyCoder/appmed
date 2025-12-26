@php
    $routeName = request()->route()?->getName();

    $pageTitle = match ($routeName) {
        'studentEspace' => 'EPIRC',
        'student.document' => 'Mes cours',
        'student.timetable' => 'Emploi du temps',
        'student.myTeacher' => 'Mes enseignants',
        'programs' => 'Programmes',
        'faq' => 'FAQ',
        'help' => 'Aide',
        default => 'Espace étudiant',
    };
@endphp

<header
    class="sticky top-0 z-30 h-16
           border-b border-gray-200/70 dark:border-gray-800/70
           bg-white/80 dark:bg-gray-950/70
           backdrop-blur supports-[backdrop-filter]:bg-white/70 supports-[backdrop-filter]:dark:bg-gray-950/60"
>
    <div class="h-full mx-auto w-full max-w-[88rem] px-3 sm:px-4 lg:px-8 flex items-center justify-between gap-3">

        {{-- LEFT: logo + title --}}
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('studentEspace') }}"
               class="inline-flex items-center gap-2 rounded-xl px-2 py-1 transition">
                <img src="{{ asset('assets/image/logo.png') }}" alt="FM UMG" class="w-16 h-16 md:w-16 md:h-16 lg:w-[72px] lg:h-[72px] rounded-xl object-cover">
                <div class="hidden sm:block leading-tight">
                    <div class="text-sm font-bold text-gray-900 dark:text-white truncate">
                        EpiRC
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        Faculté de Médecine • UMG
                    </div>
                </div>
            </a>

            {{-- Mobile title (short) --}}
            <div class="sm:hidden min-w-0">
                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                    {{ $pageTitle }}
                </div>
            </div>
        </div>

        {{-- RIGHT: actions --}}
        <div class="flex items-center gap-2">

            {{-- Dark mode --}}
            <button
                type="button"
                @click="toggleDarkMode()"
                class="inline-flex items-center justify-center h-10 w-10 rounded-full
                       border border-gray-200 dark:border-gray-800
                       bg-white dark:bg-gray-950
                       text-gray-700 dark:text-gray-200
                       hover:bg-gray-50 dark:hover:bg-gray-900
                       shadow-sm
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                aria-label="Basculer le thème"
                title="Basculer le thème"
            >
                <svg x-cloak x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>

                <svg x-cloak x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </button>

            {{-- Notifications (optionnel, tu peux supprimer ce bloc si inutile) --}}
            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    @keydown.escape.window="open = false"
                    class="relative inline-flex items-center justify-center h-10 w-10 rounded-full
                           border border-gray-200 dark:border-gray-800
                           bg-white dark:bg-gray-950
                           text-gray-700 dark:text-gray-200
                           hover:bg-gray-50 dark:hover:bg-gray-900
                           shadow-sm
                           focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                    aria-label="Notifications"
                    title="Notifications"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-2 right-2 block h-2 w-2 rounded-full bg-rose-500"></span>
                </button>

                <div
                    x-cloak
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute right-0 mt-2 w-80 overflow-hidden rounded-xl
                           border border-gray-200 dark:border-gray-800
                           bg-white dark:bg-gray-950
                           shadow-lg"
                >
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Dernières activités</p>
                    </div>

                    <div class="p-4">
                        <div class="rounded-lg border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center">
                            <p class="text-sm text-gray-700 dark:text-gray-200">Aucune nouvelle notification</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Vous êtes à jour.</p>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800">
                        <a href="#" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            Voir tout
                        </a>
                    </div>
                </div>
            </div>

            {{-- Profile dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    @keydown.escape.window="open = false"
                    class="inline-flex items-center gap-3 rounded-full pl-1 pr-2 py-1
                           hover:bg-gray-100 dark:hover:bg-gray-900
                           focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                    aria-label="Menu du compte"
                    title="Compte"
                >
                    <div class="relative">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_url)
                            <img class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-900"
                                 src="{{ Auth::user()->profile_photo_url }}"
                                 alt="{{ Auth::user()->name }}" />
                        @else
                            <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-900
                                        ring-2 ring-white dark:ring-gray-900
                                        flex items-center justify-center">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </span>
                            </div>
                        @endif

                        <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-950"></span>
                    </div>

                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div
                    x-cloak
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl
                           border border-gray-200 dark:border-gray-800
                           bg-white dark:bg-gray-950
                           shadow-lg"
                >
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('profile.show') }}"
                           class="flex items-center gap-2 px-4 py-2 text-sm
                                  text-gray-700 dark:text-gray-200
                                  hover:bg-gray-100 dark:hover:bg-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil
                        </a>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-800"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm
                                       text-gray-700 dark:text-gray-200
                                       hover:bg-gray-100 dark:hover:bg-gray-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
