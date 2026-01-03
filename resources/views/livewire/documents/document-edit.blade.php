<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    @include('livewire.documents.partials.overlay')

    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modifier le document</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                UE/EC proviennent de Programme (UE parent / EC enfant).
            </p>
        </div>

        <a href="{{ route('documents.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
            Retour
        </a>
    </div>

    @if ($errors->has('global'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-200">
            <p class="text-sm font-semibold">{{ $errors->first('global') }}</p>
        </div>
    @endif

    <div wire:loading wire:target="niveau_id,ue_id,ec_id" aria-busy="true" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-6">
            <div class="rounded-xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-800 p-5 space-y-4">
                <x-skeleton.line width="w-32" height="h-5" />
                <div class="space-y-2">
                    <x-skeleton.line width="w-24" height="h-4" />
                    <x-skeleton.line width="w-full" height="h-11" class="rounded-lg" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <x-skeleton.line width="w-24" height="h-4" />
                        <x-skeleton.line width="w-full" height="h-11" class="rounded-lg" />
                    </div>
                    <div class="space-y-2">
                        <x-skeleton.line width="w-20" height="h-4" />
                        <x-skeleton.line width="w-full" height="h-11" class="rounded-lg" />
                    </div>
                    <div class="space-y-2">
                        <x-skeleton.line width="w-24" height="h-4" />
                        <x-skeleton.line width="w-full" height="h-11" class="rounded-lg" />
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-800 p-5 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <x-skeleton.line width="w-24" height="h-5" />
                    <x-skeleton.line width="w-28" height="h-3" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <x-skeleton.button width="w-full" height="h-10" />
                    <x-skeleton.button width="w-full" height="h-10" />
                    <x-skeleton.button width="w-full" height="h-10" />
                </div>
                <x-skeleton.line width="w-full" height="h-16" class="rounded-lg" />
            </div>
        </div>
        <aside class="lg:col-span-4 space-y-4">
            <div class="rounded-xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-800 p-4 space-y-4">
                <x-skeleton.line width="w-20" height="h-4" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-skeleton.button width="w-full" height="h-10" />
                    <x-skeleton.button width="w-full" height="h-10" />
                </div>
                <div class="flex gap-2">
                    <x-skeleton.button width="w-full" height="h-10" />
                    <x-skeleton.button width="w-full" height="h-10" />
                </div>
            </div>
        </aside>
    </div>

    <form wire:submit.prevent="save" wire:loading.remove wire:target="niveau_id,ue_id,ec_id" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-8 space-y-6">

            {{-- Meta --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-5 space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model.defer="title"
                           class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title') <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Niveau -> UE -> EC (Programme) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            Niveau <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="niveau_id"
                                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionnez</option>
                            @foreach($niveaux as $n)
                                <option value="{{ $n->id }}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                        @error('niveau_id') <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            UE <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="ue_id"
                                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                @if(!$niveau_id) disabled @endif>
                            <option value="">Sélectionnez</option>
                            @foreach($ues as $ue)
                                <option value="{{ $ue->id }}">{{ $ue->code }} — {{ $ue->name }}</option>
                            @endforeach
                        </select>
                        @error('ue_id') <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            EC (optionnel)
                        </label>
                        <select wire:model.live="ec_id"
                                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                @if(!$ue_id || $ecs->isEmpty()) disabled @endif>
                            <option value="">Toute l’UE</option>
                            @foreach($ecs as $ec)
                                <option value="{{ $ec->id }}">{{ $ec->code }} — {{ $ec->name }}</option>
                            @endforeach
                        </select>
                        @error('ec_id') <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Source --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Source</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">keep / fichier / lien</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 px-3 py-2">
                        <input type="radio" wire:model.live="replace_mode" value="keep">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Garder</span>
                    </label>
                    <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 px-3 py-2">
                        <input type="radio" wire:model.live="replace_mode" value="file">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Fichier</span>
                    </label>
                    <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 px-3 py-2">
                        <input type="radio" wire:model.live="replace_mode" value="link">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Lien</span>
                    </label>
                </div>

                <div x-data x-show="$wire.replace_mode === 'file'" x-cloak class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Nouveau fichier
                    </label>
                    <input type="file"
                           wire:model.live="file"
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                           class="block w-full text-sm text-gray-700 dark:text-gray-200
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-600 file:text-white
                                  hover:file:bg-indigo-700">
                    <div wire:loading wire:target="file" class="text-sm text-gray-600 dark:text-gray-300">
                        Upload en cours…
                    </div>
                    @error('file') <p class="text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div x-data x-show="$wire.replace_mode === 'link'" x-cloak class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        URL
                    </label>
                    <input type="url"
                           wire:model.defer="source_url"
                           placeholder="https://..."
                           class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                    @error('source_url') <p class="text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/30 p-3 text-sm text-gray-700 dark:text-gray-200">
                    <p class="font-semibold">Source actuelle :</p>
                    <p class="mt-1 break-all">{{ $document->externalUrl() ?: ($document->source_url ?: $document->file_path) }}</p>
                </div>
            </div>
        </div>

        <aside class="lg:col-span-4 space-y-4 lg:sticky lg:top-20 h-fit">
            {{-- ACTIONS --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Actions</p>
                </div>

                {{-- Switches (dans Actions) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Publier --}}
                    <label class="inline-flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-800
                                bg-white dark:bg-gray-800 px-3 py-2
                                hover:bg-gray-50 dark:hover:bg-gray-800/70 transition cursor-pointer">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            Publier
                        </span>

                        <span class="relative inline-flex items-center">
                            <input type="checkbox" wire:model.defer="is_actif" class="peer sr-only">
                            <span class="h-5 w-9 rounded-full bg-gray-300 dark:bg-gray-700 peer-checked:bg-emerald-500 transition
                                        peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-2
                                        dark:peer-focus-visible:ring-offset-gray-900"></span>
                            <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white dark:bg-gray-200
                                        peer-checked:translate-x-4 transition shadow"></span>
                        </span>
                    </label>

                    {{-- Archiver --}}
                    <label class="inline-flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-800
                                bg-white dark:bg-gray-800 px-3 py-2
                                hover:bg-gray-50 dark:hover:bg-gray-800/70 transition cursor-pointer">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            Archiver
                        </span>

                        <span class="relative inline-flex items-center">
                            <input type="checkbox" wire:model.defer="is_archive" class="peer sr-only">
                            <span class="h-5 w-9 rounded-full bg-gray-300 dark:bg-gray-700 peer-checked:bg-amber-500 transition
                                        peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-2
                                        dark:peer-focus-visible:ring-offset-gray-900"></span>
                            <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white dark:bg-gray-200
                                        peer-checked:translate-x-4 transition shadow"></span>
                        </span>
                    </label>
                </div>

                {{-- Boutons --}}
                <div class="flex gap-2">
                    <a href="{{ route('documents.index') }}"
                    class="w-1/2 inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700
                            bg-white dark:bg-gray-900 px-3 py-2 text-sm font-bold text-gray-700 dark:text-gray-200
                            hover:bg-gray-50 dark:hover:bg-gray-800">
                        Annuler
                    </a>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save,file"
                            class="w-1/2 inline-flex items-center justify-center rounded-lg bg-green-600 px-3 py-2
                                text-sm font-bold text-white hover:bg-green-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save,file">Mettre à jour</span>
                        <span wire:loading wire:target="save,file" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sauvegarde…
                        </span>
                    </button>
                </div>
            </div>
        </aside>

    </form>
</div>
