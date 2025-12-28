{{-- resources/views/layouts/partials/bottom-nav.blade.php --}}
@php
    use Illuminate\Support\Facades\Route;

    // Active states (inclure routes liées si besoin)
    $isHome = request()->routeIs('studentEspace');

    $isCours = request()->routeIs('student.document')
        || request()->routeIs('document.serve')
        || request()->routeIs('document.download');

    $isUE = request()->routeIs('student.ue'); // à activer quand ta route existe

    $isEmploi = request()->routeIs('student.timetable')
        || request()->routeIs('schedule.view')
        || request()->routeIs('schedule.serve')
        || request()->routeIs('schedule.download');

    // Helpers classes
    $tabBase = "flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 text-xs transition
                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/60";

    $tabIdle = "text-gray-600 hover:text-gray-900 hover:bg-gray-50
                dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-800";

    $tabActive = "text-indigo-700 bg-indigo-50
                  dark:text-indigo-300 dark:bg-indigo-500/10";

    // Safe href (si route pas encore créée)
    $ueHref = Route::has('student.ue') ? route('student.ue') : '#';
@endphp

<nav class="fixed bottom-0 inset-x-0 z-40 border-t border-gray-200 dark:border-gray-800
            bg-white/95 dark:bg-gray-900/95 backdrop-blur">
    <div class="mx-auto max-w-4xl px-3">
        <div class="grid grid-cols-4 gap-1 py-2">

            {{-- Accueil --}}
            <a href="{{ route('studentEspace') }}"
               class="{{ $tabBase }} {{ $isHome ? $tabActive : $tabIdle }}"
               aria-current="{{ $isHome ? 'page' : 'false' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/>
                </svg>
                Accueil
            </a>

            {{-- Cours --}}
            <a href="{{ route('documents.index') }}"
               class="{{ $tabBase }} {{ $isCours ? $tabActive : $tabIdle }}"
               aria-current="{{ $isCours ? 'page' : 'false' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <line x1="10" y1="9" x2="8" y2="9" />
                </svg>
                Cours
            </a>

            {{-- Mes UE (safe si route pas prête) --}}
            <a href="{{ $ueHref }}"
               class="{{ $tabBase }} {{ $isUE ? $tabActive : $tabIdle }} {{ $ueHref === '#' ? 'opacity-60 pointer-events-none' : '' }}"
               aria-current="{{ $isUE ? 'page' : 'false' }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="4" width="16" height="16" rx="2" />
                        <path d="M4 8h16" />
                        <path d="M9 12h6" />
                        <path d="M9 16h6" />
                    </svg>
                Mes UE
            </a>

            {{-- Emploi --}}
            <a href="{{ route('student.timetable') }}"
               class="{{ $tabBase }} {{ $isEmploi ? $tabActive : $tabIdle }}"
               aria-current="{{ $isEmploi ? 'page' : 'false' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3M4 11h16M6 5h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/>
                </svg>
                Emploi
            </a>

        </div>
    </div>
</nav>
