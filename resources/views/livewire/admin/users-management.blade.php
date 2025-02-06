<div class="py-6">
    {{-- En-tête avec recherche et filtre --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('livewire.admin.sections.user-section')
        {{-- Liste des utilisateurs --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Utilisateur
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rôle
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Niveau/Parcours
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium text-sm">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach($user->roles as $role)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $role->label }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->roles->contains('name', 'student'))
                                    <div>{{ $user->niveau?->name ?? 'Non défini' }}</div>
                                    <div>{{ $user->parcour?->name ?? 'Non défini' }}</div>
                                @elseif($user->roles->contains('name', 'teacher'))
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->teacherNiveaux as $niveau)
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                    {{ $niveau->name }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400">Aucun niveau</span>
                                            @endforelse
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->teacherParcours as $parcour)
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                    {{ $parcour->name }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400">Aucun parcours</span>
                                            @endforelse
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    wire:click="toggleUserStatus({{ $user->id }})"
                                    wire:loading.attr="disabled"
                                    class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $user->status ? 'bg-green-500' : 'bg-gray-200' }}"
                                    role="switch"
                                >
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $user->status ? 'translate-x-5' : 'translate-x-0' }}"
                                    ></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <button wire:click="editUser({{ $user->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button wire:click="deleteUser({{ $user->id }})"
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
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    @include('livewire.admin.sections.user-modal')

{{-- Scripts pour Alpine.js --}}
@script
<script>
Alpine.data('usersManagement', () => ({
    init() {
        // Initialisation
        this.$watch('selectedRole', value => {
            if (value !== 'student') {
                this.$wire.set('niveau_id', null);
                this.$wire.set('parcour_id', null);
            }
        });
    }
}));
</script>
@endscript
</div>
