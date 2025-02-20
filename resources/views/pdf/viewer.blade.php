<x-app-layout>
    <div class="min-h-screen">
        <!-- Container principal -->
        <div class="viewer-container">
            <!-- Loader -->
            <div id="loading" class="loading-container">
                <div class="spinner"></div>
                <p>Chargement du document...</p>
            </div>
            <!-- Zone du flipbook -->
            <div id="flipbook-container">
                <div id="flipbook">
                    <!-- Les pages seront ajoutées dynamiquement via JavaScript -->
                </div>
            </div>
            <!-- Contrôles de navigation -->
            <div class="controls">
                <!-- Bouton précédent -->
                <button id="prev" class="control-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <span class="hidden-mobile">Précédent</span>
                </button>

                <button id="soundToggle" class="control-btn" onclick="toggleSound()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15.536 8.464a5 5 0 0 1 0 7.072m2.828-9.9a9 9 0 0 1 0 12.728M7.5 13.5 4 17H2v-4H0v-2h2V7h2l3.5 3.5z"></path>
                    </svg>
                </button>

                <!-- Contrôles de zoom -->
                <div class="zoom-controls">
                    <button id="zoom-out" class="control-btn" onclick="handleZoom(-0.1)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 12H4"/>
                        </svg>
                    </button>
                    <span class="zoom-level">100%</span>
                    <button id="zoom-in" class="control-btn" onclick="handleZoom(0.1)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>

                <!-- Bouton plein écran -->
                <button id="fullscreen" class="control-btn" title="Plein écran" onclick="toggleFullscreen()">
                    <span class="fullscreen-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h6v6M9 21H3v-6M21 9v6h-6M3 15V9h6" />
                        </svg>
                    </span>
                </button>

                <!-- Bouton suivant -->
                <button id="next" class="control-btn">
                    <span class="hidden-mobile">Suivant</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
                <audio id="turnPageSound" preload="auto" class="hidden">
                    <source src="{{ asset('assets/turnpage-99756.mp3') }}" type="audio/mpeg">
                </audio>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="error-message">
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
        @include('pdf.assets.flipbook_js')
    @endpush
</x-app-layout>
