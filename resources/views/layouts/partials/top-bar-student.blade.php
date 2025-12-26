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
        @include('layouts.partials.right-top-bar')
    </div>
</header>
