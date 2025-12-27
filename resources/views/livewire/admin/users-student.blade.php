<div class="py-6">
    <div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header (search / filters / new) --}}
        @include('livewire.admin.sections.user-section', [
            'niveaux' => $niveaux,
            'type' => $type
        ])

        {{-- Table card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10">
                <div class="flex items-start sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Liste des étudiants</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Gestion, statut et édition des comptes étudiants.
                        </p>
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Total: <span class="font-semibold text-gray-900 dark:text-white">{{ $students->total() }}</span>
                    </div>
                </div>
            </div>

            {{-- Responsive wrapper --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Étudiant
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Niveau
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($students as $student)
                            @php
                                $parts = preg_split('/\s+/', trim($student->name));
                                $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
                                if (strlen(trim($initials)) === 0) $initials = 'ST';
                            @endphp

                            <tr class="hover:bg-gray-50/60 dark:hover:bg-white/5 transition">
                                {{-- Student --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full
                                                     bg-gray-100 text-gray-900 ring-1 ring-black/5
                                                     dark:bg-gray-900/60 dark:text-white dark:ring-white/10">
                                            <span class="text-sm font-semibold">{{ $initials }}</span>
                                        </span>

                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $student->name }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                {{ $student->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Niveau --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student->niveau?->name)
                                        <span class="inline-flex items-center rounded-xl px-2.5 py-1 text-xs font-semibold
                                                     bg-blue-50 text-blue-800 ring-1 ring-blue-600/15
                                                     dark:bg-blue-900/20 dark:text-blue-200 dark:ring-blue-400/15">
                                            {{ $student->niveau->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Non défini</span>
                                    @endif
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <button
                                            wire:click="toggleUserStatus({{ $student->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="toggleUserStatus({{ $student->id }})"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full transition
                                                   focus:outline-none focus:ring-2 focus:ring-gray-900/20 dark:focus:ring-white/20
                                                   {{ $student->status ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600' }}"
                                            role="switch"
                                            aria-checked="{{ $student->status ? 'true' : 'false' }}"
                                        >
                                            <span
                                                class="pointer-events-none inline-block h-5 w-5 translate-y-0.5 rounded-full bg-white shadow transition
                                                       {{ $student->status ? 'translate-x-5' : 'translate-x-1' }}">
                                            </span>
                                        </button>

                                        <span class="text-sm font-medium {{ $student->status ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-600 dark:text-gray-300' }}">
                                            {{ $student->status ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button
                                            wire:click="editStudent({{ $student->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                                   bg-white ring-1 ring-gray-200 hover:bg-gray-50
                                                   dark:bg-gray-900/40 dark:ring-white/10 dark:hover:bg-white/5
                                                   text-gray-700 dark:text-gray-200 transition"
                                            title="Modifier"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button
                                            wire:click="confirmDelete({{ $student->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                                   bg-white ring-1 ring-gray-200 hover:bg-red-50
                                                   dark:bg-gray-900/40 dark:ring-white/10 dark:hover:bg-red-900/20
                                                   text-red-600 dark:text-red-300 transition"
                                            title="Supprimer"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-600 dark:text-gray-400">
                                    Aucun étudiant trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10">
                {{ $students->links() }}
            </div>
        </div>

        {{-- Modal --}}
        @include('livewire.admin.modal.student-modal')

    </div>
</div>
