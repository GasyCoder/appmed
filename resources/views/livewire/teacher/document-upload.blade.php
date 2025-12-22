<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Téléverser des documents
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Uploadez vos fichiers et liez-les à une UE ou un EC.
            </p>
        </div>

        <a href="{{ route('document.teacher') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>

    <form wire:submit.prevent="uploadDocuments">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- LEFT --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- Destination --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">
                            Destination
                        </h2>
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                            Champs requis (*)
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Niveau --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Niveau <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="niveau_id"
                                    class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sélectionnez</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                                @endforeach
                            </select>
                            @error('ue_id')
                                <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            @if(!empty($niveau_id) && $ues->isEmpty())
                                <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">
                                    Aucune UE trouvée. Vérifiez la table <span class="font-mono">programmes.niveau_id</span>.
                                </p>
                            @endif
                        </div>

                        {{-- UE --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                UE <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="ue_id"
                                    class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    @if(!$niveau_id) disabled @endif>
                                <option value="">Sélectionnez</option>
                                @foreach($ues as $ue)
                                    <option value="{{ $ue->id }}">{{ $ue->code }} — {{ $ue->name }}</option>
                                @endforeach
                            </select>
                            @error('ue_id')
                                <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- EC (optionnel) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                EC (optionnel)
                            </label>
                            <select wire:model.live="ec_id"
                                    class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    @if(!$ue_id || $ecs->isEmpty()) disabled @endif>
                                <option value="">Toute l’UE</option>
                                @foreach($ecs as $ec)
                                    <option value="{{ $ec->id }}">{{ $ec->code }} — {{ $ec->name }}</option>
                                @endforeach
                            </select>
                            @error('ec_id')
                                <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Dropzone --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">
                            Fichiers
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Max {{ \App\Livewire\Teacher\DocumentUpload::MAX_FILES }} fichiers — 10MB/fichier
                        </p>
                    </div>

                    <div class="mt-4"
                         x-data="{
                             dragging: false,
                             drop(e) {
                                this.dragging = false;
                                const dt = new DataTransfer();
                                for (const f of e.dataTransfer.files) dt.items.add(f);
                                this.$refs.fileInput.files = dt.files;
                                this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                             }
                         }"
                         @dragover.prevent="dragging = true"
                         @dragleave.prevent="dragging = false"
                         @drop.prevent="drop($event)">

                        <input x-ref="fileInput"
                               type="file"
                               wire:model.live="files"
                               multiple
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"
                               class="hidden"
                               id="fileInput">

                        <label for="fileInput"
                               class="flex flex-col items-center justify-center w-full rounded-xl border-2 border-dashed p-8 cursor-pointer transition
                                      border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 hover:bg-gray-100 dark:hover:bg-gray-900/40"
                               :class="dragging ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/20' : ''">

                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>

                            <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Cliquez pour choisir vos fichiers
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                ou glissez-déposez ici
                            </p>
                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-500">
                                PDF, Word, PowerPoint, Excel, Images
                            </p>
                        </label>

                        {{-- Loading --}}
                        <div wire:loading wire:target="files"
                             class="mt-3 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Chargement des fichiers…
                        </div>

                        @error('files')
                            <p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDEBAR --}}
            <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-20 h-fit">

                {{-- LISTE FICHIERS (compact) --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
                    <div class="flex items-center justify-between px-3 py-2.5 border-b border-gray-200 dark:border-gray-800">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                            Liste des fichiers
                            <span class="ml-1 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                ({{ $files ? count($files) : 0 }})
                            </span>
                        </p>

                        @if($files && count($files) > 0)
                            <button type="button"
                                    wire:click="$set('files', [])"
                                    class="text-xs font-bold text-red-600 dark:text-red-400 hover:underline">
                                Tout supprimer
                            </button>
                        @endif
                    </div>

                    <div class="px-3 py-3">
                        @if($files && count($files) > 0)
                            <div class="space-y-2.5 max-h-[calc(100vh-18rem)] overflow-y-auto pr-1">
                                @foreach($files as $index => $file)
                                    @php
                                        $ext = strtolower($file->getClientOriginalExtension());
                                        $willConvert = in_array($ext, ['doc', 'docx', 'ppt', 'pptx']);
                                        $sizeKb = round($file->getSize() / 1024, 1);
                                    @endphp

                                    <div class="rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/30 p-3"
                                         wire:key="sidebar-file-{{ $index }}">

                                        <div class="flex items-start gap-2">
                                            <div class="h-8 w-8 rounded-md bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 flex items-center justify-center shrink-0">
                                                <svg class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $file->getClientOriginalName() }}
                                                </p>
                                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="uppercase font-mono">{{ $ext }}</span>
                                                    <span class="mx-1">•</span>
                                                    <span>{{ $sizeKb }} KB</span>
                                                    @if($willConvert)
                                                        <span class="mx-1">•</span>
                                                        <span class="font-bold text-amber-600 dark:text-amber-400">→ PDF</span>
                                                    @endif
                                                </p>
                                            </div>

                                            <button type="button"
                                                    wire:click="removeFile({{ $index }})"
                                                    class="h-8 w-8 inline-flex items-center justify-center rounded-md
                                                           border border-gray-200 dark:border-gray-800
                                                           bg-white dark:bg-gray-950
                                                           text-gray-500 dark:text-gray-300
                                                           hover:text-red-600 dark:hover:text-red-400"
                                                    title="Retirer">
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="mt-2 space-y-2">
                                            <input type="text"
                                                   wire:model.lazy="titles.{{ $index }}"
                                                   placeholder="Titre du document"
                                                   class="w-full h-9 rounded-md border-gray-300 dark:border-gray-700
                                                          bg-white dark:bg-gray-950 text-sm
                                                          text-gray-900 dark:text-gray-100
                                                          focus:border-indigo-500 focus:ring-indigo-500">

                                            @error("titles.$index")
                                                <p class="text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror

                                            <label class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 px-3 py-2">
                                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                    Publier
                                                </span>

                                                <span class="relative inline-flex items-center">
                                                    <input type="checkbox" wire:model="statuses.{{ $index }}" class="peer sr-only">
                                                    <span class="h-5 w-9 rounded-full bg-gray-300 dark:bg-gray-700 peer-checked:bg-emerald-500 transition"></span>
                                                    <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white dark:bg-gray-200 peer-checked:translate-x-4 transition shadow"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/30 p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Aucun fichier sélectionné.
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                    Ajoutez des fichiers via la zone d’upload.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ACTIONS (sticky + bouton pas en bas) --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Actions</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $files ? count($files) : 0 }} fichier(s)
                        </p>
                    </div>

                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('document.teacher') }}"
                           class="w-1/2 inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                            Annuler
                        </a>

                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="uploadDocuments"
                                class="w-1/2 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-bold text-white hover:bg-indigo-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="uploadDocuments">Uploader</span>
                            <span wire:loading wire:target="uploadDocuments" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Upload…
                            </span>
                        </button>
                    </div>

                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        Les fichiers Word/PowerPoint seront automatiquement convertis en PDF.
                    </p>
                </div>

            </aside>
        </div>
    </form>
</div>
