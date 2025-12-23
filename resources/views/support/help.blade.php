<x-app-layout>
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ copied: null }">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Aide / Contact</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Contactez l’équipe technique ou la scolarité. Avant d’écrire, consultez la FAQ.
                    </p>
                </div>
                <a href="{{ route('faq') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                          border border-gray-200 dark:border-gray-700
                          bg-white dark:bg-gray-800
                          text-gray-700 dark:text-gray-200
                          hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8.228 9a3 3 0 115.544 0c0 1.5-1.5 2.25-1.5 2.25S11 12 11 13m1 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                    </svg>
                    Ouvrir la FAQ
                </a>
            </div>
        </div>

        {{-- Guidance --}}
        <div class="mb-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Avant de contacter</p>
                    <ul class="mt-1 text-sm text-gray-600 dark:text-gray-300 list-disc pl-5 space-y-1">
                        <li>Indique ton rôle (étudiant/enseignant/admin), ton niveau/parcours si applicable.</li>
                        <li>Ajoute une capture + l’URL de la page + l’heure approximative du souci.</li>
                        <li>Précise ton navigateur (Chrome/Firefox) et ton appareil (PC / mobile).</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Contact cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Informatique / Développeur --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 6h14a2 2 0 012 2v5H3V8a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Service Informatique / Développeur</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Bugs, accès, affichage, téléchargement, performance</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Email</p>
                        {{-- TODO: remplace par ton vrai email support --}}
                        <div class="mt-1 flex items-center justify-between gap-2">
                            <p class="text-sm text-gray-900 dark:text-white truncate">support.informatique@exemple.umg</p>
                            <div class="flex items-center gap-2">
                                <a class="px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition"
                                   href="mailto:support.informatique@exemple.umg?subject=AppMed%20-%20Assistance%20technique">
                                    Écrire
                                </a>
                                <button type="button"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 dark:border-gray-600
                                               text-gray-700 dark:text-gray-200 hover:bg-white/70 dark:hover:bg-gray-700 transition"
                                        @click="navigator.clipboard.writeText('support.informatique@exemple.umg'); copied='support'; setTimeout(() => copied=null, 1200)">
                                    Copier
                                </button>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-show="copied==='support'" x-cloak>
                            Copié.
                        </p>
                    </div>

                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">À fournir</p>
                        <ul class="mt-1 text-sm text-gray-600 dark:text-gray-300 list-disc pl-5 space-y-1">
                            <li>Capture + message d’erreur (si affiché)</li>
                            <li>Page concernée (ex: Emploi du temps / Documents)</li>
                            <li>Heure du problème + navigateur</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Scolarité --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 14v7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 17l14-8"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Scolarité</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Niveau, parcours, inscriptions, affectations, corrections officielles</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Email</p>
                        {{-- TODO: remplace par le vrai email scolarité --}}
                        <div class="mt-1 flex items-center justify-between gap-2">
                            <p class="text-sm text-gray-900 dark:text-white truncate">scolarite@exemple.umg</p>
                            <div class="flex items-center gap-2">
                                <a class="px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-600 text-white hover:bg-emerald-700 transition"
                                   href="mailto:scolarite@exemple.umg?subject=AppMed%20-%20Demande%20Scolarit%C3%A9">
                                    Écrire
                                </a>
                                <button type="button"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 dark:border-gray-600
                                               text-gray-700 dark:text-gray-200 hover:bg-white/70 dark:hover:bg-gray-700 transition"
                                        @click="navigator.clipboard.writeText('scolarite@exemple.umg'); copied='scolarite'; setTimeout(() => copied=null, 1200)">
                                    Copier
                                </button>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-show="copied==='scolarite'" x-cloak>
                            Copié.
                        </p>
                    </div>

                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/40 p-3">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">À fournir</p>
                        <ul class="mt-1 text-sm text-gray-600 dark:text-gray-300 list-disc pl-5 space-y-1">
                            <li>Nom + email institutionnel</li>
                            <li>Matricule (si étudiant) / identifiant (si enseignant)</li>
                            <li>Niveau + parcours + semestre</li>
                            <li>La correction demandée (ex: changer parcours)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer CTA --}}
        <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <p class="text-sm text-gray-700 dark:text-gray-200">
                    Besoin d’une réponse rapide ? Consulte la FAQ d’abord.
                </p>
                <a href="{{ route('faq') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    Ouvrir FAQ
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
