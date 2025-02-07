<form wire:submit="uploadDocument">
    <!-- Grille principale -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Niveau d'enseignement -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="space-y-4">
                <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Niveau d'enseignement
                </label>
 
                <div class="relative">
                    <select
                        wire:model.live="niveau_id"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                               focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 
                               focus:border-indigo-500 dark:focus:border-indigo-400 rounded-md"
                    >
                        <option value="">-- Sélectionner un niveau --</option>
                        @foreach($this->teacherNiveaux as $niveau)
                            <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                        @endforeach
                    </select>
 
                    @if($niveau_id && count($this->semestresActifs) > 0)
                    <div class="mt-2 space-y-2">
                        <!-- En-tête avec le nombre de semestres -->
                        <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/50 px-3 py-2 rounded-md">
                            <span class="text-sm text-green-700 dark:text-green-300">
                                {{ count($this->semestresActifs) }} semestre(s) actif(s)
                            </span>
                            <span class="font-bold text-xs bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 px-2 py-1 rounded-full">
                                @foreach($this->semestresActifs as $semestre)
                                    {{ $semestre->name }},
                                @endforeach
                            </span>
                        </div>
                    </div>
                    @elseif($niveau_id)
                        <div class="mt-2 flex items-center bg-yellow-50 dark:bg-yellow-900/50 px-3 py-2 rounded-md">
                            <svg class="w-5 h-5 text-yellow-400 dark:text-yellow-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-sm text-yellow-700 dark:text-yellow-300">Aucun semestre actif pour ce niveau</span>
                        </div>
                    @endif
 
                    @error('niveau_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
 
        <!-- Parcours -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="space-y-4">
                <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Parcours
                </label>
 
                <div class="relative">
                    <select
                        wire:model.live="parcour_id"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                               focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 
                               focus:border-indigo-500 dark:focus:border-indigo-400 rounded-md
                               disabled:bg-gray-100 dark:disabled:bg-gray-800 
                               disabled:text-gray-500 dark:disabled:text-gray-400"
                        @if(!$niveau_id) disabled @endif
                    >
                        <option value="">-- Sélectionner un parcours --</option>
                        @foreach($this->teacherParcours as $parcour)
                            <option value="{{ $parcour->id }}">{{ $parcour->name }}</option>
                        @endforeach
                    </select>
 
                    @error('parcour_id')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
 
    <!-- Zone de téléversement des fichiers -->
    <div class="mt-6">
        @include('livewire.teacher.forms.upload-file')
    </div>
 
    <!-- Bouton de soumission -->
    <div class="mt-6 flex justify-end border-t dark:border-gray-700 pt-6">
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
            <span wire:loading.remove wire:target="uploadDocument">Téléverser</span>
            <span wire:loading wire:target="uploadDocument">Téléversement en cours...</span>
        </button>
    </div>
 </form>
