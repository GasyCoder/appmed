<div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-6">
    <!-- Bouton Retour -->
    <div class="mb-4">
        <a href="{{ route('document.teacher') }}" wire:navigate
           class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Retour
        </a>
    </div>

    <!-- Messages flash -->
    <div class="mb-4">
        @include('layouts.partials.flash-msg')
    </div>

    <!-- Formulaire principal -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6 transition-all duration-300">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Modifier le document</h2>
        
        <form wire:submit="updateDocument" class="space-y-6">
            <!-- Sélection niveau, parcours, semestres -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Paramètres académiques</h3>
                @include('livewire.teacher.forms.level-parcour')
            </div>

            <!-- Upload fichier -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Fichier du document</h3>
                @include('livewire.teacher.forms.upload-file-edit')
            </div>

            <!-- Statut actif -->
            <div class="flex items-center gap-3">
                <input wire:model="is_actif"
                       type="checkbox"
                       id="is_actif"
                       class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition duration-150">
                <label for="is_actif" class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Partager le document
                </label>
            </div>

            <!-- Bouton soumettre -->
            <div class="flex justify-end mt-6">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 disabled:opacity-50 transition-all duration-200"
                        wire:loading.attr="disabled">
                    <svg wire:loading wire:target="updateDocument" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="updateDocument">Mettre à jour</span>
                    <span wire:loading wire:target="updateDocument">Mise à jour en cours...</span>
                </button>
            </div>
        </form>
    </div>
</div>