@props([
    'width' => 'w-32',
    'height' => 'h-10',
])

<div {{ $attributes->merge(['class' => "skeleton rounded-xl {$width} {$height}"]) }}></div>
