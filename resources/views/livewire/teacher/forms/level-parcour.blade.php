{{-- livewire.teacher.forms.level-parcour --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @push('styles')
    <style>
        /* Style personnalisé pour les radios */
        .custom-radio {
            appearance: none;
            cursor: pointer;
            position: relative;
        }
        
        .custom-radio:checked {
            background-color: #16a34a;
            border-color: #16a34a;
        }
        
        .custom-radio:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endpush
    
    <!-- Niveau d'enseignement -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="space-y-4">
            <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                <svg class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Niveau d'enseignement
                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                    ({{ $this->teacherNiveaux->count() }} disponible{{ $this->teacherNiveaux->count() > 1 ? 's' : '' }})
                </span>
            </label>

            <div class="space-y-2">
                @if($this->teacherNiveaux->isEmpty())
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-400 dark:text-yellow-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-sm text-yellow-700 dark:text-yellow-300">
                                Aucun niveau d'enseignement assigné. Contactez l'administrateur.
                            </span>
                        </div>
                    </div>
                @else
                    @foreach($this->teacherNiveaux as $niveau)
                        <label class="inline-flex items-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 p-2 rounded-lg transition-colors">
                            <input type="radio"
                                   wire:model.live="niveau_id"
                                   value="{{ $niveau->id }}"
                                   class="ml-4 custom-radio w-5 h-5 text-green-600 border-2 border-gray-300 focus:ring-green-500 focus:ring-2 transition-colors duration-200">
                            <div class="ml-3 flex-1">
                                <span class="text-base font-medium text-gray-700 dark:text-gray-300">
                                    {{ $niveau->name }}
                                </span>
                                @if($niveau->sigle)
                                    <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full">
                                        {{ $niveau->sigle }}
                                    </span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                @endif
            </div>

            <!-- Indicateur de chargement pour les semestres -->
            <div wire:loading wire:target="niveau_id" class="mt-3">
                <div class="flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/50 px-3 py-2 rounded-md">
                    <svg class="animate-spin h-4 w-4 text-indigo-500 dark:text-indigo-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-indigo-700 dark:text-indigo-300">Chargement des semestres...</span>
                </div>
            </div>

            <!-- Affichage des semestres (caché pendant le chargement) -->
            <div wire:loading.remove wire:target="niveau_id">
                @if($niveau_id && count($this->semestresActifs) > 0)
                    <div class="mt-3 space-y-2 fade-in">
                        <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/50 px-3 py-2 rounded-md border border-green-200 dark:border-green-800">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-green-700 dark:text-green-300 font-medium">
                                    {{ count($this->semestresActifs) }} semestre(s) actif(s)
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($this->semestresActifs as $semestre)
                                    <span class="text-xs bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 px-2 py-1 rounded-full">
                                        {{ $semestre->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($niveau_id)
                    <div class="mt-3 flex items-center bg-yellow-50 dark:bg-yellow-900/50 px-3 py-2 rounded-md border border-yellow-200 dark:border-yellow-800">
                        <svg class="w-5 h-5 text-yellow-400 dark:text-yellow-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-sm text-yellow-700 dark:text-yellow-300">Aucun semestre actif pour ce niveau</span>
                    </div>
                @endif
            </div>

            @error('niveau_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
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
                @if($niveau_id)
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                        ({{ $this->teacherParcours->count() }} disponible{{ $this->teacherParcours->count() > 1 ? 's' : '' }})
                    </span>
                @endif
            </label>

            <!-- Indicateur de chargement pour les parcours -->
            <div wire:loading wire:target="niveau_id" class="mt-2">
                <div class="flex items-center justify-center bg-purple-50 dark:bg-purple-900/50 px-3 py-2 rounded-md">
                    <svg class="animate-spin h-4 w-4 text-purple-500 dark:text-purple-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-purple-700 dark:text-purple-300">Chargement des parcours...</span>
                </div>
            </div>

            <div wire:loading.remove wire:target="niveau_id" class="space-y-3">
                @if(!$niveau_id)
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-3 py-4 rounded-md border-2 border-dashed border-gray-300 dark:border-gray-600 text-center">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Veuillez d'abord sélectionner un niveau d'enseignement
                        </p>
                    </div>
                @elseif($this->teacherParcours->isEmpty())
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-400 dark:text-yellow-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-sm text-yellow-700 dark:text-yellow-300">
                                Aucun parcours disponible pour ce niveau d'enseignement
                            </span>
                        </div>
                    </div>
                @else
                    <div class="fade-in">
                        @foreach($this->teacherParcours as $parcour)
                            <label class="inline-flex items-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 p-2 rounded-lg transition-colors {{ !$niveau_id ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <input type="radio"
                                       wire:model.live="parcour_id"
                                       value="{{ $parcour->id }}"
                                       {{ !$niveau_id ? 'disabled' : '' }}
                                       class="ml-4 custom-radio w-5 h-5 text-green-600 border-2 border-gray-300 focus:ring-green-500 focus:ring-2 transition-colors duration-200 {{ !$niveau_id ? 'opacity-50' : '' }}">
                                <div class="ml-3 flex-1">
                                    <span class="text-base font-medium text-gray-700 dark:text-gray-300">
                                        {{ $parcour->name }}
                                    </span>
                                    @if($parcour->sigle)
                                        <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full">
                                            {{ $parcour->sigle }}
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            @error('parcour_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>
</div>

{{-- Script pour les événements Livewire --}}
@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('niveau-changed', (event) => {
        console.log('Niveau changé:', event);
        // Vous pouvez ajouter des actions supplémentaires ici
    });
    
    Livewire.on('parcour-changed', (event) => {
        console.log('Parcours changé:', event);
        // Vous pouvez ajouter des actions supplémentaires ici
    });
});
</script>
@endpush