{{-- livewire.teacher.forms.upload-file --}}
<div class="space-y-4">
    <div class="flex items-center justify-between mb-3">
        <label class="text-base font-semibold text-gray-700 dark:text-gray-300">Documents</label>
        @if($file)
            <div class="flex items-center space-x-2">
                <span class="text-sm {{ count($file) > 6 ? 'text-red-600' : 'text-green-600' }} font-bold">
                    {{ count($file) }} / 6 document(s) sélectionné(s)
                </span>
                @if(count($file) > 6)
                    <span class="text-xs text-red-600">
                        (Maximum 6 fichiers autorisés)
                    </span>
                @endif
            </div>
        @endif
    </div>

    <div class="relative border-2 border-dashed rounded-lg p-6 border-gray-300 
                {{ count($file) >= 6 ? 'opacity-50 cursor-not-allowed' : '' }}">
        <input type="file"
               wire:model="file"
               multiple
               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpeg,.jpg,.png"
               class="hidden"
               id="file-upload"
               {{ count($file) >= 6 ? 'disabled' : '' }}>

        <div class="text-center">
            <label for="file-upload" class="{{ count($file) >= 6 ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="mt-2 text-sm text-gray-600">
                    @if(count($file) >= 6)
                        Limite de 6 fichiers atteinte
                    @else
                        Cliquez pour sélectionner vos fichiers
                    @endif
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    Maximum 10MB par fichier (6 fichiers maximum)
                </p>
            </label>
        </div>
    </div>

    @if ($errors->has('file'))
        <p class="mt-1 text-sm text-red-600">{{ $errors->first('file') }}</p>
    @endif

    @foreach ($errors->get('file.*') as $error)
        <p class="mt-1 text-sm text-red-600">{{ $error[0] }}</p>
    @endforeach

    <div wire:loading wire:target="file" class="text-center">
        <span class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 text-sm rounded-full">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Préparation des fichiers...
        </span>
    </div>

    @include('livewire.teacher.forms.file-preview')
</div>