<div class="py-6">
    <div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Flash --}}
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 ring-1 ring-emerald-600/15 dark:ring-emerald-400/15 p-4">
                <p class="text-sm text-emerald-800 dark:text-emerald-200">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        @error('general')
            <div class="rounded-2xl bg-rose-50 dark:bg-rose-900/20 ring-1 ring-rose-600/15 dark:ring-rose-400/15 p-4">
                <p class="text-sm text-rose-800 dark:text-rose-200">{{ $message }}</p>
            </div>
        @enderror

        {{-- Global Loading Overlay --}}
        @teleport('body')
        <div
            wire:loading.delay
            wire:target="save,toggleActive,deleteConfirmed"
            class="fixed inset-0 z-[9999] bg-gray-900/40 backdrop-blur-[1px]"
        >
            <div class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm px-4">
                <div class="rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-black/5 dark:ring-white/10 shadow-xl p-5">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 animate-spin text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Traitement en cours…</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                Sauvegarde / mise à jour. Les emails sont envoyés via queue.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endteleport




        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                    Annonces
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Créez des annonces et notifiez les enseignants / étudiants.
                </p>
            </div>

            <button
                type="button"
                wire:click="openCreate"
                class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-xl text-sm font-semibold
                       bg-gray-900 text-white hover:bg-gray-800
                       dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/>
                </svg>
                Nouvelle annonce
            </button>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Recherche</label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Titre / contenu…"
                        class="mt-1 w-full h-10 rounded-xl px-3
                               bg-white dark:bg-gray-900/40
                               text-gray-900 dark:text-white
                               ring-1 ring-gray-300/70 dark:ring-white/10
                               focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none"
                    />
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Statut</label>
                    <select
                        wire:model.live="filterActive"
                        class="mt-1 w-full h-10 rounded-xl px-3
                               bg-white dark:bg-gray-900/40
                               text-gray-900 dark:text-white
                               ring-1 ring-gray-300/70 dark:ring-white/10
                               focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none"
                    >
                        <option value="all">Tous</option>
                        <option value="active">Actives</option>
                        <option value="inactive">Inactives</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Type</label>
                    <select
                        wire:model.live="filterType"
                        class="mt-1 w-full h-10 rounded-xl px-3
                               bg-white dark:bg-gray-900/40
                               text-gray-900 dark:text-white
                               ring-1 ring-gray-300/70 dark:ring-white/10
                               focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none"
                    >
                        <option value="all">Tous</option>
                        <option value="info">Info</option>
                        <option value="success">Succès</option>
                        <option value="warning">Alerte</option>
                        <option value="danger">Danger</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/30">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Annonce</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Audience</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Période</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Vues</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($announcements as $a)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/20">
                                <td class="px-4 py-4">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full
                                            @class([
                                                'bg-blue-500' => $a->type === 'info',
                                                'bg-emerald-500' => $a->type === 'success',
                                                'bg-amber-500' => $a->type === 'warning',
                                                'bg-rose-500' => $a->type === 'danger',
                                            ])"></span>

                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $a->title }}
                                            </div>
                                            <div class="mt-0.5 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($a->body), 140) }}
                                            </div>
                                            @if($a->action_url)
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    CTA: <span class="font-medium">{{ $a->action_label ?: 'Ouvrir' }}</span>
                                                    — {{ $a->action_url }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    @if(is_null($a->audience_roles))
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold
                                                     bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                            Tous
                                        </span>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($a->audience_roles as $r)
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold
                                                             bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                                    {{ $r }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div>Début: {{ $a->starts_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                    <div>Fin: {{ $a->ends_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $a->views_count }}
                                </td>

                                <td class="px-4 py-4">
                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $a->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleActive({{ $a->id }})"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold
                                               ring-1 ring-gray-200 dark:ring-white/10
                                               {{ $a->is_active ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-200' }}"
                                    >
                                        <span class="inline-flex h-2 w-2 rounded-full {{ $a->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $a->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>

                                <td class="px-4 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button
                                            type="button"
                                            wire:click="openEdit({{ $a->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                                   ring-1 ring-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100
                                                   dark:ring-white/10 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900 transition"
                                            title="Modifier"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="confirmDelete({{ $a->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                                   ring-1 ring-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100
                                                   dark:ring-rose-500/20 dark:bg-rose-900/20 dark:text-rose-200 dark:hover:bg-rose-900/30 transition"
                                            title="Supprimer"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Aucune annonce trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $announcements->links() }}
            </div>
        </div>

        {{-- Modal Create/Edit --}}
        @if($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="min-h-screen px-4 py-8 flex items-center justify-center">
                    <div class="fixed inset-0 bg-gray-900/50" wire:click="closeModal"></div>

                    <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $announcementId ? 'Modifier une annonce' : 'Nouvelle annonce' }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Définissez le contenu, l’audience et la notification email.
                                </p>
                            </div>
                            <button wire:click="closeModal"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                           ring-1 ring-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100
                                           dark:ring-white/10 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save" class="p-6 space-y-5">
                            {{-- Title + Type --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Titre</label>
                                    <input type="text" wire:model.defer="title"
                                           class="mt-1 w-full h-11 rounded-xl px-3
                                                  bg-white dark:bg-gray-900/40
                                                  text-gray-900 dark:text-white
                                                  ring-1 ring-gray-300/70 dark:ring-white/10
                                                  focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none">
                                    @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Type</label>
                                    <select wire:model.defer="type"
                                            class="mt-1 w-full h-11 rounded-xl px-3
                                                   bg-white dark:bg-gray-900/40
                                                   text-gray-900 dark:text-white
                                                   ring-1 ring-gray-300/70 dark:ring-white/10
                                                   focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none">
                                        <option value="info">Info</option>
                                        <option value="success">Succès</option>
                                        <option value="warning">Alerte</option>
                                        <option value="danger">Danger</option>
                                    </select>
                                    @error('type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Body --}}
                            <div>
                                <label class="text-sm text-gray-700 dark:text-gray-300">Message</label>
                                <textarea wire:model.defer="body" rows="6"
                                          class="mt-1 w-full rounded-xl px-3 py-2
                                                 bg-white dark:bg-gray-900/40
                                                 text-gray-900 dark:text-white
                                                 ring-1 ring-gray-300/70 dark:ring-white/10
                                                 focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none"></textarea>
                                @error('body') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- CTA --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Label bouton (optionnel)</label>
                                    <input type="text" wire:model.defer="action_label"
                                           placeholder="En savoir plus"
                                           class="mt-1 w-full h-11 rounded-xl px-3
                                                  bg-white dark:bg-gray-900/40
                                                  text-gray-900 dark:text-white
                                                  ring-1 ring-gray-300/70 dark:ring-white/10
                                                  focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none">
                                    @error('action_label') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">URL (optionnel)</label>
                                    <input type="text" wire:model.defer="action_url"
                                           placeholder="https://..."
                                           class="mt-1 w-full h-11 rounded-xl px-3
                                                  bg-white dark:bg-gray-900/40
                                                  text-gray-900 dark:text-white
                                                  ring-1 ring-gray-300/70 dark:ring-white/10
                                                  focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none">
                                    @error('action_url') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Active + period --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Statut</label>
                                    <div class="mt-2 flex items-center gap-2">
                                        <input type="checkbox" wire:model.defer="is_active" class="rounded border-gray-300 dark:border-gray-600">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Activer</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Début (optionnel)</label>
                                    <input type="datetime-local" wire:model.defer="starts_at"
                                           class="mt-1 w-full h-11 rounded-xl px-3
                                                  bg-white dark:bg-gray-900/40
                                                  text-gray-900 dark:text-white
                                                  ring-1 ring-gray-300/70 dark:ring-white/10
                                                  focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none">
                                    @error('starts_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Fin (optionnel)</label>
                                    <input type="datetime-local" wire:model.defer="ends_at"
                                           class="mt-1 w-full h-11 rounded-xl px-3
                                                  bg-white dark:bg-gray-900/40
                                                  text-gray-900 dark:text-white
                                                  ring-1 ring-gray-300/70 dark:ring-white/10
                                                  focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 outline-none">
                                    @error('ends_at') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Audience --}}
                            <div class="rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 p-4">
                                <div class="font-semibold text-gray-900 dark:text-white">Audience (dans l’application)</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Qui verra l’annonce dans la plateforme.</div>

                                <div class="mt-3 space-y-3">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" wire:model="audienceAll" class="rounded border-gray-300 dark:border-gray-600">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">Tout le monde</span>
                                    </label>

                                    @if(!$audienceAll)
                                        <div class="flex flex-wrap gap-3">
                                            <label class="inline-flex items-center gap-2">
                                                <input type="checkbox" value="teacher" wire:model.defer="audienceRoles" class="rounded border-gray-300 dark:border-gray-600">
                                                <span class="text-sm text-gray-700 dark:text-gray-200">Enseignant</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2">
                                                <input type="checkbox" value="student" wire:model.defer="audienceRoles" class="rounded border-gray-300 dark:border-gray-600">
                                                <span class="text-sm text-gray-700 dark:text-gray-200">Étudiant</span>
                                            </label>
                                        </div>
                                        @error('audienceRoles') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    @endif
                                </div>
                            </div>

                            {{-- Email notify --}}
                            <div class="rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">Notification email</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Teachers via users + Students via authorized_emails (is_registered=1).
                                        </div>
                                    </div>

                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" wire:model="sendEmail" class="rounded border-gray-300 dark:border-gray-600">
                                        <span class="text-sm text-gray-700 dark:text-gray-200">Envoyer</span>
                                    </label>
                                </div>

                                @if($sendEmail)
                                    <div class="mt-3 space-y-3">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" wire:model="notifyAll" class="rounded border-gray-300 dark:border-gray-600">
                                            <span class="text-sm text-gray-700 dark:text-gray-200">Tous (Teacher + Student)</span>
                                        </label>

                                        @if(!$notifyAll)
                                            <div class="flex flex-wrap gap-3">
                                                <label class="inline-flex items-center gap-2">
                                                    <input type="checkbox" value="teacher" wire:model.defer="notifyRoles" class="rounded border-gray-300 dark:border-gray-600">
                                                    <span class="text-sm text-gray-700 dark:text-gray-200">Teacher</span>
                                                </label>
                                                <label class="inline-flex items-center gap-2">
                                                    <input type="checkbox" value="student" wire:model.defer="notifyRoles" class="rounded border-gray-300 dark:border-gray-600">
                                                    <span class="text-sm text-gray-700 dark:text-gray-200">Student</span>
                                                </label>
                                            </div>
                                            @error('notifyRoles') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="pt-2 flex items-center justify-end gap-2">
                                <button type="button" wire:click="closeModal"
                                        class="inline-flex items-center justify-center h-10 px-4 rounded-xl text-sm font-semibold
                                               ring-1 ring-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100
                                               dark:ring-white/10 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900 transition">
                                    Annuler
                                </button>

                                <button type="submit"
                                        wire:loading.attr="disabled"
                                        wire:target="save"
                                        class="inline-flex items-center justify-center h-10 px-4 rounded-xl text-sm font-semibold
                                               bg-gray-900 text-white hover:bg-gray-800 disabled:opacity-60 disabled:cursor-not-allowed
                                               dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                                    <span wire:loading.remove wire:target="save">
                                        {{ $announcementId ? 'Mettre à jour' : 'Créer' }}
                                    </span>

                                    <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                        Enregistrement…
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Confirm delete modal --}}
        @if($confirmingDeleteId)
            <div class="fixed inset-0 z-[70] flex items-center justify-center px-4">
                <div class="absolute inset-0 bg-gray-900/50" wire:click="cancelDelete"></div>

                <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-black/5 dark:ring-white/10 shadow-xl p-5">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        Supprimer cette annonce ?
                    </div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Cette action est irréversible.
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button wire:click="cancelDelete"
                                class="h-10 px-4 rounded-xl text-sm font-semibold
                                       ring-1 ring-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100
                                       dark:ring-white/10 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900 transition">
                            Annuler
                        </button>

                        <button wire:click="deleteConfirmed"
                                wire:loading.attr="disabled"
                                wire:target="deleteConfirmed"
                                class="h-10 px-4 rounded-xl text-sm font-semibold
                                       bg-rose-600 text-white hover:bg-rose-700 disabled:opacity-60 disabled:cursor-not-allowed transition">
                            <span wire:loading.remove wire:target="deleteConfirmed">Supprimer</span>
                            <span wire:loading wire:target="deleteConfirmed" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Suppression…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
