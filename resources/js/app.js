import './bootstrap';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

document.addEventListener('alpine:init', () => {
    Alpine.plugin(focus);
    Alpine.plugin(collapse);

    Alpine.data('theme', () => ({
        darkMode: false,

        init() {
            // ✅ Lire localStorage au démarrage
            const stored = localStorage.getItem('darkMode');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            // Initialiser avec la valeur stockée ou la préférence système
            this.darkMode = stored !== null ? (stored === 'true') : prefersDark;

            // ✅ S'assurer que la classe est appliquée
            this.applyTheme();

            // ✅ Watcher pour les changements futurs
            this.$watch('darkMode', (value) => {
                this.applyTheme();
            });
        },

        applyTheme() {
            // Persister dans localStorage
            localStorage.setItem('darkMode', this.darkMode ? 'true' : 'false');
            
            // Appliquer au DOM
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
        },
    }));

    // ✅ Écouter les événements Livewire pour réappliquer le thème
    document.addEventListener('livewire:navigated', () => {
        const stored = localStorage.getItem('darkMode');
        const isDark = stored === 'true';
        
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
});