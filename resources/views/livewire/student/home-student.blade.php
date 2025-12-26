<div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 pb-24 lg:pb-6 space-y-6">

    @php
        use Illuminate\Support\Facades\Route;

        // ✅ Sécurise menuStats (si jamais non fourni)
        $menuStats = $menuStats ?? ['documents' => 0, 'schedules' => 0];

        // ✅ URLs d’archives (si tu as des routes dédiées, elles seront utilisées)
        $courseArchiveUrl = Route::has('student.document.archive')
            ? route('student.document.archive')
            : (Route::has('student.document') ? route('student.document', ['view' => 'archives']) : '#');

        $timetableArchiveUrl = Route::has('student.timetable.archive')
            ? route('student.timetable.archive')
            : (Route::has('student.timetable') ? route('student.timetable', ['view' => 'archives']) : '#');

        // ✅ Menus (définis ici => plus de Undefined variable $primaryMenus)
        $primaryMenus = [
            [
                'label' => 'Mes cours',
                'desc'  => 'Documents, PDF, supports',
                'href'  => Route::has('student.document') ? route('student.document') : '#',
                'icon'  => 'doc',
                'active'=> request()->routeIs('student.document'),
                'badge' => (int) ($menuStats['documents'] ?? 0),
                'badgeColor' => 'bg-red-500',
            ],
            [
                'label' => 'Emploi du temps',
                'desc'  => 'Planning et horaires',
                'href'  => Route::has('student.timetable') ? route('student.timetable') : '#',
                'icon'  => 'calendar',
                'active'=> request()->routeIs('student.timetable'),
                'badge' => (int) ($menuStats['schedules'] ?? 0),
                'badgeColor' => 'bg-emerald-500',
            ],
            [
                'label' => 'Mes enseignants',
                'desc'  => 'Liste des enseignants',
                'href'  => Route::has('student.myTeacher') ? route('student.myTeacher') : '#',
                'icon'  => 'users',
                'active'=> request()->routeIs('student.myTeacher'),
                'badge' => null,
                'badgeColor' => 'bg-purple-500',
            ],
            [
                'label' => 'Programmes',
                'desc'  => 'Consulter les programmes',
                'href'  => Route::has('programs') ? route('programs') : '#',
                'icon'  => 'book',
                'active'=> request()->routeIs('programs'),
                'badge' => null,
                'badgeColor' => 'bg-gray-500',
            ],
        ];

        // ✅ Icônes SVG (inline => pas de helper/partial externe)
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 5h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/>
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
                default => '<svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                           </svg>',
            };
        };

        // ✅ Tons d’icônes (inline => pas de helper/partial externe)
        $iconTone = function(string $icon) {
            return match($icon) {
                'doc' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200 dark:bg-blue-500/10 dark:text-blue-300 dark:ring-blue-500/20',
                'calendar' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20',
                'users' => 'bg-purple-50 text-purple-700 ring-1 ring-purple-200 dark:bg-purple-500/10 dark:text-purple-300 dark:ring-purple-500/20',
                'book' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200 dark:bg-orange-500/10 dark:text-orange-300 dark:ring-orange-500/20',
                default => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200 dark:bg-gray-900 dark:text-gray-200 dark:ring-white/10',
            };
        };
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- =========================
             COLONNE PRINCIPALE
        ========================== --}}
        <div class="lg:col-span-9 space-y-6">

            {{-- =========================
                 MENU PRINCIPAL (GRID) - AVEC BADGES
            ========================== --}}
            <div class="border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-950/40
                        shadow-xl shadow-gray-900/5 dark:shadow-black/30">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Menu étudiant</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Accès rapide</div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($primaryMenus as $item)
                            @php
                                $active = (bool) $item['active'];
                                $border = $active
                                    ? 'border-indigo-400/70 dark:border-indigo-500/70 ring-2 ring-indigo-500/20'
                                    : 'border-gray-200 dark:border-gray-800';

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
                                    <div class="relative">
                                        <div class="h-14 w-14 rounded-2xl {{ $iconTone($item['icon']) }} flex items-center justify-center">
                                            {!! $iconSvg($item['icon']) !!}
                                        </div>

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
        </div>

        {{-- =========================
             SIDEBAR ARCHIVES
        ========================== --}}
        <aside class="lg:col-span-3">
            <div class="space-y-6 lg:sticky lg:top-24">

                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950/40
                            shadow-xl shadow-gray-900/5 dark:shadow-black/30 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Archives</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Accès rapide aux anciens contenus</div>
                    </div>

                    <div class="p-4 space-y-3">
                        {{-- Archives des cours --}}
                        <a href="{{ $courseArchiveUrl }}"
                           class="group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-800
                                  bg-gray-50 dark:bg-gray-900/30 p-4
                                  hover:bg-gray-100 dark:hover:bg-gray-900/50 transition">
                            <div class="h-12 w-12 rounded-2xl {{ $iconTone('doc') }} flex items-center justify-center shrink-0">
                                {!! $iconSvg('doc') !!}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Archives des cours
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                    Anciens PDF, supports, documents par année / semestre.
                                </div>
                            </div>
                            <div class="shrink-0 text-gray-400 dark:text-gray-500 pt-1">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                                </svg>
                            </div>
                        </a>

                        {{-- Archives emploi du temps --}}
                        <a href="{{ $timetableArchiveUrl }}"
                           class="group flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-800
                                  bg-gray-50 dark:bg-gray-900/30 p-4
                                  hover:bg-gray-100 dark:hover:bg-gray-900/50 transition">
                            <div class="h-12 w-12 rounded-2xl {{ $iconTone('calendar') }} flex items-center justify-center shrink-0">
                                {!! $iconSvg('calendar') !!}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Archives emploi du temps
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                    Plannings précédents (par période / session).
                                </div>
                            </div>
                            <div class="shrink-0 text-gray-400 dark:text-gray-500 pt-1">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Ressources --}}
                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950/40
                            shadow-xl shadow-gray-900/5 dark:shadow-black/30 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Ressources</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">FAQ et guide</div>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ Route::has('faq') ? route('faq') : '#' }}"
                           class="block px-3 py-2 rounded-xl text-sm text-gray-700 dark:text-gray-200
                                  hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                            FAQ
                        </a>
                        <a href="{{ Route::has('help') ? route('help') : '#' }}"
                           class="block px-3 py-2 rounded-xl text-sm text-gray-700 dark:text-gray-200
                                  hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                            Aide
                        </a>
                    </div>
                </div>

            </div>
        </aside>
    </div>
     <x-footer-version />
</div>
