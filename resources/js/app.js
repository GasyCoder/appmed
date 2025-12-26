import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import './chatbot.js';

window.Alpine = Alpine;

Alpine.plugin(focus);
Alpine.plugin(collapse);

/**
 * ✅ Source unique de vérité pour le thème
 */
window.__applyTheme = function () {
    const stored = localStorage.getItem('darkMode');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    const isDark = stored !== null ? (stored === 'true') : prefersDark;

    if (isDark) document.documentElement.classList.add('dark');
    else document.documentElement.classList.remove('dark');

    return isDark;
};

/**
 * ✅ Landing + pages publiques : même logique
 */
window.landingTheme = function () {
    return {
        darkMode: false,

        init() {
            this.darkMode = window.__applyTheme();

            // Mesure header fixed pour padding-top (ton code)
            const applyHeaderOffset = () => {
                const header = document.getElementById('landingHeader');
                if (!header) return;
                const h = Math.ceil(header.getBoundingClientRect().height || 64);
                document.documentElement.style.setProperty('--landing-header-h', h + 'px');
            };
            applyHeaderOffset();
            window.addEventListener('resize', applyHeaderOffset, { passive: true });
            setTimeout(applyHeaderOffset, 60);
            setTimeout(applyHeaderOffset, 250);

            // Sync si localStorage change (multi-tabs)
            window.addEventListener('storage', (e) => {
                if (e.key !== 'darkMode') return;
                this.darkMode = window.__applyTheme();
            });
        },

        toggleDark() {
            const next = !this.darkMode;
            localStorage.setItem('darkMode', next ? 'true' : 'false');
            this.darkMode = window.__applyTheme();
        }
    }
};

/**
 * ✅ Pour tes pages internes si tu utilises Alpine.data('theme')
 */
Alpine.data('theme', () => ({
    darkMode: false,

    init() {
        this.darkMode = window.__applyTheme();

        this.$watch('darkMode', (value) => {
            localStorage.setItem('darkMode', value ? 'true' : 'false');
            window.__applyTheme();
        });

        document.addEventListener('livewire:navigated', () => {
            this.darkMode = window.__applyTheme();
        });
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
    },
}));

// ✅ Applique immédiatement avant Alpine start (évite flash blanc/noir)
window.__applyTheme();

Alpine.start();
