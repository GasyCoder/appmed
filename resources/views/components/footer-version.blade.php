    {{-- Séparateur --}}
    <div class="border-t border-gray-200/70 dark:border-gray-800/70"></div>

    {{-- ✅ VERSION APP --}}
    <div class="pt-1 text-center">
        <p class="text-[11px] text-gray-500 dark:text-gray-400">
            @if(config('app.build'))
                Version {{ config('app.build') }}
            @endif
        </p>
    </div>

    {{-- ✅ Bottom nav : uniquement mobile --}}
    <div class="lg:hidden">
        @include('layouts.partials.bottom-nav')
    </div>

    <style>
        .line-clamp-2{
            display:-webkit-box;
            -webkit-line-clamp:2;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }
    </style>