<div
    class="w-full px-4 sm:px-6 lg:px-8 py-5 sm:py-6 space-y-5"
    x-data="{
        view: (localStorage.getItem('teacherDocsView') || 'grid'),
        setView(v){ this.view = v; localStorage.setItem('teacherDocsView', v) }
    }"
>
    @php
        // ✅ Helpers (Blade-safe)
        $isHttp = fn ($u) => \Illuminate\Support\Str::startsWith((string)$u, ['http://', 'https://']);

        $googleMeta = function (string $url): array {
            $u = (string) $url;

            // Drive file (uc download)
            if (preg_match('~drive\.google\.com/uc\?export=download&id=([^&]+)~', $u, $m)) {
                return ['provider' => 'drive', 'id' => $m[1]];
            }
            // Drive file (file/d/ID)
            if (preg_match('~drive\.google\.com/file/d/([^/]+)~', $u, $m)) {
                return ['provider' => 'drive', 'id' => $m[1]];
            }

            // Google Docs
            if (preg_match('~docs\.google\.com/document/d/([^/]+)~', $u, $m)) {
                return ['provider' => 'gdoc', 'id' => $m[1]];
            }

            // Google Slides
            if (preg_match('~docs\.google\.com/presentation/d/([^/]+)~', $u, $m)) {
                return ['provider' => 'gslides', 'id' => $m[1]];
            }

            // Google Sheets
            if (preg_match('~docs\.google\.com/spreadsheets/d/([^/]+)~', $u, $m)) {
                return ['provider' => 'gsheets', 'id' => $m[1]];
            }

            return ['provider' => 'external', 'id' => null];
        };

        $toPreviewUrl = function (string $url) use ($googleMeta): string {
            $meta = $googleMeta($url);

            return match ($meta['provider']) {
                'drive'   => $meta['id'] ? "https://drive.google.com/file/d/{$meta['id']}/preview" : $url,
                'gdoc'    => $meta['id'] ? "https://docs.google.com/document/d/{$meta['id']}/preview" : $url,
                'gslides' => $meta['id'] ? "https://docs.google.com/presentation/d/{$meta['id']}/preview" : $url,
                'gsheets' => $meta['id'] ? "https://docs.google.com/spreadsheets/d/{$meta['id']}/preview" : $url,
                default   => $url,
            };
        };

        $toDownloadUrl = function (string $url) use ($googleMeta): ?string {
            $meta = $googleMeta($url);

            return match ($meta['provider']) {
                'drive'   => $meta['id'] ? "https://drive.google.com/uc?export=download&id={$meta['id']}" : null,
                'gdoc'    => $meta['id'] ? "https://docs.google.com/document/d/{$meta['id']}/export?format=pdf" : null,
                'gslides' => $meta['id'] ? "https://docs.google.com/presentation/d/{$meta['id']}/export/pdf" : null,
                'gsheets' => $meta['id'] ? "https://docs.google.com/spreadsheets/d/{$meta['id']}/export?format=pdf" : null,
                default   => null,
            };
        };

        // ✅ Fonction pour déterminer le type de fichier (pour l'icône)
        $fileKindFromExt = function (?string $ext): string {
            $e = strtolower((string)$ext);
            
            return match (true) {
                in_array($e, ['pdf'], true) => 'pdf',
                in_array($e, ['doc','docx','dot','dotx'], true) => 'word',
                in_array($e, ['ppt','pptx'], true) => 'ppt',
                in_array($e, ['xls','xlsx'], true) => 'xls',
                in_array($e, ['jpg','jpeg','png','gif','webp'], true) => 'image',
                default => 'file',
            };
        };
    @endphp

    {{-- Header --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-hidden">
        <div class="h-1 bg-indigo-600"></div>

        <div class="p-4 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        Mes documents
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Gérer et partager vos documents pédagogiques
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    {{-- View switch (Grid/List) --}}
                    <div class="inline-flex w-full sm:w-auto rounded-xl border border-gray-200 bg-white p-1 dark:border-gray-700 dark:bg-gray-800">
                        <button type="button"
                                @click="setView('grid')"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition"
                                :class="view === 'grid'
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/40'">
                            {{-- Squares2X2Icon (outline) --}}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 3h7v7H3V3Zm11 0h7v7h-7V3ZM3 14h7v7H3v-7Zm11 0h7v7h-7v-7Z"/>
                            </svg>
                            Grille
                        </button>

                        <button type="button"
                                @click="setView('list')"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition"
                                :class="view === 'list'
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/40'">
                            {{-- Bars3Icon (outline) --}}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Liste
                        </button>
                    </div>

                    <a href="{{ route('document.upload') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                              hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2
                              dark:focus-visible:ring-offset-gray-900">
                        {{-- PlusIcon (outline) --}}
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                        </svg>
                        Nouveau document
                    </a>
                </div>
            </div>

            {{-- Stats compactes --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700
                             dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200">
                    Total <span class="text-gray-900 dark:text-white">{{ $stats['total'] }}</span>
                </span>

                <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800
                             dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-200">
                    Partagés <span class="text-emerald-900 dark:text-emerald-100">{{ $stats['shared'] }}</span>
                </span>

                <span class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700
                             dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200">
                    Brouillons <span class="text-gray-900 dark:text-white">{{ $stats['notShared'] }}</span>
                </span>

                <span class="inline-flex items-center gap-2 rounded-xl border border-purple-200 bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-800
                             dark:border-purple-900/40 dark:bg-purple-900/20 dark:text-purple-emerald-200">
                    Vues <span class="text-purple-900 dark:text-purple-100">{{ $stats['views'] }}</span>
                </span>

                <span class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-800
                             dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-200">
                    7 jours <span class="text-blue-900 dark:text-blue-100">{{ $stats['recent'] }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Search + Filters --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 p-4 sm:p-5">
        <div class="flex flex-col gap-3">
            <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
                <div class="flex-1">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            {{-- MagnifyingGlassIcon --}}
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </span>

                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Rechercher un document…"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-10 py-2.5 text-sm
                                      text-gray-900 dark:text-white placeholder-gray-400
                                      focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">

                        @if($search)
                            <button wire:click="$set('search','')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-800 dark:hover:text-gray-100">
                                {{-- XMarkIcon --}}
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Sort buttons --}}
                <div class="flex gap-2 overflow-x-auto pb-1 lg:pb-0">
                    @php
                        $sortBtnBase = "shrink-0 inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-semibold transition";
                        $sortOn = "border-indigo-200 bg-indigo-50 text-indigo-800 dark:border-indigo-900/40 dark:bg-indigo-900/20 dark:text-indigo-200";
                        $sortOff= "border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/40";
                    @endphp

                    <button wire:click="sortBy('created_at')" class="{{ $sortBtnBase }} {{ $sortField === 'created_at' ? $sortOn : $sortOff }}">Récents</button>
                    <button wire:click="sortBy('title')" class="{{ $sortBtnBase }} {{ $sortField === 'title' ? $sortOn : $sortOff }}">Titre</button>
                    <button wire:click="sortBy('view_count')" class="{{ $sortBtnBase }} {{ $sortField === 'view_count' ? $sortOn : $sortOff }}">Vues</button>
                    <button wire:click="sortBy('is_actif')" class="{{ $sortBtnBase }} {{ $sortField === 'is_actif' ? $sortOn : $sortOff }}">Statut</button>
                </div>
            </div>

            {{-- Filters --}}
            <details class="md:hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20">
                <summary class="cursor-pointer select-none px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white flex items-center justify-between">
                    Filtres
                    {{-- ChevronDownIcon --}}
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>

                <div class="p-4 pt-0 grid grid-cols-1 gap-3">
                    <select wire:model.live="filterNiveau" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tous les niveaux</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterParcour" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tous les parcours</option>
                        @foreach($parcours as $parcour)
                            <option value="{{ $parcour->id }}">{{ $parcour->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterSemestre" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tous les semestres</option>
                        @foreach($semestres as $semestre)
                            <option value="{{ $semestre->id }}">{{ $semestre->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterStatus" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option value="1">Partagé</option>
                        <option value="0">Non partagé</option>
                    </select>
                </div>
            </details>

            <div class="hidden md:grid grid-cols-2 lg:grid-cols-4 gap-3">
                <select wire:model.live="filterNiveau" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les niveaux</option>
                    @foreach($niveaux as $niveau)
                        <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterParcour" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les parcours</option>
                    @foreach($parcours as $parcour)
                        <option value="{{ $parcour->id }}">{{ $parcour->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterSemestre" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les semestres</option>
                    @foreach($semestres as $semestre)
                        <option value="{{ $semestre->id }}">{{ $semestre->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterStatus" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="1">Partagé</option>
                    <option value="0">Non partagé</option>
                </select>
            </div>

            @if($search || $filterNiveau || $filterParcour || $filterSemestre || $filterStatus !== '')
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Filtres :</span>

                    <button wire:click="$set('search',''); $set('filterNiveau',''); $set('filterParcour',''); $set('filterSemestre',''); $set('filterStatus','');"
                            class="ml-auto text-xs font-semibold text-gray-600 hover:text-gray-900 underline underline-offset-4
                                   dark:text-gray-400 dark:hover:text-white">
                        Réinitialiser
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- LISTE / GRID --}}
    <div
        wire:loading.class="opacity-60 pointer-events-none"
        :class="view === 'grid'
            ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4'
            : 'grid grid-cols-1 gap-3'"
    >
        @forelse($documents as $document)
            @php
                $filePath  = (string) ($document->file_path ?? '');
                $sourceUrl = (string) data_get($document, 'source_url', '');

                $rawUrl = $sourceUrl ?: $filePath;
                $isExternal = $isHttp($rawUrl);

                // ✅ Extension ACTUELLE (ce qui est stocké)
                $currentExt = strtolower((string) pathinfo($filePath, PATHINFO_EXTENSION));
                
                // ✅ Extension ORIGINALE (avant conversion éventuelle)
                $originalExt = strtolower((string) ($document->original_extension ?? ''));
                
                // ✅ Info de conversion
                $convertedFrom = $document->converted_from ? strtolower((string) $document->converted_from) : null;

                // ✅ URL d'ouverture
                $openUrl = $isExternal
                    ? $toPreviewUrl($rawUrl)
                    : route('document.serve', $document);

                // ✅ URL de téléchargement
                $downloadUrl = $isExternal
                    ? $toDownloadUrl($rawUrl)
                    : route('document.download', $document);

                // ✅ Type pour l'icône (basé sur l'extension ACTUELLE)
                $kind = $fileKindFromExt($currentExt);
                
                $isOn = (bool) $document->is_actif;

                $meta = $isExternal ? $googleMeta($rawUrl) : ['provider' => 'local', 'id' => null];

                $providerLabel = match ($meta['provider']) {
                    'drive'   => 'Google Drive',
                    'gdoc'    => 'Google Docs',
                    'gslides' => 'Google Slides',
                    'gsheets' => 'Google Sheets',
                    'local'   => 'Fichier local',
                    default   => 'Lien externe',
                };

                $badgeClass = match ($meta['provider']) {
                    'drive','gdoc','gslides','gsheets' => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-200',
                    'local' => 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200',
                    default => 'border-gray-200 bg-white text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200',
                };

                // ✅ Badge de type : montrer ACTUEL + origine si converti
                if ($convertedFrom && $convertedFrom !== $currentExt) {
                    $typePill = strtoupper($currentExt) . ' ← ' . strtoupper($convertedFrom);
                } else if ($isExternal) {
                    $typePill = 'LIEN';
                } else {
                    $typePill = strtoupper($currentExt ?: 'DOC');
                }
            @endphp

            <article wire:key="doc-card-{{ $document->id }}"
                     class="rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition
                            dark:border-gray-700 dark:bg-gray-800 overflow-hidden"
                     :class="view === 'list' ? 'md:flex md:items-stretch' : ''"
            >
                <div class="p-4 sm:p-5 w-full" :class="view === 'list' ? 'md:flex md:items-center md:gap-4' : ''">

                    {{-- ICON block --}}
                    <div class="h-14 w-14 rounded-2xl border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0
                                dark:border-gray-700 dark:bg-gray-900/30">
                        @if($isExternal)
                            {{-- LinkIcon - Plus fluide --}}
                            <svg class="h-7 w-7 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                            </svg>
                        @else
                            @if($kind === 'pdf')
                                {{-- FileTextIcon (PDF) - Épuré --}}
                                <svg class="h-7 w-7 text-red-700 dark:text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <line x1="10" y1="9" x2="8" y2="9" />
                                </svg>
                            @elseif($kind === 'word')
                                {{-- Word Icon --}}
                                <svg class="h-7 w-7 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <path d="M12 18v-6" />
                                    <path d="M8 18v-1" />
                                    <path d="M16 18v-3" />
                                </svg>
                            @elseif($kind === 'ppt')
                                {{-- Presentation/MonitorIcon (PPT) - Moderne --}}
                                <svg class="h-7 w-7 text-orange-700 dark:text-orange-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" />
                                    <path d="M8 21h8" />
                                    <path d="M12 17v4" />
                                    <path d="m9 8 3 3 3-3" />
                                </svg>
                            @elseif($kind === 'xls')
                                {{-- TableIcon (Excel) - Minimaliste --}}
                                <svg class="h-7 w-7 text-emerald-700 dark:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z" />
                                    <line x1="2" y1="10" x2="22" y2="10" />
                                    <line x1="2" y1="15" x2="22" y2="15" />
                                    <line x1="10" y1="2" x2="10" y2="22" />
                                </svg>
                            @elseif($kind === 'image')
                                {{-- ImageIcon - Style Moderne --}}
                                <svg class="h-7 w-7 text-sky-700 dark:text-sky-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                </svg>
                            @else
                                {{-- Generic FileIcon --}}
                                <svg class="h-7 w-7 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                    <polyline points="14 2 14 8 20 8" />
                                </svg>
                            @endif
                        @endif
                    </div>

                    {{-- CONTENT --}}
                    <div class="min-w-0 flex-1 mt-3 md:mt-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                {{-- Title clickable --}}
                                <a href="{{ $openUrl }}" target="_blank" rel="noopener noreferrer"
                                   class="text-sm font-semibold text-gray-900 dark:text-white break-words hover:underline underline-offset-4">
                                    {{ $document->title }}
                                </a>

                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-lg border px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}">
                                        {{ $providerLabel }}
                                    </span>

                                    <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-[11px] font-semibold text-gray-700
                                                 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200">
                                        {{ $typePill }}
                                    </span>

                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $document->file_size_formatted ?? '' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="shrink-0 flex flex-col items-end gap-2">
                                <button wire:click="toggleStatus({{ $document->id }})"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500
                                            {{ $isOn ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-600' }}"
                                        aria-label="Basculer statut">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $isOn ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>

                                <span class="text-[11px] font-semibold {{ $isOn ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">
                                    {{ $isOn ? 'Partagé' : 'Brouillon' }}
                                </span>
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700
                                        dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200">
                                {{ $document->niveau->name ?? '-' }}
                            </span>
                            <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700
                                        dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200">
                                {{ $document->parcour->sigle ?? '-' }}
                            </span>
                            <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700
                                        dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200">
                                {{ $document->semestre->name ?? '-' }}
                            </span>
                        </div>

                        {{-- Meta + actions --}}
                        <div class="mt-4 flex flex-col gap-3"
                             :class="view === 'list'
                                    ? 'md:flex-row md:items-center md:justify-between'
                                    : 'sm:flex-row sm:items-center sm:justify-between'">

                            <div class="text-xs text-gray-600 dark:text-gray-300 flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-1.5">
                                    {{-- EyeIcon --}}
                                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 12a3 3 0 1 1-6 0a3 3 0 0 1 6 0Z"/>
                                    </svg>
                                    <span class="font-semibold">{{ $document->view_count ?? 0 }}</span>
                                </span>

                                <span class="inline-flex items-center gap-1.5">
                                    {{-- ClockIcon --}}
                                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 6v6l4 2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z"/>
                                    </svg>
                                    <span class="font-semibold">{{ optional($document->created_at)->format('d/m/Y') }}</span>
                                </span>
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                {{-- Ouvrir --}}
                                <a href="{{ $openUrl }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                          bg-gray-50 ring-1 ring-gray-200 text-gray-800 hover:bg-gray-100
                                          dark:bg-gray-900/30 dark:ring-gray-700 dark:text-gray-100 dark:hover:bg-gray-700/40 transition"
                                   title="Ouvrir">
                                    {{-- ArrowTopRightOnSquareIcon --}}
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <path d="M10 14L21 3" />
                                        <path d="M15 3h6v6" />
                                    </svg>
                                </a>

                                {{-- Télécharger (local toujours, externe seulement si convertible Google) --}}
                                @if($downloadUrl)
                                    <a href="{{ $downloadUrl }}"
                                       @if(!$isExternal) download @endif
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                              bg-gray-50 ring-1 ring-gray-200 text-gray-800 hover:bg-gray-100
                                              dark:bg-gray-900/30 dark:ring-gray-700 dark:text-gray-100 dark:hover:bg-gray-700/40 transition"
                                       title="Télécharger">
                                        {{-- ArrowDownTrayIcon --}}
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 17V3" />
                                            <path d="m6 11 6 6 6-6" />
                                            <path d="M19 21H5a2 2 0 0 1-2-2v-2" />
                                            <path d="M21 17v2a2 2 0 0 1-2 2" />
                                        </svg>
                                    </a>
                                @endif

                                {{-- Modifier --}}
                                <a href="{{ route('document.edit', $document) }}"
                                   class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                          bg-indigo-50 ring-1 ring-indigo-200 text-indigo-800 hover:bg-indigo-100
                                          dark:bg-indigo-900/20 dark:ring-indigo-900/40 dark:text-indigo-200 dark:hover:bg-indigo-900/30 transition"
                                   title="Modifier">
                                    {{-- PencilSquareIcon --}}
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </a>

                                {{-- Supprimer --}}
                                <button type="button"
                                        @click="$dispatch('open-delete-doc', { id: {{ $document->id }}, title: @js($document->title) })"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                               bg-red-50 ring-1 ring-red-200 text-red-800 hover:bg-red-100
                                               dark:bg-red-900/20 dark:ring-red-900/40 dark:text-red-200 dark:hover:bg-red-900/30 transition"
                                        title="Supprimer">
                                    {{-- TrashIcon --}}
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full">
                <div class="w-full rounded-2xl border border-gray-200 bg-white p-8 sm:p-10 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="w-full flex flex-col items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-900/30 dark:ring-gray-700">
                            {{-- DocumentIcon --}}
                            <svg class="h-7 w-7 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>

                        <div class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                            Aucun document
                        </div>

                        <p class="max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                            Ajoute un document ou ajuste les filtres.
                        </p>

                        <div class="mt-2 w-full flex flex-col sm:flex-row sm:justify-center gap-2">
                            <a href="{{ route('document.upload') }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white
                                      hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2
                                      dark:focus-visible:ring-offset-gray-900">
                                {{-- PlusIcon --}}
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                </svg>
                                Ajouter un document
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($documents->total() > $documents->perPage())
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 px-4 sm:px-6 py-4">
            {{ $documents->links() }}
        </div>
    @endif

    {{-- Delete Modal --}}
    @include('livewire.teacher.forms.modal-delete')
</div>