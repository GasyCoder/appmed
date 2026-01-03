@extends('layouts.landing')

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden">
    {{-- Background blobs --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-40 -right-40 h-[420px] w-[420px] rounded-full bg-indigo-600/15 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-[420px] w-[420px] rounded-full bg-purple-600/15 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

            {{-- Left --}}
            <div>
                <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold
                            bg-indigo-50 text-indigo-700
                            dark:bg-indigo-900/30 dark:text-indigo-200">
                    Plateforme officielle — Parcours EpiRC
                </div>

                <h1 class="mt-3 text-[26px] sm:text-3xl lg:text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Accédez aux ressources du parcours
                    <span class="text-indigo-600 dark:text-indigo-400">Épidémiologie & Recherche Clinique</span>
                </h1>

                <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed max-w-xl">
                    Consultez vos cours, documents et plannings en toute sécurité. Accès personnalisé selon votre rôle et niveau académique.
                </p>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-sm font-semibold
                              bg-gray-900 text-white hover:bg-gray-800 
                              focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-900 
                              transition
                              dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 dark:focus-visible:ring-white"
                       aria-label="Se connecter à son compte">
                        Accédez à votre compte
                    </a>
                    
                    <a href="#features"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-sm font-semibold
                              border border-gray-300 dark:border-gray-700
                              bg-white/70 dark:bg-gray-950/40
                              text-gray-800 dark:text-gray-200
                              hover:bg-gray-50 dark:hover:bg-gray-900
                              focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500
                              transition"
                       aria-label="Découvrir les fonctionnalités">
                        Découvrir
                    </a>
                </div>

                {{-- Trust badges --}}
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <div class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Accès sécurisé par rôle</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Traçabilité complète</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Données protégées</span>
                    </div>
                </div>
            </div>

            {{-- Right: video with skeleton --}}
            <div class="lg:pl-8" x-data="{ loading: {{ $isLoading ?? 'true' }} }" x-init="if (loading) setTimeout(() => loading = false, 750)">
                {{-- Skeleton --}}
                <div x-show="loading" x-cloak>
                    <x-skeleton.card :lines="5" />
                </div>

                {{-- Video content --}}
                <div x-show="!loading" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="rounded-3xl border border-gray-200/70 dark:border-gray-800/70 bg-white/70 dark:bg-gray-950/40 backdrop-blur shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200/70 dark:border-gray-800/70">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Vidéo de présentation</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Découvrez la plateforme en 2–3 minutes.</p>
                        </div>

                        <div class="w-full bg-black/5 dark:bg-white/5">
                            <div class="w-full min-h-[240px] sm:min-h-[300px] lg:min-h-[360px]">
                                <div class="aspect-video bg-black/5 dark:bg-white/5">
                                    <video
                                        class="w-full h-full"
                                        controls
                                        preload="metadata"
                                        playsinline
                                        poster="{{ asset('assets/video/poster.png') }}"
                                    >
                                        <source src="{{ asset('assets/video/presentation.mp4') }}" type="video/mp4">
                                        Votre navigateur ne supporte pas la lecture vidéo.
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features Section --}}
<section id="features" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12" x-data="{ loading: {{ $isLoading ?? 'true' }} }" x-init="if (loading) setTimeout(() => loading = false, 850)">
    <div class="text-center mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Fonctionnalités principales</h2>
        <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">Tout ce dont vous avez besoin pour réussir votre parcours.</p>
    </div>

    {{-- Skeleton --}}
    <div x-show="loading" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-skeleton.card :lines="3" />
        <x-skeleton.card :lines="3" />
        <x-skeleton.card :lines="3" />
    </div>

    {{-- Features content --}}
    <div x-show="!loading" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Feature 1 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-indigo-600/70"></div>
            <div class="flex items-start gap-3">
                <div class="h-9 w-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5 text-indigo-700 dark:text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Accès sécurisé</div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Emails universitaires autorisés, accès par rôle et niveau.
                    </div>
                </div>
            </div>
        </div>

        {{-- Feature 2 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-emerald-600/70"></div>
            <div class="flex items-start gap-3">
                <div class="h-9 w-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/25 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5 text-emerald-700 dark:text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Documents & cours</div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Consultez et téléchargez les supports rapidement.
                    </div>
                </div>
            </div>
        </div>

        {{-- Feature 3 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-amber-500/70"></div>
            <div class="flex items-start gap-3">
                <div class="h-9 w-9 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5 text-amber-700 dark:text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Plannings fiables</div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Emplois du temps centralisés et mis à jour.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section id="stats" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 border-t border-gray-200/50 dark:border-gray-800/50" x-data="{ loading: {{ $isLoading ?? 'true' }} }" x-init="if (loading) setTimeout(() => loading = false, 950)">
    <div class="text-center mb-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">La plateforme en chiffres</h2>
        <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">Des résultats qui témoignent de notre engagement.</p>
    </div>

    {{-- Skeleton --}}
    <div x-show="loading" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-skeleton.card :lines="2" :hasHeader="false" />
        <x-skeleton.card :lines="2" :hasHeader="false" />
        <x-skeleton.card :lines="2" :hasHeader="false" />
        <x-skeleton.card :lines="2" :hasHeader="false" />
    </div>

    {{-- Stats content --}}
    <div x-show="!loading" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Stat 1 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-6 text-center">
            <div class="text-3xl sm:text-4xl font-bold text-indigo-600 dark:text-indigo-400">150+</div>
            <div class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">Étudiants actifs</div>
        </div>

        {{-- Stat 2 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-6 text-center">
            <div class="text-3xl sm:text-4xl font-bold text-emerald-600 dark:text-emerald-400">500+</div>
            <div class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">Documents disponibles</div>
        </div>

        {{-- Stat 3 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-6 text-center">
            <div class="text-3xl sm:text-4xl font-bold text-amber-600 dark:text-amber-400">25+</div>
            <div class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">Cours en ligne</div>
        </div>

        {{-- Stat 4 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-6 text-center">
            <div class="text-3xl sm:text-4xl font-bold text-purple-600 dark:text-purple-400">99%</div>
            <div class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">Taux de disponibilité</div>
        </div>
    </div>
</section>

{{-- Updates Section --}}
<section id="updates" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 border-t border-gray-200/50 dark:border-gray-800/50" x-data="{ loading: {{ $isLoading ?? 'true' }} }" x-init="if (loading) setTimeout(() => loading = false, 1050)">
    <div class="flex items-end justify-between gap-4 flex-wrap mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Dernières actualités</h2>
            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">Restez informé des nouveautés et annonces importantes.</p>
        </div>
    </div>

    {{-- Skeleton --}}
    <div x-show="loading" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-skeleton.card :lines="4" />
        <x-skeleton.card :lines="4" />
        <x-skeleton.card :lines="4" />
    </div>

    {{-- Updates content --}}
    <div x-show="!loading" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Update 1 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5">
            <div class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">3 Janvier 2026</div>
            <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">Nouveaux cours disponibles</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Les supports de cours pour le module d'Épidémiologie Analytique sont maintenant disponibles dans votre espace.
            </p>
            <div class="mt-4">
                <span class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 dark:text-indigo-400">
                    En savoir plus
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Update 2 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5">
            <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">28 Décembre 2025</div>
            <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">Mise à jour du planning</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Le planning pour le semestre en cours a été mis à jour. Consultez vos nouveaux créneaux d'enseignement.
            </p>
            <div class="mt-4">
                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                    Voir le planning
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Update 3 --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5">
            <div class="text-xs font-semibold text-amber-600 dark:text-amber-400">20 Décembre 2025</div>
            <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">Amélioration de la plateforme</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Nouvelles fonctionnalités ajoutées : téléchargement par lot, mode sombre amélioré, et bien plus encore.
            </p>
            <div class="mt-4">
                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                    Découvrir
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </div>
    </div>
</section>

{{-- Support Section --}}
<section id="support" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 border-t border-gray-200/50 dark:border-gray-800/50" x-data="{ loading: {{ $isLoading ?? 'true' }} }" x-init="if (loading) setTimeout(() => loading = false, 1150)">
    <div class="flex items-end justify-between gap-4 flex-wrap mb-6">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Support</h2>
            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-300">Besoin d'aide ? Consultez la FAQ ou contactez l'équipe.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('faq') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold 
                      border border-gray-200 dark:border-gray-800 
                      hover:bg-gray-50 dark:hover:bg-gray-900 
                      focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                      transition"
               aria-label="Consulter la FAQ">
                FAQ
            </a>
            <a href="{{ route('help') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold 
                      bg-indigo-600 text-white 
                      hover:bg-indigo-700 
                      focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500
                      transition"
               aria-label="Contacter le support">
                Aide / Contact
            </a>
        </div>
    </div>

    {{-- Skeleton --}}
    <div x-show="loading" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-skeleton.card :lines="3" />
        <x-skeleton.card :lines="3" />
    </div>

    {{-- Support content --}}
    <div x-show="!loading" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Email refusé à l'inscription</div>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Seuls les emails universitaires autorisés peuvent continuer. Contactez la scolarité si nécessaire.
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Téléchargement / affichage</div>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Essayez Chrome/Firefox à jour. Si le problème persiste, envoyez une capture + l'URL via Aide/Contact.
            </div>
        </div>
    </div>
</section>
@endsection
