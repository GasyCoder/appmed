{{-- resources/views/pdf/viewer.blade.php --}}
<x-app-layout>
    <div class="min-h-screen pt-16 pb-20"> <!-- Ajouté pb-20 pour l'espace en bas -->
        <!-- Container principal -->
        <div class="viewer-container">
            <!-- Loader -->
            <div id="loading" class="flipbook-loading-container loading-container">
                <div class="spinner"></div>
                <p>Chargement du document...</p>
            </div>

            <!-- Zone du flipbook avec padding bottom supplémentaire -->
            <div id="flipbook-container">
                <div id="flipbook">
                    <!-- Les pages seront ajoutées dynamiquement via JavaScript -->
                </div>
            </div>

            <!-- Contrôles de navigation avec design amélioré -->
            <div class="controls" id="pdf-controls">
                <!-- Bouton précédent -->
                <button id="prev" class="control-btn control-nav" aria-label="Page précédente">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                    <span class="hidden-mobile">Précédent</span>
                </button>

                <!-- Bouton son avec meilleure icône -->
                <button id="soundToggle" class="control-btn control-sound" onclick="toggleSound()" aria-label="Activer/désactiver le son">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                    </svg>
                </button>

                <!-- Contrôles de zoom avec design amélioré -->
                <div class="zoom-controls">
                    <button id="zoom-out" class="control-btn control-zoom" onclick="handleZoom(-0.1)" aria-label="Zoom arrière">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            <line x1="8" y1="11" x2="14" y2="11"/>
                        </svg>
                    </button>
                    <span class="zoom-level">100%</span>
                    <button id="zoom-in" class="control-btn control-zoom" onclick="handleZoom(0.1)" aria-label="Zoom avant">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            <line x1="11" y1="8" x2="11" y2="14"/>
                            <line x1="8" y1="11" x2="14" y2="11"/>
                        </svg>
                    </button>
                </div>

                <!-- Bouton plein écran avec meilleure icône -->
                <button id="fullscreen" class="control-btn control-fullscreen" title="Plein écran" onclick="toggleFullscreen()" aria-label="Mode plein écran">
                    <span class="fullscreen-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 3 21 3 21 9"/>
                            <polyline points="9 21 3 21 3 15"/>
                            <line x1="21" y1="3" x2="14" y2="10"/>
                            <line x1="3" y1="21" x2="10" y2="14"/>
                        </svg>
                    </span>
                </button>

                <!-- Bouton suivant -->
                <button id="next" class="control-btn control-nav" aria-label="Page suivante">
                    <span class="hidden-mobile">Suivant</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>

                <audio id="turnPageSound" preload="auto" class="hidden">
                    <source src="{{ asset('assets/turnpage-99756.mp3') }}" type="audio/mpeg">
                </audio>
            </div>

            <!-- Message d'erreur -->
            <div id="error-message" class="flipbook-error-message error-message">
                <div class="error-container">
                    <div class="text-red-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3>Erreur de chargement</h3>
                    <p id="error-text"></p>
                    <button onclick="location.reload()" class="retry-button">
                        Réessayer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        {{ $css }}
    </style>

    @push('scripts')
        <script>
            // Script pour améliorer l'interaction avec les contrôles
            document.addEventListener('DOMContentLoaded', function() {
                // Protéger la navbar
                const navbarProtector = document.createElement('div');
                navbarProtector.style.position = 'fixed';
                navbarProtector.style.top = '0';
                navbarProtector.style.left = '0';
                navbarProtector.style.right = '0';
                navbarProtector.style.height = '64px';
                navbarProtector.style.zIndex = '40';
                navbarProtector.style.pointerEvents = 'none';
                document.body.appendChild(navbarProtector);

                // Gérer l'affichage des contrôles
                const controls = document.getElementById('pdf-controls');
                let controlsTimeout;

                // Masquer les contrôles après un délai d'inactivité
                function setControlsTimeout() {
                    clearTimeout(controlsTimeout);
                    controls.style.opacity = '1';

                    controlsTimeout = setTimeout(() => {
                        if (!controls.matches(':hover')) {
                            controls.style.opacity = '0.5';
                        }
                    }, 3000);
                }

                // Activer les contrôles lors du mouvement de la souris
                document.addEventListener('mousemove', setControlsTimeout);

                // Réinitialiser le délai lorsqu'on survole les contrôles
                controls.addEventListener('mouseenter', () => {
                    controls.style.opacity = '1';
                    clearTimeout(controlsTimeout);
                });

                controls.addEventListener('mouseleave', setControlsTimeout);

                // Ajuster l'espacement pour éviter de masquer le contenu
                const pdfPageNumbers = document.querySelectorAll('.page-number');
                pdfPageNumbers.forEach(pageNumber => {
                    pageNumber.style.bottom = '40px';
                });

                // Initialiser avec les contrôles visibles
                setControlsTimeout();
            });
        </script>
    @endpush
    @include('pdf.assets.flipbook_js')
</x-app-layout>










<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
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
            <div class="bg-white rounded-xl shadow-sm border p-8 text-center">
                
                <!-- Icône PowerPoint -->
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.5 16.5c-.309.29-.765.42-1.296.42a2.23 2.23 0 0 1-.308-.018v1.426H7v-3.936A7.558 7.558 0 0 1 8.219 14.5c.557 0 .953.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zM14 9h-1V4l5 5h-4z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Titre -->
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Présentation PowerPoint
                </h2>
                
                <!-- Description -->
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Vous pouvez consulter cette présentation en ligne avec Google Slides ou la télécharger pour l'ouvrir avec PowerPoint.
                </p>
                
                <!-- Informations du fichier -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 max-w-sm mx-auto">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Type :</span>
                            <span class="font-medium">{{ strtoupper(pathinfo($filename, PATHINFO_EXTENSION)) }}</span>
                        </div>
                        @if($document->file_size)
                            <div>
                                <span class="text-gray-500">Taille :</span>
                                <span class="font-medium">{{ number_format($document->file_size / 1024 / 1024, 1) }} MB</span>
                            </div>
                        @endif
                        <div>
                            <span class="text-gray-500">Ajouté :</span>
                            <span class="font-medium">{{ $document->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Vues :</span>
                            <span class="font-medium">{{ $document->view_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                    <button onclick="openWithGoogleSlides()" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ouvrir avec Google Slides
                    </button>
                    
                    <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-medium rounded-lg hover:from-orange-700 hover:to-orange-800 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger le fichier
                    </a>
                </div>
                
                <!-- Options alternatives -->
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-3">Autres options :</p>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <button onclick="openWithOnlineViewer()" 
                            class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            📄 Office Online
                        </button>
                        
                        <button onclick="copyDownloadLink()" 
                            class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            🔗 Copier le lien
                        </button>
                    </div>
                </div>
                
                <!-- Conseils d'utilisation -->
                <div class="text-left max-w-md mx-auto">
                    <h3 class="font-semibold text-gray-900 mb-3">💡 Pour ouvrir ce fichier :</h3>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p>• <strong>Google Slides</strong> : Visualisation en ligne gratuite</p>
                        <p>• <strong>Microsoft PowerPoint</strong> : Meilleure compatibilité</p>
                        <p>• <strong>LibreOffice Impress</strong> : Alternative gratuite</p>
                        <p>• <strong>Office Online</strong> : Si Google Slides ne fonctionne pas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Fonction principale pour ouvrir avec Google Slides
        function openWithGoogleSlides() {
            const fileUrl = encodeURIComponent('{{ asset("storage/documents/" . urlencode($filename)) }}');
            
            // Méthode 1: Google Docs Viewer (recommandée)
            const googleViewerUrl = `https://docs.google.com/viewer?url=${fileUrl}&embedded=true`;
            
            // Ouvrir dans un nouvel onglet
            const newWindow = window.open(googleViewerUrl, '_blank');
            
            // Vérifier si la fenêtre s'est ouverte
            if (!newWindow) {
                showNotification('Veuillez autoriser les pop-ups pour ce site', 'warning');
            } else {
                showNotification('Ouverture avec Google Slides...', 'success');
            }
        }
        
        // Fonction alternative avec Office Online (fallback)
        function openWithOnlineViewer() {
            const fileUrl = encodeURIComponent('{{ asset("storage/documents/" . urlencode($filename)) }}');
            const officeViewerUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${fileUrl}`;
            
            const newWindow = window.open(officeViewerUrl, '_blank');
            
            if (!newWindow) {
                showNotification('Veuillez autoriser les pop-ups pour ce site', 'warning');
            } else {
                showNotification('Ouverture avec Office Online...', 'info');
            }
        }
        
        // Copier le lien de téléchargement
        function copyDownloadLink() {
            const downloadUrl = '{{ route("pdf.download", ["filename" => urlencode($filename)]) }}';
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(downloadUrl).then(() => {
                    showNotification('Lien copié dans le presse-papiers !', 'success');
                }).catch(() => {
                    fallbackCopyLink(downloadUrl);
                });
            } else {
                fallbackCopyLink(downloadUrl);
            }
        }
        
        // Méthode fallback pour copier le lien
        function fallbackCopyLink(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            
            try {
                document.execCommand('copy');
                showNotification('Lien copié !', 'success');
            } catch (err) {
                showNotification('Impossible de copier le lien', 'error');
            }
            
            document.body.removeChild(textArea);
        }
        
        // Système de notifications
        function showNotification(message, type = 'info') {
            // Supprimer les notifications existantes
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Créer la notification
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${getNotificationStyle(type)}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            // Suppression automatique après 3 secondes
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, 3000);
        }
        
        // Styles des notifications
        function getNotificationStyle(type) {
            const styles = {
                success: 'bg-green-600 text-white',
                error: 'bg-red-600 text-white',
                warning: 'bg-yellow-600 text-white',
                info: 'bg-blue-600 text-white'
            };
            return styles[type] || styles.info;
        }
        
        // Style initial pour les notifications
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .notification {
                    transform: translateX(100%);
                    opacity: 0;
                    max-width: 300px;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    @endpush
</x-app-layout>