@php
    $helpers = require resource_path('views/livewire/student/sections/helpers.php');
    $fileMeta = $helpers['fileMeta'];
    $iconSvg  = $helpers['iconSvg'];

    $buildCfg = require resource_path('views/livewire/student/sections/config-show.php');
@endphp

<div class="space-y-4">
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

        <article class="group rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition overflow-hidden {{ $archiveCardTone }}">
            <div class="p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">
                    <div class="shrink-0">
                        <div class="w-16 rounded-2xl bg-gray-100 dark:bg-gray-900 p-3 text-center border border-gray-200/70 dark:border-gray-800/70">
                            <div class="mx-auto text-gray-700 dark:text-gray-200 flex items-center justify-center">
                                {!! $iconSvg($m['icon']) !!}
                            </div>

                            <div class="mt-2 flex flex-wrap gap-2 justify-center">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1 {{ $m['badgeClass'] }}">
                                    {{ $m['badge'] }}
                                </span>

                                @if($isArchived)
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-semibold ring-1 {{ $archivePillClass }}">
                                        ARCHIVÉ
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white leading-6 line-clamp-2">
                            {{ $document->title }}
                        </h3>

                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            @if(($m['isExternal'] ?? false) && !empty($m['provider']))
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300">
                                    {{ $m['provider'] }}
                                </span>
                            @endif

                            <span class="truncate">{{ $teacherLabel }}</span>

                            <span>{{ $document->created_at?->format('d/m/Y') }}</span>
                            <span>{{ $isExternal ? '-' : $sizeLabel }}</span>

                            @if($showViewsCounter)
                                <span>{{ $views }} vues</span>
                            @endif

                            @if($showDownloadCounter)
                                <span>{{ $downloads }} dl</span>
                            @endif
                        </div>
                    </div>

                    <div class="shrink-0 w-full sm:w-auto">
                        <div class="flex gap-2 sm:flex-col sm:items-stretch">
                            @if($canConsult)
                                <a href="{{ $consultUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex h-9 items-center justify-center gap-2 rounded-xl px-3 text-xs font-semibold
                                          bg-gray-900 text-white hover:bg-gray-800
                                          dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                                    {{ $isExternal ? 'Ouvrir' : 'Consulter' }}
                                </a>
                            @endif

                            @if(!empty($downloadUrl))
                                <a href="{{ $downloadUrl }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                          text-gray-700 bg-gray-100 hover:bg-gray-200
                                          dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 transition">
                                    Télécharger
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </article>
    @endforeach
</div>

@once
    <style>
        .line-clamp-2{
            display:-webkit-box;
            -webkit-line-clamp:2;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }
    </style>
@endonce
