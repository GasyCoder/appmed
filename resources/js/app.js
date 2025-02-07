import './bootstrap';
import Alpine from 'alpinejs';

// Ne pas initialiser Alpine directement
window.Alpine = Alpine;

// Plugins
Alpine.plugin(focus);
Alpine.plugin(collapse);
Alpine.plugin(persist);
