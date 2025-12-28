{{-- Modal Création/Édition --}}
@if($showParcourModal)
    <div class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            {{-- Modal Panel --}}
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                   <form wire:submit.prevent="saveParcour">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ $parcourId ? 'Modifier le parcour' : 'Nouveau parcour' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                                    <input
                                        type="text"
                                        wire:model="name"
                                        id="name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                    @error('name')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="sigle" class="block text-sm font-medium text-gray-700">Sigle</label>
                                    <input
                                        type="text"
                                        wire:model="sigle"
                                        id="sigle"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                    @error('sigle')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        wire:model="status"
                                        id="status"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    >
                                    <label for="status" class="ml-2 block text-sm text-gray-900">
                                        Parcour actif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <!-- Submit Button -->
                            <button type="submit"
                                    class="inline-flex justify-center sm:ml-3 px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500"
                                    wire:target="saveParcour"
                                    wire:loading.attr="disabled">
                                {{ $parcourId ? 'Mettre à jour' : 'Créer' }}
                                <svg wire:loading wire:target="saveLevel" class="animate-spin ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                            </button>

                            <!-- Cancel Button -->
                            <button type="button"
                                    wire:click="resetForm"
                                    class="mt-3 sm:mt-0 px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500">
                                Annuler
                            </button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
    @endif