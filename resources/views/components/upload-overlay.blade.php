<script>
function lwUploadOverlay() {
    return {
        open: false,
        progress: 0,
        title: 'Upload en cours…',
        subtitle: 'Veuillez patienter',
        hint: 'Préparation…',

        _closeTimer: null,
        _smoothTimer: null,
        _lastRealProgress: null,
        _safetyTimeout: null, // ✅ NOUVEAU : Timeout de sécurité

        init() {
            const on = (name, fn) => window.addEventListener(name, fn);

            on('livewire-upload-start', (e) => {
                if (!this._isFilesUploadEvent(e)) return;

                this._clearClose();
                this._stopSmooth();
                this._clearSafetyTimeout(); // ✅ Clear ancien timeout

                this.open = true;
                this.progress = 0;
                this._lastRealProgress = null;

                this.title = 'Upload en cours…';
                this.subtitle = 'Veuillez patienter';
                this.hint = 'Démarrage du transfert…';

                this._startSmooth();

                // ✅ NOUVEAU : Timeout de sécurité (2 minutes max)
                this._safetyTimeout = setTimeout(() => {
                    console.warn('Upload timeout - fermeture automatique');
                    this._handleTimeout();
                }, 120000); // 2 minutes
            });

            on('livewire-upload-progress', (e) => {
                if (!this._isFilesUploadEvent(e)) return;

                const raw = e?.detail?.progress;
                const p = Number.isFinite(Number(raw)) ? Number(raw) : null;

                if (p === null) return;

                this._lastRealProgress = p;
                this._stopSmooth();

                this.progress = Math.min(99, Math.max(0, Math.round(p)));

                if (this.progress < 20) this.hint = 'Préparation…';
                else if (this.progress < 60) this.hint = 'Transfert du fichier…';
                else if (this.progress < 90) this.hint = 'Traitement côté serveur…';
                else this.hint = 'Finalisation…';

                // ✅ Si on atteint 99%, forcer la fermeture après 5 secondes de sécurité
                if (this.progress >= 99) {
                    this._clearSafetyTimeout();
                    this._safetyTimeout = setTimeout(() => {
                        console.warn('Progress 99% sans finish - fermeture automatique');
                        this._finishAndClose();
                    }, 5000);
                }
            });

            on('livewire-upload-finish', (e) => {
                if (!this._isFilesUploadEvent(e)) return;
                this._clearSafetyTimeout();
                this._finishAndClose();
            });

            on('livewire-upload-error', (e) => {
                if (!this._isFilesUploadEvent(e)) return;

                this._clearSafetyTimeout();
                this._stopSmooth();
                
                this.title = 'Erreur d'upload';
                this.subtitle = 'Vérifiez le fichier et réessayez';
                this.hint = 'Upload interrompu.';
                this.progress = 100;

                this._clearClose();
                this._closeTimer = setTimeout(() => {
                    this.open = false;
                    this.progress = 0;
                }, 3000); // 3 secondes pour lire l'erreur
            });

            // ✅ NOUVEAU : Écouter les événements Livewire généraux
            on('livewire:navigating', () => {
                // Navigation Livewire → fermer l'overlay
                this._clearSafetyTimeout();
                this._stopSmooth();
                this.open = false;
            });

            on('livewire:navigated', () => {
                // Page chargée → fermer l'overlay si encore ouvert
                this._clearSafetyTimeout();
                this._stopSmooth();
                this.open = false;
            });
        },

        _startSmooth() {
            this._smoothTimer = setInterval(() => {
                if (this._lastRealProgress !== null) return;

                if (this.progress < 15) this.progress += 2;
                else if (this.progress < 40) this.progress += 1;
                else if (this.progress < 70) this.progress += 1;
                else if (this.progress < 85) this.progress += 0.5;

                this.progress = Math.min(85, Math.round(this.progress));

                if (this.progress < 20) this.hint = 'Préparation…';
                else if (this.progress < 60) this.hint = 'Transfert du fichier…';
                else this.hint = 'Traitement côté serveur…';
            }, 180);
        },

        _stopSmooth() {
            if (this._smoothTimer) {
                clearInterval(this._smoothTimer);
                this._smoothTimer = null;
            }
        },

        _finishAndClose() {
            this._stopSmooth();

            this.title = 'Upload terminé';
            this.subtitle = 'Fichier(s) enregistré(s)';
            this.hint = 'Terminé ✓';
            this.progress = 100;

            this._clearClose();
            this._closeTimer = setTimeout(() => {
                this.open = false;
                this.progress = 0;
                this.title = 'Upload en cours…';
                this.subtitle = 'Veuillez patienter';
                this.hint = 'Préparation…';
                this._lastRealProgress = null;
            }, 800); // Temps pour voir "Terminé"
        },

        // ✅ NOUVEAU : Gérer le timeout
        _handleTimeout() {
            this._stopSmooth();

            this.title = 'Upload long';
            this.subtitle = 'Le traitement continue en arrière-plan';
            this.hint = 'Vous pouvez fermer cette fenêtre';
            this.progress = 100;

            this._clearClose();
            this._closeTimer = setTimeout(() => {
                this.open = false;
                this.progress = 0;
                this.title = 'Upload en cours…';
                this.subtitle = 'Veuillez patienter';
                this.hint = 'Préparation…';
                this._lastRealProgress = null;
            }, 3000);
        },

        _clearClose() {
            if (this._closeTimer) {
                clearTimeout(this._closeTimer);
                this._closeTimer = null;
            }
        },

        // ✅ NOUVEAU : Clear timeout de sécurité
        _clearSafetyTimeout() {
            if (this._safetyTimeout) {
                clearTimeout(this._safetyTimeout);
                this._safetyTimeout = null;
            }
        },

        _isFilesUploadEvent(e) {
            const d = e?.detail ?? {};
            const prop =
                d.propertyName ??
                d.name ??
                d.property ??
                d.uploadName ??
                d?.file?.propertyName ??
                null;

            if (!prop) return true;
            return prop === 'files';
        },
    }
}
</script>

{{-- Fullscreen Loading Overlay --}}
<div x-data="lwUploadOverlay()"
    x-init="init()"
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm">
    
    <div class="w-full max-w-sm rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-xl dark:border-gray-700 dark:bg-gray-800"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div class="flex items-start gap-3">
            {{-- Spinner --}}
            <svg class="mt-0.5 h-5 w-5 animate-spin text-indigo-600" 
                 :class="{ 'text-green-600': progress === 100 && title === 'Upload terminé' }"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <div class="min-w-0 flex-1">
                {{-- Titre + Pourcentage --}}
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="title"></p>
                    <p class="text-sm font-extrabold tabular-nums text-indigo-600 dark:text-indigo-400"
                       :class="{ 'text-green-600 dark:text-green-400': progress === 100 && title === 'Upload terminé' }">
                        <span x-text="progress"></span>%
                    </p>
                </div>

                {{-- Sous-titre --}}
                <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-300" x-text="subtitle"></p>

                {{-- Barre de progression --}}
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-full rounded-full bg-indigo-600 transition-[width] duration-300 ease-out"
                         :class="{ 'bg-green-600': progress === 100 && title === 'Upload terminé' }"
                         :style="`width: ${progress}%`"></div>
                </div>

                {{-- Hint --}}
                <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400" x-text="hint"></p>
            </div>
        </div>
    </div>
</div>