@props(['name'])

@switch($name)

    @case('doc')
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M7 3h7l3 3v15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M14 3v4h4"/>
        </svg>
        @break

    @case('calendar')
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M8 7V3m8 4V3M4 11h16"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M6 5h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/>
        </svg>
        @break

    @case('users')
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4" stroke-width="2"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M20 8v6m3-3h-6"/>
        </svg>
        @break

    @case('book')
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M6 3h12a2 2 0 0 1 2 2v16H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 7h14"/>
        </svg>
        @break

    @case('help')
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M9.09 9a3 3 0 1 1 5.82 1c0 2-3 2-3 4"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01"/>
        </svg>
        @break

    @case('support')
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M4 4h16v12H5.17L4 17.17V4z"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M8 9h8M8 12h6"/>
        </svg>
        @break

    @default
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
        </svg>

@endswitch
