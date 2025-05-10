<div>
    <div class="max-w-5xl mx-auto sm:px-3 lg:px-4 py-3">
        <!-- Bouton Retour -->
        <div class="mb-2">
            <a href="{{ route('document.teacher') }}" wire:navigate
               class="inline-flex items-center px-2 py-2
                   bg-white dark:bg-gray-800
                   border-1 border-gray-200 dark:border-gray-100
                   rounded-md
                   font-semibold text-xs text-gray-800 dark:text-white
                    tracking-widest
                    hover:bg-gray-100 dark:hover:bg-gray-700
                    active:bg-gray-200 dark:active:bg-gray-900
                    focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400
                    focus:ring-offset-2 dark:focus:ring-offset-gray-800
                    transition-all duration-150 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour
            </a>
        </div>

        <!-- Messages de statut -->
        <div>
            @if($successMessage)
            <div x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm leading-5 text-green-800 dark:text-green-100">
                            {{ $successMessage }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" wire:click="resetSuccessMessage" class="inline-flex rounded-md p-1.5 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800/50 transition duration-150 ease-in-out">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($errorMessage)
            <div x-data="{ show: true }"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm leading-5 text-red-800 dark:text-red-100">
                            {{ $errorMessage }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" wire:click="resetErrorMessage" class="inline-flex rounded-md p-1.5 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800/50 transition duration-150 ease-in-out">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Contenu Principal -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
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
                <form wire:submit.prevent="uploadDocument">
                    <!-- Grille principale -->
                    @include('livewire.teacher.forms.level-parcour')

                    <!-- Zone de téléversement des fichiers -->
                    <div class="mt-6">
                        @include('livewire.teacher.forms.upload-file')
                    </div>

                    <!-- Barre de progression améliorée -->
                    @if($isUploading)
                    <div class="mt-6 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div id="upload-progress-bar"
                             class="bg-indigo-600 dark:bg-indigo-500 h-4 rounded-full transition-all duration-300 ease-out"
                             style="width: {{ $uploadProgress }}%"
                             x-data="{ progress: {{ $uploadProgress }} }"
                             x-init="$watch('progress', val => {
                                document.getElementById('upload-progress-bar').style.width = val + '%';
                             })"
                             wire:key="progress-{{ $uploadProgress }}">
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-center text-indigo-700 dark:text-indigo-300">
                        {{ $uploadProgress }}% complété
                    </p>
                    @endif

                    <!-- Bouton de soumission -->
                    <div class="mt-6">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500
                                border border-transparent rounded-md font-semibold text-xs text-white
                                uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600
                                focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                focus:ring-indigo-500 dark:focus:ring-indigo-400
                                disabled:opacity-50 transition"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-75 cursor-wait">
                            <svg wire:loading
                                 wire:target="uploadDocument"
                                 class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="uploadDocument">Uploader les documents</span>
                            <span wire:loading wire:target="uploadDocument">Upload en cours...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('upload-progress-updated', (event) => {
                // Mettre à jour dynamiquement la barre de progression
                console.log('Progress updated:', event.progress);
            });

            @this.on('files-uploaded-successfully', () => {
                // Actions après un upload réussi
                // Reset de l'interface si nécessaire
                if (typeof uploadZone !== 'undefined') {
                    document.querySelectorAll('input[type="file"]').forEach(input => {
                        input.value = '';
                    });
                }

                // Afficher un message de succès animé
                const successMessage = document.querySelector('[x-data="{ show: true }"]');
                if (successMessage) {
                    successMessage.classList.add('animate-pulse');
                    setTimeout(() => {
                        successMessage.classList.remove('animate-pulse');
                    }, 2000);
                }
            });
        });
    </script>
    @endpush
</div>
