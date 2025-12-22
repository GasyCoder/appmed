<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Programme Master MERC
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Master en Épidémiologie et Recherche Clinique</span>
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-3">
                    <button wire:click="toggleShowEnseignants" 
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ $showEnseignants ? 'Masquer' : 'Afficher' }} les enseignants
                    </button>
                    
                    @can('manage programmes')
                    <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-lg text-sm font-medium shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nouveau Programme
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Year Filter Pills -->
        <div class="mb-6 flex items-center space-x-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtrer par année :</span>
            <div class="inline-flex rounded-lg bg-white dark:bg-gray-800 p-1 shadow-sm border border-gray-200 dark:border-gray-700">
                <button wire:click="$set('annee', null)" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ $annee === null ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Toutes
                </button>
                <button wire:click="$set('annee', 4)" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ $annee === 4 ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    4<sup>ème</sup> année
                </button>
                <button wire:click="$set('annee', 5)" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ $annee === 5 ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    5<sup>ème</sup> année
                </button>
            </div>
        </div>

        <!-- Stats Dashboard -->
        <div class="mb-8 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Total UEs -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                    <dt class="text-sm font-medium text-blue-100 mb-1">Total UEs</dt>
                    <dd class="text-3xl font-bold text-white">{{ $stats['totalUE'] }}</dd>
                </div>
            </div>

            <!-- Total ECs -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <dt class="text-sm font-medium text-emerald-100 mb-1">Total ECs</dt>
                    <dd class="text-3xl font-bold text-white">{{ $stats['totalEC'] }}</dd>
                </div>
            </div>

            <!-- Semestre 1 -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-lg">
                            <span class="text-xl font-bold text-white">S1</span>
                        </div>
                    </div>
                    <dt class="text-sm font-medium text-purple-100 mb-1">Semestre 1</dt>
                    <dd class="text-2xl font-bold text-white">
                        {{ $stats['semestre1']['ue'] }} <span class="text-sm font-normal">UEs</span>
                    </dd>
                    <dd class="text-sm text-purple-100 mt-1">{{ $stats['semestre1']['ec'] }} ECs</dd>
                </div>
            </div>

            <!-- Semestre 2 -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-lg">
                            <span class="text-xl font-bold text-white">S2</span>
                        </div>
                    </div>
                    <dt class="text-sm font-medium text-amber-100 mb-1">Semestre 2</dt>
                    <dd class="text-2xl font-bold text-white">
                        {{ $stats['semestre2']['ue'] }} <span class="text-sm font-normal">UEs</span>
                    </dd>
                    <dd class="text-sm text-amber-100 mt-1">{{ $stats['semestre2']['ec'] }} ECs</dd>
                </div>
            </div>

            <!-- Semestre 3 -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-lg">
                            <span class="text-xl font-bold text-white">S3</span>
                        </div>
                    </div>
                    <dt class="text-sm font-medium text-pink-100 mb-1">Semestre 3</dt>
                    <dd class="text-2xl font-bold text-white">
                        {{ $stats['semestre3']['ue'] }} <span class="text-sm font-normal">UEs</span>
                    </dd>
                    <dd class="text-sm text-pink-100 mt-1">{{ $stats['semestre3']['ec'] }} ECs</dd>
                </div>
            </div>

            <!-- Semestre 4 -->
            <div class="group relative overflow-hidden bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-lg">
                            <span class="text-xl font-bold text-white">S4</span>
                        </div>
                    </div>
                    <dt class="text-sm font-medium text-cyan-100 mb-1">Semestre 4</dt>
                    <dd class="text-2xl font-bold text-white">
                        {{ $stats['semestre4']['ue'] }} <span class="text-sm font-normal">UEs</span>
                    </dd>
                    <dd class="text-sm text-cyan-100 mt-1">{{ $stats['semestre4']['ec'] }} ECs</dd>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rechercher
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               class="block w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                               placeholder="Rechercher une UE, EC ou enseignant...">
                        @if($search)
                            <button wire:click="$set('search', '')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Semestre Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Semestre
                    </label>
                    <select wire:model.live="semestre" 
                            class="block w-full py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                        <option value="">Tous les semestres</option>
                        @if($annee === null || $annee == 4)
                            <option value="1">Semestre 1 (4ème année)</option>
                            <option value="2">Semestre 2 (4ème année)</option>
                        @endif
                        @if($annee === null || $annee == 5)
                            <option value="3">Semestre 3 (5ème année)</option>
                            <option value="4">Semestre 4 (5ème année)</option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $semestre || $annee)
                <div class="mt-4 flex items-center space-x-2 flex-wrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Filtres actifs :</span>
                    
                    @if($search)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                            Recherche: "{{ Str::limit($search, 20) }}"
                            <button wire:click="$set('search', '')" class="ml-2 hover:text-indigo-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    @if($annee)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                            {{ $annee }}ème année
                            <button wire:click="$set('annee', null)" class="ml-2 hover:text-purple-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    @if($semestre)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200">
                            Semestre {{ $semestre }}
                            <button wire:click="$set('semestre', null)" class="ml-2 hover:text-emerald-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </span>
                    @endif
                    
                    <button wire:click="$set('search', ''); $set('semestre', null); $set('annee', null);" 
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
                        Réinitialiser tous
                    </button>
                </div>
            @endif
        </div>

        <!-- UEs List -->
        <div class="space-y-4" wire:loading.class="opacity-50">
            @forelse($programmes as $ue)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300" 
                     x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    
                    <!-- UE Header -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-750 border-b border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                                <div class="flex items-center space-x-4 flex-1 min-w-0">
                                    <!-- UE Code Badge -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-lg">
                                            {{ $ue->code }}
                                        </span>
                                    </div>
                                    
                                    <!-- UE Name -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2 md:line-clamp-1">
                                            {{ $ue->name }}
                                        </h3>
                                    </div>
                                    
                                    <!-- Meta Info -->
                                    <div class="hidden lg:flex items-center space-x-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Semestre {{ $ue->semestre_id }}
                                        </span>
                                        
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            {{ $ue->elements->count() }} EC{{ $ue->elements->count() > 1 ? 's' : '' }}
                                        </span>
                                        
                                        @if($ue->credits)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                                {{ $ue->getTotalCredits() }} ECTS
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Toggle Icon -->
                                <button type="button" class="ml-4 p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <svg class="h-6 w-6 transform transition-transform duration-300"
                                         :class="{ 'rotate-180': open }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Mobile Meta Info -->
                            <div class="lg:hidden mt-3 flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                    S{{ $ue->semestre_id }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200">
                                    {{ $ue->elements->count() }} EC{{ $ue->elements->count() > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ECs List -->
                    <div x-show="open" 
                         x-collapse
                         class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($ue->elements as $ec)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors duration-150">
                                <div class="flex items-start justify-between gap-4">
                                    <!-- EC Info -->
                                    <div class="flex items-start space-x-4 flex-1 min-w-0">
                                        <div class="flex-shrink-0 mt-1.5">
                                            <div class="h-2 w-2 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <!-- EC Header -->
                                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                                    {{ $ec->code }}
                                                </span>
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $ec->name }}
                                                </h4>
                                            </div>

                                            <!-- EC Metadata -->
                                            <div class="flex flex-wrap items-center gap-3 mb-4 text-sm text-gray-600 dark:text-gray-400">
                                                @if($ec->credits)
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                        </svg>
                                                        {{ $ec->credits }} ECTS
                                                    </span>
                                                @endif
                                                
                                                @php
                                                    $heuresDetail = $ec->getHeuresDetail();
                                                    $totalHeures = array_sum($heuresDetail);
                                                @endphp
                                                
                                                @if($totalHeures > 0)
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $totalHeures }}h total
                                                        @if($heuresDetail['cm'] > 0) · {{ $heuresDetail['cm']}}h CM @endif
                                                        @if($heuresDetail['td'] > 0) · {{ $heuresDetail['td']}}h TD @endif
                                                        @if($heuresDetail['tp'] > 0) · {{ $heuresDetail['tp']}}h TP @endif
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Enseignants Section -->
                                            @if($showEnseignants)
                                                @if($ec->enseignants->isNotEmpty())
                                                    <div class="space-y-3">
                                                        @foreach($ec->enseignants as $enseignant)
                                                            <div class="group relative flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700 dark:to-gray-750 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                                                                <div class="flex items-center space-x-4 flex-1 min-w-0">
                                                                    <!-- Avatar -->
                                                                    <div class="flex-shrink-0">
                                                                        <div class="relative">
                                                                            <img src="{{ $enseignant->profile_photo_url }}" 
                                                                                 alt="{{ $enseignant->name }}"
                                                                                 class="h-12 w-12 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                                                                            @if($enseignant->pivot->is_responsable)
                                                                                <span class="absolute -top-1 -right-1 flex h-5 w-5">
                                                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                                                    <span class="relative inline-flex rounded-full h-5 w-5 bg-amber-500 items-center justify-center">
                                                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                                        </svg>
                                                                                    </span>
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Enseignant Info -->
                                                                    <div class="flex-1 min-w-0">
                                                                        <div class="flex items-center space-x-2 mb-1">
                                                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                                                {{ $enseignant->full_name_with_grade }}
                                                                            </p>
                                                                            @if($enseignant->pivot->is_responsable)
                                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gradient-to-r from-amber-400 to-amber-500 text-white shadow-sm">
                                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                                    </svg>
                                                                                    Responsable
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                                            </svg>
                                                                            <span class="truncate">{{ $enseignant->email }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Heures -->
                                                                <div class="hidden md:flex items-center space-x-4 text-sm">
                                                                    @if($enseignant->pivot->heures_cm > 0)
                                                                        <div class="text-center">
                                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">CM</div>
                                                                            <div class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-md font-semibold">
                                                                                {{ $enseignant->pivot->heures_cm }}h
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($enseignant->pivot->heures_td > 0)
                                                                        <div class="text-center">
                                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">TD</div>
                                                                            <div class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 rounded-md font-semibold">
                                                                                {{ $enseignant->pivot->heures_td }}h
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($enseignant->pivot->heures_tp > 0)
                                                                        <div class="text-center">
                                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">TP</div>
                                                                            <div class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-md font-semibold">
                                                                                {{ $enseignant->pivot->heures_tp }}h
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    <div class="pl-4 border-l border-gray-300 dark:border-gray-600">
                                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total</div>
                                                                        <div class="px-3 py-1 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-md font-bold shadow-sm">
                                                                            {{ $enseignant->pivot->heures_cm + $enseignant->pivot->heures_td + $enseignant->pivot->heures_tp }}h
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Mobile Heures -->
                                                                <div class="md:hidden mt-3 flex flex-wrap gap-2">
                                                                    @if($enseignant->pivot->heures_cm > 0)
                                                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded text-xs font-medium">
                                                                            CM: {{ $enseignant->pivot->heures_cm }}h
                                                                        </span>
                                                                    @endif
                                                                    @if($enseignant->pivot->heures_td > 0)
                                                                        <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 rounded text-xs font-medium">
                                                                            TD: {{ $enseignant->pivot->heures_td }}h
                                                                        </span>
                                                                    @endif
                                                                    @if($enseignant->pivot->heures_tp > 0)
                                                                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded text-xs font-medium">
                                                                            TP: {{ $enseignant->pivot->heures_tp }}h
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Note -->
                                                            @if($enseignant->pivot->note)
                                                                <div class="ml-16 -mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600 rounded-r-lg">
                                                                    <p class="text-sm text-gray-700 dark:text-gray-300 italic">
                                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                        </svg>
                                                                        {{ $enseignant->pivot->note }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center p-6 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border-2 border-dashed border-yellow-300 dark:border-yellow-700 rounded-lg">
                                                        <div class="text-center">
                                                            <svg class="mx-auto h-12 w-12 text-yellow-400 dark:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                            </svg>
                                                            <p class="mt-2 text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                                                Aucun enseignant assigné
                                                            </p>
                                                            <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-300">
                                                                Cet élément constitutif n'a pas encore d'enseignant
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    @can('manage programmes')
                                        <div class="flex-shrink-0">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" 
                                                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                    </svg>
                                                </button>
                                                
                                                <div x-show="open" 
                                                     @click.away="open = false"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                     class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 z-10">
                                                    <div class="py-1">
                                                        <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                            </svg>
                                                            Modifier l'EC
                                                        </a>
                                                        <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                            Assigner enseignant
                                                        </a>
                                                    </div>
                                                    <div class="py-1">
                                                        <a href="#" class="group flex items-center px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                            <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Supprimer
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun EC</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cette UE ne contient pas encore d'éléments constitutifs</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-md">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Aucun programme trouvé</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        Aucun programme ne correspond à vos critères de recherche.<br>
                        Essayez de modifier vos filtres ou de réinitialiser la recherche.
                    </p>
                    @if($search || $semestre || $annee)
                        <button wire:click="$set('search', ''); $set('semestre', null); $set('annee', null);" 
                                class="mt-6 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Réinitialiser les filtres
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8">
                <div class="flex items-center space-x-4">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-lg font-medium text-gray-900 dark:text-white">Chargement en cours...</span>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($programmes->hasPages())
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 px-6 py-4">
                    {{ $programmes->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Alpine.js Components -->
@push('scripts')
<script>
    // Auto-collapse when clicking outside
    document.addEventListener('alpine:init', () => {
        Alpine.data('dropdown', () => ({
            open: false,
            toggle() {
                this.open = !this.open
            }
        }))
    })
</script>
@endpush