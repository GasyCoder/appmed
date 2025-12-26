@php
    $getProvider = function(string $url) {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        return match(true) {
            str_contains($host, 'drive.google') => 'Google Drive',
            str_contains($host, 'docs.google')  => 'Google Docs',
            str_contains($host, 'dropbox')      => 'Dropbox',
            str_contains($host, 'onedrive')     => 'OneDrive',
            str_contains($host, 'sharepoint')   => 'SharePoint',
            default => 'Lien externe',
        };
    };

    $fileMeta = function($document) use ($getProvider) {
        $path = (string) ($document->file_path ?? '');
        $isExternal = \Illuminate\Support\Str::startsWith($path, ['http://','https://']);

        $ext = $document->extensionFromPath() ?? '';
        if ($ext === '') $ext = $isExternal ? 'link' : 'doc';

        $badge = match(true) {
            $isExternal => 'LIEN',
            $ext === 'pdf' => 'PDF',
            in_array($ext, ['ppt','pptx'], true) => 'PPT',
            in_array($ext, ['doc','docx'], true) => 'DOC',
            in_array($ext, ['xls','xlsx','csv'], true) => 'XLS',
            default => strtoupper($ext),
        };

        $badgeClass = match(true) {
            $isExternal => 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-300 dark:ring-indigo-800/40',
            $ext === 'pdf' => 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/20 dark:text-red-300 dark:ring-red-800/40',
            in_array($ext, ['ppt','pptx'], true) => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:ring-orange-800/40',
            in_array($ext, ['doc','docx'], true) => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:ring-sky-800/40',
            in_array($ext, ['xls','xlsx','csv'], true) => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:ring-emerald-800/40',
            default => 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:ring-blue-800/40',
        };

        $icon = match(true) {
            $isExternal => 'link',
            $ext === 'pdf' => 'pdf',
            in_array($ext, ['ppt','pptx'], true) => 'ppt',
            in_array($ext, ['doc','docx'], true) => 'doc',
            in_array($ext, ['xls','xlsx','csv'], true) => 'xls',
            default => 'file',
        };

        $provider = $isExternal ? $getProvider($path) : null;

        return compact('isExternal','ext','badge','badgeClass','icon','provider');
    };

    $iconSvg = function(string $name) {
        return match($name) {
            'pdf' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 3v4h4"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 13h8M8 17h6"/></svg>',
            'ppt' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 8h8M8 12h6M8 16h8"/></svg>',
            'doc' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 3v4h4"/></svg>',
            'xls' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 12h8M8 16h8M10 10v8"/></svg>',
            'link' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"/></svg>',
            default => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 3v4h4"/></svg>',
        };
    };
@endphp

<div class="space-y-4">
    @foreach($documents as $document)
        @php
            $m = $fileMeta($document);
            $isArchived = (bool) ($document->is_archive ?? false);
            $archivePillClass = 'bg-amber-50 text-amber-700 ring-amber-200
                                dark:bg-amber-900/20 dark:text-amber-200 dark:ring-amber-900/40';
            $archiveCardTone = $isArchived
                ? 'ring-2 ring-amber-400/30 dark:ring-amber-500/20'
                : '';
            $grade = $document->teacher?->profil?->grade;
            $teacherName = $document->uploader?->name;

            $views = (int)($document->view_count ?? 0);
            $downloads = (int)($document->download_count ?? 0);

            $sizeLabel = $document->formatted_size ?? ($document->file_size ? number_format($document->file_size / 1024 / 1024, 1).' MB' : '-');

            $canConsult =
                ($m['isExternal'] && !$document->isDirectDownloadType()) ||
                (!$m['isExternal'] && $document->isViewerLocalType());

            $consultUrl = $m['isExternal']
                ? route('document.openExternal', $document)
                : route('document.viewer', $document);

            if ($document->isDirectDownloadType()) {
                $downloadUrl = $m['isExternal']
                    ? route('document.downloadExternal', $document)
                    : route('document.download', $document);
            } else {
                $downloadUrl = $m['isExternal'] ? null : route('document.download', $document);
            }
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
                            @if($m['isExternal'] && $m['provider'])
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300">
                                    {{ $m['provider'] }}
                                </span>
                            @endif

                            <span class="truncate">
                                {{ trim(($grade ?? '').' '.($teacherName ?? '')) ?: 'Enseignant non défini' }}
                            </span>

                            <span>{{ $document->created_at?->format('d/m/Y') }}</span>
                            <span>{{ $m['isExternal'] ? '-' : $sizeLabel }}</span>
                            <span>{{ $views }} vues</span>
                            <span>{{ $downloads }} dl</span>
                        </div>
                    </div>

                    <div class="shrink-0 w-full sm:w-auto">
                        <div class="flex gap-2 sm:flex-col sm:items-stretch">
                            @if($canConsult)
                                <a href="{{ $consultUrl }}"
                                   @if($m['isExternal']) target="_blank" rel="noopener noreferrer" @endif
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                                    {{ $m['isExternal'] ? 'Ouvrir' : 'Consulter' }}
                                </a>
                            @endif

                            @if(!empty($downloadUrl))
                                <a href="{{ $downloadUrl }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 transition">
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
<style>.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}</style>
@endonce
