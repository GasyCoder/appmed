@php
    $ext = strtolower($extension ?? '');
@endphp

@if($ext === 'pdf')
    {{-- DocumentTextIcon --}}
    <svg class="h-7 w-7 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25V6.75A2.25 2.25 0 0 0 17.25 4.5H8.25A2.25 2.25 0 0 0 6 6.75v10.5A2.25 2.25 0 0 0 8.25 19.5h3.75" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25h7.5M9 12h6M9 15.75h3.75" />
    </svg>
@elseif(in_array($ext, ['doc','docx']))
    {{-- DocumentIcon --}}
    <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25V6.75A2.25 2.25 0 0 0 17.25 4.5H8.25A2.25 2.25 0 0 0 6 6.75v10.5A2.25 2.25 0 0 0 8.25 19.5h3.75" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h7.5M9 12h7.5M9 15h5" />
    </svg>
@elseif(in_array($ext, ['ppt','pptx']))
    {{-- PresentationChartBarIcon --}}
    <svg class="h-7 w-7 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M6 3v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V3M9 21h6M12 17v4" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 8h.01M9 12h.01M9 16h.01M12 10h.01M12 14h.01M15 12h.01" />
    </svg>
@elseif(in_array($ext, ['xls','xlsx']))
    {{-- TableCellsIcon --}}
    <svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5A2.25 2.25 0 0 1 18.75 19.5H5.25A2.25 2.25 0 0 1 3 17.25V6.75Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M3 12h18M3 15h18M9 4.5v15M15 4.5v15" />
    </svg>
@elseif(in_array($ext, ['jpg','jpeg','png','gif','webp']))
    {{-- PhotoIcon --}}
    <svg class="h-7 w-7 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v9A2.25 2.25 0 0 1 18.75 18.75H5.25A2.25 2.25 0 0 1 3 16.5v-9Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 10.5h.01M21 15l-5.5-5.5a1.5 1.5 0 0 0-2.12 0L6 17.25" />
    </svg>
@else
    {{-- DocumentIcon --}}
    <svg class="h-7 w-7 text-gray-600 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25V6.75A2.25 2.25 0 0 0 17.25 4.5H8.25A2.25 2.25 0 0 0 6 6.75v10.5A2.25 2.25 0 0 0 8.25 19.5h3.75" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6" />
    </svg>
@endif
