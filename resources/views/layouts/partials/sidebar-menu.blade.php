<!-- Sidebar -->
<div x-cloak :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
    class="fixed top-0 left-0 z-40 w-72 h-screen transition-transform lg:translate-x-0">

    <!-- Sidebar Container -->
    <div class="flex flex-col h-full bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
    @php
        $homeRoute = match(true) {
            auth()->user()?->hasRole('admin')   => route('adminEspace'),
            auth()->user()?->hasRole('teacher') => route('teacherEspace'),
            default                            => route('studentEspace'),
        };
    @endphp

    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
        <a href="{{ $homeRoute }}" class="flex items-center gap-3 min-w-0">
            <img
                src="{{ asset('assets/image/logo.png') }}"
                alt="logo"
                class="w-11 h-11 md:w-12 md:h-12 lg:w-[72px] lg:h-[72px] object-contain shrink-0"
            />

            <div class="min-w-0 leading-tight">
                <div class="text-sm lg:text-base font-semibold text-gray-900 dark:text-white truncate">
                    EpiRC
                </div>
                <div class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 truncate">
                    Faculté de Médecine • UMG
                </div>
            </div>
        </a>

        <button
            type="button"
            @click="sidebarOpen = false"
            class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900/15 dark:focus:ring-white/15"
            aria-label="Fermer le menu"
        >
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>


        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @role('admin')
                <!-- Admin Navigation -->
                <div class="mb-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Administration</p>
                    <nav class="mt-3 space-y-1">
                        <a href="{{ route('adminEspace') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('adminEspace') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            wire:navigate>
                            <svg class="flex-shrink-0 w-5 h-5 mr-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Accueil
                        </a>
                        @php
                            $usersActive = request()->routeIs(
                                'admin.teachers*',
                                'admin.students*',
                                'admin.authorized-emails*'
                            );
                        @endphp

                        <div class="relative" x-data="{ open: {{ $usersActive ? 'true' : 'false' }} }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium text-left rounded-lg focus:outline-none transition-colors
                                {{ $usersActive
                                    ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            >
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 w-5 h-5 mr-3
                                        {{ $usersActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Utilisateurs
                                </div>

                                <svg class="w-4 h-4 transition-transform"
                                    :class="{ 'rotate-180': open }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Sous-menu -->
                            <div x-cloak
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="pl-12 mt-1 space-y-1">

                                {{-- Enseignants --}}
                                <a href="{{ route('admin.teachers') }}" wire:navigate
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                                {{ request()->routeIs('admin.teachers*')
                                        ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 12v5c0 .6.4 1.2 1 1.4 1.4.6 3.2 1.1 5 1.1s3.6-.5 5-1.1c.6-.2 1-.8 1-1.4v-5"/>
                                    </svg>
                                    Enseignants
                                </a>

                                {{-- Étudiants --}}
                                <a href="{{ route('admin.students') }}" wire:navigate
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                                {{ request()->routeIs('admin.students*')
                                        ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 14l6.16-3.422a12.083 12.083 0 01.84 4.19c0 1.12-.15 2.2-.43 3.22-.15.54-.62.95-1.18 1.03-1.32.2-2.67.3-4.01.3s-2.69-.1-4.01-.3c-.56-.08-1.03-.49-1.18-1.03-.28-1.02-.43-2.1-.43-3.22 0-1.44.3-2.82.84-4.19L12 14z"/>
                                    </svg>
                                    Étudiants
                                </a>

                                {{-- Emails autorisés --}}
                                <a href="{{ route('admin.authorized-emails') }}" wire:navigate
                                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                                {{ request()->routeIs('admin.authorized-emails*')
                                        ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Emails autorisés
                                </a>

                            </div>
                        </div>

                    </nav>
                </div>
                <div class="mb-4">
                    <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Scolarités</p>
                    <nav class="mt-3 space-y-1">

                        <!-- Emploi du temps -->
                        <a href="{{ route('admin.timetable') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                            {{ request()->routeIs('admin.timetable') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            wire:navigate>
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Emploi du temps
                        </a>

                        <!-- Emploi du temps -->
                        <a href="{{ route('admin.announcements') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                            {{ request()->routeIs('admin.announcements') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            wire:navigate>
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Annonces
                        </a>

                        <!-- Programmes -->
                        <a href="{{ route('programs') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                            {{ request()->routeIs('programs') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                            wire:navigate>
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Programmes
                        </a>

                        <a href="{{ route('admin.niveau') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.niveau') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        wire:navigate>
                            <svg class="flex-shrink-0 w-5 h-5 mr-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Niveaux
                        </a>

                        <a href="{{ route('admin.parcour') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.parcour') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        wire:navigate>
                            <svg class="flex-shrink-0 w-5 h-5 mr-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Parcours
                        </a>

                        <a href="{{ route('admin.semestre') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.semestre') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        wire:navigate>
                            <svg class="flex-shrink-0 w-5 h-5 mr-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Semestres
                        </a>

                    </nav>
                </div>
            @endrole

            @role('teacher')
            <!-- Teacher Navigation -->
            <div class="mb-4">
                <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Enseignant</p>
                <nav class="mt-3 space-y-1">
                        <a href="{{ route('teacherEspace') }}"
                           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacherEspace') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Tableau de bord
                        </a>

                        <a href="{{ route('documents.index') }}"
                           class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                           {{ request()->routeIs(['documents.index', 'document.upload']) ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Mes Documents
                        </a>

                        <!-- Emploi du temps -->
                        <a href="{{ route('teacher.timetable') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                            {{ request()->routeIs('teacher.timetable') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Emploi du temps
                        </a>

                        <!-- Programmes -->
                        <a href="{{ route('programs') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                            {{ request()->routeIs('programs') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="flex-shrink-0 w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Programmes
                        </a>
                    </nav>
                </div>
            @endrole

            <div class="mb-4">
                <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Support</p>

                <nav class="mt-3 space-y-1">
                    {{-- FAQ --}}
                    <a href="{{ route('faq') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                    {{ request()->routeIs('faq') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    wire:navigate>
                        <svg class="flex-shrink-0 w-5 h-5 mr-3 {{ request()->routeIs('faq') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9a3 3 0 115.544 0c0 1.5-1.5 2.25-1.5 2.25S11 12 11 13m1 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                        </svg>
                        FAQ
                    </a>

                    {{-- Aide --}}
                    <a href="{{ route('help') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                    {{ request()->routeIs('help') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/75' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    wire:navigate>
                        <svg class="flex-shrink-0 w-5 h-5 mr-3 {{ request()->routeIs('help') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            aria-hidden="true">
                            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                            <path d="M12 16a4 4 0 100-8 4 4 0 000 8z"/>
                            <path d="M4.93 4.93l3.54 3.54"/>
                            <path d="M19.07 4.93l-3.54 3.54"/>
                            <path d="M19.07 19.07l-3.54-3.54"/>
                            <path d="M4.93 19.07l3.54-3.54"/>
                        </svg>
                        Aide/Support
                    </a>
                </nav>
            </div>

        </div>
        
            <x-footer-version />

            <!-- Profile Section -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay pour mobile -->
    <div x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-600 bg-opacity-75 z-30 lg:hidden"
        @click="sidebarOpen = false">
    </div>
