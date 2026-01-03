{{-- resources/views/livewire/documents/document-index.blade.php --}}

    @php
        use Illuminate\Support\Str;

        // Helpers safe
        $isHttp = fn(string $url) => Str::startsWith($url, ['http://', 'https://']);

        $fileKindFromExt = function (?string $ext) {
            $ext = strtolower((string) $ext);
            return match (true) {
                in_array($ext, ['pdf'], true) => 'pdf',
                in_array($ext, ['doc','docx','dot','dotx'], true) => 'word',
                in_array($ext, ['ppt','pptx'], true) => 'ppt',
                in_array($ext, ['xls','xlsx','csv'], true) => 'xls',
                in_array($ext, ['jpg','jpeg','png','gif','webp'], true) => 'image',
                $ext === '' => 'file',
                default => 'file',
            };
        };

        $googleMeta = function (string $url) {
            $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
            $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');

            if (str_contains($host, 'drive.google.com')) return ['provider' => 'drive'];
            if (str_contains($host, 'docs.google.com')) {
                if (str_contains($path, '/document/')) return ['provider' => 'gdoc'];
                if (str_contains($path, '/presentation/')) return ['provider' => 'gslides'];
                if (str_contains($path, '/spreadsheets/')) return ['provider' => 'gsheets'];
                return ['provider' => 'docs'];
            }
            return ['provider' => 'external'];
        };
    @endphp

    <div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 space-y-5"
         x-data="{ view: @js($viewType ?? 'grid') }">

        {{-- HEADER --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Documents</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Consultez et téléchargez les documents selon vos droits.
                </p>
            </div>
            
            {{-- VIEW TOGGLE --}}
            <div class="flex items-center gap-2">
                {{-- Action: Nouveau document (TEACHER ONLY) --}}
                    @role('teacher')
                        <a href="{{ route('document.upload') }}"
                        class="inline-flex h-10 w-full sm:w-auto items-center justify-center gap-2 rounded-xl
                                bg-indigo-600 px-4 text-sm font-semibold text-white
                                hover:bg-indigo-700 active:bg-indigo-800
                                focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2
                                dark:focus-visible:ring-offset-gray-900 transition">
                            {{-- Plus icon --}}
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>
                            <span>Nouveau document</span>
                        </a>
                    @endrole

                {{-- VIEW TOGGLE (icônes) --}}
                    {{-- GRID --}}
                    <button type="button"
                            @click="view='grid'; $wire.toggleView('grid')"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl px-3 sm:px-4 text-sm font-semibold
                                transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            :class="view==='grid'
                                ? 'bg-white text-indigo-700 shadow-sm ring-1 ring-indigo-200 dark:bg-gray-900 dark:text-indigo-200 dark:ring-indigo-900/40'
                                : 'text-gray-700 hover:bg-white/70 dark:text-gray-200 dark:hover:bg-gray-700/40'">
                        {{-- Grid icon --}}
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="8" height="8" rx="2"></rect>
                            <rect x="13" y="3" width="8" height="8" rx="2"></rect>
                            <rect x="3" y="13" width="8" height="8" rx="2"></rect>
                            <rect x="13" y="13" width="8" height="8" rx="2"></rect>
                        </svg>

                        <span class="hidden sm:inline">Grille</span>
                    </button>

                    {{-- LIST --}}
                    <button type="button"
                            @click="view='list'; $wire.toggleView('list')"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl px-3 sm:px-4 text-sm font-semibold
                                transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            :class="view==='list'
                                ? 'bg-white text-indigo-700 shadow-sm ring-1 ring-indigo-200 dark:bg-gray-900 dark:text-indigo-200 dark:ring-indigo-900/40'
                                : 'text-gray-700 hover:bg-white/70 dark:text-gray-200 dark:hover:bg-gray-700/40'">
                        {{-- List icon --}}
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <circle cx="4" cy="6" r="1"></circle>
                            <circle cx="4" cy="12" r="1"></circle>
                            <circle cx="4" cy="18" r="1"></circle>
                        </svg>

                        <span class="hidden sm:inline">Liste</span>
                    </button>
            </div>
        </div>

        {{-- FILTER BAR (simple, adapte selon ton composant Livewire) --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                    dark:border-gray-700 dark:bg-gray-800">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Recherche</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Titre du document..."
                           class="mt-1 w-full h-10 rounded-xl border-gray-200 bg-gray-50
                                  focus:border-indigo-500 focus:ring-indigo-500
                                  dark:border-gray-700 dark:bg-gray-900/30 dark:text-white" />
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Enseignant</label>
                    <select wire:model.live="teacherFilter"
                            class="mt-1 w-full h-10 rounded-xl border-gray-200 bg-gray-50
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-gray-700 dark:bg-gray-900/30 dark:text-white">
                        <option value="">Tous</option>
                        @foreach(($teachers ?? []) as $t)
                            <option value="{{ $t->id }}">{{ $t->name ?? '—' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Semestre</label>
                    <select wire:model.live="semesterFilter"
                            class="mt-1 w-full h-10 rounded-xl border-gray-200 bg-gray-50
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-gray-700 dark:bg-gray-900/30 dark:text-white">
                        <option value="">Tous</option>
                        @foreach(($semestres ?? []) as $s)
                            <option value="{{ $s->id }}">{{ $s->name ?? '—' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-200">Vu</label>
                    <select wire:model.live="viewedFilter"
                            class="mt-1 w-full h-10 rounded-xl border-gray-200 bg-gray-50
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-gray-700 dark:bg-gray-900/30 dark:text-white">
                        <option value="all">Tous</option>
                        <option value="viewed">Déjà vus</option>
                        <option value="unviewed">Non vus</option>
                    </select>
                </div>
            </div>

            {{-- scope active/archives (si tu l’utilises) --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button"
                        wire:click="setScope('active')"
                        class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-semibold
                               ring-1 ring-gray-200 bg-white hover:bg-gray-50
                               dark:bg-gray-800 dark:ring-gray-700 dark:hover:bg-gray-700/40 transition
                               {{ ($scope ?? 'active') === 'active' ? 'text-indigo-700 dark:text-indigo-200 ring-indigo-200 dark:ring-indigo-900/40' : 'text-gray-700 dark:text-gray-200' }}">
                    Actifs
                    @isset($activeTotal)
                        <span class="ml-2 inline-flex items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-bold text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                            {{ $activeTotal }}
                        </span>
                    @endisset
                </button>

                <button type="button"
                        wire:click="setScope('archives')"
                        class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-semibold
                               ring-1 ring-gray-200 bg-white hover:bg-gray-50
                               dark:bg-gray-800 dark:ring-gray-700 dark:hover:bg-gray-700/40 transition
                               {{ ($scope ?? 'active') === 'archives' ? 'text-indigo-700 dark:text-indigo-200 ring-indigo-200 dark:ring-indigo-900/40' : 'text-gray-700 dark:text-gray-200' }}">
                    Archives
                    @isset($archivedTotal)
                        <span class="ml-2 inline-flex items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-bold text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                            {{ $archivedTotal }}
                        </span>
                    @endisset
                </button>
            </div>
        </div>

        {{-- LISTE / GRID --}}
        <div wire:loading
             wire:target="search,teacherFilter,semesterFilter,viewedFilter,filterNiveau,filterParcour,filterSemestre,filterStatus,sortBy,setScope,toggleView,page,toggleArchive,toggleStatus,deleteDocument,markViewed,markDownload"
             aria-busy="true"
             class="space-y-4">
            <div x-show="view === 'grid'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                <x-skeleton.card :lines="4" />
                <x-skeleton.card :lines="4" />
                <x-skeleton.card :lines="4" />
                <x-skeleton.card :lines="4" />
                <x-skeleton.card :lines="4" />
                <x-skeleton.card :lines="4" />
            </div>
            <div x-show="view === 'list'" x-cloak class="space-y-3">
                <x-skeleton.card :lines="3" />
                <x-skeleton.card :lines="3" />
                <x-skeleton.card :lines="3" />
                <x-skeleton.card :lines="3" />
            </div>
        </div>

        <div wire:loading.remove
             wire:target="search,teacherFilter,semesterFilter,viewedFilter,filterNiveau,filterParcour,filterSemestre,filterStatus,sortBy,setScope,toggleView,page,toggleArchive,toggleStatus,deleteDocument,markViewed,markDownload"
             :class="view === 'grid'
                ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4'
                : 'grid grid-cols-1 gap-3'">

            @forelse($documents as $document)
                @php
                    $rawUrl = (string) ($document->source_url ?: $document->file_path ?: '');
                    $isExternal = $isHttp($rawUrl);

                    $currentExt = $document->extensionFromPath(); // marche local/externe
                    $kind = $fileKindFromExt($currentExt);

                    $providerLabel = 'Fichier local';
                    $badgeClass = 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-900/30 dark:text-gray-200';

                    if ($isExternal) {
                        $meta = $googleMeta($rawUrl);
                        $providerLabel = match ($meta['provider']) {
                            'drive' => 'Google Drive',
                            'gdoc' => 'Google Docs',
                            'gslides' => 'Google Slides',
                            'gsheets' => 'Google Sheets',
                            default => 'Lien externe',
                        };

                        $badgeClass = match ($meta['provider']) {
                            'drive','gdoc','gslides','gsheets' => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-200',
                            default => 'border-gray-200 bg-white text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200',
                        };
                    }

                    // OUVERTURE
                    $openUrl = $isExternal
                        ? route('document.openExternal', $document) // nouvel onglet
                        : route('document.viewer', $document);      // viewer interne (pdf/pptx/docx => pdf)

                    // TELECHARGEMENT
                    $downloadUrl = $isExternal
                        ? route('document.downloadExternal', $document) // SANS count
                        : route('document.download', $document);        // AVEC count

                    // Compteurs
                    $views = (int) ($document->view_count ?? 0);
                    $dlCount = (int) ($document->download_count ?? 0);
                    $dlLabel = $dlCount > 99 ? '99+' : (string) $dlCount;

                    // Taille
                    $sizeLabel = (string) ($document->file_size_formatted ?? '');

                    // Type pill
                    $typePill = $isExternal ? 'LIEN' : strtoupper($currentExt ?: 'DOC');
                @endphp

                <article wire:key="doc-{{ $document->id }}"
                         class="rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition
                                dark:border-gray-700 dark:bg-gray-800 overflow-hidden"
                         :class="view === 'list' ? 'md:flex md:items-stretch' : ''">
                    <div class="p-4 sm:p-5 w-full" :class="view === 'list' ? 'md:flex md:items-center md:gap-4' : ''">

                        {{-- ICON --}}
                        <div class="h-14 w-14 rounded-2xl border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0
                                    dark:border-gray-700 dark:bg-gray-900/30">
                            @if($isExternal)
                                <svg class="h-7 w-7 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                </svg>
                            @else
                                @if($kind === 'pdf')
                                    <svg class="h-7 w-7 text-red-700 dark:text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                @elseif($kind === 'word')
                                    <svg class="h-7 w-7 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <path d="M8 13h8"/>
                                        <path d="M8 17h6"/>
                                    </svg>
                                @elseif($kind === 'ppt')
                                    <svg class="h-7 w-7 text-orange-700 dark:text-orange-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                                        <path d="M8 21h8"/>
                                        <path d="M12 17v4"/>
                                        <path d="m9 8 3 3 3-3"/>
                                    </svg>
                                @elseif($kind === 'xls')
                                    <svg class="h-7 w-7 text-emerald-700 dark:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"/>
                                        <line x1="2" y1="10" x2="22" y2="10"/>
                                        <line x1="2" y1="15" x2="22" y2="15"/>
                                        <line x1="10" y1="2" x2="10" y2="22"/>
                                    </svg>
                                @elseif($kind === 'image')
                                    <svg class="h-7 w-7 text-sky-700 dark:text-sky-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="9" cy="9" r="2"/>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                @else
                                    <svg class="h-7 w-7 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                @endif
                            @endif
                        </div>

                        {{-- CONTENT --}}
                        <div class="min-w-0 flex-1 mt-3 md:mt-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ $openUrl }}"
                                       target="{{ $isExternal ? '_blank' : '_self' }}"
                                       rel="noopener noreferrer"
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
                                            {{ $sizeLabel !== '' ? $sizeLabel : '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- TAGS --}}
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

                                @if($document->is_archive)
                                    <span class="inline-flex items-center rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-800
                                                 dark:border-amber-900/40 dark:bg-amber-900/20 dark:text-amber-200">
                                        Archivé
                                    </span>
                                @endif
                            </div>

                            {{-- META + ACTIONS --}}
                            <div class="mt-4 flex flex-col gap-3"
                                 :class="view === 'list'
                                    ? 'md:flex-row md:items-center md:justify-between'
                                    : 'sm:flex-row sm:items-center sm:justify-between'">

                                <div class="text-xs text-gray-600 dark:text-gray-300 flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-5 w-5 text-gray-500 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15 12a3 3 0 1 1-6 0a3 3 0 0 1 6 0Z"/>
                                        </svg>
                                        <span class="font-semibold">{{ $views }}</span>
                                    </span>

                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-5 w-5 text-gray-500 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        <span class="font-semibold">{{ optional($document->created_at)->format('d/m/Y') }}</span>
                                    </span>
                                </div>

                                <div class="flex items-center justify-end gap-2">
                                    {{-- Ouvrir --}}
                                    <a href="{{ $openUrl }}"
                                       target="{{ $isExternal ? '_blank' : '_self' }}"
                                       rel="noopener noreferrer"
                                       class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                              bg-gray-50 ring-1 ring-gray-200 text-gray-800 hover:bg-gray-100
                                              dark:bg-gray-900/30 dark:ring-gray-700 dark:text-gray-100 dark:hover:bg-gray-700/40 transition"
                                       title="Ouvrir">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                            <path d="M10 14L21 3"/>
                                            <path d="M15 3h6v6"/>
                                        </svg>
                                    </a>

                                    {{-- Télécharger --}}
                                    <a href="{{ $downloadUrl }}"
                                       target="{{ $isExternal ? '_blank' : '_self' }}"
                                       rel="noopener noreferrer"
                                       class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl
                                              bg-gray-50 ring-1 ring-gray-200 text-gray-800 hover:bg-gray-100
                                              dark:bg-gray-900/30 dark:ring-gray-700 dark:text-gray-100 dark:hover:bg-gray-700/40 transition"
                                       title="Télécharger">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 17V3"/>
                                            <path d="m6 11 6 6 6-6"/>
                                            <path d="M19 21H5a2 2 0 0 1-2-2v-2"/>
                                            <path d="M21 17v2a2 2 0 0 1-2 2"/>
                                        </svg>

                                        {{-- Badge download_count : UNIQUEMENT LOCAL --}}
                                        @if(!$isExternal && $dlCount > 0)
                                            <sup class="absolute -top-1 -right-1 inline-flex items-center justify-center
                                                        h-5 min-w-[1.25rem] px-1.5 rounded-full text-[10px] font-bold
                                                        bg-indigo-600 text-white ring-2 ring-white
                                                        dark:ring-gray-800">
                                                {{ $dlLabel }}
                                            </sup>
                                        @endif
                                    </a>

                                    {{-- Actions teacher uniquement --}}
                                    @php $canManage = $document->canManage(auth()->user()); @endphp
                                    @if($canManage)
                                    @if(auth()->user()?->hasRole('teacher') && (int)$document->uploaded_by === (int)auth()->id())
                                        <a href="{{ route('document.edit', $document) }}"
                                           class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                                  bg-indigo-50 ring-1 ring-indigo-200 text-indigo-800 hover:bg-indigo-100
                                                  dark:bg-indigo-900/20 dark:ring-indigo-900/40 dark:text-indigo-200 dark:hover:bg-indigo-900/30 transition"
                                           title="Modifier">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>

                                        <button type="button"
                                                wire:click="toggleArchive({{ $document->id }})"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                                       bg-amber-50 ring-1 ring-amber-200 text-amber-900 hover:bg-amber-100
                                                       dark:bg-amber-900/20 dark:ring-amber-900/40 dark:text-amber-200 dark:hover:bg-amber-900/30 transition"
                                                title="{{ $document->is_archive ? 'Restaurer' : 'Archiver' }}">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4m16 0-1 14H5L4 7m16 0-2-3H6L4 7"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 11h4"/>
                                            </svg>
                                        </button>

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
                                    @endif
                                  @endif  
                                </div>
                            </div>
                        </div>

                    </div>
                </article>
            @empty
                <div class="col-span-full">
                    <div class="w-full rounded-2xl border border-gray-200 bg-white p-8 sm:p-10 text-center shadow-sm
                                dark:border-gray-700 dark:bg-gray-800">
                        <div class="w-full flex flex-col items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 ring-1 ring-gray-200
                                        dark:bg-gray-900/30 dark:ring-gray-700">
                                <svg class="h-7 w-7 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>

                            <div class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">
                                Aucun document
                            </div>

                            <p class="max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                                Ajustez les filtres ou ajoutez un document.
                            </p>

                            @if(auth()->user()?->hasRole('teacher'))
                                <div class="mt-2 w-full flex flex-col sm:flex-row sm:justify-center gap-2">
                                    <a href="{{ route('document.upload') }}"
                                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl
                                              bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white
                                              hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2
                                              focus-visible:ring-indigo-500 focus-visible:ring-offset-2
                                              dark:focus-visible:ring-offset-gray-900">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                                        </svg>
                                        Ajouter un document
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        <div class="pt-2">
            {{ $documents->links() }}
        </div>

     @include('livewire.teacher.forms.modal-delete')    
    </div>
