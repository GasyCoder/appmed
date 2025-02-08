<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">
    <div class="flex items-center gap-3">
        <!-- Barre de recherche -->
        <div class="flex-1">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Rechercher un {{ $type === 'teacher' ? 'enseignant' : 'étudiant' }}..."
                class="w-full rounded-md border-gray-300 dark:border-gray-600 
                       bg-white dark:bg-gray-700 
                       text-gray-900 dark:text-white h-10
                       shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>

        <!-- Sélecteur de niveau -->
        <div class="w-44">
            <select
                wire:model.live="niveau_filter"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 
                       bg-white dark:bg-gray-700 
                       text-gray-900 dark:text-white h-10
                       shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Tous niveaux</option>
                @foreach($niveaux as $niveau)
                    <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Sélecteur de parcours -->
        <div class="w-44">
            <select
                wire:model.live="parcour_filter"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 
                       bg-white dark:bg-gray-700 
                       text-gray-900 dark:text-white h-10
                       shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Tous parcours</option>
                @foreach($parcours as $parcour)
                    <option value="{{ $parcour->id }}">{{ $parcour->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Sélecteur par page -->
        <div class="w-20">
            <select
                wire:model.live="perPage"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 
                       bg-white dark:bg-gray-700 
                       text-gray-900 dark:text-white h-10
                       shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
        <!-- Bouton Nouveau -->
        <button
            wire:click="$set('showUserModal', true)"
            class="inline-flex items-center px-4 h-10 border border-transparent 
                   rounded-md shadow-sm text-sm font-medium text-white
                   bg-indigo-600 hover:bg-indigo-700 
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            <span class="ml-2">Nouveau</span>
        </button>
    </div>
</div>

@if (session('status'))
    <div class="bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 
                text-green-700 dark:text-green-300 p-4 mb-4">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 
                text-red-700 dark:text-red-300 p-4 mb-4">
        {{ session('error') }}
    </div>
@endif