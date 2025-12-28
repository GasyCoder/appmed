{{-- resources/views/livewire/documents/viewer.blade.php --}}
<x-app-layout>
    @php
        $teacherInfo = $teacherInfo ?? null;

        $views = (int) ($document->view_count ?? 0);
        $downloads = (int) ($document->download_count ?? 0);

        $ext = $ext ?? 'pdf';
        $extUpper = strtoupper($ext ?: 'DOC');

        $isPdf = (bool) ($isPdf ?? true);
        $fileUrl = $fileUrl ?? null;
        $pdfFullUrl = $pdfFullUrl ?? null;
        $downloadRoute = $downloadRoute ?? route('document.download', $document);

        $sizeLabel = (string) ($document->file_size_formatted ?? '');

        // ✅ Retour intelligent
        $backUrl = url()->previous();
    @endphp

    <div class="min-h-[100dvh] flex flex-col">

        {{-- TOPBAR sticky --}}
        <div class="sticky top-0 z-30">
            <div class="bg-white/90 dark:bg-gray-900/85 backdrop-blur border-b border-gray-200 dark:border-gray-800">
                <div class="mx-auto w-full max-w-[88rem] px-3 sm:px-6 lg:px-8 py-3">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                        {{-- LEFT --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start gap-2 sm:gap-3">
                                {{-- ✅ Back button (history + fallback) --}}
                                <a href="{{ $backUrl }}"
                                   onclick="if (window.history.length > 1) { history.back(); return false; }"
                                   class="inline-flex shrink-0 items-center justify-center rounded-xl
                                          border border-gray-200 dark:border-gray-700
                                          bg-white dark:bg-gray-800
                                          h-10 w-10 sm:w-auto sm:px-3
                                          text-gray-700 dark:text-gray-200
                                          hover:bg-gray-50 dark:hover:bg-gray-700/60 transition"
                                   title="Retour"
                                   aria-label="Retour">
                                    <svg class="h-5 w-5 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    <span class="hidden sm:inline text-sm font-semibold">Retour</span>
                                </a>

                                <div class="min-w-0 flex-1">
                                    <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $document->title }}
                                    </h1>

                                    @if(!empty($teacherInfo))
                                        <div class="mt-0.5 text-xs sm:text-sm text-gray-600 dark:text-gray-400 truncate">
                                            @if(!empty($teacherInfo['grade']))
                                                <span class="font-semibold text-gray-700 dark:text-gray-300">
                                                    {{ $teacherInfo['grade'] }}
                                                </span>
                                                <span class="mx-1">·</span>
                                            @endif
                                            <span>{{ $teacherInfo['name'] ?? '' }}</span>
                                        </div>
                                    @endif

                                    {{-- Chips --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] sm:text-xs">
                                        <span class="inline-flex items-center gap-1 rounded-full
                                                     border border-gray-200 dark:border-gray-700
                                                     bg-gray-50 dark:bg-gray-800 px-2.5 py-1
                                                     text-gray-700 dark:text-gray-200">
                                            <span class="font-mono font-semibold">{{ $extUpper }}</span>
                                        </span>

                                        @if($sizeLabel !== '')
                                            <span class="inline-flex items-center gap-1 rounded-full
                                                         border border-gray-200 dark:border-gray-700
                                                         bg-gray-50 dark:bg-gray-800 px-2.5 py-1
                                                         text-gray-700 dark:text-gray-200">
                                                <span class="font-semibold">{{ $sizeLabel }}</span>
                                            </span>
                                        @endif

                                        <span class="inline-flex items-center gap-1 rounded-full
                                                     border border-gray-200 dark:border-gray-700
                                                     bg-gray-50 dark:bg-gray-800 px-2.5 py-1
                                                     text-gray-700 dark:text-gray-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 1 1-6 0a3 3 0 0 1 6 0Z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $views }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">Vues</span>
                                        </span>

                                        <span class="inline-flex items-center gap-1 rounded-full
                                                     border border-gray-200 dark:border-gray-700
                                                     bg-gray-50 dark:bg-gray-800 px-2.5 py-1
                                                     text-gray-700 dark:text-gray-200">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $downloads }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">Téléch.</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT ACTIONS --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2">
                                @if($isPdf && !empty($pdfFullUrl))
                                    <a href="{{ $pdfFullUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="col-span-1 inline-flex items-center justify-center gap-2
                                              h-10 px-3 rounded-xl text-sm font-semibold
                                              bg-blue-600 text-white hover:bg-blue-700 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 3h6m0 0v6m0-6L14 10M9 21H3m0 0v-6m0 6l7-7"/>
                                        </svg>
                                        <span class="hidden sm:inline">Plein écran</span>
                                        <span class="sm:hidden">Écran</span>
                                    </a>
                                @endif

                                <a href="{{ $downloadRoute }}"
                                   class="col-span-1 inline-flex items-center justify-center gap-2
                                          h-10 px-3 rounded-xl text-sm font-semibold
                                          bg-emerald-600 text-white hover:bg-emerald-700 transition"
                                   title="Télécharger">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Télécharger</span>
                                    <span class="sm:hidden">Téléch.</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="flex-1 min-h-0">
            <div class="mx-auto w-full max-w-[88rem] px-3 sm:px-6 lg:px-8 py-4 h-full">

                @if($isPdf)
                    @if(!empty($fileUrl))
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <iframe
                                src="{{ $fileUrl }}"
                                class="w-full"
                                style="height: calc(100vh - 190px);"
                                frameborder="0"
                                allowfullscreen
                                referrerpolicy="no-referrer"
                            ></iframe>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-sm text-gray-700 dark:text-gray-300">
                            Impossible d’afficher le document. Veuillez télécharger le fichier.
                        </div>
                    @endif
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="mb-4">
                                <svg class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                Fichier {{ $extUpper }}
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Ce type de fichier doit être téléchargé pour être consulté.
                            </p>

                            <a href="{{ $downloadRoute }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-base font-semibold
                                      bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Télécharger le fichier
                            </a>

                            @if($sizeLabel !== '')
                                <p class="mt-4 text-xs text-gray-500 dark:text-gray-500">
                                    Taille : {{ $sizeLabel }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
