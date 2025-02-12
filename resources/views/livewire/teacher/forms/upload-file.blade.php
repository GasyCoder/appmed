<div x-data="uploadZone()" class="space-y-4">
    <div class="flex items-center justify-between mb-3">
        <label class="text-base font-semibold text-gray-700 dark:text-gray-300">Documents</label>
        @if($file)
            <div class="flex items-center space-x-2"
                 x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">
                <span class="text-sm {{ count($file) > 6 ? 'text-red-600' : 'text-green-600' }} font-bold">
                    {{ count($file) }} / 6 document(s) sélectionné(s)
                </span>
                @if(count($file) > 6)
                    <span class="text-xs text-red-600 animate-pulse">
                        (Maximum 6 fichiers autorisés)
                    </span>
                @endif
            </div>
        @endif
    </div>

    <div class="relative border-2 border-dashed rounded-lg p-6"
         :class="{'border-gray-300': !isDragging, 'border-indigo-400 bg-indigo-50': isDragging, 'opacity-50 cursor-not-allowed': isLimitReached}"
         @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false"
         @drop.prevent="handleDrop($event)">

        <input type="file"
               wire:model.live="file"
               multiple
               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpeg,.jpg,.png"
               class="hidden"
               id="file-upload"
               x-ref="fileInput"
               @change="handleFileChange"
               {{ count($file) >= 6 ? 'disabled' : '' }}>

        <div class="text-center"
             :class="{'transform transition-transform duration-200': true, 'scale-95': isDragging}">
            <label for="file-upload"
                   class="relative group"
                   :class="isLimitReached ? 'cursor-not-allowed' : 'cursor-pointer'">
                <div class="transition-transform duration-200 transform group-hover:scale-110">
                    <svg class="mx-auto h-12 w-12 text-gray-400 transition-colors duration-200"
                         :class="{'text-indigo-500': isDragging}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div class="mt-2 transition-all duration-200"
                     :class="{'opacity-0': isDragging}">
                    <p class="text-sm text-gray-600 group-hover:text-indigo-600 transition-colors duration-200">
                        <span x-show="!isLimitReached">
                            Glissez et déposez vos fichiers ici ou cliquez pour sélectionner
                        </span>
                        <span x-show="isLimitReached" class="text-red-600">
                            Limite de 6 fichiers atteinte
                        </span>
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Maximum 10MB par fichier (6 fichiers maximum)
                    </p>
                </div>
                <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-200"
                     x-show="isDragging"
                     x-transition:enter="transition-opacity duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100">
                    <p class="text-lg font-medium text-indigo-600">Déposez vos fichiers ici</p>
                </div>
            </label>
        </div>
    </div>

    @if ($errors->has('file'))
        <p class="mt-1 text-sm text-red-600 animate-shake">{{ $errors->first('file') }}</p>
    @endif

    @foreach ($errors->get('file.*') as $error)
        <p class="mt-1 text-sm text-red-600 animate-shake">{{ $error[0] }}</p>
    @endforeach

    <div wire:loading wire:target="file"
         class="text-center transform transition-all duration-300"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        <span class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 text-sm rounded-full">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="animate-pulse">Préparation des fichiers...</span>
        </span>
    </div>

    @include('livewire.teacher.forms.file-preview')
</div>

@push('styles')
<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>
@endpush
@push('scripts')
<script>
function uploadZone() {
    return {
        isDragging: false,
        isLimitReached: @json(count($file ?? []) >= 6),

        handleDrop(event) {
            if (this.isLimitReached) return;

            this.isDragging = false;
            const files = event.dataTransfer.files;
            this.$refs.fileInput.files = files;
            this.$refs.fileInput.dispatchEvent(new Event('change'));
        },

        handleFileChange(event) {
            const files = event.target.files;
            this.isLimitReached = files.length >= 6;
        },

        init() {
            this.$watch('$wire.file', value => {
                this.isLimitReached = (value?.length ?? 0) >= 6;
            });
        }
    }
}
</script>
@endpush
