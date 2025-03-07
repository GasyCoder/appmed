<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8"
     x-data="{ loading: true }"
     x-init="() => {
         window.addEventListener('livewire:navigating', () => { loading = true });
         window.addEventListener('livewire:navigated', () => { loading = false });
         window.addEventListener('livewire:load', () => { loading = false });
     }">
    <!-- Contenu Principal -->
    <div x-show="!loading" x-cloak class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête avec stats rapides -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
            <!-- Total des documents -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center">
                    <div class="shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-50 dark:bg-indigo-900 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Total Documents</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $uploadCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents actifs -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center">
                    <div class="shrink-0 w-10 h-10 flex items-center justify-center bg-green-50 dark:bg-green-900 rounded-lg">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Documents Partagés</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $myDocuments->where('is_actif', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents inactifs -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center">
                    <div class="shrink-0 w-10 h-10 flex items-center justify-center bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Non Partagés</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $myDocuments->where('is_actif', false)->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Nouveaux documents -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center">
                    <div class="shrink-0 w-10 h-10 flex items-center justify-center bg-violet-50 dark:bg-violet-900 rounded-lg">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Ajoutés cette semaine</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $myDocuments->where('created_at', '>=', now()->subDays(7))->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de filtres -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow mb-6">
            <div class="p-4">
                <div class="flex flex-col space-y-4">
                    <!-- Ligne supérieure : Recherche et bouton -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Barre de recherche -->
                        <div class="relative flex-1">
                            <input type="text"
                                wire:model.live="search"
                                placeholder="Rechercher un document..."
                                class="w-full h-11 rounded-lg border-gray-300 dark:border-gray-600 pl-10 pr-4 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Bouton Nouveau document (responsive) -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('document.upload')}}"
                            class="flex items-center justify-center w-full px-6 h-11 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Nouveau document</span>
                            </a>
                        </div>
                    </div>
                    <!-- Section des filtres avec toggle -->
                    <div x-data="{ showFilters: false }">
                        <!-- Bouton Toggle -->
                        <div class="flex items-center justify-between mb-2">
                            <button
                                @click="showFilters = !showFilters"
                                class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                            >
                                <span x-text="showFilters ? 'Masquer les filtres' : 'Afficher les filtres'"></span>
                                <svg
                                    class="w-5 h-5 transition-transform duration-200"
                                    :class="{ 'rotate-180': showFilters }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Badge indiquant les filtres actifs -->
                            <div class="flex items-center gap-2">
                                <template x-if="$wire.filterNiveau || $wire.filterParcour || $wire.filterSemestre || $wire.filterStatus">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                        Filtres actifs
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Ligne des filtres -->
                        <div
                            x-show="showFilters"
                            x-collapse
                            x-cloak
                            class="flex flex-col sm:flex-row gap-4"
                        >
                            <div class="flex-1">
                                <select wire:model.live="filterNiveau"
                                        class="w-full h-11 rounded-lg border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Tous les niveaux</option>
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-1">
                                <select wire:model.live="filterParcour"
                                        class="w-full h-11 rounded-lg border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Tous les parcours</option>
                                    @foreach($parcours as $parcour)
                                        <option value="{{ $parcour->id }}">{{ $parcour->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-1">
                                <select wire:model.live="filterSemestre"
                                        class="w-full h-11 rounded-lg border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Tous les semestres</option>
                                    @foreach($semestres as $semestre)
                                        <option value="{{ $semestre->id }}">{{ $semestre->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-1">
                                <select wire:model.live="filterStatus"
                                        class="w-full h-11 rounded-lg border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Tous les statuts</option>
                                    <option value="1">Partagé</option>
                                    <option value="0">Non partagé</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages flash -->
        @include('layouts.partials.flash-msg')

        <!-- Liste des documents -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">

            <!-- Version desktop -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <!-- Document Column -->
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2 cursor-pointer" wire:click="sortBy('title')">
                                    <span>Document</span>
                                    @if($sortField === 'title')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>

                            <!-- Information Column -->
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Information
                            </th>

                            <!-- Status Column -->
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <div class="flex items-center gap-2 cursor-pointer" wire:click="sortBy('is_actif')">
                                    <span>Partagé</span>
                                    @if($sortField === 'is_actif')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            @endif
                                        </svg>
                                    @endif
                                </div>
                            </th>

                            <!-- Statistics Column -->
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Statistiques
                            </th>

                            <!-- Actions Column -->
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($myDocuments as $document)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <!-- Document Cell -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            @php
                                                $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                                            @endphp
                                            @include('livewire.teacher.forms.file-icons')
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $document->title }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Ajouté le {{ $document->created_at->format('d/m/Y à H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Information Cell -->
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $document->niveau->name }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ $document->parcour->name }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ $document->semestre->name }}</div>
                                    </div>
                                </td>

                                <!-- Status Cell -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <button
                                            wire:click="toggleStatus({{ $document->id }})"
                                            wire:loading.attr="disabled"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 {{ $document->is_actif ? 'bg-green-500 dark:bg-green-600' : 'bg-gray-200 dark:bg-gray-600' }}"
                                            role="switch">
                                            <span class="sr-only">Changer le statut</span>
                                            <span
                                                aria-hidden="true"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white dark:bg-gray-200 shadow ring-0 transition duration-200 ease-in-out {{ $document->is_actif ? 'translate-x-5' : 'translate-x-0' }}"
                                            ></span>
                                        </button>
                                    </div>
                                </td>

                                <!-- Statistics Cell -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>
                                                {{ $document->view_count }} {{ Str::plural('vue', $document->view_count) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5c0-1.1.9-2 2-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15c0 1.1-.9 2-2 2h-2M8 7H6a2 2 0 00-2 2v10c0 1.1.9 2 2 2h8a2 2 0 002-2v-2"/>
                                            </svg>
                                            <span>{{ $document->formatted_size }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Actions Cell -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        <!-- Preview Button -->
                                        <button
                                            onclick="window.open('{{ $document->getSecureUrl() }}', '_blank')"
                                            class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 transition-colors duration-200"
                                            title="Prévisualiser">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <!-- Edit Button -->
                                        <a href="{{ route('document.edit', $document) }}"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors duration-200"
                                        title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        <!-- Delete Button -->
                                        <button
                                            wire:click="deleteDocument({{ $document->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer ce document ?"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-200"
                                            title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-gray-500 dark:text-gray-400">Aucun document trouvé</p>
                                        <a href="{{ route('document.upload') }}" wire:navigate
                                        class="mt-3 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 dark:bg-indigo-500 rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Ajouter un document
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Version mobile/tablet -->
            <div class="lg:hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($myDocuments as $document)
                        <div class="p-4">
                            <div class="flex items-start space-x-4">
                                <!-- Icône -->
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    @php
                                        $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                                    @endphp
                                    @include('livewire.teacher.forms.file-icons')
                                </div>

                                <!-- Informations -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $document->title }}
                                    </h4>
                                    <div class="mt-1 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $document->niveau->name }}</span>
                                        <span>•</span>
                                        <span>{{ $document->parcour->name }}</span>
                                        <span>•</span>
                                        <span>{{ $document->semestre->name }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center gap-3">
                                        <span class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ $document->view_count }} vues
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $document->formatted_size }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions et statut -->
                            <div class="mt-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <!-- Toggle de statut -->
                                    <div class="flex items-center gap-2">
                                        <button
                                            wire:click="toggleStatus({{ $document->id }})"
                                            wire:loading.attr="disabled"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:ring-offset-2 {{ $document->is_actif ? 'bg-green-500 dark:bg-green-600' : 'bg-gray-200 dark:bg-gray-600' }}"
                                            role="switch">
                                            <span class="sr-only">Changer le statut</span>
                                            <span
                                                aria-hidden="true"
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white dark:bg-gray-200 shadow ring-0 transition duration-200 ease-in-out {{ $document->is_actif ? 'translate-x-5' : 'translate-x-0' }}"
                                            ></span>
                                        </button>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $document->is_actif ? 'Partagé' : 'Non partagé' }}
                                        </span>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <!-- Prévisualiser -->
                                        <button
                                            onclick="window.open('{{ $document->getSecureUrl() }}', '_blank')"
                                            class="inline-flex items-center rounded-lg p-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/50 transition-colors"
                                            title="Prévisualiser">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="sr-only sm:not-sr-only sm:ml-2">Voir</span>
                                        </button>

                                        <!-- Modifier -->
                                        <a href="{{ route('document.edit', $document) }}"
                                           class="inline-flex items-center rounded-lg p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 transition-colors"
                                           title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span class="sr-only sm:not-sr-only sm:ml-2">Modifier</span>
                                        </a>

                                        <!-- Supprimer -->
                                        <button
                                            wire:click="deleteDocument({{ $document->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer ce document ?"
                                            class="inline-flex items-center rounded-lg p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors"
                                            title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span class="sr-only sm:not-sr-only sm:ml-2">Supprimer</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8">
                            <div class="flex flex-col items-center justify-center text-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                    Aucun document trouvé
                                </p>
                                <a href="{{ route('document.upload') }}" wire:navigate
                                   class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 dark:bg-indigo-500 rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Ajouter un document
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $myDocuments->links() }}
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>
@endpush
