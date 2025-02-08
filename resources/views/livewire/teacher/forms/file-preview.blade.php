{{-- file-preview.blade.php --}}
@if($file)
<div wire:loading.remove wire:target="file" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($file as $index => $uploadedFile)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200">
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        @php
                            $extension = strtolower($uploadedFile->getClientOriginalExtension());
                            $fileSize = number_format($uploadedFile->getSize() / 1024, 0);
                            $fileName = $uploadedFile->getClientOriginalName();
                        @endphp
                        {{-- Optimisation des icônes en les incluant directement --}}
                        <div class="w-8 h-8 flex-shrink-0">
                        @include('livewire.teacher.forms.file-icons')
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $fileName }}">
                                {{ Str::limit($fileName, 25) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $fileSize }} KB
                            </p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="removeFile({{ $index }})"
                            class="text-gray-400 hover:text-red-500 focus:outline-none">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h2V8H8zm4 0v10h2V8h-2z"/>
                        </svg>
                    </button>
                </div>

                <div class="relative">
                    <input type="text"
                           wire:model.blur="titles.{{ $index }}"
                           placeholder="Titre du document"
                           class="block w-full rounded-md border-gray-300 dark:border-gray-600 focus:border-indigo-500 text-sm">
                </div>

                <div class="mt-2 flex items-center">
                    <input type="checkbox"
                           wire:model.blur="file_status.{{ $index }}"
                           class="rounded border-gray-300 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Partager</span>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Indicateur de chargement amélioré --}}
<div wire:loading wire:target="file" class="my-4">
    <div class="flex justify-center">
        <div class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Préparation des fichiers...</span>
        </div>
    </div>
</div>
@endif