<div class="bg-white shadow-sm rounded-lg p-4 mb-6">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex flex-1 gap-4">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Rechercher un parcour..."
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 flex-1"
            >
        </div>
        <button
            wire:click="$set('showParcourModal', true)"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau parcour
        </button>
    </div>
</div>

@if (session('status'))
    <div class="bg-green-100 border-l-4 border-green-500 p-4 mb-4">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-4">
        {{ session('error') }}
    </div>
@endif
