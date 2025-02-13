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
            <button class="close-modal">&times;</button>
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
</div>

@push('styles')
<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background-color: #fff;
    margin: 2% auto;
    padding: 20px;
    width: 90%;
    max-width: 1200px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close-modal {
    position: absolute;
    right: 10px;
    top: 10px;
    font-size: 24px;
    cursor: pointer;
    background: none;
    border: none;
    color: #666;
}

.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.9);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}

.loading-content {
    text-align: center;
    padding: 20px;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

.loading-text {
    margin-top: 20px;
}

.progress-bar {
    width: 300px;
    height: 6px;
    background-color: #f3f3f3;
    border-radius: 3px;
    margin: 10px auto;
    overflow: hidden;
}

.progress-fill {
    width: 0%;
    height: 100%;
    background-color: #3498db;
    transition: width 0.3s ease;
}

.navigation {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.nav-btn {
    padding: 0.5rem;
    border-radius: 0.375rem;
    background-color: #f3f4f6;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.nav-btn:hover {
    background-color: #e5e7eb;
}

.page-number {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Styles pour le flipbook */
.flipbook {
    margin: 0 auto;
}

.flipbook .page {
    background-color: white;
}

.cover-page {
    background: linear-gradient(to bottom right, #f3f4f6, #fff);
    padding: 2rem;
    text-align: center;
}

.end-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 2rem;
    text-align: center;
}

/* Mode sombre */
.dark .modal-content {
    background-color: #1f2937;
    color: #fff;
}

.dark .nav-btn {
    background-color: #374151;
}

.dark .nav-btn:hover {
    background-color: #4b5563;
}

.dark .loading-overlay {
    background-color: rgba(31, 41, 55, 0.9);
}

.dark .progress-bar {
    background-color: #374151;
}

.dark .progress-fill {
    background-color: #60a5fa;
}
</style>
@endpush

@include('livewire.document.script')
