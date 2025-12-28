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

    <div class="min-h-screen flex flex-col">
        {{-- Header sticky (responsive + UI améliorée) --}}
        <div class="sticky top-0 z-30">
            {{-- Fond + blur pour lisibilité --}}
            <div class="bg-white/90 dark:bg-gray-900/80 backdrop-blur border-b border-gray-200 dark:border-gray-800">
                <div class="mx-auto w-full max-w-[88rem] px-3 sm:px-6 lg:px-8 py-3">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                        {{-- LEFT: back + title + meta --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start gap-2 sm:gap-3">
                                {{-- Back: compact mobile --}}
                                <a href="{{ route('document.teacher') }}"
                                        onclick="history.back()"
                                        class="inline-flex shrink-0 items-center justify-center rounded-lg
                                            border border-gray-200 dark:border-gray-700
                                            bg-gray-50 dark:bg-gray-800
                                            h-9 w-9 sm:w-auto sm:px-3
                                            text-gray-700 dark:text-gray-200
                                            hover:bg-gray-100 dark:hover:bg-gray-700
                                            transition"
                                        title="Retour">
                                    <svg class="h-5 w-5 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    <span class="hidden sm:inline text-sm font-semibold">Retour</span>
                                </a>

                                <div class="min-w-0 flex-1">
                                    {{-- Title --}}
                                    <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $document->title }}
                                    </h1>

                                    {{-- Teacher --}}
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

                                    {{-- Meta chips (wrap mobile) --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] sm:text-xs">
                                        <span class="inline-flex items-center gap-1 rounded-full
                                                    border border-gray-200 dark:border-gray-700
                                                    bg-gray-50 dark:bg-gray-800 px-2 py-1
                                                    text-gray-700 dark:text-gray-200">
                                            <span class="font-mono font-semibold">{{ $extUpper }}</span>
                                        </span>

                                        <span class="inline-flex items-center gap-1 rounded-full
                                                    border border-gray-200 dark:border-gray-700
                                                    bg-gray-50 dark:bg-gray-800 px-2 py-1
                                                    text-gray-700 dark:text-gray-200">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="font-semibold">{{ $views }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">Vues</span>
                                        </span>

                                        <span class="inline-flex items-center gap-1 rounded-full
                                                    border border-gray-200 dark:border-gray-700
                                                    bg-gray-50 dark:bg-gray-800 px-2 py-1
                                                    text-gray-700 dark:text-gray-200">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                            <span class="font-semibold">{{ $downloads }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">Téléch.</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: actions (responsive grid on mobile) --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2">
                                {{-- Plein écran --}}
                                @if($isPdf && !empty($pdfFullUrl))
                                    <a href="{{ $pdfFullUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="col-span-1 inline-flex items-center justify-center gap-2
                                            h-10 px-3 rounded-lg text-sm font-bold
                                            bg-blue-600 dark:bg-blue-500 text-white
                                            hover:bg-blue-700 dark:hover:bg-blue-600 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6m0 0v6m0-6L14 10M9 21H3m0 0v-6m0 6l7-7M3 9V3m0 0h6m-6 0l7 7m5 5l7 7m0 0v-6m0 6h-6" />
                                            </svg>
                                        <span class="hidden sm:inline">Plein écran</span>
                                        <span class="sm:hidden">Plein écran</span>
                                    </a>
                                @elseif(!$isPdf && !empty($onlineViewerUrl))
                                    <a href="{{ $onlineViewerUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="col-span-1 inline-flex items-center justify-center gap-2
                                            h-10 px-3 rounded-lg text-sm font-bold
                                            bg-blue-600 dark:bg-blue-500 text-white
                                            hover:bg-blue-700 dark:hover:bg-blue-600 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h6m0 0v6m0-6L14 10M9 21H3m0 0v-6m0 6l7-7M3 9V3m0 0h6m-6 0l7 7m5 5l7 7m0 0v-6m0 6h-6" />
                                            </svg>
                                        <span class="hidden sm:inline">Plein écran</span>
                                        <span class="sm:hidden">Écran</span>
                                    </a>
                                @else
                                    {{-- Placeholder pour garder la grille alignée sur mobile (optionnel) --}}
                                    <div class="col-span-1 hidden sm:block"></div>
                                @endif

                                {{-- Télécharger --}}
                                <a href="{{ $downloadRoute }}"
                                class="col-span-1 inline-flex items-center justify-center gap-2
                                        h-10 px-3 rounded-lg text-sm font-bold
                                        bg-emerald-600 dark:bg-emerald-500 text-white
                                        hover:bg-emerald-700 dark:hover:bg-emerald-600 transition"
                                title="Télécharger">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Télécharger</span>
                                    <span class="sm:hidden">Télécharger</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        {{-- Body (iframe full height responsive) --}}
        <div class="flex-1 min-h-0">
            <div class="max-w-10xl mx-auto px-4 py-4 h-full">
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
