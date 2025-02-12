<!-- Top bar -->
<div class="sticky top-0 z-30 flex items-center justify-between h-16 bg-white dark:bg-gray-800 border-b dark:border-gray-700 px-4">
    <button @click="sidebarOpen = true" class="p-2 lg:hidden text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
        </svg>
    </button>

    <!-- Profile Dropdown avec Notifications et Avatar -->
    <div class="flex items-center ml-auto gap-2">
        <button
        @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
    >
        <!-- Icône soleil pour le mode sombre -->
        <svg x-show="darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
        </svg>

        <!-- Icône lune pour le mode clair -->
        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
    </button>
        <!-- Notifications -->
        @role(['teacher', 'admin'])
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 relative">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <!-- Badge de notification -->
                {{-- <div class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 dark:bg-red-600 rounded-full">3</div> --}}
            </button>

            <!-- Dropdown des notifications -->
            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95">

                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                </div>

                <!-- Liste des notifications -->
                {{-- <div class="max-h-64 overflow-y-auto">
                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Nouveau document ajouté</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Il y a 5 minutes</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Nouvel utilisateur inscrit</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Il y a 1 heure</p>
                            </div>
                        </div>
                    </a>
                </div> --}}

                <!-- Pied du dropdown -->
                <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                    <a href="#" class="block text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                        Aucune nouvelle notification
                    </a>
                </div>
            </div>
        </div>
        @endrole

        <!-- Profile Dropdown -->
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <div class="flex items-center gap-3 p-1.5 cursor-pointer">
                    <div class="relative">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_url)
                            <img class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 transition-transform hover:scale-105"
                                 src="{{ Auth::user()->profile_photo_url }}"
                                 alt="{{ Auth::user()->name }}" />
                        @else
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 ring-2 ring-white dark:ring-gray-800 flex items-center justify-center transition-transform hover:scale-105">
                                <span class="text-sm font-medium text-indigo-600 dark:text-indigo-300">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <!-- Indicateur de statut actif avec animation -->
                        <div class="absolute -bottom-0.5 -right-0.5">
                            <div class="h-2.5 w-2.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            <div class="absolute inset-0 h-2.5 w-2.5 bg-green-500 rounded-full animate-ping opacity-75"></div>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="content">
                <!-- En-tête du dropdown -->
                <div class="block px-4 py-2 text-xs text-gray-400 dark:text-gray-500 dark:bg-gray-800">
                    {{ __('Gestion du compte') }}
                </div>

                <!-- Lien Profile -->
                <x-dropdown-link href="{{ route('profile.show') }}"
                    class="flex items-center gap-2 bg-transparent dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ __('Profil') }}
                </x-dropdown-link>

                <!-- Séparateur -->
                <div class="border-t border-gray-200 dark:border-gray-600/50 dark:bg-gray-800"></div>

                <!-- Formulaire de déconnexion -->
                <form method="POST" action="{{ route('logout') }}" x-data class="dark:bg-gray-800">
                    @csrf
                    <x-dropdown-link href="{{ route('logout') }}"
                        @click.prevent="$root.submit();"
                        class="flex items-center gap-2 bg-transparent dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('Déconnexion') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</div>
