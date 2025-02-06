{{--  @include('livewire.teacher.forms.upload-file-edit') --}}
<div>
    <div class="flex items-center justify-between mb-3">
        @if(!$showNewFile)
            <label class="text-base font-semibold">Document actuel</label>
        @else
            <label class="text-base font-semibold">Document noveau</label>
        @endif
        <div class="text-sm text-gray-600">
            Dernière modification: {{ $document->updated_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Fichier actuel -->
    @if(!$showNewFile)
    <div class="p-4 bg-gray-50 rounded-lg shadow-sm mb-6">
        <div class="flex items-center gap-3">
            @php
                $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
            @endphp

            @include('livewire.teacher.forms.file-icons', ['extension' => $extension])

            <div>
                <span class="text-sm text-gray-600">{{ basename($document->file_path) }}</span>
                <span class="text-xs text-gray-400 block">{{ number_format($document->file_size / 1024, 0) }} KB</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Aperçu nouveau fichier -->
    @if($newFile && $showNewFile)
    <div class="mt-4">
        <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @php
                        $extension = strtolower($newFile->getClientOriginalExtension());
                    @endphp

                    @include('livewire.teacher.forms.file-icons', ['extension' => $extension])

                    <div>
                        <span class="text-sm text-gray-600">{{ $newFile->getClientOriginalName() }}</span>
                        <span class="text-xs text-gray-400 block">{{ number_format($newFile->getSize() / 1024, 0) }} KB</span>
                    </div>
                </div>
                <button type="button"
                        wire:click="removeNewFile"
                        class="text-sm text-red-600 hover:text-red-800">
                    Annuler
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Titre du document -->
    <div class="mt-4">
        <label class="block text-base font-semibold mb-2">Titre du document</label>
        <input type="text"
               wire:model="title"
               placeholder="Titre du document"
               class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('title')<span class="text-sm text-red-600 block mt-1">{{ $message }}</span>@enderror
    </div>

    <!-- Zone d'upload -->
    <div class="mt-6">
        <div class="flex items-center justify-between mb-3">
            <label class="text-base font-semibold">
                {{ $showNewFile ? 'Nouveau fichier' : 'Remplacer le fichier' }}
            </label>
        </div>

        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-indigo-500">
            <input type="file"
                   wire:model="newFile"
                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="mt-2 text-sm text-gray-600">Cliquez ou glissez un fichier ici</p>
                <p class="mt-1 text-xs text-gray-500">PDF, Word, PowerPoint, Excel, JPEG, PNG jusqu'à 10MB</p>
            </div>
        </div>

        @error('newFile')<span class="text-sm text-red-600 block mt-1">{{ $message }}</span>@enderror
    </div>
</div>
