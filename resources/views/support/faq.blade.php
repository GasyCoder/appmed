@extends('layouts.landing')

@section('title', 'FAQ — EpiRC')

@section('content')
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-6 pb-16"
         x-data="faqPage()"
         x-init="init()">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">FAQ</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Réponses rapides aux questions fréquentes (connexion, documents, plannings…)
                </p>
            </div>

            <a href="{{ route('home') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                      border border-gray-200 dark:border-gray-800
                      bg-white dark:bg-gray-950
                      text-gray-700 dark:text-gray-300
                      hover:bg-gray-50 dark:hover:bg-gray-900
                      shadow-sm hover:shadow transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>

        {{-- Search + actions --}}
        <div class="mt-5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
            <div class="space-y-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>

                    <input id="faq-search"
                           type="text"
                           x-model.trim="query"
                           placeholder="Rechercher (ex: mot de passe, téléchargement, emploi du temps...)"
                           class="block w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700
                                  bg-white dark:bg-gray-950 text-sm text-gray-900 dark:text-white
                                  placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div class="grid grid-cols-2 sm:flex sm:items-center gap-2">
                    <button type="button"
                            @click="expandAll()"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium
                                   border border-gray-200 dark:border-gray-800
                                   bg-gray-50 dark:bg-gray-800/60
                                   text-gray-700 dark:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="hidden sm:inline">Tout ouvrir</span>
                        <span class="sm:hidden">Ouvrir</span>
                    </button>

                    <button type="button"
                            @click="collapseAll()"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium
                                   border border-gray-200 dark:border-gray-800
                                   bg-white dark:bg-gray-900
                                   text-gray-700 dark:text-gray-200
                                   hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        <span class="hidden sm:inline">Tout fermer</span>
                        <span class="sm:hidden">Fermer</span>
                    </button>

                    <a href="{{ route('help') }}"
                       class="col-span-2 sm:col-span-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium
                              bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                        </svg>
                        Aide / Contact
                    </a>
                </div>
            </div>
        </div>

        @php
            $faqs = [
                [
                    'category' => 'Connexion & Compte',
                    'items' => [
                        ['q' => "Je n'arrive pas à me connecter. Que vérifier en premier ?", 'a' => "Vérifie l'adresse email, le mot de passe et l’état du clavier (Caps Lock). Si besoin, teste un autre navigateur ou vide le cache."],
                        ['q' => "« Identifiants invalides » alors que je suis sûr du mot de passe.", 'a' => "Assure-toi que l’email est exact (sans espaces). Si tu as changé le mot de passe, déconnecte/reconnecte."],
                        ['q' => "Mot de passe oublié : comment réinitialiser ?", 'a' => "Clique « Mot de passe oublié ? » sur l’écran de connexion. Tu recevras un lien par email."],
                        ['q' => "Je ne reçois pas l’email de réinitialisation.", 'a' => "Vérifie les spams, attends 2–3 minutes puis retente. Sinon, contacte le support via Aide/Contact."],
                    ],
                ],
                [
                    'category' => 'Emploi du temps & Plannings',
                    'items' => [
                        ['q' => "Je ne vois aucun emploi du temps.", 'a' => "Vérifie la période. Certains plannings sont publiés progressivement."],
                        ['q' => "Téléchargement impossible.", 'a' => "Teste un autre navigateur et vérifie la connexion. Si le fichier a été remplacé, il doit être republié."],
                    ],
                ],
                [
                    'category' => 'Documents & Cours',
                    'items' => [
                        ['q' => "Je ne trouve pas un document.", 'a' => "Utilise la recherche et vérifie le bon module/semestre. Certains documents sont visibles selon le niveau/parcours."],
                        ['q' => "Le PDF ne s’ouvre pas.", 'a' => "Télécharge le fichier puis ouvre-le dans un lecteur PDF. Sur mobile, privilégie Chrome/Firefox."],
                    ],
                ],
                [
                    'category' => 'Problèmes techniques (navigateur, dark mode)',
                    'items' => [
                        ['q' => "Le mode sombre/clair change tout seul.", 'a' => "Vérifie les extensions et le mode navigation privée. Certaines configurations effacent le localStorage."],
                        ['q' => "Quel navigateur est recommandé ?", 'a' => "Chrome ou Firefox à jour. Évite les versions anciennes, surtout sur mobile."],
                    ],
                ],
            ];
        @endphp

        {{-- FAQ list --}}
        <div class="mt-5 space-y-3">
            <template x-for="(block, i) in filteredFaqs()" :key="`cat-${i}`">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="block.category"></h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="block.items.length"></span> question(s)
                            </p>
                        </div>

                        <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-medium
                                       border border-gray-200 dark:border-gray-800
                                       text-gray-700 dark:text-gray-200
                                       hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                                @click="toggleCategory(block.category)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ouvrir/Fermer
                        </button>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        <template x-for="(item, j) in block.items" :key="`q-${i}-${j}`">
                            <div class="p-4">
                                <button type="button"
                                        class="w-full flex items-start justify-between gap-3 text-left"
                                        @click="toggle(block.category, j)">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="item.q"></p>
                                    <svg class="h-5 w-5 text-gray-400 transition-transform"
                                         :class="isOpen(block.category, j) ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-cloak
                                     x-show="isOpen(block.category, j)"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-1"
                                     class="mt-3 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    <p x-text="item.a"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div x-show="filteredFaqs().length === 0"
                 x-cloak
                 class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-10 text-center">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Aucun résultat</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Essaie un autre mot-clé.</p>
                <a href="{{ route('help') }}"
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    Ouvrir Aide / Contact
                </a>
            </div>
        </div>

        <script>
            function faqPage() {
                return {
                    query: '',
                    openMap: {},

                    init() {},

                    normalize(s) {
                        return (s || '')
                            .toString()
                            .toLowerCase()
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '');
                    },

                    filteredFaqs() {
                        const q = this.normalize(this.query);
                        const blocks = @json($faqs);
                        if (!q) return blocks;

                        return blocks
                            .map(b => {
                                const items = b.items.filter(it => {
                                    const hay = this.normalize(it.q + ' ' + it.a);
                                    return hay.includes(q);
                                });
                                return { category: b.category, items };
                            })
                            .filter(b => b.items.length > 0);
                    },

                    ensureCategory(cat) {
                        if (!this.openMap[cat]) this.openMap[cat] = new Set();
                    },

                    isOpen(cat, idx) {
                        this.ensureCategory(cat);
                        return this.openMap[cat].has(idx);
                    },

                    toggle(cat, idx) {
                        this.ensureCategory(cat);
                        if (this.openMap[cat].has(idx)) this.openMap[cat].delete(idx);
                        else this.openMap[cat].add(idx);
                    },

                    toggleCategory(cat) {
                        const blocks = this.filteredFaqs();
                        const block = blocks.find(b => b.category === cat);
                        if (!block) return;

                        this.ensureCategory(cat);

                        const allOpen = block.items.every((_, idx) => this.openMap[cat].has(idx));
                        if (allOpen) this.openMap[cat].clear();
                        else block.items.forEach((_, idx) => this.openMap[cat].add(idx));
                    },

                    expandAll() {
                        const blocks = this.filteredFaqs();
                        blocks.forEach(b => {
                            this.ensureCategory(b.category);
                            b.items.forEach((_, idx) => this.openMap[b.category].add(idx));
                        });
                    },

                    collapseAll() {
                        Object.keys(this.openMap).forEach(k => this.openMap[k].clear());
                    }
                }
            }
        </script>

    </div>

@endsection
