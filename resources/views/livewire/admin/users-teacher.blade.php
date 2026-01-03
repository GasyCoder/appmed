<div class="py-6">
    <div class="max-w-10xl mx-auto sm:px-6 lg:px-8 space-y-4">

        @include('livewire.admin.sections.user-section')

        <div wire:loading wire:target="search,niveau_filter,perPage,page,editTeacher,confirmDelete,deleteUser,createTeacher,toggleUserStatus" aria-busy="true" class="space-y-4">
            <div class="md:hidden space-y-3">
                <x-skeleton.card :lines="4" />
                <x-skeleton.card :lines="4" />
                <x-skeleton.card :lines="4" />
            </div>
            <div class="hidden md:block">
                <x-skeleton.table :columns="4" :rows="6" />
            </div>
        </div>

        <div wire:loading.remove wire:target="search,niveau_filter,perPage,page,editTeacher,confirmDelete,deleteUser,createTeacher,toggleUserStatus">
            {{-- MOBILE: Cards --}}
            <div class="md:hidden space-y-3">
                @forelse($teachers as $teacher)
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center shrink-0">
                                    <span class="text-indigo-800 dark:text-indigo-200 font-semibold text-sm">
                                        {{ strtoupper(mb_substr($teacher->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ trim(($teacher->profil?->grade ? $teacher->profil->grade.'. ' : '').$teacher->name) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ $teacher->email }}
                                    </div>
                                </div>
                            </div>

                            <button
                                wire:click="toggleUserStatus({{ $teacher->id }})"
                                wire:loading.attr="disabled"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition
                                    {{ $teacher->status ? 'bg-green-500 dark:bg-green-600' : 'bg-gray-200 dark:bg-gray-600' }}">
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition
                                    {{ $teacher->status ? 'translate-x-5' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </div>

                        <div class="mt-3 space-y-2">
                            <div class="flex flex-wrap gap-1">
                                @forelse($teacher->teacherNiveaux as $niveau)
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ $niveau->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Aucun niveau</span>
                                @endforelse
                            </div>

                            <div class="flex flex-wrap gap-1">
                                @forelse($teacher->teacherParcours as $parcour)
                                    <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200">
                                        {{ $parcour->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Aucun parcours</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <button
                                wire:click="editTeacher({{ $teacher->id }})"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                       border border-gray-200 dark:border-gray-700
                                       bg-white dark:bg-gray-900
                                       text-sm text-gray-700 dark:text-gray-200
                                       hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                Modifier
                            </button>

                            <button
                                wire:click="confirmDelete({{ $teacher->id }})"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                       border border-red-200 dark:border-red-900/50
                                       bg-red-50 dark:bg-red-900/20
                                       text-sm text-red-700 dark:text-red-300
                                       hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                Supprimer
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                        Aucun enseignant trouvé
                    </div>
                @endforelse
            </div>

            {{-- DESKTOP: Table --}}
            <div class="hidden md:block rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Enseignant</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Niveaux & Parcours</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($teachers as $teacher)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                <span class="text-indigo-800 dark:text-indigo-200 font-semibold text-sm">
                                                    {{ strtoupper(mb_substr($teacher->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ trim(($teacher->profil?->grade ? $teacher->profil->grade.'. ' : '').$teacher->name) }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $teacher->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($teacher->teacherNiveaux as $niveau)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                        {{ $niveau->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-400 dark:text-gray-500">Aucun niveau</span>
                                                @endforelse
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($teacher->teacherParcours as $parcour)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200">
                                                        {{ $parcour->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-400 dark:text-gray-500">Aucun parcours</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <button
                                            wire:click="toggleUserStatus({{ $teacher->id }})"
                                            wire:loading.attr="disabled"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition
                                                {{ $teacher->status ? 'bg-green-500 dark:bg-green-600' : 'bg-gray-200 dark:bg-gray-600' }}">
                                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition
                                                {{ $teacher->status ? 'translate-x-5' : 'translate-x-1' }}">
                                            </span>
                                        </button>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <button
                                                wire:click="editTeacher({{ $teacher->id }})"
                                                class="px-3 py-2 rounded-xl text-sm font-medium
                                                       border border-gray-200 dark:border-gray-700
                                                       bg-white dark:bg-gray-900
                                                       text-gray-700 dark:text-gray-200
                                                       hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                                Modifier
                                            </button>

                                            <button
                                                wire:click="confirmDelete({{ $teacher->id }})"
                                                class="px-3 py-2 rounded-xl text-sm font-medium
                                                       border border-red-200 dark:border-red-900/50
                                                       bg-red-50 dark:bg-red-900/20
                                                       text-red-700 dark:text-red-300
                                                       hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Aucun enseignant trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $teachers->links() }}
            </div>
        </div>

        @include('livewire.admin.modal.teacher-modal')
    </div>
</div>
