@props(['name', 'class' => 'size-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('bag')
            <path d="M6 7.5h12l1 13H5l1-13Z" />
            <path d="M9 10V6a3 3 0 0 1 6 0v4" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="3.6" />
            <path d="M4.8 20.2a7.5 7.5 0 0 1 14.4 0" />
            @break
        @case('arrow-right')
            <path d="M4 12h16m0 0-6-6m6 6-6 6" />
            @break
        @case('arrow-left')
            <path d="M20 12H4m0 0 6-6m-6 6 6 6" />
            @break
        @case('arrow-up-right')
            <path d="M7 17 17 7m0 0H8m9 0v9" />
            @break
        @case('x')
            <path d="M6 6l12 12M18 6 6 18" />
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('minus')
            <path d="M5 12h14" />
            @break
        @case('check')
            <path d="m5 13 4.5 4.5L19 7" />
            @break
        @case('check-circle')
            <circle cx="12" cy="12" r="9" />
            <path d="m8.5 12.5 2.5 2.5 4.8-5.3" />
            @break
        @case('truck')
            <path d="M3 7h11v9H3zM14 10h4l3 3v3h-7" />
            <circle cx="7" cy="18" r="1.8" />
            <circle cx="17.5" cy="18" r="1.8" />
            @break
        @case('returns')
            <path d="M9 5 5 9l4 4" />
            <path d="M5 9h9a5 5 0 0 1 0 10H8" />
            @break
        @case('shield')
            <path d="M12 3.5 5 6.2v5.1c0 4.4 3 7.6 7 9.2 4-1.6 7-4.8 7-9.2V6.2L12 3.5Z" />
            <path d="m9.2 11.8 2 2 3.8-4" />
            @break
        @case('star')
            <path d="m12 4 2.3 4.9 5.2.7-3.8 3.7.9 5.2L12 16l-4.6 2.5.9-5.2L4.5 9.6l5.2-.7L12 4Z" fill="currentColor" stroke="none" />
            @break
        @case('sparkle')
            <path d="M12 3.5c.6 3.8 2.3 5.7 6.5 6.5-4.2.8-5.9 2.7-6.5 6.5-.6-3.8-2.3-5.7-6.5-6.5 4.2-.8 5.9-2.7 6.5-6.5Z" />
            @break
        @case('book')
            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15.5H6.5A2.5 2.5 0 0 0 4 21V5.5Z" />
            <path d="M4 18.5A2.5 2.5 0 0 1 6.5 16H20" />
            @break
        @case('record')
            <circle cx="12" cy="12" r="8.5" />
            <circle cx="12" cy="12" r="2.2" />
            <path d="M12 6.8a5.2 5.2 0 0 1 5.2 5.2" />
            @break
        @case('dice')
            <rect x="4" y="4" width="16" height="16" rx="3.5" />
            <circle cx="9" cy="9" r="0.6" fill="currentColor" />
            <circle cx="15" cy="9" r="0.6" fill="currentColor" />
            <circle cx="9" cy="15" r="0.6" fill="currentColor" />
            <circle cx="15" cy="15" r="0.6" fill="currentColor" />
            <circle cx="12" cy="12" r="0.6" fill="currentColor" />
            @break
        @case('search')
            <circle cx="11" cy="11" r="6.5" />
            <path d="m20 20-4.4-4.4" />
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16" />
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('package')
            <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" />
            <path d="M4 7.5l8 4.5 8-4.5M12 12v9" />
            @break
        @case('mail')
            <rect x="3" y="5.5" width="18" height="13" rx="2.5" />
            <path d="m4 7.5 8 5.5 8-5.5" />
            @break
        @case('lock')
            <rect x="5" y="10.5" width="14" height="9.5" rx="2.5" />
            <path d="M8 10.5V8a4 4 0 0 1 8 0v2.5" />
            @break
        @case('card')
            <rect x="3" y="5.5" width="18" height="13" rx="2.5" />
            <path d="M3 10h18M7 14.5h4" />
            @break
    @endswitch
</svg>
