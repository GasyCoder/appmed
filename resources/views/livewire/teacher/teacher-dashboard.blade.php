<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section avec Profil -->
    <div class="relative rounded-sm overflow-hidden bg-gradient-to-br from-blue-600 to-red-400 dark:from-blue-800 dark:to-red-100">
        <div class="absolute inset-0 bg-grid-white/[0.05] dark:bg-grid-white/[0.02]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="relative z-10 flex flex-col items-center gap-8 md:flex-row md:items-center">
                <!-- Photo de profil et infos de base -->
                <div class="flex-shrink-0 flex justify-center md:justify-start">
                    <div class="relative group">
                        <div class="relative">
                            <img class="h-32 w-32 rounded-2xl object-cover ring-4 ring-white/10 shadow-2xl transform transition hover:scale-105"
                                src="{{ auth()->user()->profile_photo_url }}"
                                alt="{{ auth()->user()->name }}">
                            <div class="absolute -bottom-1.5 -right-1.5 h-6 w-6 bg-green-400 rounded-full ring-4 ring-white dark:ring-gray-900"></div>
                        </div>
                        @if(auth()->user()->hasRole('teacher'))
                            <a href="{{ route('profile.show') }}"
                            class="absolute -bottom-3 -right-3 bg-white dark:bg-gray-800 p-2.5 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Informations du profil -->
                <div class="flex-1 text-center md:text-left">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 flex-wrap justify-center md:justify-start">
                            <h1 class="text-3xl font-bold text-white">
                                {{ auth()->user()->profil?->grade ?? '' }} {{ auth()->user()->name }}
                            </h1>
                            <span class="px-3 py-1 text-sm font-medium bg-white/10 text-white rounded-full backdrop-blur-sm">
                                Enseignant
                            </span>
                        </div>
                        <p class="text-indigo-200">{{ auth()->user()->email }}</p>
                    </div>

                    <!-- Contact Info -->
                    <div class="mt-6 flex flex-wrap gap-4 justify-center md:justify-start">
                        @if(auth()->user()->profil?->telephone)
                            <div class="group flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl hover:bg-white/20 transition">
                                <svg class="h-5 w-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="ml-2 text-sm text-white">{{ auth()->user()->profil->telephone }}</span>
                            </div>
                        @endif

                        @if(auth()->user()->profil?->ville)
                            <div class="group flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl hover:bg-white/20 transition">
                                <svg class="h-5 w-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="ml-2 text-sm text-white">
                                    {{ auth()->user()->profil->ville }}
                                    @if(auth()->user()->profil->departement)
                                        · {{ auth()->user()->profil->departement }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex-shrink-0 mt-6 md:mt-0 md:border-l md:border-white/10 md:pl-6 max-w-sm mx-auto md:mx-0">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-white">Niveaux & Semestres</h3>
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-white/10 text-white rounded-full">
                                {{ $stats['niveaux_count'] }} niveau(x)
                            </span>
                        </div>

                        <div class="space-y-2 max-h-32 overflow-y-auto scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent">
                            @forelse($niveauxSemestres as $niveau)
                                <div class="bg-white/5 rounded-lg p-2">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs font-medium text-white">{{ $niveau['name'] }}</span>
                                        <span class="text-xs text-indigo-200">{{ count($niveau['semestres']) }} sem.</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($niveau['semestres'] as $semestre)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{
                                                $semestre['is_active']
                                                    ? 'bg-green-400/20 text-green-200'
                                                    : 'bg-white/5 text-gray-300'
                                            }}">
                                                <span class="w-1 h-1 rounded-full mr-1 {{
                                                    $semestre['is_active'] ? 'bg-green-400' : 'bg-gray-400'
                                                }}"></span>
                                                {{ $semestre['name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-2">
                                    <span class="text-xs text-gray-300">Aucun niveau assigné</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<!-- Contenu principal -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Documents récents - Maintenant à gauche -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Documents récents
                    </h2>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $document)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-500/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $document->title }}
                                    </h4>
                                    <div class="mt-1 flex items-center gap-4">
                                        <span class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ $document->view_count }} vues
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                            $document->is_actif
                                                ? 'bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300'
                                                : 'bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-300'
                                        }}">
                                            {{ $document->is_actif ? 'Partagé' : 'Non partagé' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                Aucun document récent
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>


        <!-- Section droite avec les activités -->
        <div class="space-y-6">
            <!-- Activité mensuelle -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Activité mensuelle
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($monthlyStats as $index => $stat)
                        @php
                            $previousCount = isset($monthlyStats[$index + 1]) ? $monthlyStats[$index + 1]->count : 0;
                            $evolution = $stat->count - $previousCount;
                        @endphp

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ Carbon\Carbon::parse($stat->month)->locale('fr')->isoFormat('MMMM YYYY') }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $stat->count }} documents
                                </p>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                $evolution > 0
                                    ? 'bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-300'
                                    : ($evolution < 0
                                        ? 'bg-red-50 dark:bg-red-500/20 text-red-700 dark:text-red-300'
                                        : 'bg-gray-50 dark:bg-gray-500/20 text-gray-700 dark:text-gray-300')
                            }}">
                                @if($evolution > 0)
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    +{{ $evolution }}
                                @elseif($evolution < 0)
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ $evolution }}
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                    0
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Activités de connexion -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                        Activités de connexion
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($loginActivities as $activity)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-500 dark:text-white">
                                    Votre adresse Ip:
                                    <strong>
                                        {{ $activity->ip_address ?? 'Inconnu' }}
                                    </strong>
                                </p>
                                <p class="text-sm text-gray-500 dark:text-white">
                                    Dernière connexion :
                                    <strong>
                                        {{ \Carbon\Carbon::createFromTimestamp($activity->last_activity)->translatedFormat('d M Y, H:i') }}
                                    </strong>
                                </p>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Aucune activité de connexion récente
                            </p>
                        </div>
                    @endforelse
                </div>



            </div>

        </div>

    </div>
</di>
