{{-- Modal du profil détaillé (UI slim / senior) --}}
@if($showTeacherModal && $selectedTeacher)
    <div
        class="fixed inset-0 z-50"
        aria-modal="true"
        role="dialog"
        aria-labelledby="teacher-modal-title"
        aria-describedby="teacher-modal-desc"
    >
        {{-- Overlay (click to close) --}}
        <div
            class="absolute inset-0 bg-black/40 dark:bg-black/60"
            wire:click="closeTeacherModal"
        ></div>

        {{-- Panel --}}
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div
                class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden"
                wire:click.stop
            >
                {{-- Top bar --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3 min-w-0">
                        <img
                            class="h-11 w-11 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                            src="{{ $selectedTeacher->profile_photo_url }}"
                            alt="{{ $selectedTeacher->name }}"
                        />

                        <div class="min-w-0">
                            @php
                                $grade = $selectedTeacher->profil->grade ?? null;
                                $fullName = trim(($grade ? $grade.'. ' : '').$selectedTeacher->name);
                            @endphp

                            <h3 id="teacher-modal-title" class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                {{ $fullName }}
                            </h3>

                            <p id="teacher-modal-desc" class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $selectedTeacher->email }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="closeTeacherModal"
                        class="inline-flex items-center justify-center h-9 w-9 rounded-xl
                               text-gray-500 hover:text-gray-900 hover:bg-gray-100
                               dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        aria-label="Fermer"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-5 py-4 space-y-4">

                    {{-- Infos rapides --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @php
                            $departement = $selectedTeacher->profil->departement ?? null;
                            $telephone  = $selectedTeacher->profil->telephone ?? null;
                        @endphp

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Département</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $departement ?: 'Non renseigné' }}
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Téléphone</div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $telephone ?: 'Non renseigné' }}
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Statistiques</div>

                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium
                                         bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <span class="text-gray-500 dark:text-gray-400">Niveaux</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $selectedTeacher->teacherNiveaux->count() }}</span>
                            </span>

                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium
                                         bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <span class="text-gray-500 dark:text-gray-400">Parcours</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $selectedTeacher->teacherParcours->count() }}</span>
                            </span>

                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium
                                         bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <span class="text-gray-500 dark:text-gray-400">Documents</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ (int)($selectedTeacher->documents_count ?? 0) }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a
                            href="mailto:{{ $selectedTeacher->email }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                                   transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Envoyer un email
                        </a>

                        @if($telephone)
                            <a
                                href="tel:{{ $telephone }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                       bg-gray-100 text-gray-900 hover:bg-gray-200
                                       dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600
                                       transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Appeler
                            </a>
                        @else
                            <button
                                type="button"
                                disabled
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                                       bg-gray-100 text-gray-400 cursor-not-allowed
                                       dark:bg-gray-700 dark:text-gray-500"
                            >
                                Téléphone indisponible
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <button
                        type="button"
                        wire:click="closeTeacherModal"
                        class="px-4 py-2 rounded-xl text-sm font-medium
                               bg-gray-100 text-gray-900 hover:bg-gray-200
                               dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600
                               transition focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
