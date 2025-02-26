<x-app-layout>
    <div class="min-h-screen pt-16 pb-20"> <!-- Ajouté pb-20 pour l'espace en bas -->
        <!-- Container principal -->
        <div class="viewer-container">
            <!-- Loader -->
            <div id="loading" class="flipbook-loading-container loading-container">
                <div class="spinner"></div>
                <p>Chargement du document...</p>
            </div>

            <!-- Zone du flipbook avec padding bottom supplémentaire -->
            <div id="flipbook-container">
                <div id="flipbook">
                    <!-- Les pages seront ajoutées dynamiquement via JavaScript -->
                </div>
            </div>

            <!-- Contrôles de navigation avec design amélioré -->
            <div class="controls" id="pdf-controls">
                <!-- Bouton précédent -->
                <button id="prev" class="control-btn control-nav" aria-label="Page précédente">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <span class="hidden-mobile">Précédent</span>
                </button>

                <!-- Bouton son avec meilleure icône -->
                <button id="soundToggle" class="control-btn control-sound" onclick="toggleSound()" aria-label="Activer/désactiver le son">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                    </svg>
                </button>

                <!-- Contrôles de zoom avec design amélioré -->
                <div class="zoom-controls">
                    <button id="zoom-out" class="control-btn control-zoom" onclick="handleZoom(-0.1)" aria-label="Zoom arrière">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            <line x1="8" y1="11" x2="14" y2="11"/>
                        </svg>
                    </button>
                    <span class="zoom-level">100%</span>
                    <button id="zoom-in" class="control-btn control-zoom" onclick="handleZoom(0.1)" aria-label="Zoom avant">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            <line x1="11" y1="8" x2="11" y2="14"/>
                            <line x1="8" y1="11" x2="14" y2="11"/>
                        </svg>
                    </button>
                </div>

                <!-- Bouton plein écran avec meilleure icône -->
                <button id="fullscreen" class="control-btn control-fullscreen" title="Plein écran" onclick="toggleFullscreen()" aria-label="Mode plein écran">
                    <span class="fullscreen-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 3 21 3 21 9"/>
                            <polyline points="9 21 3 21 3 15"/>
                            <line x1="21" y1="3" x2="14" y2="10"/>
                            <line x1="3" y1="21" x2="10" y2="14"/>
                        </svg>
                    </span>
                </button>

                <!-- Bouton suivant -->
                <button id="next" class="control-btn control-nav" aria-label="Page suivante">
                    <span class="hidden-mobile">Suivant</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>

                <audio id="turnPageSound" preload="auto" class="hidden">
                    <source src="{{ asset('assets/turnpage-99756.mp3') }}" type="audio/mpeg">
                </audio>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="flipbook-error-message error-message">
                <div class="error-container">
                    <div class="text-red-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3>Erreur de chargement</h3>
                    <p id="error-text"></p>
                    <button onclick="location.reload()" class="retry-button">
                        Réessayer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        {{ $css }}
    </style>

    @push('scripts')
        <script>
            // Script pour améliorer l'interaction avec les contrôles
            document.addEventListener('DOMContentLoaded', function() {
                // Protéger la navbar
                const navbarProtector = document.createElement('div');
                navbarProtector.style.position = 'fixed';
                navbarProtector.style.top = '0';
                navbarProtector.style.left = '0';
                navbarProtector.style.right = '0';
                navbarProtector.style.height = '64px';
                navbarProtector.style.zIndex = '40';
                navbarProtector.style.pointerEvents = 'none';
                document.body.appendChild(navbarProtector);

                // Gérer l'affichage des contrôles
                const controls = document.getElementById('pdf-controls');
                let controlsTimeout;

                // Masquer les contrôles après un délai d'inactivité
                function setControlsTimeout() {
                    clearTimeout(controlsTimeout);
                    controls.style.opacity = '1';

                    controlsTimeout = setTimeout(() => {
                        if (!controls.matches(':hover')) {
                            controls.style.opacity = '0.5';
                        }
                    }, 3000);
                }

                // Activer les contrôles lors du mouvement de la souris
                document.addEventListener('mousemove', setControlsTimeout);

                // Réinitialiser le délai lorsqu'on survole les contrôles
                controls.addEventListener('mouseenter', () => {
                    controls.style.opacity = '1';
                    clearTimeout(controlsTimeout);
                });

                controls.addEventListener('mouseleave', setControlsTimeout);

                // Ajuster l'espacement pour éviter de masquer le contenu
                const pdfPageNumbers = document.querySelectorAll('.page-number');
                pdfPageNumbers.forEach(pageNumber => {
                    pageNumber.style.bottom = '40px';
                });

                // Initialiser avec les contrôles visibles
                setControlsTimeout();
            });
        </script>
    @endpush
    @include('pdf.assets.flipbook_js')
</x-app-layout>
