@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pptxjs/1.21.1/pptxjs.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');

/* ========== VARIABLES & RESET ========== */
:root {
    /* Couleurs */
    --primary: #0345fc;
    --secondary: #ff0000;
    --dark: #1a1a1a;
    --light: #ffffff;
    --gray: #f5f5f5;

    /* Dimensions par défaut */
    --desktop-width: 1200px;
    --desktop-height: 800px;
    --tablet-width: 90vw;
    --mobile-width: 95vw;

    /* Espacement */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 2rem;
    --spacing-xl: 3rem;

    /* Transitions */
    --transition-fast: 0.2s ease;
    --transition-normal: 0.3s ease;
    --transition-slow: 0.5s ease;

    /* Z-index layers */
    --z-modal: 1000;
    --z-overlay: 2000;
    --z-dropdown: 3000;
    --z-tooltip: 4000;
}

/* ========== RESET GÉNÉRAL ========== */
*, *::before, *::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ========== STYLES DE BASE ========== */
body.modal-open {
    overflow: hidden;
    position: fixed;
    width: 100%;
    height: 100%;
}

/* ========== MODAL PRINCIPAL ========== */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: var(--z-modal);
    overflow: auto;
}

.modal-content {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 2rem;
    overflow-y: auto;
}

/* ========== CONTENEUR FLIPBOOK ========== */
.flipbook-container {
    position: relative;
    width: 100%;
    height: auto;
    min-height: 80vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    gap: var(--spacing-lg);
    margin: 0 auto;
}

.flipbook {
    position: relative;
    background: var(--light);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    margin: 0 auto;
    transform-origin: center top;
}

/* ========== PAGES DU FLIPBOOK ========== */
.page {
    background: white;
    box-shadow: inset -1px 0 3px rgba(0, 0, 0, 0.1);
}

.doc-info {
    font-size: 12px;
    opacity: 0.8;
}

.page img {
    object-fit: contain;
}

/* ========== PAGE DE COUVERTURE ========== */
.cover-page {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: white;
}

.cover-container {
    padding: 3rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.cover-header {
    text-align: center;
    margin-bottom: 2rem;
}

.university {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    letter-spacing: 1px;
}

.fac-university {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    opacity: 0.9;
}

.logo-book {
    width: 100px;
    height: auto;
    margin: var(--spacing-lg) auto;
}

.cover-body {
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.document-title {
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 2rem;
    line-height: 1.2;
    text-transform: capitalize;
}

.document-meta {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    font-size: 1.1rem;
}

.cover-footer {
    text-align: center;
    margin-top: 2rem;
}

.auteur-name {
    font-size: 0.9rem;
    font-weight: 400;
    margin-bottom: 0.2rem;
}

.document-page {
    font-size: 0.6rem;
    opacity: 0.8;
}

/* ========== NAVIGATION ========== */
.navigation {
    position: relative;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-md) var(--spacing-lg);
    background: rgba(44, 62, 80, 0.95);
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: var(--z-dropdown);
}

.nav-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: none;
    background: #34495e;
    color: #f1c40f;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition-fast);
}

.nav-btn:hover {
    background: #f1c40f;
    color: #34495e;
    transform: scale(1.1);
}

.page-number {
    min-width: 40px;
    text-align: center;
    color: var(--light);
    font-weight: 500;
    padding: var(--spacing-sm) var(--spacing-md);
    background: rgba(52, 73, 94, 0.5);
    border-radius: 20px;
}

/* ========== BOUTON FERMETURE ========== */
.close-modal {
    position: absolute;
    right: var(--spacing-lg);
    top: var(--spacing-lg);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--light);
    color: var(--secondary);
    border: 2px solid var(--secondary);
    font-size: 2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition-fast);
    z-index: var(--z-dropdown);
}

.close-modal:hover {
    background: var(--secondary);
    color: var(--light);
    transform: scale(1.1);
}

/* ========== LOADING OVERLAY ========== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: var(--dark);
    z-index: var(--z-overlay);
    display: none;
}

.loading-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: var(--light);
    width: 90%;
    max-width: 400px;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    margin: 0 auto var(--spacing-lg);
    border: 4px solid rgba(255, 255, 255, 0.1);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    margin: var(--spacing-md) 0;
    overflow: hidden;
}

.progress-fill {
    width: 0%;
    height: 100%;
    background: var(--primary);
    border-radius: 2px;
    transition: width var(--transition-normal);
}

/* ========== RESPONSIVE DESIGN ========== */
/* PC */
@media (min-width: 1024px) {
    .flipbook {
        width: var(--desktop-width) !important;
        height: var(--desktop-height) !important;
        max-width: 90vw;
        max-height: 85vh;
    }

    .flipbook-container {
        padding: var(--spacing-xl);
    }

    .page {
        width: 100% !important;
        height: 100% !important;
    }
}

/* Tablette */
@media (min-width: 768px) and (max-width: 1023px) {
    .flipbook {
        width: var(--tablet-width) !important;
        height: auto !important;
        aspect-ratio: 4/3;
    }

    .document-title {
        font-size: 1.5rem;
    }

    .navigation {
        padding: var(--spacing-sm) var(--spacing-lg);
    }

    .page {
        width: 100% !important;
        height: 100% !important;
    }
}

/* Mobile */
@media (max-width: 767px) {
    .flipbook {
        width: var(--mobile-width) !important;
        height: auto !important;
        aspect-ratio: 3/4;
    }

    .modal-content {
        padding: var(--spacing-md);
    }

    .navigation {
        position: fixed;
        bottom: var(--spacing-lg);
        left: 50%;
        transform: translateX(-50%);
    }

    .nav-btn {
        width: 35px;
        height: 35px;
    }

    .document-title {
        font-size: 1.2rem;
    }

    .close-modal {
        right: var(--spacing-md);
        top: var(--spacing-md);
        width: 40px;
        height: 40px;
        font-size: 1.5rem;
    }

    .page {
        width: 100% !important;
        height: 100% !important;
    }
}

/* Mode paysage mobile */
@media (max-height: 480px) and (orientation: landscape) {
    .flipbook {
        height: 60vh !important;
    }

    .navigation {
        bottom: var(--spacing-sm);
    }
}

/* ========== ANIMATIONS ========== */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* ========== OPTIMISATIONS ========== */
.flipbook, .page {
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
    backface-visibility: hidden;
    perspective: 1000px;
}

/* ========== UTILITAIRES ========== */
.hidden {
    display: none !important;
}

.fade {
    transition: opacity var(--transition-normal);
}

.fade-enter {
    opacity: 0;
}

.fade-enter-active {
    opacity: 1;
}

/* ========== PROTECTION IMPRESSION ========== */
@media print {
    .modal,
    .flipbook,
    .navigation {
        display: none !important;
    }
}
</style>
@endpush
