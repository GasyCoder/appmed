<div>
    {{-- Modal de confirmation --}}
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
                    class="relative inline-block transform overflow-hidden rounded-2xl border border-red-200 bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle dark:border-red-900 dark:bg-gray-900"
                >
                    
                    {{-- Icon de warning --}}
                    <div class="bg-red-50 px-6 pt-6 dark:bg-red-900/20">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center">
                            Confirmer la suppression
                        </h3>
                        
                        @if($programme)
                            <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Vous êtes sur le point de supprimer :
                                </p>
                                <p class="mt-2 text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $programme->code }} - {{ $programme->name }}
                                </p>
                                @if($programme->parent)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        UE parente : {{ $programme->parent->code }}
                                    </p>
                                @endif
                                
                                @if($programme->enseignants->count() > 0)
                                    <p class="mt-3 flex items-center gap-2 text-sm text-amber-600 dark:text-amber-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        {{ $programme->enseignants->count() }} enseignant(s) assigné(s) seront détachés
                                    </p>
                                @endif
                            </div>

                            <p class="mt-4 text-center text-sm text-red-600 dark:text-red-400">
                                ⚠️ Cette action est irréversible !
                            </p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-900/60">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                                Annuler
                            </button>
                            <button type="button" 
                                    wire:click="deleteProgramme"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50">
                                <span wire:loading.remove wire:target="deleteProgramme">Oui, supprimer</span>
                                <span wire:loading wire:target="deleteProgramme" class="flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Suppression...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>