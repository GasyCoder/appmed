@props([
    'columns' => 5,
    'rows' => 5,
    'hasHeader' => true,
    'hasActions' => true,
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-950/40']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            @if($hasHeader)
                <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        @for($i = 0; $i < $columns; $i++)
                            <th class="px-6 py-3">
                                <x-skeleton.line width="{{ $i === 0 ? 'w-24' : ($i === $columns - 1 ? 'w-16' : 'w-28') }}" height="h-3" />
                            </th>
                        @endfor
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @for($r = 0; $r < $rows; $r++)
                    <x-skeleton.table-row :columns="$columns" :hasActions="$hasActions" />
                @endfor
            </tbody>
        </table>
    </div>
</div>
