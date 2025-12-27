<x-app-layout>
    @php
        $teacherInfo = $teacherInfo ?? null;
        $views = (int) ($document->view_count ?? 0);
        $downloads = (int) ($document->download_count ?? 0);
        $extUpper = strtoupper($ext ?: 'DOC');

        // $fileUrl doit être: route('document.serve', ['document' => $document->id, 'embedded' => 1])
        $fileUrl = $fileUrl ?? null;

        // $onlineViewerUrl pour ppt/pptx (gview)
        $onlineViewerUrl = $onlineViewerUrl ?? null;

        $downloadRoute = $downloadRoute ?? '#';

        // URL “plein écran” PDF (ouvre le fichier direct dans le viewer natif du navigateur)
        $pdfFullUrl = $fileUrl ?: null;
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
        {{-- Header sticky --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <button type="button"
                                    onclick="history.back()"
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

                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Type: {{ $extUpper }} • Vues: {{ $views }} • Téléchargements: {{ $downloads }}
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Plein écran --}}
                        @if($isPdf && !empty($pdfFullUrl))
                            <a href="{{ $pdfFullUrl }}" target="_blank" rel="noopener noreferrer"
                               class="px-3 py-2 text-sm bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                Plein écran
                            </a>
                        @elseif(!$isPdf && !empty($onlineViewerUrl))
                            <a href="{{ $onlineViewerUrl }}" target="_blank" rel="noopener noreferrer"
                               class="px-3 py-2 text-sm bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                Plein écran
                            </a>
                        @endif

                        {{-- Télécharger --}}
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
            </div>
        </div>

        {{-- Body (iframe full height responsive) --}}
        <div class="flex-1 min-h-0">
            <div class="max-w-7xl mx-auto px-4 py-4 h-full">
                @if($isPdf)
                    @if(!empty($fileUrl))
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                            <iframe
                                src="{{ $fileUrl }}"
                                class="w-full h-full"
                                style="height: calc(100vh - 170px);"
                                frameborder="0"
                                allowfullscreen
                                referrerpolicy="no-referrer"
                            ></iframe>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-sm text-gray-700 dark:text-gray-300">
                            URL PDF introuvable. Télécharge le fichier.
                        </div>
                    @endif
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Lecture en ligne (PPT/PPTX)</span>
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
                                style="height: calc(100vh - 210px);"
                                frameborder="0"
                                allowfullscreen
                                referrerpolicy="no-referrer"
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
    </div>
</x-app-layout>
