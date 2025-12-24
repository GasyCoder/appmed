<div
    class="w-full px-4 sm:px-6 lg:px-8 py-5 sm:py-6 space-y-5"
    x-data="{
        view: (localStorage.getItem('teacherDocsView') || 'grid'),
        setView(v){ this.view = v; localStorage.setItem('teacherDocsView', v) }
    }">

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
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Grille
                        </button>

                        <button type="button"
                                @click="setView('list')"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition"
                                :class="view === 'list'
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700/40'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                            </svg>
                            Liste
                        </button>
                    </div>

                    <a href="{{ route('document.upload') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                              hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2
                              dark:focus-visible:ring-offset-gray-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
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
                             dark:border-purple-900/40 dark:bg-purple-900/20 dark:text-purple-200">
                    Vues <span class="text-purple-900 dark:text-purple-100">{{ $stats['views'] }}</span>
                </span>

                <span class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-800
                             dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-200">
                    7 jours <span class="text-blue-900 dark:text-blue-100">{{ $stats['recent'] }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Search + Filters (réduits) --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 p-4 sm:p-5">
        <div class="flex flex-col gap-3">

            {{-- Search + sort --}}
            <div class="flex flex-col lg:flex-row gap-3 lg:items-center">
                <div class="flex-1">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
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
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

                    <button wire:click="sortBy('created_at')" class="{{ $sortBtnBase }} {{ $sortField === 'created_at' ? $sortOn : $sortOff }}">
                        Récents
                    </button>
                    <button wire:click="sortBy('title')" class="{{ $sortBtnBase }} {{ $sortField === 'title' ? $sortOn : $sortOff }}">
                        Titre
                    </button>
                    <button wire:click="sortBy('view_count')" class="{{ $sortBtnBase }} {{ $sortField === 'view_count' ? $sortOn : $sortOff }}">
                        Vues
                    </button>
                    <button wire:click="sortBy('is_actif')" class="{{ $sortBtnBase }} {{ $sortField === 'is_actif' ? $sortOn : $sortOff }}">
                        Statut
                    </button>
                </div>
            </div>

            {{-- Filters: mobile collapsible / desktop inline --}}
            <details class="md:hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/20">
                <summary class="cursor-pointer select-none px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white flex items-center justify-between">
                    Filtres
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
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
            : 'grid grid-cols-1 gap-3'">
        @forelse($documents as $document)
            @php
                $ext = $document->extension ?? strtolower(pathinfo($document->file_path ?? '', PATHINFO_EXTENSION));
                $isOn = (bool) $document->is_actif;
            @endphp

            <article wire:key="doc-card-{{ $document->id }}"
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition
                            dark:border-gray-700 dark:bg-gray-800 overflow-hidden"
                    :class="view === 'list' ? 'md:flex md:items-stretch' : ''"
            >
                <div class="p-4 sm:p-5 w-full"
                    :class="view === 'list' ? 'md:flex md:items-center md:gap-4' : ''"
                >
                    {{-- Left block (icon + content) --}}
                    <div class="flex items-start gap-3 w-full"
                        :class="view === 'list' ? 'md:items-center' : ''"
                    >
                        {{-- File icon --}}
                        <div class="h-12 w-12 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0
                                    dark:border-gray-700 dark:bg-gray-900/30">
                            <div class="h-6 w-6 text-gray-700 dark:text-gray-200">
                                @include('livewire.teacher.forms.file-icons', ['extension' => $ext])
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white break-words">
                                        {{ $document->title }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $document->file_size_formatted }} • {{ strtoupper($ext ?: 'DOC') }}
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
                                    : 'sm:flex-row sm:items-center sm:justify-between'"
                            >
                                <div class="text-xs text-gray-500 dark:text-gray-400 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ $document->view_count ?? 0 }}
                                    </span>

                                    <span class="text-gray-300 dark:text-gray-600">•</span>

                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $document->created_at->format('d/m/Y') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('document.edit', $document) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                            text-indigo-600 hover:bg-indigo-50 hover:text-indigo-900
                                            dark:text-indigo-400 dark:hover:bg-indigo-900/20 dark:hover:text-indigo-300 transition"
                                    title="Modifier">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <button
                                        type="button"
                                        @click="$dispatch('open-delete-doc', { id: {{ $document->id }}, title: @js($document->title) })"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                            text-red-600 hover:bg-red-50 hover:text-red-900
                                            dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300 transition"
                                        title="Supprimer"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
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
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
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
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
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
