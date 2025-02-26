<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="{ 'dark': darkMode }">
   <head>
       <meta charset="utf-8">
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <meta name="csrf-token" content="{{ csrf_token() }}">
       @PwaHead
       <title>{{ config('app.name', 'Laravel') }}</title>
       <link rel="icon" type="image/png" href="{{ asset('assets/image/logo_med.png') }}">
       <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
       <!-- Police Plus Jakarta Sans et JetBrains Mono via Google Fonts -->
       <link rel="preconnect" href="https://fonts.googleapis.com">
       <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
       <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
       @vite(['resources/css/app.css', 'resources/js/app.js'])
       @livewireStyles
       @stack('styles')
       <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
   </head>
   <body class="font-sans antialiased min-h-screen flex flex-col bg-white dark:bg-gray-900" x-data="{ sidebarOpen: false }">
       <div class="flex-grow bg-gray-100 dark:bg-gray-900">
           @include('top-bar')
           <div class="flex">
               @include('sidebar-menu')
               <!-- Page Content -->
               <main class="flex-1 lg:pl-72">
                   <div class="py-3 px-2 sm:px-3 lg:px-4">
                       {{ $slot }}
                   </div>
               </main>
           </div>
       </div>
       @RegisterServiceWorkerScript
       <!-- Footer -->
       <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4">
           <div class="text-center text-sm text-gray-500 dark:text-gray-400">
               &copy; {{ date('Y') }} Faculté de Médecine - Université de Mahajanga
               <span class="mx-2 dark:text-gray-500">•</span>
               Conçu par <a href="#" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">GasyCoder</a>
           </div>
       </footer>

       @stack('modals')
       @livewireScripts
       @stack('scripts')
       <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
       <x-livewire-alert::scripts />

   </body>
</html>
