<div>
    <!-- En-tête -->
    <div class="flex items-center justify-between mb-4">
        @if(!$showNewFile)
            <label class="text-base font-semibold text-gray-900 dark:text-gray-100">Document actuel</label>
        @else
            <label class="text-base font-semibold text-gray-900 dark:text-gray-100">Nouveau document</label>
        @endif
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Dernière modification : {{ $document->updated_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Fichier actuel -->
    @if(!$showNewFile)
    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-sm mb-6 transition-all duration-200">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                @php
                    $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                @endphp
                @include('livewire.teacher.forms.file-icons', ['extension' => $extension])
                <div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $document->getDisplayFilename() }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $document->formatted_size }}</span>
                    @if($document->wasConverted())
                        <span class="text-xs text-gray-500 dark:text-gray-400 block">
                            Converti de {{ $document->original_extension }} en {{ $document->getExtensionAttribute() }}
                        </span>
                    @endif
                </div>
            </div>
            <button
                onclick="window.open('{{ route('document.serve', $document->id) }}', '_blank')"
                class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-md hover:bg-green-200 dark:hover:bg-green-800 transition-all duration-200"
                title="Prévisualiser">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Voir
            </button>
        </div>
    </div>
    @endif

    <!-- Aperçu nouveau fichier -->
    @if($newFile && $showNewFile)
    <div class="mt-4">
        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-sm transition-all duration-200">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @php
                        $extension = strtolower($newFile->getClientOriginalExtension());
                    @endphp
                    @include('livewire.teacher.forms.file-icons', ['extension' => $extension])
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $newFile->getClientOriginalName() }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ number_format($newFile->getSize() / 1024, 0) }} KB</span>
                    </div>
                </div>
                <button type="button"
                        wire:click="removeNewFile"
                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-all duration-200">
                    Annuler
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Titre du document -->
    <div class="mt-6">
        <label class="block text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">Titre du document</label>
        <input type="text"
               wire:model="title"
               placeholder="Titre du document"
               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all duration-200">
        @error('title')
            <span class="text-sm text-red-600 dark:text-red-400 block mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- Zone d'upload -->
    <div class="mt-6">
        <div class="flex items-center justify-between mb-3">
            <label class="text-base font-semibold text-gray-900 dark:text-gray-100">
                {{ $showNewFile ? 'Nouveau fichier' : 'Remplacer le fichier' }}
            </label>
        </div>
        <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-200">
            <input type="file"
                   wire:model="newFile"
                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors duration-200" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Cliquez ou glissez un fichier ici</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">PDF, Word, PowerPoint, Excel, JPEG, PNG jusqu'à 10MB</p>
            </div>
        </div>
        @error('newFile')
            <span class="text-sm text-red-600 dark:text-red-400 block mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>