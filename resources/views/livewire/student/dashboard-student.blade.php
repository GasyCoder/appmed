<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- HEADER (simple, lisible) --}}
    @php
        $u = $student;
        $niveauLabel = $u?->niveau?->sigle ?? $u?->niveau?->name ?? 'Niveau non défini';
        $parcourLabel = $u?->parcour?->sigle ?? $u?->parcour?->name ?? null;
    @endphp

    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="min-w-0">
                <div class="text-xs text-gray-500 dark:text-gray-400">Espace étudiant</div>
                <div class="mt-1 text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate">
                    {{ $u?->name }}
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        {{ $niveauLabel }}
                    </span>

                    @if($parcourLabel)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            {{ $parcourLabel }}
                        </span>
                    @endif

                    @if(!empty($lastLoginAt))
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Dernière activité : {{ $lastLoginAt->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 md:justify-end">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $currentDateTime->format('d/m/Y H:i') }}
                </div>

                <a href="{{ route('student.document') }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-medium
                          bg-gray-900 text-white hover:bg-gray-800
                          dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                    Mes cours
                </a>
            </div>
        </div>
    </div>

    {{-- STATS (compact, pas trop) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Documents</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Aujourd’hui</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['today'] ?? 0 }}</div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Vues</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['views'] ?? 0 }}</div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Téléchargements</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['downloads'] ?? 0 }}</div>
        </div>
    </div>

    {{-- CONTENU --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- BLOC PRINCIPAL: UNE SEULE LISTE "DOCUMENTS" (moins redondant) --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="min-w-0">
                    <div class="font-semibold text-gray-900 dark:text-white">Documents</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Nouveaux aujourd’hui + derniers ajouts
                    </div>
                </div>

                <a href="{{ route('student.document') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition">
                    Voir tout
                </a>
            </div>

            <div class="p-5 space-y-5">
                {{-- 2) Derniers documents (mini grid, très léger) --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">Récents</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ ($recentDocuments?->count() ?? 0) }} élément(s)
                        </div>
                    </div>

                    @if(empty($recentDocuments) || $recentDocuments->isEmpty())
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Aucun document récent.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($recentDocuments as $doc)
                                @php
                                    $extension = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                @endphp

                                <a href="{{ route('document.serve', $doc) }}" target="_blank" rel="noopener"
                                   class="group rounded-xl border border-gray-200 dark:border-gray-700 p-4
                                          hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700">
                                            @include('livewire.teacher.forms.file-icons', ['extension' => $extension])
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">
                                                {{ $doc->title }}
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                                                {{ $doc->uploader?->getFullNameWithGradeAttribute() ?? $doc->uploader?->name ?? 'Enseignant' }}
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                                <span>{{ $doc->created_at->format('d/m/Y') }}</span>
                                                <span>•</span>
                                                <span>{{ $doc->formatted_size ?? '-' }}</span>
                                                <span>•</span>
                                                <span>{{ strtoupper($extension) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- COLONNE DROITE (petit, utile, pas envahissant) --}}
        <div class="space-y-6">

        {{-- Enseignants --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="font-semibold text-gray-900 dark:text-white">Enseignants</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Ceux qui publient</div>
            </div>

            <div class="p-5">
                @if(empty($teachers) || $teachers->isEmpty())
                    <div class="text-sm text-gray-500 dark:text-gray-400">Aucun enseignant trouvé.</div>
                @else
                    <div class="space-y-2">
                        @foreach($teachers as $t)
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                                <img class="h-9 w-9 rounded-full" src="{{ $t->profile_photo_url }}" alt="{{ $t->name }}">

                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $t->getFullNameWithGradeAttribute() }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ (int) ($t->docs_count ?? 0) }} document(s)
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('student.myTeacher', $t->id) }}"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-medium
                                            bg-gray-100 text-gray-800 hover:bg-gray-200
                                            dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                                        Voir
                                    </a>

                                    {{-- Optionnel: si tu as une messagerie --}}
                                    {{-- 
                                    <a href="{{ route('messages.compose', ['to' => $t->id]) }}"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-medium
                                            bg-gray-900 text-white hover:bg-gray-800
                                            dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                                        Message
                                    </a> 
                                    --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>


        </div>
    </div>

    <style>
        .line-clamp-2{
            display:-webkit-box;
            -webkit-line-clamp:2;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }
    </style>
</div>
