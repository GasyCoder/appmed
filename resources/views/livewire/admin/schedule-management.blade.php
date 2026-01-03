<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Gestion des emplois du temps
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $schedules->total() }} emploi(s) du temps au total
            </p>
        </div>
        <a href="{{ route('admin.schedules.upload') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau
        </a>
    </div>

    {{-- Filtres --}}
    <div class="mb-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Recherche --}}
            <div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Rechercher..."
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
            </div>

            {{-- Type --}}
            <div>
                <select wire:model.live="typeFilter"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                    <option value="">Tous les types</option>
                    <option value="emploi_du_temps">Emploi du temps</option>
                    <option value="planning_examens">Planning examens</option>
                    <option value="calendrier">Calendrier</option>
                </select>
            </div>

            {{-- Niveau --}}
            <div>
                <select wire:model.live="niveauFilter"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                    <option value="">Tous les niveaux</option>
                    @foreach(\App\Models\Niveau::where('status', true)->get() as $niveau)
                        <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Statut --}}
            <div>
                <select wire:model.live="statusFilter"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                    <option value="">Tous les statuts</option>
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Liste --}}
    <div wire:loading
         wire:target="search,typeFilter,niveauFilter,statusFilter,toggleStatus,deleteSchedule,page"
         aria-busy="true"
         class="space-y-4">
        <x-skeleton.card :lines="4" />
        <x-skeleton.card :lines="4" />
        <x-skeleton.card :lines="4" />
    </div>

    <div wire:loading.remove
         wire:target="search,typeFilter,niveauFilter,statusFilter,toggleStatus,deleteSchedule,page"
         class="space-y-4">
        @forelse($schedules as $schedule)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4 flex-1">
                        {{-- Icône --}}
                        <div class="flex-shrink-0">
                            @if($schedule->isPdf())
                                <div class="h-12 w-12 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="h-12 w-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Informations --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $schedule->title }}
                                </h3>
                                @if($schedule->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                        Inactif
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $schedule->type)) }}
                                </span>

                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $schedule->academic_year }}
                                </span>

                                @if($schedule->niveau)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        {{ $schedule->niveau->name }}
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ $schedule->view_count }} vues
                                </span>

                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    {{ $schedule->download_count }}
                                </span>

                                <span>{{ $schedule->file_size_formatted }}</span>
                            </div>

                            @if($schedule->start_date || $schedule->end_date)
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Période: 
                                    @if($schedule->start_date)
                                        du {{ $schedule->start_date->format('d/m/Y') }}
                                    @endif
                                    @if($schedule->end_date)
                                        au {{ $schedule->end_date->format('d/m/Y') }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        {{-- Voir --}}
                        <a href="{{ route('schedule.view', $schedule->id) }}" 
                           target="_blank"
                           class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>

                        {{-- Toggle Status --}}
                        <button wire:click="toggleStatus({{ $schedule->id }})"
                                class="p-2 text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400">
                            @if($schedule->is_active)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            @endif
                        </button>

                        {{-- Supprimer --}}
                        <button wire:click="deleteSchedule({{ $schedule->id }})"
                                wire:confirm="Êtes-vous sûr de vouloir supprimer cet emploi du temps ?"
                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                    Aucun emploi du temps
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Commencez par uploader un emploi du temps
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($schedules->hasPages())
        <div class="mt-6">
            {{ $schedules->links() }}
        </div>
    @endif
</div>
