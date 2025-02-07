<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <a href="{{ route('document.teacher') }}" wire:navigate
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour
            </a>
        </div>

        <div>
            @include('layouts.partials.flash-msg')
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold">Modifier le document</h2>
                </div>

                <form wire:submit="updateDocument" class="space-y-6">
                    <!-- Formulaire principal -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <!-- Niveau & Parcours -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Niveau d'enseignement -->
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
                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md dark:bg-gray-700 dark:text-gray-300"
                                    >
                                        <option value="">-- Sélectionner un niveau --</option>
                                        @foreach($this->teacherNiveaux as $niveau)
                                            <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('niveau_id')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if($niveau_id && count($this->semestresActifs) > 0)
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between bg-green-50 dark:bg-green-900 px-3 py-2 rounded-md">
                                            <span class="text-sm text-green-700 dark:text-green-300">
                                                {{ count($this->semestresActifs) }} semestre(s) actif(s)
                                            </span>
                                            <span class="text-xs bg-green-100 dark:bg-green-800 font-bold text-green-800 dark:text-green-300 px-2 py-1 rounded-full">
                                                @foreach($this->semestresActifs as $semestre)
                                                    {{ $semestre->name }} ,
                                                @endforeach
                                            </span>
                                        </div>
                                    </div>
                                @elseif($niveau_id)
                                    <div class="flex items-center bg-yellow-50 dark:bg-yellow-900 px-3 py-2 rounded-md">
                                        <svg class="w-5 h-5 text-yellow-400 dark:text-yellow-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span class="text-sm text-yellow-700 dark:text-yellow-300">Aucun semestre actif pour ce niveau</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Parcours -->
                            <div class="space-y-4">
                                <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Parcours
                                </label>

                                <select
                                    wire:model="parcour_id"
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md dark:bg-gray-700 dark:text-gray-300"
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
                    <div class="flex justify-end">
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
