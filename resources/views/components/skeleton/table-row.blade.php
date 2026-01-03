@props([
    'columns' => 5,
    'hasActions' => true,
])

<tr class="border-b border-gray-200 dark:border-gray-700">
    @for($i = 0; $i < $columns; $i++)
        <td class="px-4 py-3">
            <x-skeleton.line 
                width="{{ $i === 0 ? 'w-20' : ($i === $columns - 1 && $hasActions ? 'w-24' : 'w-full') }}" 
                height="h-4" 
            />
        </td>
    @endfor
</tr>
