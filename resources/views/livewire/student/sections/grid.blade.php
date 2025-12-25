@php
    use Illuminate\Support\Str;

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
        $isExternal = Str::startsWith($path, ['http://','https://']);

        $ext = '';
        if ($isExternal) {
            $urlPath = parse_url($path, PHP_URL_PATH) ?? '';
            $ext = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
        } else {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        }

        if ($ext === '') $ext = $isExternal ? 'link' : 'doc';

        $isPdf = $ext === 'pdf';
        $isPpt = in_array($ext, ['ppt','pptx'], true);
        $isDoc = in_array($ext, ['doc','docx'], true);
        $isXls = in_array($ext, ['xls','xlsx','csv'], true);

        $badge = match(true) {
            $isExternal => 'LIEN',
            $isPdf      => 'PDF',
            $isPpt      => 'PPT',
            $isDoc      => 'DOC',
            $isXls      => 'XLS',
            default     => strtoupper($ext),
        };

        $badgeClass = match(true) {
            $isExternal => 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-300 dark:ring-indigo-800/40',
            $isPdf      => 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/20 dark:text-red-300 dark:ring-red-800/40',
            $isPpt      => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:ring-orange-800/40',
            $isDoc      => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:ring-sky-800/40',
            $isXls      => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:ring-emerald-800/40',
            default     => 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:ring-blue-800/40',
        };

        $icon = match(true) {
            $isExternal => 'link',
            $isPdf      => 'pdf',
            $isPpt      => 'ppt',
            $isDoc      => 'doc',
            $isXls      => 'xls',
            default     => 'file',
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

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    @foreach($documents as $document)
        @php
            $m = $fileMeta($document);

            $grade = $document->teacher?->profil?->grade;
            $teacherName = $document->uploader?->name;

            $views = (int)($document->view_count ?? 0);
            $sizeLabel = $document->formatted_size
                ?? ($document->file_size ? number_format($document->file_size / 1024 / 1024, 1).' MB' : '-');

            $openLabel = $m['isExternal'] ? 'Ouvrir' : 'Consulter';
            $openIcon = $m['isExternal']
                ? '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 3h7v7"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M10 14 21 3"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 14v7H3V3h7"/></svg>'
                : '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        @endphp

        <article class="group rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950
                        shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition overflow-hidden">

            {{-- Header --}}
            <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-800/70">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 h-12 w-12 rounded-2xl bg-gray-100 dark:bg-gray-900
                                flex items-center justify-center text-gray-700 dark:text-gray-200">
                        {!! $iconSvg($m['icon']) !!}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ring-1 {{ $m['badgeClass'] }}">
                                {{ $m['badge'] }}
                            </span>

                            <span class="text-[11px] text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $document->created_at?->format('d/m/Y') }}
                            </span>
                        </div>

                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white leading-5 line-clamp-2">
                            {{ $document->title }}
                        </h3>

                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex flex-wrap items-center gap-2">
                            @if($m['isExternal'] && $m['provider'])
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px]
                                             bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300">
                                    {{ $m['provider'] }}
                                </span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                            @endif

                            @if($grade)
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $grade }}</span>
                            @endif

                            @if($teacherName)
                                <span class="text-gray-400 dark:text-gray-500">•</span>
                                <span class="truncate">{{ $teacherName }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-4 sm:p-5">
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $m['isExternal'] ? '-' : $sizeLabel }}
                        </span>

                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ $views }} vues
                        </span>
                    </div>

                    <span class="hidden sm:inline-flex items-center gap-1.5 font-medium">
                        @if($m['isExternal'])
                            <span class="text-indigo-600 dark:text-indigo-400">Ouverture externe</span>
                        @elseif($m['ext'] === 'pdf')
                            <span class="text-emerald-600 dark:text-emerald-400">Lecture directe</span>
                        @elseif(in_array($m['ext'], ['ppt','pptx'], true))
                            <span class="text-orange-600 dark:text-orange-400">Téléchargement conseillé</span>
                        @else
                            <span class="text-gray-500 dark:text-gray-400">Document</span>
                        @endif
                    </span>
                </div>

{{-- Actions (compact, pas plein largeur) --}}
<div class="mt-4 flex items-center justify-end gap-2">
    {{-- Ouvrir / Consulter --}}
    <a href="{{ $m['isExternal'] ? ($document->file_path) : route('document.serve', $document) }}"
       target="_blank" rel="noopener noreferrer"
       class="inline-flex h-9 items-center justify-center gap-2
              rounded-xl px-3 text-xs font-semibold
              text-white bg-blue-600 hover:bg-blue-700
              focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:ring-offset-2 dark:focus:ring-offset-gray-950
              transition">
        {!! $openIcon !!}
        <span class="hidden sm:inline">{{ $openLabel }}</span>
    </a>

    {{-- Télécharger (icône seulement) --}}
    @if(!$m['isExternal'])
        <a href="{{ route('document.download', $document) }}"
           class="inline-flex h-9 w-9 items-center justify-center
                  rounded-xl border border-gray-200 bg-gray-50 text-gray-700
                  hover:bg-gray-100
                  dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800
                  focus:outline-none focus:ring-2 focus:ring-gray-400/70 focus:ring-offset-2 dark:focus:ring-offset-gray-950
                  transition"
           aria-label="Télécharger"
           title="Télécharger">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </a>
    @endif
</div>




                <div class="mt-3 text-center sm:hidden">
                    @if($m['isExternal'])
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Lien externe</span>
                    @elseif($m['ext'] === 'pdf')
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">✓ Lecture directe</span>
                    @elseif(in_array($m['ext'], ['ppt','pptx'], true))
                        <span class="text-xs text-orange-600 dark:text-orange-400 font-medium">Téléchargement recommandé</span>
                    @else
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Document</span>
                    @endif
                </div>
            </div>
        </article>
    @endforeach
</div>

@once
<style>
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
</style>
@endonce
