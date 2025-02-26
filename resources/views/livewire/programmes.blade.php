<div>
    <div class="w-full">
        <!-- Dashboard Stats -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Total UEs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total UEs
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $programmes->where('type', 'UE')->count() }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total ECs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total ECs
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $stats['totalEC'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1er Regroupement (Semestre 1) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    S1
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $stats['semestre1']['ue'] }} UEs
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $stats['semestre1']['ec'] }} ECs
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2ème Regroupement (Semestre 2) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    S2
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $stats['semestre2']['ue'] }} UEs
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $stats['semestre2']['ec'] }} ECs
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3ème Regroupement (Semestre 3) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-pink-100 dark:bg-pink-900 rounded-full">
                            <svg class="w-6 h-6 text-pink-600 dark:text-pink-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    S3
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $stats['semestre3']['ue'] }} UEs
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $stats['semestre3']['ec'] }} ECs
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4ème Regroupement (Semestre 4) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-teal-100 dark:bg-teal-900 rounded-full">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    S4
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $stats['semestre4']['ue'] }} UEs
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $stats['semestre4']['ec'] }} ECs
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- En-tête -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Programme Master en Epidémiologie et Recherche Clinique
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Liste des unités d'enseignement et leurs éléments constitutifs
            </p>
        </div>

        <!-- Filtres et recherche -->
        <div class="mb-6 grid gap-4 md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <input type="text"
                           wire:model.live="search"
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400"
                           placeholder="Rechercher un UE ou EC...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Filtre par semestre -->
            <div class="flex space-x-2">
                <select wire:model.live="semestre" class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les semestres</option>
                    <option value="1">1er Semestre</option>
                    <option value="2">2ème Semestre</option>
                    <option value="3">3ème Semestre</option>
                    <option value="4">4ème Semestre</option>
                </select>
            </div>
        </div>

        <!-- Liste des UEs et ECs -->
        <div class="space-y-6">
            @foreach($programmes as $ue)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow" x-data="{ open: true }">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center space-x-2">
                            <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2 py-1 rounded-md text-sm">
                                {{ $ue->code }}
                            </span>
                            <!-- Troncature responsive : tronqué sur mobile, complet sur PC -->
                            <div class="relative group inline-block">
                                <!-- Texte tronqué visible uniquement sur mobile -->
                                <span class="md:hidden truncate inline-block max-w-[180px]">
                                    {{ Str::limit($ue->name, 25) }}
                                </span>

                                <!-- Texte complet visible sur tablette/desktop -->
                                <span class="hidden md:inline">
                                    {{ $ue->name }}
                                </span>

                                <!-- Tooltip visible uniquement sur mobile au survol -->
                                <div class="md:hidden absolute z-10 left-0 top-full mt-1 hidden group-hover:block bg-white dark:bg-dark-800 p-2 rounded-md shadow-md border border-gray-200 dark:border-dark-700 text-sm">
                                    {{ $ue->name }}
                                </div>
                            </div>
                            <span class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-md">
                                S {{ $ue->semestre_id }}
                            </span>
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="h-5 w-5 transform transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700" x-show="open" x-collapse>
                    @foreach($ue->elements as $ec)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-3">
                                <div class="h-2 w-2 rounded-full bg-indigo-500 dark:bg-indigo-400"></div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="flex-shrink-0 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                    {{ $ec->code }}
                                </span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $ec->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($programmes->hasPages())
        <div class="mt-6">
            <nav class="border-t border-gray-200 dark:border-gray-700 px-4 flex items-center justify-between sm:px-0">
                <!-- Pagination Mobile -->
                <div class="flex w-0 flex-1 md:hidden">
                    @if($programmes->onFirstPage())
                        <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed">
                            Précédent
                        </span>
                    @else
                        <button wire:click="previousPage" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-400">
                            Précédent
                        </button>
                    @endif
                </div>
                <div class="flex w-0 flex-1 justify-end md:hidden">
                    @if($programmes->hasMorePages())
                        <button wire:click="nextPage" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-400">
                            Suivant
                        </button>
                    @else
                        <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed">
                            Suivant
                        </span>
                    @endif
                </div>

                <!-- Pagination Desktop -->
                <div class="hidden md:flex md:items-center md:justify-between w-full">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Affichage de
                            <span class="font-medium">{{ $programmes->firstItem() }}</span>
                            à
                            <span class="font-medium">{{ $programmes->lastItem() }}</span>
                            sur
                            <span class="font-medium">{{ $programmes->total() }}</span>
                            résultats
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <!-- Bouton Précédent -->
                            @if($programmes->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @else
                                <button wire:click="previousPage" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif

                            <!-- Numéros de page -->
                            @for($i = 1; $i <= $programmes->lastPage(); $i++)
                                @if($i == $programmes->currentPage())
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-indigo-50 dark:bg-indigo-900 text-sm font-medium text-indigo-600 dark:text-indigo-200">
                                        {{ $i }}
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $i }})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        {{ $i }}
                                    </button>
                                @endif
                            @endfor

                            <!-- Bouton Suivant -->
                            @if($programmes->hasMorePages())
                                <button wire:click="nextPage" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </nav>
        </div>
        @endif
    </div>
</div>
