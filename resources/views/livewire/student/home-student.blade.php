<div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-6 space-y-6">

    @php
        use Illuminate\Support\Facades\DB;
        use Illuminate\Support\Facades\Schema;

        // =========================
        // Compteur annonces (safe)
        // =========================
        $annCount = 0;

        if (Schema::hasTable('announcements') && auth()->check()) {
            $roles = method_exists(auth()->user(), 'getRoleNames')
                ? auth()->user()->getRoleNames()->values()->all()
                : [];

            $q = DB::table('announcements')
                ->where('is_active', 1)
                ->where(function ($qq) {
                    $qq->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($qq) {
                    $qq->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->where(function ($qq) use ($roles) {
                    $qq->whereNull('audience_roles');
                    foreach ($roles as $r) {
                        $qq->orWhereRaw("JSON_CONTAINS(audience_roles, JSON_QUOTE(?))", [$r]);
                    }
                });

            $annCount = (int) $q->count();
        }

        // =========================
        // Menus avec COMPTEURS
        // =========================
        $primaryMenus = [
            [
                'label' => 'Mes cours',
                'desc'  => 'Documents, PDF, supports',
                'href'  => route('student.document'),
                'icon'  => 'doc',
                'active'=> request()->routeIs('student.document'),
                'badge' => $menuStats['documents'] ?? 0,  // ✅ Documents NON VUS
                'badgeColor' => 'bg-red-500',
            ],
            [
                'label' => 'Emploi du temps',
                'desc'  => 'Planning et horaires',
                'href'  => route('student.timetable'),
                'icon'  => 'calendar',
                'active'=> request()->routeIs('student.timetable'),
                'badge' => $menuStats['schedules'] ?? 0,  // ✅ Schedules NON VUS
                'badgeColor' => 'bg-emerald-500',
            ],
            [
                'label' => 'Mes enseignants',
                'desc'  => 'Liste des enseignants',
                'href'  => route('student.myTeacher'),
                'icon'  => 'users',
                'active'=> request()->routeIs('student.myTeacher'),
                'badge' => null,  // ✅ PAS DE BADGE
                'badgeColor' => 'bg-purple-500',
            ],
            [
                'label' => 'Programmes',
                'desc'  => 'Consulter les programmes',
                'href'  => route('programs'),
                'icon'  => 'book',
                'active'=> request()->routeIs('programs'),
                'badge' => null,
                'badgeColor' => 'bg-gray-500',
            ],
        ];

        // Support en LISTE (avec séparateurs)
        $supportMenus = [
            [
                'label' => 'Annonces',
                'desc'  => 'Informations scolarité',
                'href'  => route('announcements.index'),
                'icon'  => 'bell',
                'active'=> request()->routeIs('announcements.index'),
                'badge' => $annCount,
                'badgeColor' => 'bg-amber-500',
            ],
            [
                'label' => 'FAQ',
                'desc'  => 'Questions fréquentes',
                'href'  => route('faq'),
                'icon'  => 'help',
                'active'=> request()->routeIs('faq'),
                'badge' => null,
            ],
            [
                'label' => 'Aide',
                'desc'  => 'Support et guide',
                'href'  => route('help'),
                'icon'  => 'support',
                'active'=> request()->routeIs('help'),
                'badge' => null,
            ],
        ];

        // =========================
        // Icônes SVG (PLUS GRANDES)
        // =========================
        $iconSvg = function(string $name) {
            return match($name) {
                'doc' => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <line x1="10" y1="9" x2="8" y2="9" />
                                </svg>',
                'calendar' => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 7V3m8 4V3M4 11h16"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6 5h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/>
                              </svg>',
                'users' => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>',
                'book' => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="4" width="16" height="16" rx="2" />
                            <path d="M4 8h16" />
                            <path d="M9 12h6" />
                            <path d="M9 16h6" />
                            </svg>',
                'help' => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.09 9a3 3 0 1 1 5.82 1c0 2-3 2-3 4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01"/>
                          </svg>',
                'support' => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                               <path stroke-linecap="round" stroke-linejoin="round"
                                     d="M4 4h16v12H5.17L4 17.17V4z"/>
                               <path stroke-linecap="round" stroke-linejoin="round"
                                     d="M8 9h8M8 12h6"/>
                             </svg>',
                'bell' => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                          </svg>',
                default => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                           </svg>',
            };
        };

        // =========================
        // Couleurs icônes
        // =========================
        $iconTone = function(string $icon) {
            return match($icon) {
                'doc' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200
                          dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20',
                'calendar' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200
                               dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20',
                'users' => 'bg-purple-50 text-purple-700 ring-1 ring-purple-200
                            dark:bg-purple-500/10 dark:text-purple-300 dark:ring-purple-500/20',
                'book' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200
                           dark:bg-orange-500/10 dark:text-orange-300 dark:ring-orange-500/20',
                'bell' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200
                           dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/20',
                'help' => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200
                           dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-500/20',
                'support' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200
                              dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20',
                default => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200
                            dark:bg-gray-900 dark:text-gray-200 dark:ring-white/10',
            };
        };
    @endphp


    {{-- =========================
         MENU PRINCIPAL (GRID) - AVEC BADGES
    ========================== --}}
   <div class="border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Menu étudiant</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Accès rapide</div>
        </div>

        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($primaryMenus as $item)
                    @php
                        $active = (bool) $item['active'];
                        $border = $active ? 'border-indigo-400/70 dark:border-indigo-500/70 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-gray-800';
                        
                        $badgeValue = $item['badge'] ?? null;
                        $badgeColor = $item['badgeColor'] ?? 'bg-gray-500';
                    @endphp

                    <a href="{{ $item['href'] }}"
                       class="group relative rounded-2xl border {{ $border }}
                              bg-white dark:bg-gray-950/40
                              p-4 sm:p-5
                              shadow-xl shadow-gray-900/5 dark:shadow-black/30
                              hover:bg-gray-50 dark:hover:bg-gray-900/50
                              hover:-translate-y-0.5 transition
                              focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/60">

                        <div class="flex flex-col items-center text-center gap-3">
                            {{-- ✅ Icône avec badge exposé --}}
                            <div class="relative">
                                <div class="h-14 w-14 rounded-2xl {{ $iconTone($item['icon']) }}
                                            flex items-center justify-center">
                                    {!! $iconSvg($item['icon']) !!}
                                </div>

                                {{-- ✅ Badge exposé sur l'icône --}}
                                @if($badgeValue && $badgeValue > 0)
                                    <span class="absolute -top-2 -right-2 inline-flex items-center justify-center
                                                 min-w-[1.75rem] h-7 px-2 rounded-full
                                                 text-xs font-bold text-white {{ $badgeColor }}
                                                 ring-2 ring-white dark:ring-gray-950
                                                 shadow-lg">
                                        {{ $badgeValue > 99 ? '99+' : $badgeValue }}
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $item['label'] }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ $item['desc'] }}
                                </div>
                            </div>
                        </div>

                        <span class="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/5 dark:ring-white/5"></span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Séparateur --}}
    <div class="border-t border-gray-200/70 dark:border-gray-800/70"></div>

    {{-- =========================
        SUPPORT (LISTE)
    ========================== --}}
  <div class="border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Support</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Annonces, FAQ, Aide</div>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-800">
            @foreach($supportMenus as $item)
                @php
                    $active = (bool) $item['active'];
                    $rowBg  = $active ? 'bg-indigo-50/60 dark:bg-indigo-900/10' : 'bg-transparent';
                    $leftBorder = $active ? 'border-l-4 border-indigo-500' : 'border-l-4 border-transparent';

                    // ✅ Couleur de l'icône
                    $tone = $iconTone($item['icon']);
                    
                    // ✅ Badge value et couleur selon le type
                    $badgeValue = $item['badge'] ?? null;
                    $badgeColor = match($item['icon']) {
                        'bell' => 'bg-amber-500',      // Orange pour annonces
                        'help' => 'bg-indigo-500',     // Bleu pour FAQ
                        'support' => 'bg-emerald-500', // Vert pour aide
                        default => 'bg-gray-500',
                    };
                @endphp

                <a href="{{ $item['href'] }}"
                class="block {{ $rowBg }} {{ $leftBorder }}
                        hover:bg-gray-50 dark:hover:bg-gray-900/40 transition
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/60">
                    <div class="p-4 sm:p-5 flex items-center gap-4">

                        {{-- ✅ Icône avec badge exposé (comme menu principal) --}}
                        <div class="relative shrink-0">
                            <div class="h-12 w-12 rounded-2xl flex items-center justify-center {{ $tone }}">
                                {!! $iconSvg($item['icon']) !!}
                            </div>

                            {{-- ✅ Badge exposé (seulement si > 0) --}}
                            @if($badgeValue && $badgeValue > 0)
                                <span class="absolute -top-2 -right-2 inline-flex items-center justify-center
                                            min-w-[1.75rem] h-7 px-2 rounded-full
                                            text-xs font-bold text-white {{ $badgeColor }}
                                            ring-2 ring-white dark:ring-gray-950
                                            shadow-lg">
                                    {{ $badgeValue > 99 ? '99+' : $badgeValue }}
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $item['label'] }}
                            </div>

                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                {{ $item['desc'] }}
                            </div>
                        </div>

                        <div class="shrink-0 text-gray-400 dark:text-gray-500">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <x-footer-version />

</div>
