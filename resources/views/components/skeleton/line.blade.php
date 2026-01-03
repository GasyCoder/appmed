@props([
    'width' => 'w-full',
    'height' => 'h-4',
])

<div {{ $attributes->merge(['class' => "skeleton {$width} {$height}"]) }}></div>
