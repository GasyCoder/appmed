@php
    $helpers = require resource_path('views/livewire/student/sections/helpers.php');
    $fileMeta = $helpers['fileMeta'];
    $iconSvg  = $helpers['iconSvg'];

    $buildCfg = require resource_path('views/livewire/student/sections/config-show.php');
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    @foreach($documents as $document)
        @php
            $cfg = $buildCfg($document, $fileMeta);

            $m = $cfg['m'];
            $isArchived = $cfg['isArchived'];
            $archivePillClass = $cfg['archivePillClass'];
            $archiveCardTone = $cfg['archiveCardTone'];

            $teacherLabel = $cfg['teacherLabel'];
            $grade = $cfg['grade'];
            $teacherName = $cfg['teacherName'];

            $views = $cfg['views'];
            $downloads = $cfg['downloads'];
            $sizeLabel = $cfg['sizeLabel'];

            $isExternal = $cfg['isExternal'];

            $showViewsCounter = $cfg['showViewsCounter'];
            $showDownloadCounter = $cfg['showDownloadCounter'];

            $consultUrl = $cfg['consultUrl'];
            $canConsult = $cfg['canConsult'];
            $downloadUrl = $cfg['downloadUrl'];
        @endphp


        <article class="group rounded-2xl border border-gray-200/70 dark:border-gray-800/70
                bg-white dark:bg-gray-950 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition overflow-hidden
                {{ $archiveCardTone }}">

            <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-800/70">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 h-12 w-12 rounded-2xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center text-gray-700 dark:text-gray-200">
                        {!! $iconSvg($m['icon']) !!}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ring-1 {{ $m['badgeClass'] }}">
                                    {{ $m['badge'] }}
                                </span>

                                @if($isArchived)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ring-1 {{ $archivePillClass }}">
                                        ARCHIVÉ
                                    </span>
                                @endif
                            </div>

                            <span class="text-[11px] text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $document->created_at?->format('d/m/Y') }}
                            </span>
                        </div>

                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white leading-5 line-clamp-2">
                            {{ $document->title }}
                        </h3>

                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex flex-wrap items-center gap-2">
                            @if($m['isExternal'] && $m['provider'])
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300">
                                    {{ $m['provider'] }}
                                </span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                            @endif

                            @if(!empty($grade))
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $grade }}</span>
                            @endif

                            @if(!empty($teacherName))
                                <span class="text-gray-400 dark:text-gray-500">•</span>
                                <span class="truncate">{{ $teacherName }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5">
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        @if(!$isExternal)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $sizeLabel }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ $views }} vues
                        </span>

                        @if($showDownloadCounter)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                </svg>
                                {{ $downloads }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2">
                    {{-- Consulter / Ouvrir --}}
                    @if($canConsult)
                        <a href="{{ $consultUrl }}"
                           target="_blank" rel="noopener noreferrer"
                           wire:click="markViewed({{ $document->id }})"
                           class="inline-flex h-9 items-center justify-center gap-2 rounded-xl px-3 text-xs font-semibold
                                  bg-gray-900 text-white hover:bg-gray-800
                                  dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ $isExternal ? 'Ouvrir' : 'Consulter' }}</span>
                        </a>
                    @endif

                    {{-- Télécharger --}}
                    @if(!empty($downloadUrl))
                        <a href="{{ $downloadUrl }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                  bg-gray-50 ring-1 ring-gray-200 text-gray-800 hover:bg-gray-100
                                  dark:bg-gray-900/30 dark:ring-gray-700 dark:text-gray-100 dark:hover:bg-gray-700/40 transition"
                           title="Télécharger">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </article>
    @endforeach
</div>

@once
<style>.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}</style>
@endonce
