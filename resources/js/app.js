import './bootstrap';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

// Application simple du mode sombre au chargement
if (localStorage.getItem('darkMode') === 'true') {
    document.documentElement.classList.add('dark');
}

document.addEventListener('alpine:init', () => {
    Alpine.plugin(focus);
    Alpine.plugin(collapse);

    Alpine.data('fileUpload', () => ({
        files: [],
        handleFileSelect() {
            this.files = [...this.$refs.fileInput.files];
        },
        formatFileSize(bytes) {
            return Math.round(bytes / 1024) + ' KB';
        }
    }));

    // Ajout minimal du dark mode
    Alpine.data('darkModeData', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true',

        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode ? 'true' : 'false');

            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }));
});
