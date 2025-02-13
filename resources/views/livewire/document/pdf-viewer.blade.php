<!-- livewire/document/pdf-viewer.blade.php -->
<div wire:ignore>
    <!-- Loading Overlay -->
    <div id="loadingIndicator" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">
                <h3>Chargement du document</h3>
                <p class="progress-text">Préparation des pages...</p>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Modal -->
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <button class="close-modal">&times;</button> <!-- Bouton placé à l'intérieur du modal-content -->

            <!-- Flipbook Container -->
            <div id="flipbook" class="flipbook"></div>
            <br>
            <!-- Navigation -->
            <div class="navigation">
                <button id="firstPage" class="nav-btn" title="Première page">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <button id="prev" class="nav-btn" title="Page précédente">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="page-number">
                    <span id="currentPage" class="font-medium">1</span>
                </div>
                <button id="next" class="nav-btn" title="Page suivante">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button id="lastPage" class="nav-btn" title="Dernière page">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @include('livewire.document.style')
</div>
@include('livewire.document.script')
<script>
    function openPdfViewer(documentData) {
    // Émettre l'événement Livewire
    Livewire.dispatch('view-document', { documentId: documentData.id });
}
</script>

