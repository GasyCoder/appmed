@props([
    'lines' => 3,
    'hasHeader' => true,
    'hasFooter' => false,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40 p-5']) }}>
    @if($hasHeader)
        <div class="mb-4">
            <x-skeleton.line width="w-3/4" height="h-5" class="mb-2" />
            <x-skeleton.line width="w-1/2" height="h-3" />
        </div>
    @endif

    <div class="space-y-3">
        @for($i = 0; $i < $lines; $i++)
            <x-skeleton.line 
                width="{{ $i === $lines - 1 ? 'w-4/5' : 'w-full' }}" 
                height="h-4" 
            />
        @endfor
    </div>

    @if($hasFooter)
        <div class="mt-4 pt-4 border-t border-gray-200/70 dark:border-gray-800/70">
            <x-skeleton.button width="w-32" height="h-9" />
        </div>
    @endif
</div>
