{{-- Interface d'upload moderne avec logique niveau simple --}}
<div class="max-w-5xl mx-auto sm:px-3 lg:px-4 py-3">
    <!-- Bouton Retour -->
    <div class="mb-6">
        <a href="{{ route('document.teacher') }}" wire:navigate
           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour aux documents
        </a>
    </div>

    <!-- En-tête avec info -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mb-2">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            Téléverser des documents
        </h1>
        <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Uploadez vos fichiers au niveau du niveau - les documents Word et PowerPoint seront automatiquement convertis en PDF
        </p>
    </div>

    <!-- Messages d'alerte -->
    @if($successMessage)
    <div class="mb-6 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm">
        <div class="flex">
            <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-700 dark:text-green-200 font-medium">{{ $successMessage }}</p>
        </div>
    </div>
    @endif

    @if($errorMessage)
    <div class="mb-6 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm">
        <div class="flex">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-red-700 dark:text-red-200 font-medium">{{ $errorMessage }}</p>
        </div>
    </div>
    @endif

    <form wire:submit="uploadDocument" class="space-y-8">
        <!-- Configuration du cours -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Configuration du cours</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Sélectionnez le niveau et parcours</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Niveau et Parcours -->
                @include('livewire.teacher.forms.level-parcour')
            </div>
        </div>

        <!-- Zone d'upload moderne -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
             x-data="modernUploadZone()">
            
            <!-- En-tête de la zone d'upload -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Documents à uploader</h2>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">1 fichier = 1 entrée au niveau du niveau</p>
                        </div>
                    </div>
                    
                    <!-- Compteur de fichiers -->
                    @if($file && count($file) > 0)
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm font-medium">
                            {{ count($file) }} fichier{{ count($file) > 1 ? 's' : '' }}
                        </div>
                        @php 
                            $needsConversion = collect($file)->filter(function($f) {
                                return in_array(strtolower($f->getClientOriginalExtension()), ['docx', 'pptx', 'doc', 'ppt']);
                            })->count(); 
                        @endphp
                        @if($needsConversion > 0)
                        <div class="bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 px-3 py-1 rounded-full text-sm font-medium flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ $needsConversion }} à convertir
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <!-- Zone de drop élégante -->
                <div class="relative border-2 border-dashed rounded-xl p-8 text-center transition-all duration-300"
                     :class="{
                         'border-gray-300 dark:border-gray-600 hover:border-blue-400 hover:bg-blue-50/30 dark:hover:bg-blue-900/10': !isDragging && !$wire.isUploading,
                         'border-blue-500 bg-blue-50 dark:bg-blue-900/20 scale-102': isDragging,
                         'border-green-500 bg-green-50 dark:bg-green-900/20': $wire.isUploading
                     }"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)"
                     wire:loading.class="pointer-events-none">
                    
                    <input type="file" 
                           wire:model.live="file" 
                           multiple 
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpeg,.jpg,.png"
                           class="hidden" 
                           id="fileUpload"
                           x-ref="fileInput">
                    
                    <div @click="$refs.fileInput.click()" class="cursor-pointer">
                        <!-- Icône centrale avec animation -->
                        <div class="mb-6">
                            <div class="relative inline-block">
                                <svg class="w-20 h-20 mx-auto transition-all duration-300"
                                     :class="{
                                         'text-gray-400 dark:text-gray-500': !isDragging && !$wire.isUploading,
                                         'text-blue-500 scale-110': isDragging,
                                         'text-green-500 animate-pulse': $wire.isUploading
                                     }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                
                                <!-- Badge de conversion -->
                                <div x-show="$wire.isUploading" 
                                     class="absolute -top-2 -right-2 w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Messages dynamiques -->
                        <div class="space-y-3">
                            <h3 class="text-2xl font-bold transition-colors duration-300"
                                :class="{
                                    'text-gray-900 dark:text-white': !isDragging && !$wire.isUploading,
                                    'text-blue-600': isDragging,
                                    'text-green-600': $wire.isUploading
                                }">
                                <span x-show="!isDragging && !$wire.isUploading">Sélectionnez vos fichiers</span>
                                <span x-show="isDragging">Déposez vos fichiers ici</span>
                                <span x-show="$wire.isUploading">Conversion en cours...</span>
                            </h3>
                            
                            <p class="text-gray-600 dark:text-gray-400 text-lg">
                                <span x-show="!$wire.isUploading">Glissez-déposez ou cliquez pour choisir</span>
                                <span x-show="$wire.isUploading">Traitement et conversion des fichiers</span>
                            </p>
                            
                            <!-- Formats supportés avec icônes -->
                            <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-gray-500 dark:text-gray-400 mt-6">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 18h12V6l-4-4H4v16z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">PDF</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 18h12V6l-4-4H4v16z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Word → PDF</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 18h12V6l-4-4H4v16z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">PowerPoint → PDF</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Images</span>
                                </div>
                            </div>
                            
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-4">
                                Maximum 10MB par fichier • 6 fichiers maximum • 1 fichier = 1 entrée
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prévisualisation moderne des fichiers -->
                @if($file && count($file) > 0)
                <div class="mt-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Fichiers sélectionnés</h3>
                        <button type="button" 
                                wire:click="$set('file', [])"
                                class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                            Tout supprimer
                        </button>
                    </div>
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach($file as $index => $uploadedFile)
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 p-5 group hover:shadow-lg hover:scale-102 transition-all duration-200">
                            
                            <!-- En-tête du fichier avec conversion -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3 min-w-0 flex-1">
                                    @php
                                        $extension = strtolower($uploadedFile->getClientOriginalExtension());
                                        $willConvert = in_array($extension, ['docx', 'pptx', 'doc', 'ppt']);
                                        $fileSize = round($uploadedFile->getSize() / 1024, 1);
                                    @endphp
                                    
                                    <!-- Icône avec badge de conversion -->
                                    <div class="relative flex-shrink-0">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-200
                                            @if($extension === 'pdf') bg-red-100 dark:bg-red-900/30 group-hover:bg-red-200 dark:group-hover:bg-red-900/50
                                            @elseif(in_array($extension, ['doc', 'docx'])) bg-blue-100 dark:bg-blue-900/30 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50
                                            @elseif(in_array($extension, ['ppt', 'pptx'])) bg-orange-100 dark:bg-orange-900/30 group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50
                                            @elseif(in_array($extension, ['jpg', 'jpeg', 'png'])) bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-900/50
                                            @else bg-gray-100 dark:bg-gray-900/30 group-hover:bg-gray-200 dark:group-hover:bg-gray-900/50
                                            @endif">
                                            
                                            @if($extension === 'pdf')
                                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6l-4-4H4v16z"/>
                                                </svg>
                                            @elseif(in_array($extension, ['doc', 'docx']))
                                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6l-4-4H4v16z"/>
                                                </svg>
                                            @elseif(in_array($extension, ['ppt', 'pptx']))
                                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6l-4-4H4v16z"/>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6l-4-4H4v16z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        
                                        @if($willConvert)
                                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center border-2 border-white dark:border-gray-800">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Informations du fichier -->
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate text-sm">
                                            {{ pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME) }}
                                        </p>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span class="bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded uppercase font-mono">
                                                {{ $extension }}
                                            </span>
                                            <span>{{ $fileSize }} KB</span>
                                            @if($willConvert)
                                            <div class="flex items-center text-amber-600 dark:text-amber-400 font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                                <span>PDF</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Bouton supprimer -->
                                <button type="button" 
                                        wire:click="removeFile({{ $index }})"
                                        class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all duration-200 p-2 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Champs de saisie -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Titre du document
                                    </label>
                                    <input type="text" 
                                           wire:model.live="titles.{{ $index }}"
                                           placeholder="Entrez le titre du document..."
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition-all">
                                    @error("titles.$index")
                                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <label class="flex items-center space-x-3 cursor-pointer group">
                                    <input type="checkbox" 
                                           wire:model.live="file_status.{{ $index }}"
                                           class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        Publier immédiatement
                                    </span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Barre de progression -->
        @if($isUploading)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Traitement en cours</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Conversion et upload des fichiers...</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $uploadProgress }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Progression</div>
                </div>
            </div>
            
            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 via-green-500 to-green-600 h-full transition-all duration-500 ease-out"
                     style="width: {{ $uploadProgress }}%"></div>
            </div>
        </div>
        @endif

        <!-- Actions finales -->
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0 bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                @if($file && count($file) > 0)
                    <div class="flex items-center space-x-4">
                        <span class="font-medium">{{ count($file) }} fichier(s) prêt(s)</span>
                        @php $conversionCount = collect($file)->filter(fn($f) => in_array(strtolower($f->getClientOriginalExtension()), ['docx', 'pptx', 'doc', 'ppt']))->count(); @endphp
                        @if($conversionCount > 0)
                            <span class="text-amber-600 dark:text-amber-400 font-medium">
                                • {{ $conversionCount }} seront convertis en PDF
                            </span>
                        @endif
                        
                        <!-- Affichage simplifié pour logique niveau -->
                        <span class="text-green-600 dark:text-green-400 font-medium">
                            • {{ count($file) }} entrée(s) seront créées (1 par fichier)
                        </span>
                    </div>
                @else
                    <span>Aucun fichier sélectionné</span>
                @endif
            </div>
            
            <button type="submit" 
                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-lg"
                    wire:loading.attr="disabled">
                
                <svg wire:loading wire:target="uploadDocument" 
                     class="animate-spin -ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <span wire:loading.remove wire:target="uploadDocument">
                    🚀 Uploader au niveau
                </span>
                <span wire:loading wire:target="uploadDocument">
                    Traitement en cours...
                </span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function modernUploadZone() {
    return {
        isDragging: false,
        
        handleDrop(event) {
            event.preventDefault();
            this.isDragging = false;
            
            const files = event.dataTransfer.files;
            this.$refs.fileInput.files = files;
            this.$refs.fileInput.dispatchEvent(new Event('change'));
        },
        
        init() {
            // Écouter les événements de progression
            this.$wire.on('upload-progress-updated', (event) => {
                console.log('Upload progress:', event.progress);
            });
            
            this.$wire.on('conversion-status-updated', (event) => {
                console.log('Conversion status:', event);
            });

            // Écouter les changements de niveau
            this.$wire.on('niveau-changed', (event) => {
                console.log('Niveau changed:', event);
            });
        }
    }
}
</script>

<style>
.scale-102 {
    transform: scale(1.02);
}

.hover\:scale-102:hover {
    transform: scale(1.02);
}
</style>
@endpush