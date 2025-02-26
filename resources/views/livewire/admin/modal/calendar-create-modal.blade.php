{{-- livewire.admin.modal.calendar-create-modal --}}
@if($showCreateModal)
<div class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        {{-- Overlay avec effet de flou --}}
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 backdrop-blur-sm dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>

        {{-- Modal Panel avec animation --}}
        <div class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl dark:bg-gray-800 rounded-2xl">
            <form wire:submit="{{ $lessonToEdit ? 'updateLesson' : 'createLesson' }}">
                    @csrf
                    {{-- En-tête du modal --}}
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-100 rounded-lg dark:bg-indigo-900">
                                @if($lessonToEdit)
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $lessonToEdit ? 'Modifier emploi du temps' : 'Nouveau emploi du temps' }}
                                </h2>
                                @if($semestres->isNotEmpty())
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Semestre actif : {{ $semestres->pluck('name')->join(', ') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6">
                    {{-- Section Horaires et Dates --}}
                        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700/50">
                            <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                                Dates
                            </h3>
                            <div class="grid gap-6">
                                {{-- Dates --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date de début</label>
                                        <div class="relative">
                                            <input type="date"
                                                wire:model="startDate"
                                                min="{{ now()->format('Y-m-d') }}"
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('startDate')
                                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date de fin</label>
                                        <div class="relative">
                                            <input type="date"
                                                wire:model="endDate"
                                                min="{{ $startDate }}"
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('endDate')
                                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700/50">
                            <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                                Niveau/Parcours
                            </h3>
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
                        </div>
                        {{-- Enseignant --}}
                        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700/50">
                            <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                                Enseignant
                            </h3>
                            <div class="space-y-2">
                                <select wire:model="selectedTeacher"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">Sélectionner un enseignant</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">
                                            {{ $teacher->getFullNameWithGradeAttribute() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedTeacher')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- UE et EC en parallèle --}}
                        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700/50">
                            <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                                Programme
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                {{-- UE --}}
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Unité d'Enseignement (UE)
                                    </label>
                                    <div class="relative">
                                        <select wire:model.live="selectedUe"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">Choisir une UE</option>
                                            @if(isset($programmes['ues']))
                                                @foreach($programmes['ues'] as $ue)
                                                    <option value="{{ $ue->id }}">
                                                        {{ $ue->code }} - {{ $ue->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>

                                        <div wire:loading wire:target="selectedNiveau, selectedParcour"
                                            class="absolute right-0 top-0 bottom-0 flex items-center pr-3 pointer-events-none">
                                            <div class="flex items-center">
                                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    @error('selectedUe')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- EC --}}
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Élément Constitutif (EC)
                                    </label>
                                    <div class="relative">
                                        <select wire:model="selectedProgramme"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <option value="">Choisir un EC</option>
                                            @if($selectedUe && isset($programmes['ues']))
                                                @php
                                                    $selectedUeData = $programmes['ues']->firstWhere('id', $selectedUe);
                                                @endphp
                                                @if($selectedUeData)
                                                    @foreach($selectedUeData->elements as $ec)
                                                        <option value="{{ $ec->id }}">
                                                            {{ $ec->code }} - {{ $ec->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </select>

                                        <div wire:loading wire:target="selectedUe"
                                            class="absolute right-0 top-10 bottom-0 flex items-center pr-3 pointer-events-none">
                                            <div class="flex items-center">
                                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    @error('selectedProgramme')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section Horaires --}}
                        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700/50">
                            <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                                Horaires
                            </h3>
                            <div class="grid gap-6">
                                {{-- Horaires --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Jour</label>
                                        <select wire:model="weekday"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        <option value="">Sélectionner</option>
                                        @foreach($weekDays as $key => $day)
                                            <option value="{{ $key }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                    @error('weekday')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Heure début</label>
                                    <input type="time"
                                        wire:model="startTime"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    @error('startTime')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Heure fin</label>
                                    <input type="time"
                                        wire:model="endTime"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    @error('endTime')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section Détails supplémentaires --}}
                        <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700/50">
                            <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                                Détails supplémentaires
                            </h3>
                            <div class="grid gap-6">
                                {{-- Salle --}}
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Salle</label>
                                    <div class="relative">
                                        <input type="text"
                                            wire:model="salle"
                                            placeholder="Ex: Amphi A"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-10">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('salle')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Couleur et  Section Mode d'enseignement  --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
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
                                    {{-- Mode d'enseignement --}}
                                    <div class="space-y-2">
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
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button"
                            wire:click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Annuler
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                            wire:loading.attr="disabled">
                            <span>{{ $lessonToEdit ? 'Mettre à jour' : 'Ajouter' }}</span>
                            <svg wire:loading class="w-4 h-4 ml-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
            </form>
    </div>
</div>
</div>
@endif
