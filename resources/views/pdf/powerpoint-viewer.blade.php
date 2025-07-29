<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Header avec contrôles -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button onclick="history.back()" 
                            class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour aux documents
                        </button>
                        
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">{{ $document->title }}</h1>
                            @if($teacherInfo)
                                <div class="text-sm text-gray-600 mt-1">
                                    @if($teacherInfo['grade']){{ $teacherInfo['grade'] }} @endif
                                    {{ $teacherInfo['name'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                        class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger PowerPoint
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Panel principal -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border p-8 text-center">
                        
                        <!-- Icône PowerPoint grande -->
                        <div class="mb-6">
                            <div class="mx-auto w-24 h-24 bg-orange-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-12 h-12 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.5 16.5c-.309.29-.765.42-1.296.42a2.23 2.23 0 0 1-.308-.018v1.426H7v-3.936A7.558 7.558 0 0 1 8.219 14.5c.557 0 .953.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zM14 9h-1V4l5 5h-4z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Titre et description -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            Présentation PowerPoint
                        </h2>
                        
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">
                            Cette présentation PowerPoint contient des diapositives interactives. 
                            Téléchargez le fichier pour le consulter avec PowerPoint ou un logiciel compatible.
                        </p>
                        
                        <!-- Aperçu du contenu (si disponible) -->
                        <div id="contentPreview" class="hidden">
                            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                                <h3 class="font-semibold text-gray-900 mb-3">Aperçu du contenu :</h3>
                                <div id="slidePreview" class="space-y-2 text-sm text-gray-700">
                                    <!-- Le contenu sera inséré ici par JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-medium rounded-lg hover:from-orange-700 hover:to-orange-800 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Télécharger le fichier
                            </a>
                            
                            <button onclick="openWithOnlineViewer()" 
                                class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Ouvrir en ligne
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Panel latéral avec infos -->
                <div class="space-y-6">
                    
                    <!-- Informations du fichier -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Informations</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type :</span>
                                <span class="font-medium">{{ strtoupper(pathinfo($filename, PATHINFO_EXTENSION)) }}</span>
                            </div>
                            
                            @if($document->file_size)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Taille :</span>
                                    <span class="font-medium">{{ number_format($document->file_size / 1024 / 1024, 1) }} MB</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Ajouté le :</span>
                                <span class="font-medium">{{ $document->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Vues :</span>
                                <span class="font-medium">{{ $document->view_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conseils d'utilisation -->
                    <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                        <h3 class="font-semibold text-blue-900 mb-3">💡 Conseils</h3>
                        <div class="text-sm text-blue-800 space-y-2">
                            <p>• Utilisez Microsoft PowerPoint pour la meilleure expérience</p>
                            <p>• LibreOffice Impress est une alternative gratuite</p>
                            <p>• Google Slides peut ouvrir la plupart des fichiers PPT</p>
                        </div>
                    </div>
                    
                    <!-- Actions rapides -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Actions</h3>
                        
                        <div class="space-y-3">
                            <button onclick="copyDownloadLink()" 
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copier le lien
                            </button>
                            
                            <button onclick="shareDocument()" 
                                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                </svg>
                                Partager
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Fonction pour ouvrir avec un viewer en ligne
        function openWithOnlineViewer() {
            const fileUrl = encodeURIComponent('{{ route("pdf.download", ["filename" => urlencode($filename)]) }}');
            const viewerUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${fileUrl}`;
            window.open(viewerUrl, '_blank');
        }
        
        // Copier le lien de téléchargement
        function copyDownloadLink() {
            const downloadUrl = '{{ route("pdf.download", ["filename" => urlencode($filename)]) }}';
            navigator.clipboard.writeText(downloadUrl).then(() => {
                // Feedback visuel
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<svg class="w-4 h-4 inline mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Lien copié !';
                button.classList.add('text-green-600');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('text-green-600');
                }, 2000);
            });
        }
        
        // Partager le document
        function shareDocument() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $document->title }}',
                    text: 'Consultez cette présentation PowerPoint',
                    url: window.location.href
                });
            } else {
                copyDownloadLink();
            }
        }
        
        // Tenter d'extraire des métadonnées (basique)
        document.addEventListener('DOMContentLoaded', function() {
            // Ici on pourrait ajouter une requête AJAX pour extraire 
            // des informations basiques du fichier PowerPoint
            // Pour l'instant, on simule avec des données statiques
            
            const preview = document.getElementById('contentPreview');
            const slidePreview = document.getElementById('slidePreview');
            
            // Simulation d'aperçu (vous pouvez implémenter une vraie extraction)
            const sampleContent = [
                "📊 Diapositive 1: Introduction au sujet",
                "📈 Diapositive 2: Données et statistiques", 
                "🎯 Diapositive 3: Objectifs principaux",
                "💡 Diapositive 4: Solutions proposées",
                "✅ Diapositive 5: Conclusions"
            ];
            
            // Afficher l'aperçu après un délai simulé
            setTimeout(() => {
                slidePreview.innerHTML = sampleContent.map(item => 
                    `<div class="flex items-center gap-2 p-2 bg-white rounded border-l-2 border-orange-300">${item}</div>`
                ).join('');
                preview.classList.remove('hidden');
            }, 1000);
        });
    </script>
    @endpush
</x-app-layout>