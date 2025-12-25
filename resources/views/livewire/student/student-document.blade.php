{{-- resources/views/livewire/student/student-document.blade.php --}}
<div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-6 space-y-6">

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
            @if ($viewType === 'grid')
                @include('livewire.student.sections.grid')
            @else
                @include('livewire.student.sections.liste')
            @endif
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
