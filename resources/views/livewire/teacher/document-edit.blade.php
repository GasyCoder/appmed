<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Bouton Retour --}}
    <div class="mb-4">
        <a href="{{ route('document.teacher') }}" wire:navigate
           class="inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Retour
        </a>
    </div>

    {{-- Messages flash --}}
    <div class="mb-4">
        @include('layouts.partials.flash-msg')
    </div>

    @php
        $isExternal = \Illuminate\Support\Str::startsWith((string) $document->file_path, ['http://','https://'])
            || (string) $document->file_type === 'link'
            || (string) $document->original_extension === 'url';

        $openUrl = $isExternal ? $document->file_path : route('document.serve', $document);
    @endphp

    {{-- Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Modifier le document
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Mettez à jour le titre, le rattachement (UE/EC) et le fichier (local ou lien externe).
                </p>
            </div>

            {{-- Badge état --}}
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                         {{ $is_actif ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300' }}">
                {{ $is_actif ? 'Publié' : 'Non publié' }}
            </span>
        </div>

        <form wire:submit="updateDocument" class="space-y-6">

            {{-- Titre --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    Titre du document <span class="text-red-500">*</span>
                </label>

                <input type="text"
                       wire:model="title"
                       placeholder="Ex: Support de cours — Chapitre 1"
                       class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">

                @error('title')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Paramètres académiques : Niveau / UE / EC --}}
            <div class="bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    Paramètres académiques
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Niveau --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Niveau <span class="text-red-500">*</span>
                        </label>

                        <select wire:model.live="niveau_id"
                                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionnez</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                            @endforeach
                        </select>

                        @error('niveau_id')
                            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- UE --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            UE <span class="text-red-500">*</span>
                        </label>

                        <select wire:model.live="ue_id"
                                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                                @if(empty($niveau_id)) disabled @endif>
                            <option value="">Sélectionnez</option>
                            @foreach($ues as $ue)
                                <option value="{{ $ue->id }}">{{ $ue->code ?? '' }} — {{ $ue->name }}</option>
                            @endforeach
                        </select>

                        @error('ue_id')
                            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        @if(!empty($niveau_id) && $ues->isEmpty())
                            <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">
                                Aucune UE active pour ce niveau.
                            </p>
                        @endif
                    </div>

                    {{-- EC (optionnel) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            EC (optionnel)
                        </label>

                       <select wire:model.live="ec_id"
                                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                                @if($ecs->isEmpty()) disabled @endif>
                            <option value="">Toute l'UE</option>
                            @foreach($ecs as $ec)
                                <option value="{{ $ec->id }}">{{ $ec->code ?? '' }} — {{ $ec->name }}</option>
                            @endforeach
                        </select>

                        @error('ec_id')
                            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- semestre_id est calculé automatiquement côté component --}}
                <input type="hidden" wire:model="semestre_id">
            </div>

            {{-- Fichier --}}
            <div class="bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                            Fichier du document
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Choisissez un fichier local ou un lien externe (Drive, Google Docs export, etc.).
                        </p>
                    </div>

                    <a href="{{ $openUrl }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                              bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                              text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Voir le fichier
                    </a>
                </div>

                {{-- Fichier actuel --}}
                <div class="flex items-center justify-between gap-4 p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $document->original_filename ?: basename($document->file_path) }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                            Type : {{ $document->file_type ?? '—' }}
                            <span class="mx-2 text-gray-300 dark:text-gray-600">•</span>
                            Taille : {{ $document->file_size ? number_format($document->file_size / 1024, 1) . ' KB' : '—' }}
                        </p>

                        @if($isExternal)
                            <p class="mt-1 text-xs text-indigo-600 dark:text-indigo-300 break-all">
                                {{ $document->file_path }}
                            </p>
                        @endif
                    </div>

                    <span class="shrink-0 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                 bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                        Actuel
                    </span>
                </div>

                {{-- Mode fichier --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Source du document <span class="text-red-500">*</span>
                    </label>

                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.live="file_mode" value="local"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                            Fichier local
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input type="radio" wire:model.live="file_mode" value="external"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                            Lien externe (Drive, etc.)
                        </label>
                    </div>

                    @error('file_mode')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lien externe --}}
                @if($file_mode === 'external')
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Lien externe <span class="text-red-500">*</span>
                        </label>

                        <input type="url"
                               wire:model.defer="external_url"
                               placeholder="https://drive.google.com/..."
                               class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">

                        @error('external_url')
                            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                            Conseil : utilisez un lien “export/pdf” ou “uc?export=download&id=...”.
                        </p>
                    </div>
                @endif

                {{-- Upload nouveau fichier --}}
                @if($file_mode === 'local')
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Remplacer par un nouveau fichier (optionnel)
                        </label>

                        <input type="file"
                               wire:model="newFile"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"
                               class="block w-full text-sm text-gray-700 dark:text-gray-200
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-600 file:text-white
                                      hover:file:bg-indigo-700
                                      bg-white dark:bg-gray-900
                                      border border-gray-300 dark:border-gray-700 rounded-lg">

                        @error('newFile')
                            <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Aperçu nouveau fichier --}}
                        @if($showNewFile && $newFile)
                            <div class="mt-3 flex items-start justify-between gap-3 p-3 rounded-lg border border-indigo-200 dark:border-indigo-800 bg-indigo-50/60 dark:bg-indigo-900/10">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $newFile->getClientOriginalName() }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                        Taille : {{ round($newFile->getSize() / 1024, 1) }} KB
                                    </p>
                                </div>

                                <button type="button"
                                        wire:click="removeNewFile"
                                        class="inline-flex items-center justify-center h-9 w-9 rounded-lg
                                               bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                               text-gray-500 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        <div wire:loading wire:target="newFile" class="mt-2 text-sm text-indigo-600 dark:text-indigo-300">
                            Chargement du fichier...
                        </div>
                    </div>
                @endif
            </div>

            {{-- Statut --}}
            <div class="flex items-center gap-3">
                <input wire:model="is_actif"
                       type="checkbox"
                       id="is_actif"
                       class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                <label for="is_actif" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                    Partager le document (visible aux étudiants)
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('document.teacher') }}" wire:navigate
                   class="px-4 py-2 rounded-lg text-sm font-semibold
                          bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                          text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Annuler
                </a>

                <button type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white
                               bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
                    <svg wire:loading wire:target="updateDocument" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span wire:loading.remove wire:target="updateDocument">Mettre à jour</span>
                    <span wire:loading wire:target="updateDocument">Mise à jour...</span>
                </button>
            </div>

        </form>
    </div>
</div>
