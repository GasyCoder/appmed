<div class="mx-auto w-full max-w-10xl px-4 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-5 sm:space-y-6">

    @php
        $me = $user; // passé par le component
        $grade = $me->profil?->grade;
        $fullName = trim(($grade ? $grade.'. ' : '').$me->name);

        $dept = $me->profil?->departement;
        $ville = $me->profil?->ville;
        $tel  = $me->profil?->telephone;
    @endphp

    {{-- Header (Senior UI redesign) --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-hidden">
        {{-- Top strip / subtle accent --}}
        <div class="h-1 bg-gray-900/90 dark:bg-white/90"></div>

        <div class="p-4 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

                {{-- Identity block --}}
                <div class="flex items-start gap-3 sm:gap-4 min-w-0">
                    <div class="relative shrink-0">
                        <img
                            class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                            src="{{ $me->profile_photo_url }}"
                            alt="{{ $me->name }}"
                        />
                        {{-- Status dot (purely UI) --}}
                        <span class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-800"></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        {{-- Kicker + name --}}
                        <div class="flex flex-col gap-1 min-w-0">
                            <div class="inline-flex items-center gap-2 text-[11px] sm:text-xs font-medium text-gray-500 dark:text-gray-400">
                                <span class="inline-flex h-5 items-center rounded-full border border-gray-200 bg-gray-50 px-2
                                            text-[11px] font-semibold text-gray-700
                                            dark:border-gray-700 dark:bg-gray-700/40 dark:text-gray-200">
                                    Enseignant
                                </span>
                                <span class="truncate">Tableau de bord</span>
                            </div>

                            <div class="text-lg sm:text-xl font-semibold tracking-tight text-gray-900 dark:text-white leading-snug break-words">
                                {{ $fullName }}
                            </div>

                            <div class="text-sm text-gray-600 dark:text-gray-300 break-all sm:break-words">
                                {{ $me->email }}
                            </div>
                        </div>

                        {{-- Metrics row --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                <span class="h-2 w-2 rounded-full bg-gray-900 dark:bg-white"></span>
                                {{ $stats['niveaux_count'] }} niveau(x)
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700
                                        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                <span class="h-2 w-2 rounded-full bg-gray-900 dark:bg-white"></span>
                                {{ $stats['parcours_count'] }} parcours
                            </span>

                            @if($lastLoginAt)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    Dernière connexion : {{ $lastLoginAt->diffForHumans() }}
                                </span>
                            @endif
                        </div>

                        {{-- Meta (dept/ville/tel) --}}
                        @if($dept || $ville || $tel)
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                @if($dept)
                                    <span class="max-w-full inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1
                                                dark:border-gray-700 dark:bg-gray-700/40 truncate">
                                        {{ $dept }}
                                    </span>
                                @endif

                                @if($ville)
                                    <span class="max-w-full inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1
                                                dark:border-gray-700 dark:bg-gray-700/40 truncate">
                                        {{ $ville }}
                                    </span>
                                @endif

                                @if($tel)
                                    <a href="tel:{{ $tel }}"
                                    class="max-w-full inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1
                                            hover:bg-gray-100 transition
                                            dark:border-gray-700 dark:bg-gray-700/40 dark:hover:bg-gray-700 truncate">
                                        {{ $tel }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="w-full lg:w-auto">
                    <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:justify-end">
                        {{-- Secondary --}}
                        <a href="{{ route('profile.show') }}"
                        class="col-span-1 w-full inline-flex items-center justify-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold
                                border border-gray-200 bg-white text-gray-900 shadow-sm
                                hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20
                                dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700/50 dark:focus-visible:ring-white/20 transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Profil
                        </a>

                        {{-- Primary --}}
                        <a href="{{ route('documents.index') ?? '#' }}"
                        class="col-span-1 w-full inline-flex items-center justify-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold
                                bg-gray-900 text-white shadow-sm
                                hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/30
                                dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 dark:focus-visible:ring-white/30 transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"/>
                            </svg>
                            Documents
                        </a>
                    </div>

                    {{-- Hint line (optional, purely UI) --}}
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-left lg:text-right">
                        Accès rapide à votre profil et vos ressources.
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Stats (mobile collapsible) --}}
    <div class="space-y-3" x-data="{ statsOpen: false }">

        {{-- Mobile résumé + toggle --}}
        <div class="sm:hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <button type="button"
                    class="w-full px-4 py-3 flex items-center justify-between gap-3"
                    @click="statsOpen = !statsOpen"
                    :aria-expanded="statsOpen">
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Statistiques</div>
                    <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $stats['total_uploads'] }} uploads · {{ $stats['public_documents'] }} partagés · {{ $stats['pending_documents'] }} brouillons
                    </div>
                </div>

                <span class="shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-xl
                             text-gray-500 hover:bg-gray-50 hover:text-gray-700
                             dark:text-gray-300 dark:hover:bg-gray-700/40 dark:hover:text-white">
                    <svg class="h-5 w-5 transform transition-transform duration-200"
                         :class="{ 'rotate-180': statsOpen }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </button>

            <div x-show="statsOpen" x-collapse x-cloak class="px-4 pb-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Uploads</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['total_uploads'] }}</div>
                    </div>

                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Partagés</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['public_documents'] }}</div>
                    </div>

                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Brouillons</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['pending_documents'] }}</div>
                    </div>

                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Téléchargements</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['total_downloads'] }}</div>
                    </div>

                    <div class="col-span-2 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Vues</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $stats['total_views'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Desktop/tablet: stats visibles --}}
        <div class="hidden sm:grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
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
    </div>

    {{-- Layout principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

        {{-- Col gauche --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">

            {{-- Documents récents --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
                    <div class="font-semibold text-gray-900 dark:text-white">Documents récents</div>
                    <a href="{{ route('documents.index') ?? '#' }}"
                       class="text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition shrink-0">
                        Voir tout
                    </a>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $doc)
                        @php
                            $ext = $doc->extension ?? strtolower(pathinfo($doc->file_path ?? '', PATHINFO_EXTENSION));
                        @endphp

                        <div class="px-4 sm:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                            <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                                <div class="flex items-start gap-3 min-w-0 flex-1">
                                    <div class="p-2 rounded-xl bg-gray-100 dark:bg-gray-700 shrink-0">
                                        @include('livewire.teacher.forms.file-icons', ['extension' => $ext])
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white break-words sm:truncate">
                                            {{ $doc->title }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex flex-wrap gap-x-2 gap-y-1">
                                            <span>{{ $doc->created_at->format('d/m/Y H:i') }}</span>
                                            <span class="opacity-60">•</span>
                                            <span>{{ strtoupper($ext) }}</span>
                                            @if($doc->niveau?->sigle || $doc->niveau?->name)
                                                <span class="opacity-60">•</span>
                                                <span>{{ $doc->niveau?->sigle ?? $doc->niveau?->name }}</span>
                                            @endif
                                            @if($doc->parcour?->sigle || $doc->parcour?->name)
                                                <span class="opacity-60">•</span>
                                                <span>{{ $doc->parcour?->sigle ?? $doc->parcour?->name }}</span>
                                            @endif
                                            @if($doc->semestre?->name)
                                                <span class="opacity-60">•</span>
                                                <span>{{ $doc->semestre->name }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2">
                                            <span class="inline-flex w-max items-center px-2.5 py-1 rounded-full text-xs font-medium
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
                                </div>

                                {{-- CTA: en bas sur mobile --}}
                                <div class="w-full sm:w-auto">
                                    <a href="{{ route('document.serve', $doc) }}"
                                       target="_blank" rel="noopener"
                                       class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 rounded-xl text-sm font-semibold
                                              bg-gray-900 text-white hover:bg-gray-800
                                              dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                                        Ouvrir
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 sm:px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            Aucun document récent.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Niveaux & semestres --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="font-semibold text-gray-900 dark:text-white">Niveaux & semestres</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Vos affectations actives</div>
                </div>

                <div class="p-4 sm:p-5 space-y-3">
                    @forelse($niveauxSemestres as $niveau)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                            <div class="flex items-start sm:items-center justify-between gap-2">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white break-words">
                                    {{ $niveau['name'] }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 shrink-0">
                                    {{ count($niveau['semestres']) }} semestre(s)
                                </div>
                            </div>

                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($niveau['semestres'] as $s)
                                    <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium max-w-full
                                        {{ $s['is_active']
                                            ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                            : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">
                                        <span class="truncate">{{ $s['name'] }}</span>
                                        <span class="text-[11px] opacity-80 shrink-0">({{ (int) $s['documents_count'] }})</span>
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
        <div class="space-y-4 sm:space-y-6">

            {{-- Activité mensuelle --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="font-semibold text-gray-900 dark:text-white">Activité mensuelle</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Nombre d’uploads</div>
                </div>

                <div class="p-4 sm:p-5 space-y-2">
                    @forelse($monthlyStats as $index => $stat)
                        @php
                            $previousCount = isset($monthlyStats[$index + 1]) ? (int)$monthlyStats[$index + 1]->count : 0;
                            $evolution = (int)$stat->count - $previousCount;
                        @endphp

                        <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white break-words">
                                        {{ \Carbon\Carbon::parse($stat->month . '-01')->locale('fr')->isoFormat('MMMM YYYY') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ (int)$stat->count }} document(s)
                                    </div>
                                </div>

                                <div class="text-xs font-semibold shrink-0
                                    {{ $evolution > 0 ? 'text-green-600 dark:text-green-300' : ($evolution < 0 ? 'text-red-600 dark:text-red-300' : 'text-gray-500 dark:text-gray-400') }}">
                                    @if($evolution > 0) +{{ $evolution }}
                                    @elseif($evolution < 0) {{ $evolution }}
                                    @else 0
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">Aucune donnée.</div>
                    @endforelse
                </div>
            </div>

            {{-- Connexions récentes --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="font-semibold text-gray-900 dark:text-white">Connexions</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">5 dernières sessions</div>
                </div>

                <div class="p-4 sm:p-5 space-y-2">
                    @forelse($loginActivities as $a)
                        <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white break-words">
                                    {{ $a->ip_address ?? 'IP inconnue' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 shrink-0">
                                    {{ \Carbon\Carbon::createFromTimestamp($a->last_activity)->diffForHumans() }}
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 break-words">
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
