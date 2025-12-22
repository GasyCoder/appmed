{{-- view principale --}}
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-4 space-y-6 max-w-7xl mx-auto">
    {{-- En-tête avec filtres --}}
    @include('livewire.admin.pages.filtre-state')

    {{-- Calendrier --}}
    @include('livewire.admin.modal.calendar-grid')

    {{-- Modal de création --}}
    @include('livewire.admin.modal.calendar-create-modal')

    </div>
</div>

@push('styles')

<style>
/* Calendar Styles */
.calendar-container {
    @apply bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 w-full overflow-x-auto;
}

.calendar-header {
    @apply relative bg-gradient-to-r from-indigo-600 to-indigo-800 dark:from-indigo-800 dark:to-indigo-900
           text-white p-3 sm:p-6 rounded-t-lg;
}

.calendar-filters {
    @apply bg-white dark:bg-gray-800 border-b dark:border-gray-700 p-2 sm:p-4 flex flex-col sm:flex-row gap-2 sm:gap-4;
}

.calendar-select {
    @apply w-full text-xs sm:text-sm rounded-lg border-gray-300 dark:border-gray-600
           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
           focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400
           transition-all duration-200;
}

.stats-card {
    @apply bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl
           transition-all duration-300 p-3 sm:p-5 relative overflow-hidden;
}

.course-card {
    @apply p-2 sm:p-3 rounded-lg transition-all duration-200 cursor-pointer
           hover:shadow-lg relative overflow-hidden text-xs sm:text-sm;
}

/* Responsive Grid Layout */
.calendar-grid {
    @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-2 sm:gap-4 min-w-full;
}

/* Responsive Text Sizes */
.calendar-title {
    @apply text-lg sm:text-2xl font-bold;
}

.calendar-subtitle {
    @apply text-xs sm:text-sm;
}

/* Mobile-first Navigation */
.calendar-nav {
    @apply flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-4 mb-4;
}

/* Responsive Filters */
.filters-container {
    @apply grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 sm:gap-4;
}

/* Responsive Stats Grid */
.stats-grid {
    @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6;
}

/* Enhanced Mobile Touch Targets */
.interactive-element {
    @apply min-h-[44px] sm:min-h-[inherit];
}

/* Responsive Modal */
.modal-content {
    @apply w-[95%] sm:w-[80%] md:w-[70%] lg:w-[60%] max-h-[90vh] overflow-y-auto;
}

/* Loading States */
.loading-overlay {
    @apply fixed inset-0 bg-white/50 dark:bg-gray-900/50
           flex items-center justify-center z-50;
}

.loading-spinner {
    @apply w-6 h-6 sm:w-8 sm:h-8 text-indigo-600 dark:text-indigo-400 animate-spin;
}

/* Media Queries for Calendar Slots */
@media (max-width: 640px) {
    .calendar-slot {
        @apply min-w-[100%] p-2;
    }

    .course-info {
        @apply text-xs;
    }

    .course-tag {
        @apply text-[10px] px-1.5 py-0.5;
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    .calendar-slot {
        @apply min-w-[200px] p-3;
    }
}

@media (min-width: 1025px) {
    .calendar-slot {
        @apply min-w-[250px] p-4;
    }
}

/* Dark Mode Enhancements */
@media (prefers-color-scheme: dark) {
    .calendar-container {
        @apply bg-gray-900;
    }

    .calendar-select {
        @apply bg-gray-800 border-gray-700;
    }
}
</style>

@endpush
