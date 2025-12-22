<!-- Header Section (UI améliorée + null-safe) -->
@php
    $user = auth()->user();
    $niveauName = $user?->niveau?->name ?? 'Niveau non défini';
    $parcourName = $user?->parcour?->name ?? 'Parcours non défini';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/70 dark:border-gray-700/70 overflow-hidden mb-8">
    <!-- Top section -->
    <div class="p-4 sm:p-6 border-b border-gray-200/60 dark:border-gray-700/60 bg-gradient-to-r from-blue-50/60 dark:from-blue-900/20 to-indigo-50/60 dark:to-indigo-900/20">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Left -->
            <div class="flex items-start gap-4">
                <div class="p-3 bg-white dark:bg-gray-700 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-600">
                    <svg class="h-7 w-7 sm:h-8 sm:w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                            Documents
                        </h2>

                        <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                     bg-white/70 dark:bg-gray-700/70 text-gray-700 dark:text-gray-200 ring-1 ring-gray-200 dark:ring-gray-600">
                            {{ $documents->total() ?? '' }} disponibles
                        </span>
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold
                                     bg-blue-500/10 dark:bg-blue-400/10 text-blue-700 dark:text-blue-300 ring-1 ring-blue-200/70 dark:ring-blue-800/40">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h6" />
                            </svg>
                            {{ $niveauName }}
                        </span>

                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold
                                     bg-indigo-500/10 dark:bg-indigo-400/10 text-indigo-700 dark:text-indigo-300 ring-1 ring-indigo-200/70 dark:ring-indigo-800/40">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16" />
                            </svg>
                            {{ $parcourName }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">
                <div class="flex items-center gap-3 text-sm px-4 py-2.5 bg-white/80 dark:bg-gray-700/70 rounded-2xl
                            border border-gray-200/70 dark:border-gray-600/70 shadow-sm w-full sm:w-auto">
                    <svg class="h-5 w-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <time class="font-semibold text-gray-700 dark:text-gray-200" datetime="{{ now()->format('Y-m-d\TH:i:s') }}">
                        {{ now()->format('d/m/Y H:i') }}
                    </time>
                </div>

                <!-- Reset filters -->
                <button type="button"
                        wire:click="$set('search',''); $set('teacherFilter',''); $set('semesterFilter','');"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-semibold
                               bg-gray-100 hover:bg-gray-200 text-gray-700
                               dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200
                               border border-gray-200/70 dark:border-gray-600/70
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                               transition w-full sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v6h6M20 20v-6h-6M5.5 18.5A9 9 0 1018.5 5.5" />
                    </svg>
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="p-4 sm:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
            <!-- Search -->
            <div class="relative lg:col-span-5">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Rechercher un document..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-200/70 dark:border-gray-600/70 rounded-2xl
                           bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400
                           placeholder-gray-500 dark:placeholder-gray-400 transition"
                >
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Teacher -->
            <div class="relative lg:col-span-3">
                <select
                    wire:model.live="teacherFilter"
                    class="w-full appearance-none pl-10 pr-10 py-3 rounded-2xl text-sm
                           bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                           border border-gray-200/70 dark:border-gray-600/70
                           focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition"
                >
                    <option value="">Tous les enseignants</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <!-- Semester -->
            <div class="relative lg:col-span-2">
                <select
                    wire:model.live="semesterFilter"
                    class="w-full appearance-none pl-10 pr-10 py-3 rounded-2xl text-sm
                           bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white
                           border border-gray-200/70 dark:border-gray-600/70
                           focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition"
                >
                    <option value="">Tous les semestres</option>
                    @foreach($semestres as $semestre)
                        <option value="{{ $semestre->id }}">{{ $semestre->name }}</option>
                    @endforeach
                </select>
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <!-- View toggle -->
            <div class="lg:col-span-2">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl p-1 flex gap-1 w-full justify-center">
                    <button type="button"
                        wire:click="toggleView('grid')"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold transition
                               {{ $viewType === 'grid'
                                    ? 'bg-white dark:bg-gray-600 shadow text-blue-700 dark:text-blue-300'
                                    : 'text-gray-600 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-600/60' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span class="hidden sm:inline">Grille</span>
                    </button>

                    <button type="button"
                        wire:click="toggleView('list')"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold transition
                               {{ $viewType === 'list'
                                    ? 'bg-white dark:bg-gray-600 shadow text-blue-700 dark:text-blue-300'
                                    : 'text-gray-600 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-600/60' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span class="hidden sm:inline">Liste</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading indicator -->
        <div wire:loading class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            Chargement...
        </div>
    </div>
</div>
