<div class="min-h-screen  text-gray-900 dark:text-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<!-- En-tête du profil -->
<div class="bg-gradient-to-r from-white dark:from-gray-800 to-indigo-50/30 dark:to-gray-700/50 shadow-xl rounded-xl overflow-hidden mb-6">
    <div class="md:flex">
        <!-- Section gauche - Photo et infos principales -->
        <div class="p-8 flex-1">
            <div class="flex items-start space-x-6">
                <!-- Photo de profil avec badge de statut -->
                <div class="relative group">
                    <div class="relative">
                        <img class="h-24 w-24 rounded-2xl object-cover shadow-xl ring-2 ring-indigo-100 dark:ring-indigo-500/20"
                            src="{{ auth()->user()->profile_photo_url }}"
                            alt="{{ auth()->user()->name }}">
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                    </div>
                    @if(auth()->user()->hasRole('teacher'))
                        <a href="{{ route('profile.show') }}"
                           class="absolute -bottom-3 -right-3 bg-white dark:bg-gray-700 p-2 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>
                    @endif
                </div>

                <!-- Informations du profil -->
                <div class="flex-1 space-y-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            {{ auth()->user()->profil?->grade ?? '' }} {{ auth()->user()->name }}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300">
                                Enseignant
                            </span>
                        </h2>
                        <p class="text-indigo-600 dark:text-indigo-400 font-medium">{{ auth()->user()->email }}</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @if(auth()->user()->profil?->telephone)
                            <div class="group flex items-center bg-white dark:bg-gray-800 px-4 py-2 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="p-1.5 bg-indigo-50 dark:bg-indigo-500/20 rounded-lg group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/30 transition-colors">
                                    <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-300">{{ auth()->user()->profil->telephone }}</span>
                            </div>
                        @endif

                        @if(auth()->user()->profil?->ville)
                            <div class="group flex items-center bg-white dark:bg-gray-800 px-4 py-2 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="p-1.5 bg-indigo-50 dark:bg-indigo-500/20 rounded-lg group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/30 transition-colors">
                                    <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                                    {{ auth()->user()->profil->ville }}
                                    @if(auth()->user()->profil->departement)
                                        <span class="text-gray-400 dark:text-gray-500 mx-1">•</span>
                                        {{ auth()->user()->profil->departement }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Niveaux et Semestres -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Niveaux & Semestres
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-500/20 text-indigo-800 dark:text-indigo-200">
                            {{ $stats['niveaux_count'] }} niveau(x)
                        </span>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        @forelse($niveauxSemestres as $niveau)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    {{ $niveau['name'] }}
                                </h4>
                                <div class="space-y-3">
                                    @foreach($niveau['semestres'] as $semestre)
                                        <div class="flex items-center justify-between bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-lg px-4 py-3 hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-2.5 h-2.5 rounded-full {{ $semestre['is_active'] ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $semestre['name'] }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $semestre['documents_count'] }} documents)</span>
                                            </div>
                                            @if($semestre['is_active'])
                                                <span class="px-2.5 py-1 text-xs font-medium bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-300 rounded-full">
                                                    Actif
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 flex items-center justify-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">
                                    Aucun niveau assigné
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Section droite - Statistiques -->
        <div class="border-l shadow-xl border-indigo-100 dark:border-gray-700 bg-gradient-to-br from-indigo-50/50 dark:from-gray-800 to-blue-50/50 dark:to-gray-700/50 p-8 md:w-96">
            <h3 class="text-sm font-medium text-indigo-600 dark:text-indigo-400 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Vue d'ensemble
            </h3>
            <div class="grid grid-cols-1 gap-2">
                <!-- Niveaux -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden">
                    <div class="relative flex flex-col items-center">
                        <div class="p-2 bg-indigo-50 rounded-lg mb-3 group-hover:bg-indigo-100 transition-colors">
                            <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="block text-2xl font-bold text-indigo-600 mb-1 group-hover:text-indigo-700">
                            {{ $stats['niveaux_count'] }}
                        </span>
                        <span class="text-sm font-medium text-gray-500 group-hover:text-gray-600">Niveaux</span>
                    </div>
                </div>

                <!-- Parcours -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden">
                    <div class="relative flex flex-col items-center">
                        <div class="p-2 bg-blue-50 rounded-lg mb-3 group-hover:bg-blue-100 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="block text-2xl font-bold text-blue-600 mb-1 group-hover:text-blue-700">
                            {{ auth()->user()->teacherStats['parcours_count'] }}
                        </span>
                        <span class="text-sm font-medium text-gray-500 group-hover:text-gray-600">Parcours</span>
                    </div>
                </div>

                <!-- Documents -->
                <div class="group bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden">
                    <div class="relative flex flex-col items-center">
                        <div class="p-2 bg-purple-50 rounded-lg mb-3 group-hover:bg-purple-100 transition-colors">
                            <svg class="w-6 h-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="block text-2xl font-bold text-purple-600 mb-1 group-hover:text-purple-700">
                            {{ auth()->user()->teacherStats['documents_count'] }}
                        </span>
                        <span class="text-sm font-medium text-gray-500 group-hover:text-gray-600">Documents</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Documents récents -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Documents récents</h3>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $document)
                        <li class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                            {{ $document->title }}
                                        </h4>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $document->view_count }} vues
                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                            $document->is_actif
                                                ? 'bg-green-50 dark:bg-green-500/20 text-green-700 dark:text-green-400 ring-1 ring-green-600/20 dark:ring-green-500/30'
                                                : 'bg-yellow-50 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 ring-1 ring-yellow-600/20 dark:ring-yellow-500/30'
                                        }}">
                                            {{ $document->is_actif ? 'Partagé' : 'Non partagé' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Aucun document récent</span>
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Statistiques mensuelles -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Activité mensuelle</h3>
                </div>
                <div class="p-6">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left text-sm font-medium text-gray-500 dark:text-gray-400 pb-3">Mois</th>
                                <th class="text-right text-sm font-medium text-gray-500 dark:text-gray-400 pb-3">Documents</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($monthlyStats as $stat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $stat->month)->format('F Y') }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $stat->count }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 </div>
