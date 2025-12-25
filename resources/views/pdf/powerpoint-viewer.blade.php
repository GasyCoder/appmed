{{-- view/pdf/powerpoint-viewer.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <!-- Header avec contrôles -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button onclick="history.back()" 
                            class="inline-flex items-center px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour aux documents
                        </button>
                        
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $document->title }}</h1>
                            @if($teacherInfo)
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
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
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Panel principal -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                        
                        <!-- Icône PowerPoint grande -->
                        <div class="mb-6">
                            <div class="mx-auto w-24 h-24 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center">
                                <svg class="w-12 h-12 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9.5 16.5c-.309.29-.765.42-1.296.42a2.23 2.23 0 0 1-.308-.018v1.426H7v-3.936A7.558 7.558 0 0 1 8.219 14.5c.557 0 .953.106 1.22.319.254.202.426.533.426.923-.001.392-.131.723-.367.948zM14 9h-1V4l5 5h-4z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Titre et description -->
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            Présentation PowerPoint
                        </h2>
                        
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                            Vous pouvez consulter cette présentation en ligne avec Google Slides ou la télécharger pour l'ouvrir avec PowerPoint.
                        </p>
                        
                        <!-- Indicateur de chargement -->
                        <div id="contentLoading" class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-center space-x-3">
                                <!-- Spinner animé -->
                                <div class="animate-spin rounded-full h-5 w-5 border-2 border-orange-300 border-t-orange-600"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Préparation de l'aperçu...</span>
                                </div>
                            </div>
                            
                            <!-- Barre de progression stylisée -->
                            <div class="mt-4">
                                <div class="bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-full animate-pulse rounded-full progress-bar"></div>
                                </div>
                            </div>
                            
                            <!-- Messages de chargement rotatifs -->
                            <div class="mt-3 text-center">
                                <span id="loadingMessage" class="text-xs text-gray-500 dark:text-gray-400 italic">
                                    Analyse du document en cours...
                                </span>
                            </div>
                        </div>

                        <!-- Aperçu du contenu (affiché après chargement) -->
                        <div id="contentPreview" class="hidden">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6 text-left">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Aperçu du contenu :</h3>
                                    <span class="text-xs text-green-600 dark:text-green-400 font-medium flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Prêt
                                    </span>
                                </div>
                                <div id="slidePreview" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <!-- Le contenu sera inséré ici par JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action principaux -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-4">
                            <button onclick="openWithGoogleSlides()" 
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ouvrir avec Google Slides
                            </button>
                            
                            <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Télécharger le fichier
                            </a>
                        </div>
                        
                        <!-- Options alternatives -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Options alternatives :</p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <button onclick="openWithOfficeOnline()" 
                                    class="inline-flex items-center px-2 py-1.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Office
                                </button>
                                
                                <button onclick="openInGoogleDrive()" 
                                    class="inline-flex items-center px-2 py-1.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                    </svg>
                                    Drive
                                </button>
                                
                                <button onclick="copyDownloadLink()" 
                                    class="inline-flex items-center px-2 py-1.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Copier
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Panel latéral avec infos -->
                <div class="space-y-6">
                    
                    <!-- Informations du fichier -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Informations</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Type :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ strtoupper(pathinfo($filename, PATHINFO_EXTENSION)) }}</span>
                            </div>
                            
                            @if($document->file_size)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Taille :</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($document->file_size / 1024 / 1024, 1) }} MB</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Ajouté le :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $document->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Vues :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $document->view_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conseils d'utilisation -->
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                        <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-3">💡 Conseils</h3>
                        <div class="text-sm text-blue-800 dark:text-blue-300 space-y-2">
                            <p>• <strong>Google Slides</strong> : Visualisation en ligne gratuite</p>
                            <p>• <strong>Office Online</strong> : Alternative Microsoft</p>
                            <p>• <strong>PowerPoint</strong> : Meilleure compatibilité</p>
                            <p>• <strong>LibreOffice</strong> : Solution gratuite</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Fonction principale pour ouvrir avec Google Slides (corrigée)
        function openWithGoogleSlides() {
            // Afficher les instructions car Google ne peut pas accéder aux fichiers privés
            showGoogleSlidesInstructions();
        }
        
        // Instructions spécifiques pour Google Slides
        function showGoogleSlidesInstructions() {
            const downloadUrl = '{{ route("pdf.download", ["filename" => urlencode($filename)]) }}';
            
            showInstructions(`
                <div class="text-left">
                    <h3 class="font-bold mb-3 text-gray-900 dark:text-white">🎯 Ouvrir avec Google Slides :</h3>
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3 mb-4">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>💡 Info :</strong> Google ne peut pas accéder directement aux fichiers de votre serveur.
                        </p>
                    </div>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li>Téléchargez le fichier PowerPoint</li>
                        <li>Allez sur <a href="https://drive.google.com" target="_blank" class="text-blue-600 dark:text-blue-400 underline">Google Drive</a></li>
                        <li>Glissez-déposez le fichier ou cliquez sur "Nouveau" → "Importer un fichier"</li>
                        <li>Double-cliquez sur le fichier dans Drive</li>
                        <li>Cliquez sur "Ouvrir avec Google Slides"</li>
                    </ol>
                    <div class="mt-4 flex gap-2">
                        <a href="${downloadUrl}" download class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex-1 justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/>
                            </svg>
                            Télécharger
                        </a>
                        <a href="https://drive.google.com" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex-1 justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Ouvrir Drive
                        </a>
                    </div>
                </div>
            `);
            
            showNotification('📋 Instructions affichées pour Google Slides', 'info');
        }
        
        // Fonction alternative avec Office Online (corrigée)
        function openWithOfficeOnline() {
            const downloadUrl = '{{ route("pdf.download", ["filename" => urlencode($filename)]) }}';
            
            showInstructions(`
                <div class="text-left">
                    <h3 class="font-bold mb-3 text-gray-900 dark:text-white">📊 Ouvrir avec Office Online :</h3>
                    <div class="bg-orange-50 dark:bg-orange-900/30 rounded-lg p-3 mb-4">
                        <p class="text-sm text-orange-800 dark:text-orange-200">
                            <strong>💡 Info :</strong> Office Online nécessite que le fichier soit accessible publiquement.
                        </p>
                    </div>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li>Téléchargez le fichier PowerPoint</li>
                        <li>Allez sur <a href="https://www.office.com" target="_blank" class="text-blue-600 dark:text-blue-400 underline">Office.com</a></li>
                        <li>Connectez-vous avec votre compte Microsoft</li>
                        <li>Cliquez sur "Télécharger" et sélectionnez votre fichier</li>
                        <li>Le fichier s'ouvrira automatiquement dans PowerPoint Online</li>
                    </ol>
                    <div class="mt-4 flex gap-2">
                        <a href="${downloadUrl}" download class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 flex-1 justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/>
                            </svg>
                            Télécharger
                        </a>
                        <a href="https://www.office.com" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex-1 justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Office.com
                        </a>
                    </div>
                </div>
            `);
            
            showNotification('📋 Instructions affichées pour Office Online', 'info');
        }
        
        // Fonction pour ouvrir via Google Drive (instruction à l'utilisateur)
        function openInGoogleDrive() {
            const downloadUrl = '{{ route("pdf.download", ["filename" => urlencode($filename)]) }}';
            
            // Créer un modal avec instructions
            showInstructions(`
                <div class="text-left">
                    <h3 class="font-bold mb-3 text-gray-900 dark:text-white">📝 Instructions pour Google Drive :</h3>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li>Téléchargez le fichier en cliquant ci-dessous</li>
                        <li>Allez sur <a href="https://drive.google.com" target="_blank" class="text-blue-600 dark:text-blue-400 underline">Google Drive</a></li>
                        <li>Uploadez le fichier téléchargé</li>
                        <li>Double-cliquez sur le fichier dans Drive</li>
                        <li>Il s'ouvrira automatiquement dans Google Slides</li>
                    </ol>
                    <div class="mt-4">
                        <a href="${downloadUrl}" download class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"/>
                            </svg>
                            Télécharger le fichier
                        </a>
                    </div>
                </div>
            `);
        }
        
        // Copier le lien de téléchargement
        function copyDownloadLink() {
            const downloadUrl = '{{ route("pdf.download", ["filename" => urlencode($filename)]) }}';
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(downloadUrl).then(() => {
                    showNotification('📋 Lien copié dans le presse-papiers !', 'success');
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
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.select();
            
            try {
                document.execCommand('copy');
                showNotification('📋 Lien copié !', 'success');
            } catch (err) {
                showNotification('❌ Impossible de copier le lien', 'error');
            }
            
            document.body.removeChild(textArea);
        }
        
        // Afficher des options alternatives (avec dark mode et corrigées)
        function showAlternativeOptions() {
            showInstructions(`
                <div class="text-left">
                    <h3 class="font-bold mb-3 text-gray-900 dark:text-white">🔧 Options alternatives :</h3>
                    <div class="space-y-3">
                        <button onclick="showGoogleSlidesInstructions(); closeModal();" class="w-full text-left px-4 py-2 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg">
                            <strong class="text-gray-900 dark:text-white">Google Slides</strong><br>
                            <small class="text-gray-600 dark:text-gray-400">Instructions pour ouvrir avec Google Slides</small>
                        </button>
                        <button onclick="openWithOfficeOnline(); closeModal();" class="w-full text-left px-4 py-2 bg-orange-50 dark:bg-orange-900/30 hover:bg-orange-100 dark:hover:bg-orange-900/50 rounded-lg">
                            <strong class="text-gray-900 dark:text-white">Office Online</strong><br>
                            <small class="text-gray-600 dark:text-gray-400">Instructions pour Office Online</small>
                        </button>
                        <a href="{{ route('pdf.download', ['filename' => urlencode($filename)]) }}" download class="block w-full text-left px-4 py-2 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg">
                            <strong class="text-gray-900 dark:text-white">Téléchargement direct</strong><br>
                            <small class="text-gray-600 dark:text-gray-400">Ouvrir avec PowerPoint local</small>
                        </a>
                    </div>
                </div>
            `);
        }
        
        // Système de notifications (avec dark mode)
        function showNotification(message, type = 'info') {
            // Supprimer les notifications existantes
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Créer la notification
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 ${getNotificationStyle(type)}`;
            notification.innerHTML = message;
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            // Suppression automatique après 4 secondes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.transform = 'translateX(100%)';
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 4000);
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
        
        // Système de modal pour les instructions (avec dark mode)
        function showInstructions(content) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center z-50 p-4';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 relative border dark:border-gray-700">
                    <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    ${content}
                </div>
            `;
            document.body.appendChild(modal);
            
            // Fermer avec Escape
            document.addEventListener('keydown', function escapeHandler(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', escapeHandler);
                }
            });
        }
        
        function closeModal() {
            const modal = document.querySelector('.fixed.inset-0');
            if (modal) {
                modal.remove();
            }
        }
        
        // Initialisation de l'aperçu du contenu avec chargement animé
        document.addEventListener('DOMContentLoaded', function() {
            const loading = document.getElementById('contentLoading');
            const preview = document.getElementById('contentPreview');
            const slidePreview = document.getElementById('slidePreview');
            const loadingMessage = document.getElementById('loadingMessage');
            
            // Messages de chargement rotatifs
            const loadingMessages = [
                'Analyse du document en cours...',
                'Extraction des diapositives...',
                'Préparation de l\'aperçu...',
                'Génération du contenu...',
                'Finalisation...'
            ];
            
            let messageIndex = 0;
            let messageInterval;
            
            // Fonction pour changer les messages de chargement
            function rotateMessage() {
                if (loadingMessage) {
                    loadingMessage.style.opacity = '0';
                    setTimeout(() => {
                        messageIndex = (messageIndex + 1) % loadingMessages.length;
                        loadingMessage.textContent = loadingMessages[messageIndex];
                        loadingMessage.style.opacity = '1';
                    }, 200);
                }
            }
            
            // Démarrer la rotation des messages
            messageInterval = setInterval(rotateMessage, 1500);
            
            // Simulation d'aperçu (vous pouvez implémenter une vraie extraction)
            const sampleContent = [
                "📊 Diapositive 1: Introduction au sujet",
                "📈 Diapositive 2: Données et statistiques", 
                "🎯 Diapositive 3: Objectifs principaux",
                "💡 Diapositive 4: Solutions proposées",
                "✅ Diapositive 5: Conclusions"
            ];
            
            // Simuler le chargement avec progression
            let progress = 0;
            const progressBar = document.querySelector('.progress-bar');
            
            const progressInterval = setInterval(() => {
                progress += Math.random() * 20;
                if (progress > 100) progress = 100;
                
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }
                
                if (progress >= 100) {
                    clearInterval(progressInterval);
                    
                    // Arrêter la rotation des messages
                    clearInterval(messageInterval);
                    
                    // Changer le message final
                    if (loadingMessage) {
                        loadingMessage.textContent = 'Aperçu généré avec succès !';
                        loadingMessage.classList.remove('text-gray-500', 'dark:text-gray-400');
                        loadingMessage.classList.add('text-green-600', 'dark:text-green-400');
                    }
                    
                    // Attendre un peu puis afficher le contenu
                    setTimeout(() => {
                        // Cacher le chargement avec animation
                        if (loading) {
                            loading.style.opacity = '0';
                            loading.style.transform = 'translateY(-10px)';
                            
                            setTimeout(() => {
                                loading.classList.add('hidden');
                                
                                // Afficher l'aperçu avec animation
                                if (slidePreview && preview) {
                                    slidePreview.innerHTML = sampleContent.map((item, index) => 
                                        `<div class="flex items-center gap-2 p-3 bg-white dark:bg-gray-800 rounded-lg border-l-2 border-orange-300 dark:border-orange-500 shadow-sm transform transition-all duration-300" 
                                             style="animation-delay: ${index * 0.1}s; opacity: 0;">${item}</div>`
                                    ).join('');
                                    
                                    preview.classList.remove('hidden');
                                    preview.style.opacity = '0';
                                    preview.style.transform = 'translateY(10px)';
                                    
                                    // Animation d'apparition
                                    setTimeout(() => {
                                        preview.style.opacity = '1';
                                        preview.style.transform = 'translateY(0)';
                                        
                                        // Animer chaque élément de l'aperçu
                                        const previewItems = slidePreview.querySelectorAll('div');
                                        previewItems.forEach((item, index) => {
                                            setTimeout(() => {
                                                item.style.opacity = '1';
                                                item.style.transform = 'translateX(0)';
                                            }, index * 150);
                                        });
                                    }, 100);
                                }
                            }, 300);
                        }
                    }, 800);
                }
            }, 200);
            
            // Style initial pour les animations
            const style = document.createElement('style');
            style.textContent = `
                .notification {
                    transform: translateX(100%);
                    opacity: 0;
                    max-width: 350px;
                }
                
                #contentLoading {
                    transition: all 0.3s ease-out;
                }
                
                #contentPreview {
                    transition: all 0.3s ease-out;
                }
                
                .progress-bar {
                    width: 0%;
                    transition: width 0.3s ease-out;
                }
                
                @keyframes slideInContent {
                    from {
                        opacity: 0;
                        transform: translateX(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
                
                #slidePreview div {
                    animation: slideInContent 0.5s ease-out forwards;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    @endpush
</x-app-layout>