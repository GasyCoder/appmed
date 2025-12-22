{{-- resources/views/livewire/programmes.blade.php --}}

<div>
   <div class="max-w-10xl mx-auto">

        @php
            // UI tokens (cohérents light/dark)
            $card      = "rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900";
            $cardPad   = "p-5 sm:p-6";
            $muted     = "text-slate-600 dark:text-slate-400";
            $title     = "text-slate-900 dark:text-white";
            $btnGhost  = "inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm
                          hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                          dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800";
            $btnPrimary= "inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm
                          hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2
                          dark:focus-visible:ring-offset-slate-950";
            $pill      = "inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700
                          dark:border-slate-800 dark:bg-slate-800 dark:text-slate-200";
            $pillSoft  = "inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700
                          dark:border-indigo-900/40 dark:bg-indigo-900/20 dark:text-indigo-200";
        @endphp

        {{-- HEADER --}}
        <div class="mb-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight {{ $title }}">
                        Programme Master MERC
                    </h1>
                    <p class="mt-1 flex items-center gap-2 text-sm {{ $muted }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="truncate">Master en Épidémiologie et Recherche Clinique</span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button wire:click="toggleShowEnseignants"
                            class="{{ $btnGhost }}"
                            wire:loading.attr="disabled"
                            wire:target="toggleShowEnseignants">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ $showEnseignants ? 'Masquer' : 'Afficher' }} les enseignants
                    </button>

                    @can('manage programmes')
                        <button class="{{ $btnPrimary }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nouveau Programme
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- FILTER: ANNÉE (segmented control sobre) --}}
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 text-sm font-medium {{ $muted }}">
                <span>Filtrer par année :</span>
            </div>

            <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <button wire:click="$set('annee', null)"
                        class="px-4 py-2 rounded-md text-sm font-medium transition
                               {{ $annee === null
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                    Toutes
                </button>

                <button wire:click="$set('annee', 4)"
                        class="px-4 py-2 rounded-md text-sm font-medium transition
                               {{ (int)$annee === 4
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                    4<sup>ème</sup> année
                </button>

                <button wire:click="$set('annee', 5)"
                        class="px-4 py-2 rounded-md text-sm font-medium transition
                               {{ (int)$annee === 5
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                    5<sup>ème</sup> année
                </button>
            </div>
        </div>

        {{-- STATS (sobre, même style, accent minimal) --}}
        <div class="mb-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $statCard = "rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition
                             dark:border-slate-800 dark:bg-slate-900";
                $statLabel= "text-xs font-medium text-slate-500 dark:text-slate-400";
                $statValue= "mt-1 text-2xl font-semibold text-slate-900 dark:text-white";
                $statMeta = "mt-1 text-xs text-slate-500 dark:text-slate-400";
            @endphp

            <div class="{{ $statCard }}">
                <div class="flex items-center justify-between">
                    <span class="{{ $statLabel }}">Total UEs</span>
                    <span class="{{ $pillSoft }}">Global</span>
                </div>
                <div class="{{ $statValue }}">{{ $stats['totalUE'] }}</div>
            </div>

            <div class="{{ $statCard }}">
                <div class="flex items-center justify-between">
                    <span class="{{ $statLabel }}">Total ECs</span>
                    <span class="{{ $pillSoft }}">Global</span>
                </div>
                <div class="{{ $statValue }}">{{ $stats['totalEC'] }}</div>
            </div>

            @foreach([1,2,3,4] as $s)
                @php
                    $key = 'semestre'.$s;
                @endphp
                <div class="{{ $statCard }}">
                    <div class="flex items-center justify-between">
                        <span class="{{ $statLabel }}">Semestre {{ $s }}</span>
                        <span class="{{ $pill }}">S{{ $s }}</span>
                    </div>
                    <div class="{{ $statValue }}">
                        {{ $stats[$key]['ue'] ?? 0 }}
                        <span class="text-sm font-normal text-slate-500 dark:text-slate-400">UEs</span>
                    </div>
                    <div class="{{ $statMeta }}">{{ $stats[$key]['ec'] ?? 0 }} ECs</div>
                </div>
            @endforeach
        </div>

        {{-- SEARCH + FILTERS CARD --}}
        <div class="{{ $card }} mb-6">
            <div class="{{ $cardPad }}">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {{-- Search --}}
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium {{ $muted }} mb-2">
                            Rechercher
                        </label>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   class="block w-full rounded-lg border border-slate-300 bg-white py-3 pl-10 pr-10 text-sm text-slate-900 placeholder-slate-400
                                          focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500
                                          dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500"
                                   placeholder="Rechercher une UE, EC ou enseignant...">

                            @if($search)
                                <button wire:click="$set('search', '')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Semestre --}}
                    <div>
                        <label class="block text-sm font-medium {{ $muted }} mb-2">
                            Semestre
                        </label>

                        <select wire:model.live="semestre"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500
                                       dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <option value="">Tous les semestres</option>

                            @if($annee === null || (int)$annee === 4)
                                <option value="1">Semestre 1 (4ème année)</option>
                                <option value="2">Semestre 2 (4ème année)</option>
                            @endif

                            @if($annee === null || (int)$annee === 5)
                                <option value="3">Semestre 3 (5ème année)</option>
                                <option value="4">Semestre 4 (5ème année)</option>
                            @endif
                        </select>
                    </div>
                </div>

                {{-- Active filters --}}
                @if($search || $semestre || $annee)
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm {{ $muted }}">Filtres actifs :</span>

                        @if($search)
                            <span class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-800
                                         dark:border-indigo-900/40 dark:bg-indigo-900/20 dark:text-indigo-200">
                                Recherche: “{{ \Illuminate\Support\Str::limit($search, 22) }}”
                                <button wire:click="$set('search', '')" class="opacity-80 hover:opacity-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        @if($annee)
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm font-medium text-slate-700
                                         dark:border-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                {{ $annee }}ème année
                                <button wire:click="$set('annee', null)" class="opacity-80 hover:opacity-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        @if($semestre)
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm font-medium text-slate-700
                                         dark:border-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                Semestre {{ $semestre }}
                                <button wire:click="$set('semestre', null)" class="opacity-80 hover:opacity-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        <button wire:click="$set('search',''); $set('semestre', null); $set('annee', null);"
                                class="ml-auto text-sm font-medium text-slate-600 hover:text-slate-900 underline underline-offset-4
                                       dark:text-slate-400 dark:hover:text-white">
                            Réinitialiser tous
                        </button>
                    </div>
                @endif
            </div>
        </div>


        {{-- LISTE UEs --}}
        <div class="space-y-5"
            wire:loading.class="opacity-60 pointer-events-none"
            x-data="{ openId: @js(optional($programmes->first())->id) }">
            @forelse($programmes as $ue)
                @php
                    $ecCount = $ue->elements->count();
                @endphp

                <article wire:key="ue-{{ $ue->id }}"
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm
                        hover:shadow-md transition
                        dark:border-gray-800 dark:bg-gray-900">
                    {{-- UE HEADER (Card header clickable) --}}
                    <header class="bg-gray-50/80 dark:bg-gray-900/60 border-b border-gray-200 dark:border-gray-800">
                        <button
                            type="button"
                            class="w-full px-5 sm:px-6 py-5 text-left"
                            @click="openId = (openId === {{ $ue->id }} ? null : {{ $ue->id }})"
                            :aria-expanded="openId === {{ $ue->id }}"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4 min-w-0">
                                    {{-- UE Code --}}
                                    <span class="shrink-0 inline-flex items-center rounded-xl bg-indigo-600 px-3.5 py-2 text-sm font-bold text-white">
                                        {{ $ue->code }}
                                    </span>

                                    {{-- UE Title + meta --}}
                                    <div class="min-w-0">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $ue->name }}
                                        </h3>

                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            {{-- Semestre --}}
                                            <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700
                                                        dark:border-gray-800 dark:bg-gray-950 dark:text-gray-200">
                                                Semestre {{ $ue->semestre_id }}
                                            </span>

                                            {{-- EC count --}}
                                            <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700
                                                        dark:border-gray-800 dark:bg-gray-950 dark:text-gray-200">
                                                {{ $ecCount }} EC{{ $ecCount > 1 ? 's' : '' }}
                                            </span>

                                            {{-- Credits (optionnel) --}}
                                            @if(isset($ue->credits) && $ue->credits)
                                                <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700
                                                            dark:border-gray-800 dark:bg-gray-950 dark:text-gray-200">
                                                    {{ $ue->getTotalCredits() }} ECTS
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Toggle icon --}}
                                <span class="shrink-0 mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-xl
                                            text-gray-500 hover:bg-gray-100 hover:text-gray-700
                                            dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white">
                                    <svg
                                        class="h-5 w-5 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': openId === {{ $ue->id }} }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </span>
                            </div>

                            {{-- Mobile summary row --}}
                            <div class="mt-3 flex flex-wrap gap-2 lg:hidden">
                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700
                                            dark:bg-indigo-900/20 dark:text-indigo-200">
                                    S{{ $ue->semestre_id }}
                                </span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700
                                            dark:bg-gray-800 dark:text-gray-200">
                                    {{ $ecCount }} EC{{ $ecCount > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </button>
                    </header>

                    {{-- UE BODY --}}
                    <section
                        x-show="openId === {{ $ue->id }}"
                        x-collapse
                        x-cloak
                        class="p-5 sm:p-6"
                    >
                        @forelse($ue->elements as $ec)
                            @php
                                $heuresDetail = $ec->getHeuresDetail();
                                $totalHeures = is_array($heuresDetail) ? array_sum($heuresDetail) : 0;
                            @endphp

                            {{-- EC cards grid --}}
                            @if($loop->first)
                                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @endif

                            <div
                                wire:key="ec-{{ $ec->id }}-{{ $ec->updated_at }}"
                                class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm
                                    hover:shadow-md transition
                                    dark:border-gray-800 dark:bg-gray-950"
                            >
                                {{-- EC Header --}}
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700
                                                        dark:border-indigo-900/40 dark:bg-indigo-900/20 dark:text-indigo-200">
                                                {{ $ec->code }}
                                            </span>

                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $ec->name }}
                                            </h4>
                                        </div>

                                        {{-- EC meta --}}
                                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                                            @if(isset($ec->credits) && $ec->credits)
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                    </svg>
                                                    {{ $ec->credits }} ECTS
                                                </span>
                                            @endif

                                            @if($totalHeures > 0)
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $totalHeures }}h
                                                    @if(!empty($heuresDetail['cm'])) · {{ $heuresDetail['cm'] }}h CM @endif
                                                    @if(!empty($heuresDetail['td'])) · {{ $heuresDetail['td'] }}h TD @endif
                                                    @if(!empty($heuresDetail['tp'])) · {{ $heuresDetail['tp'] }}h TP @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions Menu --}}
                                    @role('admin')
                                        <div wire:key="menu-{{ $ec->id }}" class="shrink-0 relative" x-data="{ open: false }">
                                            <button
                                                type="button"
                                                @click="open = !open"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                                    text-gray-500 hover:bg-gray-100 hover:text-gray-700
                                                    dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white
                                                    transition-colors duration-150"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                </svg>
                                            </button>

                                            {{-- Dropdown Menu --}}
                                            <div
                                                x-show="open"
                                                @click.away="open = false"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl border border-gray-200 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none
                                                    dark:border-gray-700 dark:bg-gray-800 z-50"
                                                style="display: none;"
                                            >
                                                <div class="py-1">
                                                    <button
                                                        type="button"
                                                        wire:click="$dispatch('openAssignModal', { programmeId: {{ $ec->id }} })"
                                                        @click="open = false"
                                                        class="group flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100
                                                            dark:text-gray-200 dark:hover:bg-gray-700 transition-colors duration-150"
                                                    >
                                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        Assigner enseignant
                                                    </button>
                                                    
                                                    <button
                                                        type="button"
                                                        wire:click="$dispatch('openEditModal', { programmeId: {{ $ec->id }} })"
                                                        @click="open = false"
                                                        class="group flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100
                                                            dark:text-gray-200 dark:hover:bg-gray-700 transition-colors duration-150"
                                                    >
                                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                        Modifier l'EC
                                                    </button>
                                                </div>
                                                
                                                <div class="border-t border-gray-200 dark:border-gray-700 py-1">
                                                    <button
                                                        type="button"
                                                        wire:click="$dispatch('openDeleteModal', { programmeId: {{ $ec->id }} })"
                                                        @click="open = false"
                                                        class="group flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50
                                                            dark:text-red-400 dark:hover:bg-red-900/20 transition-colors duration-150"
                                                    >
                                                        <svg class="h-4 w-4 text-red-500 group-hover:text-red-600 dark:group-hover:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endrole
                                </div>

                                {{-- Enseignants (sobres, UX clean) --}}
                                @if($showEnseignants)
                                    <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                                        @if($ec->enseignants->isNotEmpty())
                                            <div class="space-y-2">
                                                @foreach($ec->enseignants as $ens)
                                                    @php
                                                        $cm = (int)($ens->pivot->heures_cm ?? 0);
                                                        $td = (int)($ens->pivot->heures_td ?? 0);
                                                        $tp = (int)($ens->pivot->heures_tp ?? 0);
                                                        $tot = $cm + $td + $tp;
                                                    @endphp

                                                    <div wire:key="ens-{{ $ens->id }}-{{ $ec->id }}" 
                                                        class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2
                                                                dark:border-gray-800 dark:bg-gray-900">
                                                        <div class="flex items-center gap-3 min-w-0">
                                                            <img
                                                                src="{{ $ens->profile_photo_url }}"
                                                                alt="{{ $ens->name }}"
                                                                class="h-9 w-9 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-800"
                                                            />

                                                            <div class="min-w-0">
                                                                <div class="flex items-center gap-2">
                                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                                        {{ $ens->full_name_with_grade ?? $ens->name }}
                                                                    </p>

                                                                    @if(!empty($ens->pivot->is_responsable))
                                                                        <span class="inline-flex items-center rounded-full bg-indigo-600 px-2 py-0.5 text-[11px] font-semibold text-white">
                                                                            Responsable
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                                                    {{ $ens->email }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="shrink-0 text-xs font-semibold text-gray-700 dark:text-gray-200">
                                                            {{ $tot }}h
                                                        </div>
                                                    </div>

                                                    @if(!empty($ens->pivot->note))
                                                        <div class="rounded-xl border border-gray-200 bg-white p-3 text-sm text-gray-700 italic
                                                                    dark:border-gray-800 dark:bg-gray-950 dark:text-gray-200">
                                                            {{ $ens->pivot->note }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 text-center
                                                        dark:border-gray-700 dark:bg-gray-900/40">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    Aucun enseignant assigné
                                                </p>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    Cet EC n'a pas encore d'enseignant.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @if($loop->last)
                                </div>
                            @endif
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center
                                        dark:border-gray-700 dark:bg-gray-900/40">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Aucun EC</p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Cette UE ne contient pas encore d'éléments constitutifs.</p>
                            </div>
                        @endforelse
                    </section>
                </article>
            @empty
                <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aucun programme trouvé</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Aucun programme ne correspond à vos critères. Essayez d'ajuster les filtres.
                    </p>
                </div>
            @endforelse
        </div>


        {{-- Pagination --}}
        @if($programmes->hasPages())
            <div class="mt-8 {{ $card }}">
                <div class="px-6 py-4">
                    {{ $programmes->links() }}
                </div>
            </div>
        @endif

        {{-- Loading overlay --}}
        <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xl dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium {{ $title }}">Chargement…</span>
                </div>
            </div>
        </div>
    </div>

 {{-- Modal d'assignation --}}
<livewire:programmes.assign-enseignant />
<livewire:programmes.edit-programme />
<livewire:programmes.delete-programme /> 

</div>
