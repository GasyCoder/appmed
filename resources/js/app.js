import './bootstrap';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

document.addEventListener('alpine:init', () => {
    Alpine.plugin(focus);
    Alpine.plugin(collapse);

    Alpine.data('theme', () => ({
        darkMode: false,

        init() {
            const stored = localStorage.getItem('darkMode');
            this.darkMode = stored === 'true';

            // Synchronisation automatique (persist + DOM)
            this.$watch('darkMode', (value) => {
                localStorage.setItem('darkMode', value ? 'true' : 'false');
                document.documentElement.classList.toggle('dark', value);
            });

            // Applique immédiatement au chargement
            document.documentElement.classList.toggle('dark', this.darkMode);
        },

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
        },
    }));
});
