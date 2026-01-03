@extends('layouts.landing')

@section('title', 'Aide / Contact — EpiRC')

@section('content')
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-6 pb-16" x-data="{ copied: null }">

        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Aide / Contact</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Contactez l’équipe technique ou la scolarité. Avant d’écrire, consultez la FAQ.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('faq') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          border border-gray-200 dark:border-gray-800
                          bg-white dark:bg-gray-950
                          text-gray-700 dark:text-gray-300
                          hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                    Ouvrir la FAQ
                </a>

                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          border border-gray-200 dark:border-gray-800
                          bg-white dark:bg-gray-950
                          text-gray-700 dark:text-gray-300
                          hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                    Retour
                </a>
            </div>
        </div>

        {{-- Guidance --}}
        <div class="mt-5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Avant de contacter</p>
            <ul class="mt-2 text-sm text-gray-600 dark:text-gray-300 list-disc pl-5 space-y-1">
                <li>Indique ton rôle (étudiant/enseignant/admin), et ton niveau/parcours si applicable.</li>
                <li>Ajoute une capture + l’URL + l’heure approximative du souci.</li>
                <li>Précise ton navigateur et ton appareil (PC / mobile).</li>
            </ul>
        </div>

        {{-- Cards --}}
        <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Informatique --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Service Informatique / Développeur</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bugs, accès, affichage, téléchargement</p>

                <div class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 p-4">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Mon SiteWeb</p>

                    <div class="mt-1 flex items-center justify-between gap-2">
                        <p class="text-sm text-gray-900 dark:text-white truncate">Développeur</p>

                        <div class="flex items-center gap-2">
                            <a class="px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition"
                               href="https://me.gasycoder.com" target="_blank">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scolarité --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Scolarité</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Niveau, parcours, inscriptions, corrections officielles</p>

                <div class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 p-4">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Email</p>

                    <div class="mt-1 flex items-center justify-between gap-2">
                        <p class="text-sm text-gray-900 dark:text-white truncate">facmed@mahajanga-univ.mg</p>

                        <div class="flex items-center gap-2">
                            <a class="px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-600 text-white hover:bg-emerald-700 transition"
                               href="mailto:facmed@mahajanga-univ.mgg?subject=EpiRC%20-%20Demande%20Scolarit%C3%A9">
                                Écrire
                            </a>

                            <button type="button"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 dark:border-gray-700
                                           text-gray-700 dark:text-gray-200 hover:bg-white/70 dark:hover:bg-gray-800 transition"
                                    @click="navigator.clipboard.writeText('facmed@mahajanga-univ.mg'); copied='facmed'; setTimeout(() => copied=null, 1200)">
                                Copier
                            </button>
                        </div>
                    </div>

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-show="copied==='scolarite'" x-cloak>Copié.</p>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <p class="text-sm text-gray-700 dark:text-gray-200">Besoin d’une réponse rapide ? Consulte la FAQ d’abord.</p>
                <a href="{{ route('faq') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    Ouvrir FAQ
                </a>
            </div>
        </div>

    </div>

@endsection
