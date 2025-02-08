import './bootstrap';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

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
});