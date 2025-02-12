<div>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-3">
        <!-- Bouton Retour -->
        <div class="mb-2">
            <a href="{{ route('document.teacher') }}" wire:navigate
                class="inline-flex items-center px-2 py-2
                    bg-white dark:bg-gray-800
                    border-1 border-gray-200 dark:border-gray-100
                    rounded-md
                    font-semibold text-xs text-gray-800 dark:text-white
                    tracking-widest
                    hover:bg-gray-100 dark:hover:bg-gray-700
                    active:bg-gray-200 dark:active:bg-gray-900
                    focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400
                    focus:ring-offset-2 dark:focus:ring-offset-gray-800
                    transition-all duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                Retour
            </a>
        </div>
        <div>
            @include('layouts.partials.flash-msg')
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-50">Modifier le document</h2>
                </div>
                <form wire:submit="updateDocument" class="space-y-6">
                    <!-- Formulaire principal -->
                    @include('livewire.teacher.forms.level-parcour')

                    @include('livewire.teacher.forms.upload-file-edit')
                    <!-- Is Actif -->
                    <div class="flex items-center gap-2">
                        <input wire:model="is_actif"
                               type="checkbox"
                               id="is_actif"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="is_actif" class="text-sm text-gray-700">
                            Partager document
                        </label>
                    </div>
                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
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
    </div>
</div>
