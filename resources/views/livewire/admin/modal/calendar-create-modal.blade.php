{{-- livewire.admin.modal.calendar-create-modal --}}
@if($showCreateModal)
<div class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center w-full min-h-screen p-4 text-center sm:block sm:p-0">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"></div>
        {{-- Modal Panel --}}
        <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form wire:submit="{{ $lessonToEdit ? 'updateLesson' : 'createLesson' }}">
            @csrf
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            @if($lessonToEdit)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Modifier le cours</span>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>Nouveau cours</span>
                            @endif
                        </h3>

                        @if($semestres->isNotEmpty())
                            <span class="text-sm bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-3 py-1 rounded-full">
                                {{ $semestres->pluck('name')->join(', ') }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-4">

                        {{-- Niveau et Parcours --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Niveau --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Niveau</label>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($niveaux as $niveau)
                                        <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <input type="radio"
                                                wire:model.live="selectedNiveau"
                                                name="niveau"
                                                value="{{ $niveau->id }}"
                                                class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $niveau->sigle }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedNiveau')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Parcours --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Parcours</label>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($parcours as $parcour)
                                        <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <input type="radio"
                                                wire:model="selectedParcour"
                                                name="parcour"
                                                value="{{ $parcour->id }}"
                                                class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                                @if(!$selectedNiveau) disabled @endif>
                                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $parcour->sigle }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedParcour')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        {{-- Type de cours et Enseignant --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Type de cours --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Mode d'enseignement
                                </label>
                                <div>
                                    <label class="inline-flex items-center p-2 border rounded-md cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <input type="checkbox"
                                            wire:model.live="typeCours"
                                            name="type_cours"
                                            value="VC"
                                            @if($typeCours === 'VC') checked @endif
                                            class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Visio Conférence
                                        </span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Mode actuel : {{ $typeCours === 'VC' ? 'Visio Conférence' : 'Cours Magistral' }}
                                    </p>
                                </div>
                                @error('typeCours')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Enseignant --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enseignant</label>
                                <select wire:model="selectedTeacher"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none">
                                    <option value="">Sélectionner</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->getFullNameWithGradeAttribute() }}</option>
                                    @endforeach
                                </select>
                                @error('selectedTeacher')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Programme (UE et EC) --}}
                        <div class="space-y-4">
                            {{-- Sélection de l'UE --}}
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Unité d'Enseignement (UE)
                                </label>
                                <select wire:model.live="selectedUe"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none">
                                    <option value="">Choisir une UE</option>
                                    @if(isset($programmes['ues']))
                                        @foreach($programmes['ues'] as $ue)
                                            <option value="{{ $ue->id }}" class="py-2">
                                                {{ $ue->code }} - {{ $ue->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('selectedUe')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Sélection de l'EC --}}
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Élément Constitutif (EC)
                                </label>
                                <select wire:model="selectedProgramme"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none">
                                    <option value="">Choisir un EC</option>
                                    @if($selectedUe && isset($programmes['ues']))
                                        @php
                                            $selectedUeData = $programmes['ues']->firstWhere('id', $selectedUe);
                                        @endphp
                                        @if($selectedUeData)
                                            @foreach($selectedUeData->elements as $ec)
                                                <option value="{{ $ec->id }}" class="py-2">
                                                    {{ $ec->code }} - {{ $ec->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                                @error('selectedProgramme')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Horaires --}}
                        <div class="grid grid-cols-3 gap-4">
                            {{-- Jour --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jour</label>
                                <select wire:model="weekday"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none">
                                    <option value="">Sélectionner</option>
                                    @foreach($weekDays as $key => $day)
                                        <option value="{{ $key }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                                @error('weekday')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Heure début --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Début</label>
                                <input type="time" wire:model="startTime"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none">
                                @error('startTime')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Heure fin --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fin</label>
                                <input type="time" wire:model="endTime"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none">
                                @error('endTime')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Salle --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salle</label>
                            <input type="text" wire:model="salle"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none"
                                   placeholder="Ex: Amphi A">
                            @error('salle')
                                <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Couleur (deuxième bloc) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Couleur du cours
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="color"
                                    wire:model="color"
                                    class="p-1 h-10 w-14 block bg-white border border-gray-200 cursor-pointer rounded-lg
                                            disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-800 dark:border-gray-700"
                                    title="Choisir une couleur">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $color }}
                                </span>
                            </div>
                            @error('color')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea wire:model="description" rows="2"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-3 pr-10 py-2 appearance-none"
                                    placeholder="Description optionnelle..."></textarea>
                            @error('description')
                                <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2
                           bg-indigo-600 dark:bg-indigo-500 text-base font-medium text-white
                           hover:bg-indigo-700 dark:hover:bg-indigo-600
                           focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                           focus:ring-indigo-500 dark:focus:ring-indigo-400
                           sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            wire:loading.attr="disabled">
                        <span>{{ $lessonToEdit ? 'Mettre à jour' : 'Créer' }}</span>
                        <svg wire:loading class="animate-spin ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                    <button type="button" wire:click="resetForm"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600
                                   shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium
                                   text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                   focus:ring-indigo-500 dark:focus:ring-indigo-400
                                   sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endif
