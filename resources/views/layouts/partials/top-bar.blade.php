<!-- Top bar -->
<header
    class="sticky top-0 z-30 h-16
           border-b border-gray-200/70 dark:border-gray-800/70
           bg-white/80 dark:bg-gray-950/70
           backdrop-blur supports-[backdrop-filter]:bg-white/70 supports-[backdrop-filter]:dark:bg-gray-950/60"
>
    <div class="h-full px-3 sm:px-4 lg:px-6 flex items-center justify-between gap-3">
        <!-- Left: Burger only -->
        <div class="flex items-center gap-2">
            <button
                type="button"
                @click="sidebarOpen = true"
                class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-lg
                       text-gray-700 dark:text-gray-200
                       hover:bg-gray-100 dark:hover:bg-gray-900
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                aria-label="Ouvrir le menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
        </div>

        <!-- Right: Actions -->
       @include('layouts.partials.right-top-bar')
    </div>
</header>
