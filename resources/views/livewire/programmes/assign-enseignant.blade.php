<div>
    {{-- Modal --}}
    @if($showModal)
        <div 
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
            style="display: none;"
        >
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div 
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/75 transition-opacity" 
                    @click="$wire.closeModal()"
                ></div>

                {{-- Center modal --}}
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                {{-- Modal panel --}}
                <div 
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block transform overflow-hidden rounded-2xl border border-gray-200 bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle dark:border-gray-800 dark:bg-gray-900"
                >
                    
                    {{-- Header --}}
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-5 dark:border-gray-800 dark:bg-gray-900/60">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $enseignantActuel ? 'Modifier' : 'Assigner' }} l'enseignant
                                </h3>
                                @if($programme)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ $programme->code }}</span> - {{ $programme->name }}
                                    </p>
                                    @if($programme->parent)
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            UE : {{ $programme->parent->code }} - {{ $programme->parent->name }}
                                        </p>
                                    @endif
                                @endif
                                
                                {{-- Avertissement si un enseignant existe déjà --}}
                                @if($enseignantActuel)
                                    <div class="mt-3 flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 dark:bg-amber-900/20">
                                        <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                            Enseignant actuel : {{ $enseignantActuel->full_name_with_grade ?? $enseignantActuel->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <button type="button" wire:click="closeModal" 
                                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>


                    {{-- Body --}}
                    <form wire:submit.prevent="assignEnseignant" class="px-6 py-5 space-y-5">
                        {{-- Sélection enseignant --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Enseignant <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="selectedEnseignant" 
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                                    required>
                                <option value="">Sélectionnez un enseignant</option>
                                @foreach($enseignants as $ens)
                                    <option value="{{ $ens->id }}">
                                        {{ $ens->full_name_with_grade ?? $ens->name }} ({{ $ens->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedEnseignant') 
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Heures d'enseignement --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Heures CM <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       wire:model="heures_cm" 
                                       min="0" 
                                       max="200"
                                       class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                                       required>
                                @error('heures_cm') 
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Heures TD <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       wire:model="heures_td" 
                                       min="0" 
                                       max="200"
                                       class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                                       required>
                                @error('heures_td') 
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Heures TP <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       wire:model="heures_tp" 
                                       min="0" 
                                       max="200"
                                       class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                                       required>
                                @error('heures_tp') 
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="rounded-lg bg-indigo-50 p-4 dark:bg-indigo-900/20">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-indigo-900 dark:text-indigo-200">
                                    Total heures d'enseignement
                                </span>
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $heures_cm + $heures_td + $heures_tp }}h
                                </span>
                            </div>
                        </div>

                        {{-- Responsable --}}
                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       wire:model="is_responsable"
                                       class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Définir comme enseignant responsable
                                </span>
                            </label>
                            <p class="mt-1 ml-8 text-sm text-gray-500 dark:text-gray-400">
                                L'enseignant responsable coordonne cet EC
                            </p>
                        </div>

                        {{-- Note --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Note ou remarque (optionnel)
                            </label>
                            <textarea wire:model="note" 
                                      rows="3"
                                      maxlength="500"
                                      class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
                                      placeholder="Ajoutez des informations complémentaires..."></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ strlen($note ?? '') }}/500 caractères
                            </p>
                        </div>
                    </form>

                    {{-- Footer --}}
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/60">
                        <div class="flex items-center justify-between gap-3">
                            {{-- Bouton retirer (si un enseignant existe) --}}
                            @if($enseignantActuel)
                                <button type="button" 
                                        wire:click="retirerEnseignant"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 dark:border-red-900 dark:bg-gray-900 dark:text-red-400 dark:hover:bg-red-900/20">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Retirer l'enseignant
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div class="flex items-center gap-3">
                                <button type="button" 
                                        wire:click="closeModal"
                                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                                    Annuler
                                </button>
                                <button type="button" 
                                        wire:click="assignEnseignant"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="assignEnseignant">
                                        {{ $enseignantActuel ? 'Modifier' : 'Assigner' }} l'enseignant
                                    </span>
                                    <span wire:loading wire:target="assignEnseignant" class="flex items-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        En cours...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>