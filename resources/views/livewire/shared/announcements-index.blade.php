<div class="mx-auto w-full max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-lg font-semibold text-gray-900 dark:text-white">Annonces</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Informations officielles (scolarité)
            </div>
        </div>

        <a href="/"
           class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium
                  border border-gray-200 dark:border-gray-800
                  bg-white dark:bg-gray-950/40
                  hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>
    </div>

    <div class="border-t border-gray-200/70 dark:border-gray-800/70"></div>

    @php
        $badgeClass = function(string $type) {
            return match($type) {
                'success' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200',
                'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200',
                'danger'  => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200',
                default   => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200',
            };
        };

        $dotClass = function(string $type) {
            return match($type) {
                'success' => 'bg-emerald-500',
                'warning' => 'bg-amber-500',
                'danger'  => 'bg-rose-500',
                default   => 'bg-indigo-500',
            };
        };
    @endphp

    @if($items->isEmpty())
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950/40
                    p-6 text-center shadow-xl shadow-gray-900/5 dark:shadow-black/30">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Aucune annonce</div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Vous êtes à jour.</div>
        </div>
    @else
        <div class="space-y-3">
            @foreach($items as $a)
                @php
                    $type  = $a->type ?? 'info';
                    $title = $a->title ?? '';
                    $body  = $a->body ?? '';
                    $ctaLabel = $a->action_label;
                    $ctaUrl   = $a->action_url;
                @endphp

                <div class="rounded-2xl border border-gray-200 dark:border-gray-800
                            bg-white dark:bg-gray-950/40
                            shadow-xl shadow-gray-900/5 dark:shadow-black/30 overflow-hidden">
                    <div class="p-4 sm:p-5">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-2 w-2 rounded-full {{ $dotClass($type) }}"></span>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[11px] font-semibold px-2 py-1 rounded-full {{ $badgeClass($type) }}">
                                        {{ strtoupper($type) }}
                                    </span>

                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $title }}
                                    </span>

                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        • {{ optional($a->created_at)->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <div class="mt-2 text-sm text-gray-700 dark:text-gray-200 leading-snug break-words">
                                    {{ $body }}
                                </div>

                                @if($ctaUrl)
                                    <div class="mt-3">
                                        <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2
                                               rounded-xl px-3 py-2 text-sm font-semibold
                                               border border-gray-200 dark:border-gray-800
                                               bg-white dark:bg-gray-900
                                               hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                            {{ $ctaLabel ?: 'Consulter' }}
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
    @endif

    <div class="border-t border-gray-200/70 dark:border-gray-800/70 pt-3"></div>

    <div class="text-center">
        <p class="text-[11px] text-gray-500 dark:text-gray-400">
            Version {{ config('app.version') }}
            @if(config('app.build'))
                • Build {{ config('app.build') }}
            @endif
        </p>
    </div>

</div>
