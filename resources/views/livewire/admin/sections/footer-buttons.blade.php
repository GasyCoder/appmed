<!-- Footer avec boutons -->
<div class="mt-6 flex items-center justify-between border-t pt-4">
    <!-- Bouton Annuler -->
    <button type="button"
            wire:click="resetForm"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Annuler
    </button>

    <div class="flex items-center gap-3">
        <!-- Bouton Précédent -->
        <button type="button"
                x-show="activeTab !== 'info'"
                @click="activeTab = activeTab === 'niveaux' ? 'profile' : 'info'"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Précédent
        </button>

        <!-- Bouton Suivant -->
        <button type="button"
                x-show="activeTab !== 'niveaux'"
                @click="activeTab = activeTab === 'info' ? 'profile' : 'niveaux'"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Suivant
            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <!-- Bouton Créer -->
        <button type="submit"
                x-show="activeTab === 'niveaux'"
                x-cloak
                wire:loading.attr="disabled"
                wire:target="createTeacher"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed min-w-[150px]">
            <div wire:loading.flex wire:target="createTeacher" class="items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Traitement...</span>
            </div>
            <span wire:loading.remove wire:target="createTeacher">
                {{ $userId ? 'Mettre à jour' : 'Créer' }}
            </span>
        </button>
    </div>
</div>
