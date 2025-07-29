<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Header avec contrôles -->
        <div class="bg-white shadow-sm border-b sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    
                    <!-- Titre et infos -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <button onclick="history.back()" 
                                class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Retour
                            </button>
                            
                            <h1 class="text-lg font-semibold text-gray-900 truncate">
                                {{ $document->title ?? $filename }}
                            </h1>
                        </div>
                        
                        @if($teacherInfo)
                            <div class="text-sm text-gray-600">
                                @if($teacherInfo['grade']){{ $teacherInfo['grade'] }} @endif
                                {{ $teacherInfo['name'] }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Contrôles -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="zoomOut()" 
                            class="px-3 py-2 text-sm bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                            </svg>
                        </button>
                        
                        <span id="zoomLevel" class="px-3 py-2 text-sm bg-gray-100 rounded-md">100%</span>
                        
                        <button onclick="zoomIn()" 
                            class="px-3 py-2 text-sm bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </button>
                        
                        <button onclick="toggleFullscreen()" 
                            class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                        </button>
                        
                        <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                            class="px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </a>
                        
                        <button onclick="location.reload()" 
                            class="px-3 py-2 text-sm bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container du PDF -->
        <div class="flex-1" id="pdfContainer">
            
            <!-- Loading -->
            <div id="loading" class="flex flex-col items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Chargement du document...</p>
            </div>
            
            <!-- Error -->
            <div id="error" class="flex flex-col items-center justify-center py-20 hidden">
                <div class="text-red-500 text-5xl mb-4">⚠️</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Erreur de chargement</h3>
                <p id="errorMessage" class="text-gray-600 mb-4 text-center"></p>
                <div class="flex gap-3">
                    <button onclick="location.reload()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Réessayer
                    </button>
                    <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Télécharger
                    </a>
                </div>
            </div>
            
            <!-- PDF Viewer -->
            <div id="pdfViewer" class="hidden">
                <div class="bg-gray-100 min-h-screen flex justify-center p-4">
                    <div class="bg-white shadow-lg" id="pdfWrapper">
                        <canvas id="pdfCanvas" class="max-w-full h-auto"></canvas>
                    </div>
                </div>
                
                <!-- Navigation des pages -->
                <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-full shadow-lg border px-4 py-2">
                    <div class="flex items-center gap-3">
                        <button id="prevBtn" onclick="previousPage()" disabled
                            class="p-2 text-gray-400 hover:text-gray-600 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        
                        <span class="text-sm font-medium">
                            Page <span id="currentPage">1</span> sur <span id="totalPages">-</span>
                        </span>
                        
                        <button id="nextBtn" onclick="nextPage()" disabled
                            class="p-2 text-gray-400 hover:text-gray-600 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Configuration PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        let pdfDoc = null;
        let currentPageNum = 1;
        let totalPages = 0;
        let currentScale = 1.2;
        let isFullscreen = false;
        
        // Éléments DOM
        const loading = document.getElementById('loading');
        const error = document.getElementById('error');
        const viewer = document.getElementById('pdfViewer');
        const canvas = document.getElementById('pdfCanvas');
        const ctx = canvas.getContext('2d');
        const currentPageSpan = document.getElementById('currentPage');
        const totalPagesSpan = document.getElementById('totalPages');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const zoomLevel = document.getElementById('zoomLevel');

        // Chargement du PDF
        async function loadPDF() {
            try {
                const fileUrl = '{{ $fileUrl }}';
                console.log('Chargement du PDF:', fileUrl);
                
                pdfDoc = await pdfjsLib.getDocument({
                    url: fileUrl,
                    cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                    cMapPacked: true
                }).promise;
                
                totalPages = pdfDoc.numPages;
                totalPagesSpan.textContent = totalPages;
                
                loading.style.display = 'none';
                viewer.style.display = 'block';
                
                await renderPage(1);
                updateControls();
                updateZoomLevel();
                
            } catch (err) {
                console.error('Erreur chargement PDF:', err);
                showError('Impossible de charger le document PDF. Le fichier pourrait être corrompu ou inaccessible.');
            }
        }

        // Rendu d'une page
        async function renderPage(pageNum) {
            try {
                if (!pdfDoc || pageNum < 1 || pageNum > totalPages) return;
                
                const page = await pdfDoc.getPage(pageNum);
                const viewport = page.getViewport({ scale: currentScale });
                
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                await page.render(renderContext).promise;
                
                currentPageNum = pageNum;
                currentPageSpan.textContent = currentPageNum;
                updateControls();
                
            } catch (err) {
                console.error('Erreur rendu page:', err);
                showError('Erreur lors de l\'affichage de la page.');
            }
        }

        // Navigation
        function nextPage() {
            if (currentPageNum < totalPages) {
                renderPage(currentPageNum + 1);
            }
        }

        function previousPage() {
            if (currentPageNum > 1) {
                renderPage(currentPageNum - 1);
            }
        }

        // Zoom
        function zoomIn() {
            currentScale = Math.min(currentScale * 1.25, 4.0);
            renderPage(currentPageNum);
            updateZoomLevel();
        }

        function zoomOut() {
            currentScale = Math.max(currentScale / 1.25, 0.25);
            renderPage(currentPageNum);
            updateZoomLevel();
        }

        function updateZoomLevel() {
            zoomLevel.textContent = Math.round(currentScale * 100) + '%';
        }

        // Plein écran
        function toggleFullscreen() {
            const container = document.getElementById('pdfContainer');
            
            if (!isFullscreen) {
                if (container.requestFullscreen) {
                    container.requestFullscreen();
                } else if (container.webkitRequestFullscreen) {
                    container.webkitRequestFullscreen();
                } else if (container.msRequestFullscreen) {
                    container.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }

        // Écouteurs plein écran
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);

        function handleFullscreenChange() {
            isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement);
        }

        // Mise à jour des contrôles
        function updateControls() {
            prevBtn.disabled = currentPageNum <= 1;
            nextBtn.disabled = currentPageNum >= totalPages;
            
            prevBtn.className = currentPageNum <= 1 ? 
                'p-2 text-gray-300 cursor-not-allowed' : 
                'p-2 text-gray-600 hover:text-gray-800 cursor-pointer';
                
            nextBtn.className = currentPageNum >= totalPages ? 
                'p-2 text-gray-300 cursor-not-allowed' : 
                'p-2 text-gray-600 hover:text-gray-800 cursor-pointer';
        }

        // Affichage d'erreur
        function showError(message) {
            loading.style.display = 'none';
            viewer.style.display = 'none';
            error.style.display = 'flex';
            document.getElementById('errorMessage').textContent = message;
        }

        // Navigation clavier
        document.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'ArrowLeft':
                    previousPage();
                    break;
                case 'ArrowRight':
                    nextPage();
                    break;
                case 'Escape':
                    if (isFullscreen) {
                        toggleFullscreen();
                    }
                    break;
            }
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadPDF();
        });
    </script>
    @endpush
</x-app-layout>