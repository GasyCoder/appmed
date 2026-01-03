<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="theme()"
      x-init="init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo_med.png') }}">

    <style>[x-cloak]{display:none !important;}</style>

    <script>
        (function () {
            try {
                const stored = localStorage.getItem('darkMode');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = stored !== null ? (stored === 'true') : prefersDark;
                
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {
                console.error('Error loading dark mode:', e);
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100"
      x-data="{ pageLoading: true }"
      x-init="setTimeout(() => { pageLoading = false }, 650)">
    <div x-show="pageLoading" x-cloak aria-busy="true" aria-live="polite">
        <span class="sr-only">Chargement de la page…</span>
        <x-skeleton.auth />
    </div>

    <div x-show="!pageLoading" x-cloak>
        {{ $slot }}
    </div>
    
    @livewireScripts
</body>
</html>
