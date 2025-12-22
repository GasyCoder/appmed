<x-app-layout>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
            
            <!-- Icône d'erreur animée -->
            <div class="mb-6">
                <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-3">
                Document non disponible
            </h1>
            
            <p class="text-gray-600 mb-6">
                Le document que vous essayez de consulter n'est pas accessible actuellement.
            </p>
            
            @if(isset($error))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-left">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-medium text-red-800 mb-1">Détails :</h3>
                            <p class="text-red-700 text-sm">{{ $error }}</p>
                            @if(isset($filename))
                                <p class="text-red-600 text-xs mt-2">
                                    <strong>Fichier :</strong> {{ $filename }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-3">
                <button onclick="history.back()" 
                    class="w-full px-4 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-all duration-200">
                    ← Retour aux documents
                </button>
                
                <a href="{{ route('dashboard') }}" 
                    class="w-full inline-block px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-all duration-200">
                    🏠 Tableau de bord
                </a>
                
                <button onclick="location.reload()" 
                    class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-all duration-200">
                    🔄 Réessayer
                </button>
            </div>
        </div>
    </div>
</x-app-layout>