@props([
    'href' => '#',
    'label' => '',
    'desc' => '',
    'icon' => 'doc',
    'active' => false,
])

@php
    // Border toujours visible
    $border = $active
        ? "border-indigo-400 dark:border-indigo-500"
        : "border-gray-200 dark:border-gray-800";

    // ✅ Shadow plus visible + hover lift
    $shadow = $active
        ? "shadow-xl shadow-indigo-500/10 dark:shadow-indigo-500/10"
        : "shadow-xl shadow-gray-900/5 dark:shadow-black/30";

    $base = "group block rounded-2xl border transition
             focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70";

    $bg = "bg-white dark:bg-gray-950/40 hover:bg-gray-50 dark:hover:bg-gray-900/50";

    // Mobile-first: tuile compacte et stable
    $size = "p-4 min-h-[110px] sm:min-h-[120px]";

    // micro interaction
    $motion = "hover:-translate-y-0.5 hover:shadow-2xl";
@endphp

<a href="{{ $href }}" class="{{ $base }} {{ $bg }} {{ $border }} {{ $shadow }} {{ $size }} {{ $motion }}">
    <div class="h-full flex flex-col items-center justify-center text-center gap-3">
        <div class="rounded-2xl bg-gray-100 dark:bg-gray-900 p-3 text-gray-700 dark:text-gray-200">
            <x-student.menu-icon :name="$icon" />
        </div>

        <div class="min-w-0 w-full">
            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                {{ $label }}
            </div>

            {{-- Desc masquée sur mobile, visible dès sm --}}
            <div class="mt-1 hidden sm:block text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                {{ $desc }}
            </div>
        </div>
    </div>

    <style>
        .line-clamp-2{
            display:-webkit-box;
            -webkit-line-clamp:2;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }
    </style>
</a>
