{{-- resources/views/livewire/student/student-document.blade.php --}}
<div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-6 space-y-6">
    {{-- Filtre Vu / Non vu - Responsive --}}
<div class="flex items-center gap-2 overflow-x-auto pb-1 
            [-ms-overflow-style:none] [scrollbar-width:none] 
            [&::-webkit-scrollbar]:hidden">
    
    {{-- Tous --}}
    <button wire:click="setViewedFilter('all')"
            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition shrink-0
                   {{ $viewedFilter === 'all' 
                      ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20' 
                      : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="hidden min-[375px]:inline">Tous</span>
        <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                     {{ $viewedFilter === 'all' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800' }}">
            {{ ($viewedCount ?? 0) + ($unviewedCount ?? 0) }}
        </span>
    </button>

    {{-- Non vus --}}
    <button wire:click="setViewedFilter('unviewed')"
            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition shrink-0
                   {{ $viewedFilter === 'unviewed' 
                      ? 'bg-red-600 text-white shadow-sm ring-2 ring-red-600/20' 
                      : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        </svg>
        <span class="hidden min-[375px]:inline whitespace-nowrap">Non vus</span>
        @if(($unviewedCount ?? 0) > 0)
            <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                         {{ $viewedFilter === 'unviewed' ? 'bg-white/20' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' }}">
                {{ $unviewedCount }}
            </span>
        @endif
    </button>

    {{-- Vus --}}
    <button wire:click="setViewedFilter('viewed')"
            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition shrink-0
                   {{ $viewedFilter === 'viewed' 
                      ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-600/20' 
                      : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <span class="hidden min-[375px]:inline">Vus</span>
        @if(($viewedCount ?? 0) > 0)
            <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                         {{ $viewedFilter === 'viewed' ? 'bg-white/20' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' }}">
                {{ $viewedCount }}
            </span>
        @endif
    </button>

    <div class="inline-flex w-full sm:w-auto rounded-xl border border-gray-200 bg-white p-1 dark:border-gray-800 dark:bg-gray-900">
    <button type="button"
            wire:click="setScope('active')"
            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition
            {{ $scope === 'active' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
        Actifs
        <span class="min-w-[1.5rem] h-5 px-2 rounded-full text-[10px] font-bold
                     {{ $scope === 'active' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }}">
            {{ $activeTotal ?? 0 }}
        </span>
    </button>

    <button type="button"
            wire:click="setScope('archives')"
            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition
            {{ $scope === 'archives' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
        Archives
        <span class="min-w-[1.5rem] h-5 px-2 rounded-full text-[10px] font-bold
                     {{ $scope === 'archives' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }}">
            {{ $archivedTotal ?? 0 }}
        </span>
    </button>
</div>

</div>
    {{-- Header + switch grid/list + infos --}}
    @include('livewire.student.sections.header-liste')

    {{-- Content --}}
    <div wire:key="view-type-{{ $viewType }}">
        @if (($documents?->count() ?? 0) === 0)
            {{-- Empty state --}}
            <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-950 p-8 sm:p-10 text-center">
                <div class="mx-auto h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center text-gray-600 dark:text-gray-300">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 3v4h4"/>
                    </svg>
                </div>

                <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                    Aucun document disponible
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Les cours apparaîtront ici dès qu’un enseignant publie des supports.
                </p>
            </div>
        @else
        
            @include('livewire.documents.liste-document')

        @endif
    </div>

    {{-- Pagination --}}
    @if(($documents?->count() ?? 0) > 0)
        <div class="pt-2">
            {{ $documents->links() }}
        </div>
    @endif

    <x-footer-version />


</div>
