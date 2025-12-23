<x-app-layout>
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
         x-data="faqPage()"
         x-init="init()">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">FAQ</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Réponses rapides aux questions les plus fréquentes (connexion, emplois du temps, documents, scolarité…)
            </p>
        </div>

        {{-- Search + actions --}}
        <div class="mb-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                <div class="flex-1">
                    <label class="sr-only" for="faq-search">Rechercher</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>

                        <input id="faq-search"
                               type="text"
                               x-model.trim="query"
                               placeholder="Rechercher (ex : mot de passe, emploi du temps, document, téléchargement…)"
                               class="block w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white
                                      placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="expandAll()"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                                   border border-gray-200 dark:border-gray-700
                                   bg-gray-50 dark:bg-gray-700/40
                                   text-gray-700 dark:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        Tout ouvrir
                    </button>

                    <button type="button"
                            @click="collapseAll()"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                                   border border-gray-200 dark:border-gray-700
                                   bg-white dark:bg-gray-800
                                   text-gray-700 dark:text-gray-200
                                   hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 12H4"/>
                        </svg>
                        Tout fermer
                    </button>

                    <a href="{{ route('help') }}"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
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
                        ['q' => 'Je n’arrive pas à me connecter. Que vérifier en premier ?', 'a' => 'Vérifie l’adresse email, le mot de passe, puis l’état du clavier (Caps Lock). Si le problème persiste, essaye un autre navigateur ou vide le cache.'],
                        ['q' => '“Identifiants invalides” alors que je suis sûr du mot de passe.', 'a' => 'Assure-toi que l’email est exactement celui enregistré (sans espaces). Si tu as récemment changé de mot de passe, déconnecte/reconnecte.'],
                        ['q' => 'Mot de passe oublié : comment réinitialiser ?', 'a' => 'Utilise “Mot de passe oublié ?” sur l’écran de connexion. Tu recevras un lien de réinitialisation par email.'],
                        ['q' => 'Je ne reçois pas l’email de réinitialisation.', 'a' => 'Vérifie les spams. Attends 2–3 minutes. Si rien, retente la demande. Sinon, contacte le support via la page Aide/Contact.'],
                        ['q' => 'Je veux changer mon mot de passe.', 'a' => 'Va dans Profil > Sécurité (ou Paramètres du compte) si disponible. Sinon, utilise la procédure “mot de passe oublié”.'],
                        ['q' => 'Déconnexion automatique ou session expirée.', 'a' => 'Cela arrive si la session est expirée ou si tu as ouvert l’application sur plusieurs appareils. Reconnecte-toi et évite de multiplier les sessions.'],
                    ],
                ],
                [
                    'category' => 'Emploi du temps & Plannings',
                    'items' => [
                        ['q' => 'Je ne vois aucun emploi du temps.', 'a' => 'Vérifie les filtres (type) et la période (dates). Certains plannings sont publiés progressivement.'],
                        ['q' => 'Un emploi du temps n’est pas celui de mon niveau/parcours.', 'a' => 'Assure-toi que ton compte a bien le bon niveau/parcours. Si c’est incorrect, contacte la scolarité pour mise à jour.'],
                        ['q' => 'Le bouton “Voir” ouvre une page vide.', 'a' => 'Teste en navigation privée, puis vérifie si le fichier est bien accessible (problème de stockage / lien). Contacte le support si besoin.'],
                        ['q' => 'Téléchargement impossible.', 'a' => 'Teste un autre navigateur et vérifie ta connexion. Si le fichier a été remplacé/supprimé côté serveur, le support doit le republier.'],
                        ['q' => 'Le planning affiché est ancien.', 'a' => 'Le plus récent est généralement en haut (tri par date). Vérifie aussi le badge “année académique” et la période.'],
                    ],
                ],
                [
                    'category' => 'Documents & Cours',
                    'items' => [
                        ['q' => 'Je ne trouve pas un document de cours.', 'a' => 'Utilise la recherche et vérifie le bon module/semestre. Certains documents sont visibles selon ton niveau/parcours.'],
                        ['q' => 'Le PDF ne s’ouvre pas.', 'a' => 'Télécharge le fichier puis ouvre-le avec un lecteur PDF. Sur mobile, privilégie Chrome/Firefox.'],
                        ['q' => 'Un document est incomplet ou illisible.', 'a' => 'Signale-le au support avec le titre exact du document et une capture.'],
                        ['q' => 'Je vois “Accès refusé”.', 'a' => 'Tu n’as peut-être pas les droits (rôle, niveau, parcours). Contacte la scolarité pour vérifier ton profil.'],
                    ],
                ],
                [
                    'category' => 'Programmes & Scolarité',
                    'items' => [
                        ['q' => 'Mes informations de niveau/parcours sont incorrectes.', 'a' => 'Seule la scolarité peut corriger officiellement le niveau/parcours. Contacte-la via la page Aide/Contact.'],
                        ['q' => 'Je ne vois pas mes enseignants.', 'a' => 'Cela dépend de l’affectation des enseignants dans le système. Si les affectations ne sont pas encore saisies, la liste peut être vide.'],
                        ['q' => 'Je veux signaler une erreur dans un programme.', 'a' => 'Envoie le programme concerné (année/semestre) et la correction attendue à la scolarité.'],
                    ],
                ],
                [
                    'category' => 'Problèmes techniques (UI, navigateur, dark mode)',
                    'items' => [
                        ['q' => 'Le mode sombre/clair change tout seul.', 'a' => 'Vérifie que le navigateur n’efface pas le localStorage (mode privé, extensions). Essaie un autre navigateur pour comparer.'],
                        ['q' => 'Une interface ne répond plus après une navigation.', 'a' => 'Fais un “hard refresh” (Ctrl+F5) et teste si le problème est lié à Livewire navigation (wire:navigate).'],
                        ['q' => 'Des boutons disparaissent ou le texte ne s’affiche pas.', 'a' => 'C’est souvent lié à Alpine non initialisé ou à x-cloak/x-show. Recharge la page et vérifie la console du navigateur.'],
                        ['q' => 'Quel navigateur est recommandé ?', 'a' => 'Chrome ou Firefox à jour. Évite les versions anciennes, surtout sur mobile.'],
                    ],
                ],
            ];
        @endphp

        {{-- FAQ List --}}
        <div class="space-y-4">
            <template x-for="(block, i) in filteredFaqs()" :key="`cat-${i}`">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                    <div class="px-4 sm:px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="block.category"></h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="block.items.length"></span> question(s)
                            </p>
                        </div>

                        <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium
                                       border border-gray-200 dark:border-gray-700
                                       text-gray-700 dark:text-gray-200
                                       hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                @click="toggleCategory(block.category)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Ouvrir/Fermer
                        </button>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(item, j) in block.items" :key="`q-${i}-${j}`">
                            <div class="p-4 sm:p-5">
                                <button type="button"
                                        class="w-full flex items-start justify-between gap-4 text-left"
                                        @click="toggle(block.category, j)">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="item.q"></p>
                                    </div>

                                    <span class="flex-shrink-0 mt-0.5">
                                        <svg class="h-5 w-5 text-gray-400 transition-transform"
                                             :class="isOpen(block.category, j) ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </span>
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

            {{-- Empty state --}}
            <div x-show="filteredFaqs().length === 0"
                 x-cloak
                 class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-10 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-white">
                    Aucun résultat
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Essaie un autre mot-clé, ou contacte le support.
                </p>
                <div class="mt-4">
                    <a href="{{ route('help') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        Ouvrir Aide / Contact
                    </a>
                </div>
            </div>
        </div>

        <script>
            function faqPage() {
                return {
                    query: '',
                    openMap: {}, // { "Categorie": Set(indices) }

                    init() {
                        // Rien à casser : init minimal
                    },

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
                        if (allOpen) {
                            this.openMap[cat].clear();
                        } else {
                            block.items.forEach((_, idx) => this.openMap[cat].add(idx));
                        }
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
</x-app-layout>
