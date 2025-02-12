<form wire:submit="uploadDocument">
    <!-- Grille principale -->
    @include('livewire.teacher.forms.level-parcour')
    <!-- Zone de téléversement des fichiers -->
    <div class="mt-6">
        @include('livewire.teacher.forms.upload-file')
    </div>

    <!-- Bouton de soumission -->
    <div class="mt-6 ">
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500
                border border-transparent rounded-md font-semibold text-xs text-white
                uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600
                focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                focus:ring-indigo-500 dark:focus:ring-indigo-400
                disabled:opacity-50 transition"
                wire:loading.attr="disabled">
            <svg wire:loading
                 wire:target="uploadDocument"
                 class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="uploadDocument">Uploader les documents</span>
            <span wire:loading wire:target="uploadDocument">Uploader en cours...</span>
        </button>
    </div>
 </form>
