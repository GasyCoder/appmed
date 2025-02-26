{{-- Modal Création/Édition --}}
@if($showSemestreModal)
    <div wire:ignore.self class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            {{-- Modal Panel --}}
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="saveSemestre">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            {{ $semestreId ? 'Modifier le semestre' : 'Nouveau semestre' }}
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                                <input
                                    type="text"
                                    wire:model="name"
                                    id="name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                @error('name')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="niveau_id" class="block text-sm font-medium text-gray-700">Niveau</label>
                                <select
                                    wire:model="niveau_id"
                                    id="niveau_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">Sélectionner un niveau</option>
                                    @foreach(\App\Models\Niveau::all() as $niveau)
                                        <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                                    @endforeach
                                </select>
                                @error('niveau_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="is_active"
                                    id="is_active"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                >
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Semestre actif
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="status"
                                    id="status"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                >
                                <label for="status" class="ml-2 block text-sm text-gray-900">
                                    Status
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                wire:loading.attr="disabled">
                            {{ $semestreId ? 'Mettre à jour' : 'Créer' }}
                            <svg wire:loading wire:target="saveSemestre" class="animate-spin ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>

                        <button type="button"
                                wire:click="resetForm"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
