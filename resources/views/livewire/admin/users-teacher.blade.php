<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('livewire.admin.sections.user-section')
        <!-- Liste des enseignants -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Enseignant
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Niveaux et Parcours
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
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <span class="text-indigo-800 dark:text-indigo-200 font-medium text-sm">
                                                {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $teacher->profil?->grade. '. ' .$teacher->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $teacher->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($teacher->teacherNiveaux as $niveau)
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                {{ $niveau->name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 dark:text-gray-500">Aucun niveau</span>
                                        @endforelse
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($teacher->teacherParcours as $parcour)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                {{ $parcour->name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 dark:text-gray-500">Aucun parcours</span>
                                        @endforelse
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleUserStatus({{ $teacher->id }})" wire:loading.attr="disabled"
                                    class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 {{ $teacher->status ? 'bg-green-500 dark:bg-green-600' : 'bg-gray-200 dark:bg-gray-600' }}"
                                    role="switch">
                                    <span aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white dark:bg-gray-200 shadow transform ring-0 transition ease-in-out duration-200 {{ $teacher->status ? 'translate-x-5' : 'translate-x-0' }}">
                                    </span>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <button wire:click="editTeacher({{ $teacher->id }})"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteUser({{ $teacher->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir supprimer cet enseignant ?"
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Aucun enseignant trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="mt-4">
            {{ $teachers->links() }}
        </div>
        @include('livewire.admin.sections.teacher-modal')
    </div>
</div>
