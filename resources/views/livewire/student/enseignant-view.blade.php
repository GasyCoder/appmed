<div class="py-6">
    <div class="mx-auto sm:px-6 lg:px-8 space-y-5">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                    Mes enseignants
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Retrouvez les enseignants de votre niveau et consultez leur profil.
                </p>
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $teachers->total() ?? $teachers->count() }} enseignant(s)
            </div>
        </div>

        {{-- Search --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>

                    <input
                        wire:model.live="search"
                        type="search"
                        class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                               bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm
                               placeholder-gray-500 dark:placeholder-gray-400
                               focus:outline-none focus:ring-2 focus:ring-gray-900/10 dark:focus:ring-white/10
                               focus:border-gray-300 dark:focus:border-gray-600"
                        placeholder="Rechercher par nom, grade, département…"
                    >
                </div>

                <button
                    type="button"
                    wire:click="$set('search','')"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-medium
                           bg-gray-100 text-gray-800 hover:bg-gray-200
                           dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition"
                >
                    Réinitialiser
                </button>
            </div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($teachers as $teacher)
                @php
                    $grade = $teacher->profil->grade ?? null;
                    $fullName = $grade ? ($grade . '. ' . $teacher->name) : $teacher->name;
                    $dept = $teacher->profil->departement ?? null;

                    $niveauxCount = $teacher->teacherNiveaux?->count() ?? 0;
                    $parcoursCount = $teacher->teacherParcours?->count() ?? 0;
                    $docsCount = (int)($teacher->documents_count ?? $teacher->documentsCount ?? 0);
                @endphp

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5
                            hover:border-gray-300 dark:hover:border-gray-600 transition">
                    {{-- Top --}}
                    <div class="flex items-start gap-3">
                        <img class="h-12 w-12 rounded-xl object-cover border border-gray-200 dark:border-gray-700"
                             src="{{ $teacher->profile_photo_url }}"
                             alt="{{ $teacher->name }}">

                        <div class="min-w-0 flex-1">
                            <div class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                {{ $fullName }}
                            </div>

                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400 truncate">
                                {{ $teacher->email }}
                            </div>

                            @if($dept)
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $dept }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Stats (chips sobres) --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-gray-100 text-gray-700
                                     dark:bg-gray-700 dark:text-gray-200">
                            {{ $niveauxCount }} niveaux
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-gray-100 text-gray-700
                                     dark:bg-gray-700 dark:text-gray-200">
                            {{ $parcoursCount }} parcours
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-gray-100 text-gray-700
                                     dark:bg-gray-700 dark:text-gray-200">
                            {{ $docsCount }} documents
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-5 flex gap-2">
                        <button
                            wire:click="showTeacherProfile({{ $teacher->id }})"
                            type="button"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-medium
                                   bg-gray-900 text-white hover:bg-gray-800
                                   dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Profil
                        </button>

                        <a href="mailto:{{ $teacher->email }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-medium
                                  bg-gray-100 text-gray-800 hover:bg-gray-200
                                  dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition"
                           title="Envoyer un email">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </a>
                    </div>
                </div>

            @empty
                <div class="col-span-full">
                    <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 p-10 text-center">
                        <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="text-base font-semibold text-gray-900 dark:text-white">Aucun enseignant trouvé</div>
                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Aucun enseignant n'est disponible pour votre niveau actuellement.
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination (si tu utilises paginate()) --}}
        @if(method_exists($teachers, 'links'))
            <div class="pt-2">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>

    {{-- Modal du profil détaillé --}}
    @include('livewire.student.modal.teacher-info')
</div>
