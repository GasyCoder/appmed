{{-- resources/views/livewire/student/sections/header-liste.blade.php --}}
@php
    $total = method_exists($documents, 'total') ? (int) $documents->total() : (int) ($documents?->count() ?? 0);
@endphp

<div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 overflow-hidden shadow-xl shadow-gray-900/5 dark:shadow-black/30">
    <div class="px-4 sm:px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-start sm:items-center justify-between gap-3">

            {{-- LEFT : Retour Accueil + titre --}}
            <div class="min-w-0 flex items-start sm:items-center gap-3">
                <a href="{{ route('studentEspace') }}"
                   class="inline-flex items-center justify-center h-10 w-10 rounded-xl
                          border border-gray-200 dark:border-gray-800
                          bg-white dark:bg-gray-950
                          text-gray-700 dark:text-gray-200
                          hover:bg-gray-50 dark:hover:bg-gray-900/40 transition
                          focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/60"
                   aria-label="Retour à l'accueil"
                   title="Retour à l'accueil">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M15 18l-6-6 6-6"/>
                    </svg>
                </a>

                <div class="min-w-0">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        Mes cours
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $total }} document(s) disponibles
                    </div>
                </div>
            </div>

            {{-- RIGHT : Switch grid/list --}}
            <div class="shrink-0 flex items-center gap-2">
                <button type="button"
                        wire:click="$set('viewType','grid')"
                        class="inline-flex items-center justify-center h-10 w-10 rounded-xl border
                               {{ $viewType === 'grid'
                                    ? 'border-indigo-400/70 bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 dark:border-indigo-500/70'
                                    : 'border-gray-200 bg-white text-gray-700 dark:bg-gray-950 dark:text-gray-200 dark:border-gray-800' }}
                               hover:bg-gray-50 dark:hover:bg-gray-900/40 transition
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/60"
                        aria-label="Affichage grille"
                        title="Grille">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z"/>
                    </svg>
                </button>

                <button type="button"
                        wire:click="$set('viewType','list')"
                        class="inline-flex items-center justify-center h-10 w-10 rounded-xl border
                               {{ $viewType !== 'grid'
                                    ? 'border-indigo-400/70 bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300 dark:border-indigo-500/70'
                                    : 'border-gray-200 bg-white text-gray-700 dark:bg-gray-950 dark:text-gray-200 dark:border-gray-800' }}
                               hover:bg-gray-50 dark:hover:bg-gray-900/40 transition
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/60"
                        aria-label="Affichage liste"
                        title="Liste">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                              d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Hint UX (optionnel) --}}
    <div class="px-4 sm:px-5 py-3 bg-gray-50 dark:bg-gray-900/30">
        <p class="text-xs text-gray-600 dark:text-gray-300">
            Astuce : “Liste” pour aller vite, “Grille” pour un affichage plus visuel.
        </p>
    </div>
</div>
