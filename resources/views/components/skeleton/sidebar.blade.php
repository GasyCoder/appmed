@props([
    'sections' => 2,
    'items' => 5,
])

<div {{ $attributes->merge(['class' => 'h-full w-full space-y-6 px-4 py-5']) }}>
    @for($s = 0; $s < $sections; $s++)
        <div class="space-y-3">
            <x-skeleton.line width="w-24" height="h-3" />
            <div class="space-y-2">
                @for($i = 0; $i < $items; $i++)
                    <div class="flex items-center gap-3">
                        <x-skeleton.avatar size="h-8 w-8" />
                        <x-skeleton.line width="{{ $i === 0 ? 'w-32' : 'w-24' }}" height="h-3" />
                    </div>
                @endfor
            </div>
        </div>
    @endfor
</div>
