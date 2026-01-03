@props([
    'hasSidebar' => false,
    'variant' => 'app',
])

@if($variant === 'landing')
    <div {{ $attributes->merge(['class' => 'min-h-screen bg-white dark:bg-gray-950']) }}>
        <div class="border-b border-gray-200/70 dark:border-gray-800/70 bg-white/70 dark:bg-gray-950/70 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <x-skeleton.avatar size="h-10 w-10" />
                    <x-skeleton.line width="w-28" height="h-4" />
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <x-skeleton.line width="w-20" height="h-3" />
                    <x-skeleton.line width="w-20" height="h-3" />
                    <x-skeleton.line width="w-20" height="h-3" />
                </div>
                <div class="flex items-center gap-2">
                    <x-skeleton.button width="w-24" height="h-8" />
                    <x-skeleton.button width="w-20" height="h-8" />
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 space-y-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <x-skeleton.line width="w-3/4" height="h-8" />
                    <x-skeleton.line width="w-2/3" height="h-6" />
                    <x-skeleton.line width="w-full" height="h-4" />
                    <x-skeleton.line width="w-5/6" height="h-4" />
                    <div class="flex items-center gap-3 pt-2">
                        <x-skeleton.button width="w-32" height="h-10" />
                        <x-skeleton.button width="w-28" height="h-10" />
                    </div>
                </div>
                <div class="rounded-3xl border border-gray-200/70 dark:border-gray-800/70 p-6">
                    <x-skeleton.card :lines="4" :hasHeader="false" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-skeleton.card :lines="3" />
                <x-skeleton.card :lines="3" />
                <x-skeleton.card :lines="3" />
            </div>
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'min-h-screen bg-gray-50 dark:bg-gray-950']) }}>
        <div class="border-b border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950">
            <div class="mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <x-skeleton.avatar size="h-9 w-9" />
                    <x-skeleton.line width="w-32" height="h-4" />
                </div>
                <div class="flex items-center gap-2">
                    <x-skeleton.button width="w-24" height="h-8" />
                    <x-skeleton.button width="w-16" height="h-8" />
                </div>
            </div>
        </div>

        <div class="flex">
            @if($hasSidebar)
                <aside class="hidden lg:block w-72 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
                    <x-skeleton.sidebar />
                </aside>
            @endif

            <main class="flex-1">
                <div class="mx-auto max-w-[88rem] px-4 sm:px-6 lg:px-8 py-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="space-y-2">
                            <x-skeleton.line width="w-48" height="h-6" />
                            <x-skeleton.line width="w-64" height="h-4" />
                        </div>
                        <div class="flex items-center gap-2">
                            <x-skeleton.button width="w-28" height="h-9" />
                            <x-skeleton.button width="w-24" height="h-9" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <x-skeleton.card :lines="3" />
                        <x-skeleton.card :lines="3" />
                        <x-skeleton.card :lines="3" />
                    </div>

                    <x-skeleton.table :columns="4" :rows="5" />
                </div>
            </main>
        </div>
    </div>
@endif
