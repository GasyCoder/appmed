{{-- student-document.blade.php --}}
<div>
    <div class="min-h-screen bg-white py-4 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('livewire.student.sections.header-liste')

            <div wire:key="view-type-{{ $viewType }}">
                @if ($viewType === 'grid')
                    @include('livewire.student.sections.grid')
                @else
                    @include('livewire.student.sections.liste')
                @endif
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $documents->links() }}
            </div>

        </div>
    </div>
    <livewire:pdf-viewer :key="'pdf-viewer-'.uniqid()" />
</div>
@push('scripts')
@endpush
