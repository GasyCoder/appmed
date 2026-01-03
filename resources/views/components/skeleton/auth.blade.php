@props([
    'cardWidth' => 'max-w-md',
])

<div class="min-h-screen flex items-center justify-center px-4 py-10 bg-gray-50 dark:bg-gray-950">
    <div class="w-full {{ $cardWidth }}">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200/70 dark:border-gray-800/70 overflow-hidden">
            <div class="p-6 border-b border-gray-200/70 dark:border-gray-800/70">
                <div class="flex items-center gap-4">
                    <x-skeleton.avatar size="h-[120px] w-[120px]" class="rounded-xl" />
                    <div class="flex-1 space-y-2">
                        <x-skeleton.line width="w-3/4" height="h-5" />
                        <x-skeleton.line width="w-1/2" height="h-4" />
                    </div>
                </div>
                <div class="mt-5 space-y-2">
                    <x-skeleton.line width="w-40" height="h-5" />
                    <x-skeleton.line width="w-3/4" height="h-4" />
                </div>
            </div>
            <div class="p-6 space-y-5">
                <x-skeleton.form :fields="2" />
                <div class="flex items-center justify-between">
                    <x-skeleton.line width="w-28" height="h-4" />
                    <x-skeleton.line width="w-20" height="h-4" />
                </div>
            </div>
            <div class="px-6 py-5 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200/70 dark:border-gray-800/70">
                <x-skeleton.line width="w-48 mx-auto" height="h-4" />
            </div>
        </div>
    </div>
</div>
