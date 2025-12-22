{{-- student-document.blade.php --}}
<div>
    <div class="max-w-10xl mx-auto">
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
@push('scripts')
@endpush
