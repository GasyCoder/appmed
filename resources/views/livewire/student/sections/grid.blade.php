<!-- Grille de Documents Améliorée -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($documents as $document)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 transition-all duration-300 overflow-hidden">
            
            <!-- En-tête -->
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-start gap-3 mb-3">
                    @php
                        $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                        $isPdf = $extension === 'pdf';
                        $isPowerPoint = in_array($extension, ['ppt', 'pptx']);
                    @endphp
                    
                    <!-- VOS ICÔNES EXISTANTES -->
                    <div class="flex-shrink-0 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        @php
                            $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                        @endphp
                        @include('livewire.teacher.forms.file-icons')
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <!-- Badge d'extension -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isPdf ? 'bg-red-100 text-red-800' : ($isPowerPoint ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }} mb-2">
                            {{ strtoupper($extension) }}
                        </span>
                        
                        <!-- Titre -->
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm leading-5 line-clamp-2 mb-2">
                            {{ $document->title }}
                        </h3>
                        
                        <!-- Infos enseignant -->
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            @if($document->teacher && $document->teacher->profil)
                                <span class="font-medium">{{ $document->teacher->profil->grade }}</span>
                                @if($document->uploader)
                                    <span class="mx-1">•</span>
                                    <span>{{ $document->uploader->name }}</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer avec actions -->
            <div class="p-5">
                <!-- Métadonnées -->
                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 mb-4">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $document->created_at->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center">
                        @if($document->file_size)
                            {{ number_format($document->file_size / 1024 / 1024, 1) }} MB
                        @else
                            -
                        @endif
                    </div>
                </div>

                <!-- Boutons d'action modernes -->
                <div class="flex gap-2">
                    @php
                        $filename = basename($document->file_path);
                    @endphp
                    
                    <!-- Bouton Consulter (principal) -->
                    <a href="{{ route('pdf.viewer', ['filename' => urlencode($filename)]) }}"
                        class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Consulter
                    </a>
                    
                    <!-- Bouton Télécharger (secondaire) -->
                    <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                        class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                </div>
                
                <!-- Indicateur de type de fichier -->
                <div class="mt-3 text-center">
                    @if($isPdf)
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">✓ Lecture directe</span>
                    @elseif($isPowerPoint)
                        <span class="text-xs text-orange-600 dark:text-orange-400 font-medium">📱 Téléchargement recommandé</span>
                    @else
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">📄 Fichier document</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <!-- État vide amélioré -->
        <div class="col-span-full">
            <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="mb-6">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                    Aucun document disponible
                </h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    Modifiez vos critères de recherche ou revenez plus tard pour découvrir de nouveaux documents.
                </p>
            </div>
        </div>
    @endforelse
</div>

<style>
/* Animations supplémentaires */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.grid > div {
    animation: fadeInUp 0.5s ease-out;
}

/* Effet hover sur les cartes */
.grid > div:hover {
    transform: translateY(-2px);
}

/* Classes utilitaires pour le line-clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>