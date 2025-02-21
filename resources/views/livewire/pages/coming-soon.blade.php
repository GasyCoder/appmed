<div>
    <div class="min-h-screen  text-gray-900 dark:text-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Icône construction -->
            <div class="flex justify-center">
                <svg class="w-24 h-24 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <!-- Message -->
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                    Page en construction
                </h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                    Cette fonctionnalité n'est pas encore disponible.
                    <br>
                    Veuillez revenir plus tard.
                </p>
            </div>

            <!-- Bouton retour -->
            <div class="mt-8 flex justify-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                   wire:navigate>
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>
