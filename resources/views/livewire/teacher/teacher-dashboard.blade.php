<div class="p-4 space-y-6">

    @php
        $me = $user; // passé par le component
        $grade = $me->profil?->grade;
        $fullName = trim(($grade ? $grade.'. ' : '').$me->name);

        $dept = $me->profil?->departement;
        $ville = $me->profil?->ville;
        $tel  = $me->profil?->telephone;
    @endphp

    {{-- Header (sobre, actionnable) --}}
    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div class="flex items-center gap-4 min-w-0">
                <img
                    class="h-14 w-14 rounded-2xl object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                    src="{{ $me->profile_photo_url }}"
                    alt="{{ $me->name }}"
                />

                <div class="min-w-0">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Espace enseignant</div>
                    <div class="mt-0.5 text-lg font-semibold text-gray-900 dark:text-white truncate">
                        {{ $fullName }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ $me->email }}
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            {{ $stats['niveaux_count'] }} niveau(x)
                        </span>

                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            {{ $stats['parcours_count'] }} parcours
                        </span>

                        @if($lastLoginAt)
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Dernière connexion : {{ $lastLoginAt->diffForHumans() }}
                            </span>
                        @endif
                    </div>

                    @if($dept || $ville || $tel)
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            @if($dept)
                                <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-700">
                                    {{ $dept }}
                                </span>
                            @endif
                            @if($ville)
                                <span class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-700">
                                    {{ $ville }}
                                </span>
                            @endif
                            @if($tel)
                                <a href="tel:{{ $tel }}"
                                   class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    {{ $tel }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('profile.show') }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold
                          bg-gray-100 text-gray-900 hover:bg-gray-200
                          dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 transition">
                    Modifier profil
                </a>

                <a href="{{ route('document.teacher') ?? '#' }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold
                          bg-gray-900 text-white hover:bg-gray-800
                          dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                    Mes documents
                </a>

                {{-- Si tu as une route upload, remplace --}}
                {{-- <a href="{{ route('teacher.documents.create') }}" ...>Ajouter</a> --}}
            </div>
        </div>
    </div>

    {{-- Stats (compactes) --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Uploads</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_uploads'] }}</div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Partagés</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['public_documents'] }}</div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Brouillons</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['pending_documents'] }}</div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Téléchargements</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_downloads'] }}</div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-xs text-gray-500 dark:text-gray-400">Vues</div>
            <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_views'] }}</div>
        </div>
    </div>

    {{-- Layout principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Col gauche --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Documents récents (une seule liste claire) --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="font-semibold text-gray-900 dark:text-white">Documents récents</div>
                    <a href="{{ route('document.teacher') ?? '#' }}"
                       class="text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition">
                        Voir tout
                    </a>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $doc)
                        @php
                            $ext = $doc->extension ?? strtolower(pathinfo($doc->file_path ?? '', PATHINFO_EXTENSION));
                        @endphp

                        <div class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700">
                                    @include('livewire.teacher.forms.file-icons', ['extension' => $ext])
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $doc->title }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex flex-wrap gap-2">
                                        <span>{{ $doc->created_at->format('d/m/Y H:i') }}</span>
                                        <span>•</span>
                                        <span>{{ strtoupper($ext) }}</span>
                                        @if($doc->niveau?->sigle || $doc->niveau?->name)
                                            <span>•</span>
                                            <span>{{ $doc->niveau?->sigle ?? $doc->niveau?->name }}</span>
                                        @endif
                                        @if($doc->parcour?->sigle || $doc->parcour?->name)
                                            <span>•</span>
                                            <span>{{ $doc->parcour?->sigle ?? $doc->parcour?->name }}</span>
                                        @endif
                                        @if($doc->semestre?->name)
                                            <span>•</span>
                                            <span>{{ $doc->semestre->name }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                            {{ $doc->is_actif
                                                ? 'bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300'
                                                : 'bg-amber-50 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300' }}">
                                            {{ $doc->is_actif ? 'Partagé' : 'Brouillon' }}
                                        </span>

                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ (int) $doc->view_count }} vues • {{ (int) $doc->download_count }} téléchargements
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('document.serve', $doc) }}"
                                       target="_blank" rel="noopener"
                                       class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-sm font-semibold
                                              bg-gray-900 text-white hover:bg-gray-800
                                              dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                                        Ouvrir
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            Aucun document récent.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Niveaux & semestres (compact) --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="font-semibold text-gray-900 dark:text-white">Niveaux & semestres</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Vos affectations actives</div>
                </div>

                <div class="p-5 space-y-3">
                    @forelse($niveauxSemestres as $niveau)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $niveau['name'] }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ count($niveau['semestres']) }} semestre(s)
                                </div>
                            </div>

                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($niveau['semestres'] as $s)
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $s['is_active']
                                            ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                            : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">
                                        {{ $s['name'] }}
                                        <span class="text-[11px] opacity-80">({{ (int) $s['documents_count'] }})</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">Aucun niveau assigné.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Col droite --}}
        <div class="space-y-6">

            {{-- Activité mensuelle --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="font-semibold text-gray-900 dark:text-white">Activité mensuelle</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Nombre d’uploads</div>
                </div>

                <div class="p-5 space-y-2">
                    @forelse($monthlyStats as $index => $stat)
                        @php
                            $previousCount = isset($monthlyStats[$index + 1]) ? (int)$monthlyStats[$index + 1]->count : 0;
                            $evolution = (int)$stat->count - $previousCount;
                        @endphp

                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($stat->month . '-01')->locale('fr')->isoFormat('MMMM YYYY') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ (int)$stat->count }} document(s)
                                </div>
                            </div>

                            <div class="text-xs font-semibold
                                {{ $evolution > 0 ? 'text-green-600 dark:text-green-300' : ($evolution < 0 ? 'text-red-600 dark:text-red-300' : 'text-gray-500 dark:text-gray-400') }}">
                                @if($evolution > 0) +{{ $evolution }}
                                @elseif($evolution < 0) {{ $evolution }}
                                @else 0
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">Aucune donnée.</div>
                    @endforelse
                </div>
            </div>

            {{-- Connexions récentes --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="font-semibold text-gray-900 dark:text-white">Connexions</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">5 dernières sessions</div>
                </div>

                <div class="p-5 space-y-2">
                    @forelse($loginActivities as $a)
                        <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $a->ip_address ?? 'IP inconnue' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::createFromTimestamp($a->last_activity)->diffForHumans() }}
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::createFromTimestamp($a->last_activity)->translatedFormat('d M Y, H:i') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">Aucune activité récente.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
