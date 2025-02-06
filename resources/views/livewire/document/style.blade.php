@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pptxjs/1.21.1/pptxjs.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');

/* Base & Variables */
:root {
    --primary: #0345fc;
    --primary-darker: #0056b3;
    --secondary: #ff0000;
    --bg-dark: rgba(10, 15, 30, 0.98);
    --text-light: #ffffff;
}
.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--bg-dark);
    backdrop-filter: blur(10px);
    z-index: 2000;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

.slide-content {
    width: 100%;
    height: 100%;
    padding: 20px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

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

/* Styles pour les pages */
.page {
    background: white;
    box-shadow: inset -1px 0 3px rgba(0, 0, 0, 0.1);
}

.doc-info {
    font-size: 12px;
    opacity: 0.8;
}


/* Modal principale */
.modal {
    display: none; /* Important : garder display: none */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 1000;
    animation: modalFadeIn 0.3s ease; /* Ajout d'une animation */
}

/* Animation pour l'apparition de la modal */
@keyframes modalFadeIn {
    from {
        background: rgba(0,0,0,0);
    }
    to {
        background: rgba(0,0,0,0.8);
    }
}

.modal-content {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1cqminrem;
    /* background: rgba(15, 23, 42, 0.98); */
}


.close-modal {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 40px;
    cursor: pointer;
    z-index: 1001;
    color: #ff0000;
    background: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    border: 2px solid #ff0000;
}

.close-modal:hover {
    transform: scale(1.1);
    background: #ff0000;
    color: white;
}

/* Flipbook styles */
.flipbook {
    /* background: white; */
    /* box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); */
    border-radius: 8px;
    overflow: hidden;
    position: relative;
}

.flipbook::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 25%;
    transform: translate(-50%, -50%);
    width: 150px;
    height: 150px;
    /* background: url('/assets/image/logo.png') no-repeat center center; */
    background-size: contain;
    opacity: 0.1;
    pointer-events: none;
}

.logo-book {
    width: 80px; /* Ajuster selon la taille souhaitée */
    height: auto;
    margin: 10px auto; /* Centrage horizontal */
    display: block;
}

.loading-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    border: 4px solid transparent;
    border-top: 4px solid #3498db;
    border-right: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-text h3 {
    font-size: 24px;
    margin-bottom: 10px;
    font-weight: 500;
}

.loading-text p {
    font-size: 16px;
    color: #a0aec0;
    margin-bottom: 15px;
}

.progress-bar {
    width: 300px;
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    overflow: hidden;
    margin: 0 auto;
}

.progress-fill {
    width: 0%;
    height: 100%;
    background: #3498db;
    border-radius: 2px;
    transition: width 0.3s ease;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
/* Animation pour l'apparition du flipbook */
@keyframes flipbookAppear {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.flipbook .hard {
    background: #060346 !important;
    color: #fff;
    font-weight: bold;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.flipbook .page {
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}


/* Navigation styles */
.navigation {
    display: flex;
    align-items: center;
    gap: 15px;
    /* position: fixed; */
    /* bottom: 20px;
    padding: 10px 20px; */
    border-radius: 30px;
    background: rgba(44, 62, 80, 0.9); /* Réactivé pour meilleure visibilité */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); /* Réactivé pour meilleure profondeur */
    animation: navAppear 0.5s ease 0.3s both; /* Animation ajoutée */
}

/* Animation pour l'apparition de la navigation */
@keyframes navAppear {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
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
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
}

.nav-btn:hover {
    background: #f1c40f;
    color: #34495e;
    transform: scale(1.1);
}

.page-info {
    color: white;
    font-size: 16px;
    min-width: 30px;
    text-align: center;
    font-weight: 500;
    background: #34495e;
    border-radius: 100%;
    padding: 5px 10px;
}

/* Loading indicator */
#loadingIndicator {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 8px;
    z-index: 2000;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    display: none;
}

.spinner {
    text-align: center;
    font-size: 16px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.spinner::before {
    content: '';
    display: block;
    width: 20px;
    height: 20px;
    border: 2px solid #2c3e50;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Media queries */
@media (max-width: 768px) {
    .close-modal {
        font-size: 30px;
        width: 40px;
        height: 40px;
        right: 15px;
        top: 15px;
    }

    .document-list {
        padding: 10px;
    }

    .flipbook {
        width: 90%;
        height: 400px;
    }

    .navigation {
        padding: 8px 15px;
        border-radius: 25px;
    }

    .nav-btn {
        width: 35px;
        height: 35px;
    }

    .page-info {
        font-size: 14px;
    }

    .document-card {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}

.slide-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
    background: white;
    padding: 2rem;
}

.slide-number {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 1rem;
}

.slide-text {
    font-size: 1.2rem;
    color: #333;
}

</style>
@endpush
