<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="chatbot-auth" content="{{ auth()->check() ? '1' : '0' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @PwaHead

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo.png') }}">

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
    (() => {
    const stored = localStorage.getItem('darkMode');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored === null ? prefersDark : stored === 'true';
    document.documentElement.classList.toggle('dark', isDark);
    })();
    </script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

@php
    $authUser = auth()->user();
    $isStudent = $authUser && method_exists($authUser, 'hasRole') && $authUser->hasRole('student');
@endphp

<body
    class="min-h-screen font-sans antialiased flex flex-col
           bg-gray-50 text-gray-900
           dark:bg-gray-950 dark:text-gray-100"
    x-data="{ sidebarOpen: false, pageLoading: true }"
    x-init="setTimeout(() => { pageLoading = false }, 750)"
>
    <div x-show="pageLoading" x-cloak aria-busy="true" aria-live="polite" class="flex-1 flex flex-col">
        <span class="sr-only">Chargement de la page…</span>
        <x-skeleton.page :has-sidebar="!$isStudent" />
    </div>

    <div x-show="!pageLoading" x-cloak class="flex-1 flex flex-col">
        @unless($isStudent)
            @include('layouts.partials.top-bar')
        @else
            @include('layouts.partials.top-bar-student')
        @endunless
        <div class="flex flex-1 min-h-0">
            {{-- Sidebar uniquement pour admin/teacher --}}
            @unless($isStudent)
                @include('layouts.partials.sidebar-menu')
            @endunless

            <main class="{{ $isStudent ? 'flex-1 min-h-0' : 'flex-1 lg:pl-80 min-h-0' }}">
                <div class="h-full overflow-auto bg-gray-50 dark:bg-gray-950">
                    <div class="py-4 px-3 sm:px-4 lg:px-6 {{ $isStudent ? 'pb-24' : '' }}">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Bottom navigation (mobile) uniquement étudiant --}}
    @if($isStudent)
        <div class="md:hidden" x-show="!pageLoading" x-cloak>
            @include('layouts.partials.bottom-nav')
        </div>
    @endif

    @RegisterServiceWorkerScript

    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-4" x-show="!pageLoading" x-cloak>
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} Faculté de Médecine - Université de Mahajanga
            <span class="mx-2 dark:text-gray-500">•</span>
            Conçu par
            <a href="https://me.gasycoder.com/" target="_blank"
               class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                GasyCoder
            </a>
        </div>
    </footer>

    @stack('modals')
    @livewireScripts
    @stack('scripts')

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />

</body>
</html>
