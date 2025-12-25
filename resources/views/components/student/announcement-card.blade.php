@props([
    'items' => [],
    'title' => 'Annonces scolarité',
    'subtitle' => 'Informations officielles',
])

@php
    $items = is_array($items) ? $items : [];
    if (count($items) === 0) return; // ✅ reste affiché sauf si vide/inactif (plus tard via DB)

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

<div class="rounded-2xl border border-gray-200 dark:border-gray-800
            bg-white dark:bg-gray-950/40
            shadow-xl shadow-gray-900/5 dark:shadow-black/30 overflow-hidden">

    {{-- Header --}}
    <div class="px-4 sm:px-5 py-4 border-b border-gray-200/80 dark:border-gray-800/80">
        <div class="flex items-start gap-3">
            <div class="shrink-0 rounded-2xl bg-gray-100 dark:bg-gray-900 p-3 text-gray-700 dark:text-gray-200">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>

            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $subtitle }}
                </div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="divide-y divide-gray-200/70 dark:divide-gray-800/70">
        @foreach($items as $a)
            @php
                $type  = $a['type'] ?? 'info';
                $t     = $a['title'] ?? null;
                $text  = $a['text'] ?? '';
                $meta  = $a['meta'] ?? null;
                $href  = $a['href'] ?? null;
            @endphp

            @if($href)
                <a href="{{ $href }}"
                   class="block px-4 sm:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-900/40 transition
                          focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2 w-2 rounded-full {{ $dotClass($type) }}"></span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[11px] font-semibold px-2 py-1 rounded-full {{ $badgeClass($type) }}">
                                    {{ strtoupper($type) }}
                                </span>

                                @if($t)
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $t }}
                                    </span>
                                @endif

                                @if($meta)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        • {{ $meta }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-200 leading-snug break-words">
                                {{ $text }}
                            </div>
                        </div>

                        <div class="shrink-0 text-gray-400 dark:text-gray-500 mt-1">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @else
                <div class="px-4 sm:px-5 py-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2 w-2 rounded-full {{ $dotClass($type) }}"></span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[11px] font-semibold px-2 py-1 rounded-full {{ $badgeClass($type) }}">
                                    {{ strtoupper($type) }}
                                </span>

                                @if($t)
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $t }}
                                    </span>
                                @endif

                                @if($meta)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        • {{ $meta }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-200 leading-snug break-words">
                                {{ $text }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
