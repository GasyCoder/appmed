<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex flex-1 gap-4">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Rechercher un niveau..."
                class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                       text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400
                       shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400
                       focus:ring-indigo-500 dark:focus:ring-indigo-400 flex-1"
            >
        </div>
        <button
            wire:click="$set('showLevelModal', true)"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm
                   text-sm font-medium text-white bg-indigo-600 dark:bg-indigo-500
                   hover:bg-indigo-700 dark:hover:bg-indigo-600
                   focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                   focus:ring-indigo-500 dark:focus:ring-indigo-400"
        >
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau niveau
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
