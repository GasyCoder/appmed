{{-- livewire.documents.partials.overlay --}}

@once
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

        init() {
            const on = (name, fn) => window.addEventListener(name, fn);

            on('livewire-upload-start', (e) => {
                if (!this._isFilesUploadEvent(e)) return;

                this._clearClose();
                this._stopSmooth();

                this.open = true;
                this.progress = 0;
                this._lastRealProgress = null;

                this.title = 'Upload en cours…';
                this.subtitle = 'Veuillez patienter';
                this.hint = 'Démarrage du transfert…';

                this._startSmooth();
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
                else if (this.progress < 90) this.hint = 'Traitement…';
                else this.hint = 'Finalisation…';
            });

            on('livewire-upload-finish', (e) => {
                if (!this._isFilesUploadEvent(e)) return;
                this._finishAndClose();
            });

            on('livewire-upload-error', (e) => {
                if (!this._isFilesUploadEvent(e)) return;

                this._stopSmooth();
                this.title = 'Erreur d’upload';
                this.subtitle = 'Vérifiez le fichier et réessayez';
                this.hint = 'Upload interrompu.';
                this.progress = 100;

                this._clearClose();
                this._closeTimer = setTimeout(() => {
                    this.open = false;
                    this.progress = 0;
                }, 900);
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
                else this.hint = 'Traitement…';
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
            this.subtitle = 'Veuillez patienter';
            this.hint = 'Terminé.';
            this.progress = 100;

            this._clearClose();
            this._closeTimer = setTimeout(() => {
                this.open = false;
                this.progress = 0;
                this.title = 'Upload en cours…';
                this.subtitle = 'Veuillez patienter';
                this.hint = 'Préparation…';
                this._lastRealProgress = null;
            }, 350);
        },

        _clearClose() {
            if (this._closeTimer) {
                clearTimeout(this._closeTimer);
                this._closeTimer = null;
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
@endonce

{{-- Fullscreen Loading Overlay --}}
<div x-data="lwUploadOverlay()"
     x-init="init()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-xl dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-start gap-3">
            <svg class="mt-0.5 h-5 w-5 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="title"></p>
                    <p class="text-sm font-extrabold tabular-nums text-indigo-600 dark:text-indigo-400">
                        <span x-text="progress"></span>%
                    </p>
                </div>

                <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-300" x-text="subtitle"></p>

                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-full rounded-full bg-indigo-600 transition-[width] duration-200"
                         :style="`width: ${progress}%`"></div>
                </div>

                <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400" x-text="hint"></p>
            </div>
        </div>
    </div>
</div>
