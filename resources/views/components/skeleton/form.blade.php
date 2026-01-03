@props([
    'fields' => 3,
    'hasButton' => true,
])

<div {{ $attributes->merge(['class' => 'space-y-5']) }}>
    @for($i = 0; $i < $fields; $i++)
        <div class="space-y-1.5">
            <x-skeleton.line width="w-32" height="h-4" class="mb-2" />
            <x-skeleton.line width="w-full" height="h-11" />
        </div>
    @endfor

    @if($hasButton)
        <div class="pt-1">
            <x-skeleton.button width="w-full" height="h-11" />
        </div>
    @endif
</div>
