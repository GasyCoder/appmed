{{-- livewire.teacher.forms.upload-file --}}
<div>
    <div class="flex items-center justify-between mb-3">
        <label class="text-base font-semibold text-gray-700 dark:text-gray-300">Documents</label>
        @if($file)
            <span class="text-sm text-green-600">{{ count($file) }} document(s) sélectionné(s)</span>
        @endif
    </div>

    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-indigo-500 transition-colors">
        <input type="file"
               wire:model.live="file"
               multiple
               accept=".pdf,.doc,.docx,.dotx,.dot,.ppt,.pptx,.xls,.xlsx,.jpeg,.jpg,.png"
               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="mt-2 text-sm text-gray-600">Cliquez ou glissez vos fichiers ici</p>
            <p class="mt-1 text-xs text-gray-500">PDF, Word, PowerPoint, Excel, JPEG, PNG jusqu'à 10MB</p>
        </div>
    </div>

    @error('file.*')<span class="text-sm text-red-600 block mt-1">{{ $message }}</span>@enderror

    <div wire:loading wire:target="file" class="mt-4">
        <!-- Message de chargement -->
        <div class="text-center mb-4">
            <span class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 text-sm rounded-full">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Préparation des fichiers...
            </span>
        </div>
    </div>

    @if($file)
    <div class="mt-4 space-y-4" wire:loading.remove wire:target="file">
        @foreach($file as $index => $uploadedFile)
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    @php
                        $extension = strtolower($uploadedFile->getClientOriginalExtension());
                    @endphp
                    @include('livewire.teacher.forms.file-icons')
                    <div>
                        <span class="text-sm text-gray-600">{{ $uploadedFile->getClientOriginalName() }}</span>
                        <span class="text-xs text-gray-400 block">{{ number_format($uploadedFile->getSize() / 1024, 0) }} KB</span>
                    </div>
                </div>
                <input type="text"
                       wire:model.live="titles.{{ $index }}"
                       placeholder="Titre du document"
                       required
                       class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('titles.' . $index)<span class="text-sm text-red-600 block mt-1">{{ $message }}</span>@enderror
                <!-- Checkbox pour chaque fichier -->
                <div class="flex items-center gap-2">
                    <input
                        wire:model.live="file_status.{{ $index }}"
                        type="checkbox"
                        id="is_actif_{{ $index }}"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm"
                    >
                    <label for="is_actif_{{ $index }}" class="text-sm text-gray-700">Partager</label>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
