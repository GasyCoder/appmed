
@push('styles')
{{-- Styles supplémentaires pour les animations --}}
<style>
    .calendar-course-card {
        transition: all 0.3s ease-in-out;
    }
    .calendar-course-card:hover {
        transform: translateY(-2px);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
</style>
@endpush

@push('scripts')
{{-- Scripts pour les animations et l'auto-refresh --}}
<script>
    document.addEventListener('livewire:init', function () {
        // Mise à jour automatique toutes les 5 minutes
        setInterval(function() {
            @this.call('$refresh')
        }, 300000);

        // Animation lors du chargement des données
        Livewire.on('calendarUpdated', () => {
            // Vous pouvez ajouter des animations supplémentaires ici
        });
    });
</script>
@endpush
