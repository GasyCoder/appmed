<x-app-layout>
    @php
        $teacherInfo = $teacherInfo ?? null;
        $views = (int) ($document->view_count ?? 0);
        $downloads = (int) ($document->download_count ?? 0);
        $extUpper = strtoupper($ext ?: 'DOC');
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <button onclick="history.back()"
                                    class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Retour
                            </button>

                            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                                {{ $document->title }}
                            </h1>
                        </div>

                        @if(!empty($teacherInfo))
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                @if(!empty($teacherInfo['grade'])){{ $teacherInfo['grade'] }} @endif
                                {{ $teacherInfo['name'] ?? '' }}
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if($isPdf)
                            <button onclick="zoomOut()"
                                    class="px-3 py-2 text-sm bg-gray-600 dark:bg-gray-700 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors"
                                    title="Zoom -">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                                </svg>
                            </button>

                            <span id="zoomLevel" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md">120%</span>

                            <button onclick="zoomIn()"
                                    class="px-3 py-2 text-sm bg-gray-600 dark:bg-gray-700 text-white rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors"
                                    title="Zoom +">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </button>
                        @endif

                        {{-- ✅ Téléchargement local => route (compteur ok) --}}
                        <a href="{{ $downloadRoute }}"
                           class="px-3 py-2 text-sm bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition-colors"
                           title="Télécharger">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Type: {{ $extUpper }} • Vues: {{ $views }} • Téléchargements: {{ $downloads }}
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="max-w-7xl mx-auto px-4 py-6">
            @if($isPdf)
                {{-- PDF LOCAL via pdf.js --}}
                <div id="pdfContainer" class="min-h-[70vh]">
                    <div id="loading" class="flex flex-col items-center justify-center py-20">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                        <p class="text-gray-600 dark:text-gray-300">Chargement du document...</p>
                    </div>

                    <div id="error" class="flex flex-col items-center justify-center py-20 hidden">
                        <div class="text-red-500 text-5xl mb-4">⚠️</div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Erreur de chargement</h3>
                        <p id="errorMessage" class="text-gray-600 dark:text-gray-300 mb-4 text-center"></p>
                        <div class="flex gap-3">
                            <button onclick="location.reload()"
                                    class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600">
                                Réessayer
                            </button>
                            <a href="{{ $downloadRoute }}"
                               class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600">
                                Télécharger
                            </a>
                        </div>
                    </div>

                    <div id="pdfViewer" class="hidden">
                        <div class="bg-gray-100 dark:bg-gray-800 flex justify-center p-4">
                            <div class="bg-white dark:bg-gray-900 shadow-lg" id="pdfWrapper">
                                <canvas id="pdfCanvas" class="max-w-full h-auto"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- PPTX LOCAL via Google gview (iframe) --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Lecture en ligne (PPTX)</span>
                        @if(!empty($onlineViewerUrl))
                            <a href="{{ $onlineViewerUrl }}" target="_blank" rel="noopener noreferrer"
                               class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Ouvrir plein écran
                            </a>
                        @endif
                    </div>

                    @if(!empty($onlineViewerUrl))
                        <iframe
                            src="{{ $onlineViewerUrl }}"
                            class="w-full"
                            style="height: 78vh;"
                            frameborder="0"
                            allowfullscreen
                        ></iframe>
                    @else
                        <div class="p-6 text-sm text-gray-700 dark:text-gray-300">
                            Impossible d’afficher ce fichier en lecture web. Télécharge le fichier.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        @if($isPdf)
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
            <script>
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                let pdfDoc = null;
                let currentPageNum = 1;
                let totalPages = 0;
                let currentScale = 1.2;

                const loading = document.getElementById('loading');
                const error = document.getElementById('error');
                const viewer = document.getElementById('pdfViewer');
                const canvas = document.getElementById('pdfCanvas');
                const ctx = canvas.getContext('2d');
                const zoomLevel = document.getElementById('zoomLevel');

                async function loadPDF() {
                    try {
                        const fileUrl = @json($fileUrl);

                        pdfDoc = await pdfjsLib.getDocument({
                            url: fileUrl,
                            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                            cMapPacked: true
                        }).promise;

                        totalPages = pdfDoc.numPages;

                        loading.style.display = 'none';
                        viewer.style.display = 'block';

                        await renderPage(1);
                        updateZoomLevel();
                    } catch (err) {
                        console.error(err);
                        showError('Impossible de charger le PDF.');
                    }
                }

                async function renderPage(pageNum) {
                    try {
                        if (!pdfDoc || pageNum < 1 || pageNum > totalPages) return;

                        const page = await pdfDoc.getPage(pageNum);
                        const viewport = page.getViewport({ scale: currentScale });

                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        await page.render({ canvasContext: ctx, viewport }).promise;

                        currentPageNum = pageNum;
                    } catch (err) {
                        console.error(err);
                        showError('Erreur rendu page.');
                    }
                }

                function zoomIn() {
                    currentScale = Math.min(currentScale * 1.25, 4.0);
                    renderPage(currentPageNum); updateZoomLevel();
                }
                function zoomOut() {
                    currentScale = Math.max(currentScale / 1.25, 0.25);
                    renderPage(currentPageNum); updateZoomLevel();
                }
                function updateZoomLevel() {
                    if (zoomLevel) zoomLevel.textContent = Math.round(currentScale * 100) + '%';
                }

                function showError(message) {
                    loading.style.display = 'none';
                    viewer.style.display = 'none';
                    error.classList.remove('hidden');
                    document.getElementById('errorMessage').textContent = message;
                }

                document.addEventListener('DOMContentLoaded', loadPDF);
            </script>
        @endif
    @endpush
</x-app-layout>
