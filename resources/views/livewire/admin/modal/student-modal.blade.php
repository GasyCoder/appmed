@if($showUserModal)
<div class="fixed inset-0 z-50">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-[1px]" wire:click="resetForm"></div>

    {{-- Dialog --}}
    <div class="relative mx-auto flex min-h-screen max-w-3xl items-center justify-center p-4">
        <div class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">

            {{-- Header --}}
            <div class="p-6 border-b border-gray-100 dark:border-white/10">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $userId ? "Modifier l'étudiant" : "Nouvel étudiant" }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Renseignez les informations, puis enregistrez.
                        </p>
                    </div>

                    <button wire:click="resetForm"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                   bg-white ring-1 ring-gray-200 hover:bg-gray-50
                                   dark:bg-gray-900/40 dark:ring-white/10 dark:hover:bg-white/5
                                   text-gray-700 dark:text-gray-200 transition"
                            title="Fermer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="createStudent" class="p-6" x-data="{ tab: 'personal' }">
                {{-- Tabs --}}
                <div class="flex items-center gap-2 rounded-2xl bg-gray-50 dark:bg-gray-900/40 ring-1 ring-gray-200 dark:ring-white/10 p-1">
                    <button type="button"
                            @click="tab='personal'"
                            class="flex-1 px-3 py-2 rounded-xl text-sm font-semibold transition"
                            :class="tab==='personal'
                                ? 'bg-white text-gray-900 ring-1 ring-black/5 dark:bg-gray-800 dark:text-white dark:ring-white/10'
                                : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'">
                        Informations personnelles
                    </button>

                    <button type="button"
                            @click="tab='pedago'"
                            class="flex-1 px-3 py-2 rounded-xl text-sm font-semibold transition"
                            :class="tab==='pedago'
                                ? 'bg-white text-gray-900 ring-1 ring-black/5 dark:bg-gray-800 dark:text-white dark:ring-white/10'
                                : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'">
                        Pédagogique
                    </button>
                </div>

                <x-validation-errors class="mt-4" />

                {{-- Tab: Personal --}}
                <div class="mt-5 space-y-4" x-show="tab==='personal'">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Name --}}
                        <div class="sm:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nom complet</label>
                            <input type="text"
                                   wire:model.defer="name"
                                   class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                          ring-1 ring-gray-300/70 dark:ring-white/10
                                          bg-white dark:bg-gray-900/40
                                          text-gray-900 dark:text-white
                                          placeholder:text-gray-400 dark:placeholder:text-gray-500
                                          focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                   placeholder="Nom et prénom">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="sm:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email"
                                   wire:model.defer="email"
                                   class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                          ring-1 ring-gray-300/70 dark:ring-white/10
                                          bg-white dark:bg-gray-900/40
                                          text-gray-900 dark:text-white
                                          placeholder:text-gray-400 dark:placeholder:text-gray-500
                                          focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                   placeholder="ex: prenom.nom@umg.mg">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Telephone --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                            <input type="text"
                                   wire:model.defer="telephone"
                                   class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                          ring-1 ring-gray-300/70 dark:ring-white/10
                                          bg-white dark:bg-gray-900/40
                                          text-gray-900 dark:text-white
                                          placeholder:text-gray-400 dark:placeholder:text-gray-500
                                          focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                   placeholder="+261 ...">
                            @error('telephone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Genre --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Genre</label>
                            <select wire:model.defer="sexe"
                                    class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                           ring-1 ring-gray-300/70 dark:ring-white/10
                                           bg-white dark:bg-gray-900/40
                                           text-gray-900 dark:text-white
                                           focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none">
                                <option value="">—</option>
                                <option value="homme">Homme</option>
                                <option value="femme">Femme</option>
                            </select>
                            @error('sexe') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Département --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Département</label>
                            <input type="text"
                                   wire:model.defer="departement"
                                   class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                          ring-1 ring-gray-300/70 dark:ring-white/10
                                          bg-white dark:bg-gray-900/40
                                          text-gray-900 dark:text-white
                                          focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                   placeholder="Département">
                            @error('departement') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ville</label>
                            <input type="text"
                                   wire:model.defer="ville"
                                   class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                          ring-1 ring-gray-300/70 dark:ring-white/10
                                          bg-white dark:bg-gray-900/40
                                          text-gray-900 dark:text-white
                                          focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                   placeholder="Ville">
                            @error('ville') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Adresse --}}
                        <div class="sm:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Adresse</label>
                            <textarea wire:model.defer="adresse"
                                      rows="3"
                                      class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                             ring-1 ring-gray-300/70 dark:ring-white/10
                                             bg-white dark:bg-gray-900/40
                                             text-gray-900 dark:text-white
                                             placeholder:text-gray-400 dark:placeholder:text-gray-500
                                             focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none"
                                      placeholder="Adresse complète..."></textarea>
                            @error('adresse') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="sm:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                            <div class="mt-2 flex items-center gap-3">
                                <button type="button"
                                        wire:click="$toggle('status')"
                                        class="relative inline-flex h-6 w-11 rounded-full transition
                                               focus:outline-none focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20
                                               {{ $status ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600' }}"
                                        role="switch">
                                    <span class="pointer-events-none inline-block h-5 w-5 translate-y-0.5 rounded-full bg-white shadow transition
                                                {{ $status ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                </button>

                                <span class="text-sm font-medium {{ $status ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-300' }}">
                                    {{ $status ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Pedago --}}
                <div class="mt-5 space-y-4" x-show="tab==='pedago'">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Niveau</label>
                        <select wire:model.defer="niveau_id"
                                class="mt-1 block w-full px-3 py-2.5 rounded-xl border-0
                                       ring-1 ring-gray-300/70 dark:ring-white/10
                                       bg-white dark:bg-gray-900/40
                                       text-gray-900 dark:text-white
                                       focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20 focus:outline-none">
                            <option value="">Sélectionner un niveau</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                            @endforeach
                        </select>
                        @error('niveau_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Le parcours est géré automatiquement (si vous n’avez qu’un seul parcours).
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-white/10 pt-4">
                    <button type="button"
                            wire:click="resetForm"
                            class="inline-flex items-center justify-center h-11 px-4 rounded-xl text-sm font-semibold
                                   bg-white ring-1 ring-gray-200 hover:bg-gray-50
                                   dark:bg-gray-900/40 dark:ring-white/10 dark:hover:bg-white/5
                                   text-gray-700 dark:text-gray-200 transition">
                        Annuler
                    </button>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="createStudent"
                            class="inline-flex items-center justify-center h-11 px-5 rounded-xl text-sm font-semibold
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                   disabled:opacity-60 disabled:cursor-not-allowed transition">
                        <svg wire:loading wire:target="createStudent" class="h-5 w-5 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>

                        <span>{{ $userId ? 'Enregistrer' : 'Créer le compte' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
