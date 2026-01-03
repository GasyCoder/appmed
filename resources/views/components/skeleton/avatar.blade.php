@props([
    'size' => 'h-12 w-12',
])

<div {{ $attributes->merge(['class' => "skeleton rounded-full {$size}"]) }}></div>
