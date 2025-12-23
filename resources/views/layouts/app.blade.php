<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="theme()"
      x-init="init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @PwaHead

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo_med.png') }}">

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- x-cloak (évite le flash des éléments x-show) --}}
    <style>[x-cloak]{display:none !important;}</style>
    
    {{-- ✅ Script de persistance du mode dark/light --}}
    <script>
        (function () {
            try {
                const stored = localStorage.getItem('darkMode');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = stored !== null ? (stored === 'true') : prefersDark;
                
                // Appliquer immédiatement avant le chargement de la page
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
    @stack('styles')
</head>

<body
    class="min-h-screen font-sans antialiased flex flex-col
           bg-gray-50 text-gray-900
           dark:bg-gray-950 dark:text-gray-100"
    x-data="{ sidebarOpen: false }"
>
    <div class="flex-1 flex flex-col">
        @include('top-bar')

        <div class="flex flex-1 min-h-0">
            @include('sidebar-menu')

            <main class="flex-1 lg:pl-80 min-h-0">
                <div class="h-full overflow-auto bg-gray-50 dark:bg-gray-950">
                    <div class="py-4 px-3 sm:px-4 lg:px-6">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    @RegisterServiceWorkerScript

    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-4">
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