@props([
    'hasIcon' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1">
            <x-skeleton.line width="w-20" height="h-4" class="mb-2" />
            <x-skeleton.line width="w-32" height="h-8" />
        </div>
        
        @if($hasIcon)
            <x-skeleton.avatar size="h-12 w-12" />
        @endif
    </div>
</div>
