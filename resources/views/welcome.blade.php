@extends('layouts.landing')

@section('content')
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
                    Plateforme officielle pour consulter les cours, documents et plannings, avec accès sécurisé selon votre rôle et votre niveau.
                </p>

                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-sm font-semibold
                              bg-gray-900 text-white hover:bg-gray-800 transition
                              dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        Accédez à votre compte
                    </a>
                </div>

                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    Inscription réservée aux emails universitaires autorisés.
                </p>
            </div>

            {{-- Right: video --}}
            <div class="lg:pl-8">
                <div class="rounded-3xl border border-gray-200/70 dark:border-gray-800/70 bg-white/70 dark:bg-gray-950/40 backdrop-blur shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200/70 dark:border-gray-800/70">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Vidéo de présentation</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Découvrez la plateforme en 2–3 minutes.</p>
                    </div>

                    {{-- ✅ plus grand que aspect-video --}}
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

        {{-- Features (accent couleurs sobres) --}}
        <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- 1 --}}
            <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5 relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1 bg-indigo-600/70"></div>
                <div class="flex items-start gap-3">
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-700 dark:text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.657-1.343 3-3 3S6 12.657 6 11s1.343-3 3-3 3 1.343 3 3zm0 0c0 1.657 1.343 3 3 3s3-1.343 3-3-1.343-3-3-3-3 1.343-3 3z"/>
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

            {{-- 2 --}}
            <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5 relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1 bg-emerald-600/70"></div>
                <div class="flex items-start gap-3">
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/25 flex items-center justify-center">
                        <svg class="h-5 w-5 text-emerald-700 dark:text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            {{-- 3 --}}
            <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5 relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1 bg-amber-500/70"></div>
                <div class="flex items-start gap-3">
                    <div class="h-9 w-9 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                        <svg class="h-5 w-5 text-amber-700 dark:text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    </div>
</section>

{{-- Support --}}
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-end justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Support</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Besoin d’aide ? Consulte la FAQ ou contacte l’équipe.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('faq') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                FAQ
            </a>
            <a href="{{ route('help') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">
                Aide / Contact
            </a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Email refusé à l’inscription</div>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Seuls les emails universitaires autorisés peuvent continuer. Contactez la scolarité si nécessaire.
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Téléchargement / affichage</div>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Essayez Chrome/Firefox à jour. Si le problème persiste, envoyez une capture + l’URL via Aide/Contact.
            </div>
        </div>
    </div>
</section>
@endsection
