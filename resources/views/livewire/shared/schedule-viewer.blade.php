<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Emplois du temps
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Consultez vos emplois du temps et plannings
        </p>
    </div>

    {{-- Filtres --}}
    <div class="mb-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <div class="flex items-center gap-4">
            <button wire:click="$set('typeFilter', '')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="$wire.typeFilter === '' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'">
                Tous
            </button>
            <button wire:click="$set('typeFilter', 'emploi_du_temps')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="$wire.typeFilter === 'emploi_du_temps' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'">
                Emplois du temps
            </button>
            <button wire:click="$set('typeFilter', 'planning_examens')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="$wire.typeFilter === 'planning_examens' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'">
                Plannings examens
            </button>
            <button wire:click="$set('typeFilter', 'calendrier')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="$wire.typeFilter === 'calendrier' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'">
                Calendrier
            </button>
        </div>
    </div>

    {{-- Grille des emplois du temps --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($schedules as $schedule)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:shadow-lg transition-all">
                {{-- En-tête --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $schedule->title }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                                {{ ucfirst(str_replace('_', ' ', $schedule->type)) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ $schedule->academic_year }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Aperçu --}}
                <div class="mb-4 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                    @if($schedule->isImage())
                        <img src="{{ $schedule->file_url }}" 
                             alt="{{ $schedule->title }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="h-48 flex items-center justify-center">
                            <svg class="h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Infos --}}
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2 mb-4">
                    @if($schedule->niveau)
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            {{ $schedule->niveau->name }}
                        </div>
                    @endif
                    
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ $schedule->view_count }}
                        </span>
                        <span>{{ $schedule->file_size_formatted }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2">
                    <a href="{{ route('schedule.view', $schedule->id) }}"
                       target="_blank"
                       wire:click="viewSchedule({{ $schedule->id }})"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Voir
                    </a>
                    <button wire:click="downloadSchedule({{ $schedule->id }})"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                    Aucun emploi du temps disponible
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Les emplois du temps seront bientôt disponibles
                </p>
            </div>
        @endforelse
    </div>
</div>