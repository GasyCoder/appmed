<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <!-- Bouton Retour -->
        <div class="mb-2">
            <a href="{{ route('document.teacher') }}" wire:navigate
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest 
                       hover:bg-gray-700 dark:hover:bg-gray-600 
                       focus:bg-gray-700 dark:focus:bg-gray-600 
                       active:bg-gray-900 dark:active:bg-gray-800 
                       focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 
                       focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour
            </a>
        </div>
 
        <!-- Messages Flash -->
        <div>
            @include('layouts.partials.flash-msg')
        </div>
 
        <!-- Contenu Principal -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <!-- En-tête -->
                    <div class="pb-0">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Téléversement de documents
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Veuillez remplir les informations suivantes pour téléverser vos documents.
                        </p>
                    </div>
 
                    <!-- Info Tooltip -->
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @mouseenter="open = true" 
                            @mouseleave="open = false"
                            class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" 
                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" 
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                        
                        <!-- Tooltip Content -->
                        <div x-show="open" 
                             class="absolute right-0 mt-2 bg-white dark:bg-gray-700 p-2 rounded shadow-lg 
                                    text-sm text-gray-600 dark:text-gray-300 w-64 z-50
                                    border border-gray-100 dark:border-gray-600">
                            Types de fichiers acceptés: PDF, Word, PowerPoint, Excel, JPEG, PNG
                            <br>Taille maximale: 10MB
                        </div>
                    </div>
                </div>
 
                <!-- Formulaire -->
                @include('livewire.teacher.forms.file-form')
            </div>
        </div>
    </div>
 </div>
