<div class="py-6">
    {{-- En-tête avec recherche et filtre --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('livewire.admin.sections.parcour-section')
        {{-- Liste des parcour --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Nom
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Sigle
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($parcours as $key => $parcour)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">{{$key +1}}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">
                                <div class="text-sm font-medium text-gray-900 text-gray-900 dark:text-gray-300">{{ $parcour->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">
                                {{ $parcour->sigle }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-300">
                                <button
                                    wire:click="toggleParcourStatus({{ $parcour->id }})"
                                    wire:loading.attr="disabled"
                                    class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2
                                    focus:ring-indigo-500 {{ $parcour->status ? 'bg-green-500' : 'bg-gray-200' }}"
                                    role="switch"
                                >
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform
                                        ring-0 transition ease-in-out duration-200 {{ $parcour->status ? 'translate-x-5' : 'translate-x-0' }}"
                                    ></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
                                <div class="flex items-center gap-3">
                                    <button wire:click="editParcour({{ $parcour->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button wire:click="deleteParcour({{ $parcour->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer cet utilisateur ?"
                                            class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-900 dark:text-gray-300">
                                Aucun parcour trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $parcours->links() }}
        </div>
    </div>

    @include('livewire.admin.sections.parcour-modal')
</div>
