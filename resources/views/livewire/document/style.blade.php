@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pptxjs/1.21.1/pptxjs.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');

:root {
    --primary: #0345fc;
    --primary-darker: #0056b3;
    --secondary: #ff0000;
    --bg-dark: rgba(10, 15, 30, 0.98);
    --text-light: #ffffff;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

/* Loading Overlay */
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

.loading-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    width: clamp(280px, 90%, 400px);
}

.loading-spinner {
    width: clamp(40px, 8vw, 60px);
    height: clamp(40px, 8vw, 60px);
    margin: 0 auto 20px;
    border: 4px solid transparent;
    border-top: 4px solid #3498db;
    border-right: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-text h3 {
    font-size: clamp(18px, 4vw, 24px);
    margin-bottom: clamp(8px, 2vw, 10px);
    font-weight: 500;
}

.loading-text p {
    font-size: clamp(14px, 3vw, 16px);
    color: #a0aec0;
    margin-bottom: clamp(12px, 3vw, 15px);
}

.progress-bar {
    width: clamp(250px, 90%, 300px);
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

/* Modal & Content */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 1000;
    animation: modalFadeIn 0.3s ease;
}

.modal-content {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: clamp(0.5rem, 2vw, 1rem);
}

.close-modal {
    position: absolute;
    right: clamp(10px, 3vw, 20px);
    top: clamp(10px, 3vw, 20px);
    width: clamp(36px, 5vw, 50px);
    height: clamp(36px, 5vw, 50px);
    font-size: clamp(24px, 4vw, 40px);
    cursor: pointer;
    z-index: 1001;
    color: var(--secondary);
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    border: 2px solid var(--secondary);
}

/* Flipbook Styles */
.flipbook {
    width: min(100vw - 20px, 1000px);
    height: min(calc(100vh - 120px), 600px);
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    margin: 0 auto;
}

.flipbook::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 25%;
    transform: translate(-50%, -50%);
    width: clamp(100px, 20vw, 150px);
    height: clamp(100px, 20vw, 150px);
    background-size: contain;
    opacity: 0.1;
    pointer-events: none;
}

.flipbook .hard {
    background: #060346 !important;
    color: #fff;
    font-weight: bold;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(16px, 3vw, 20px);
}

.flipbook .page {
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* Cover Styles */
.cover-page {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: white;
}

.cover-container {
    padding: clamp(1.5rem, 4vw, 3rem);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.cover-header {
    text-align: center;
    margin-bottom: clamp(1rem, 3vw, 2rem);
}

.university {
    font-size: clamp(0.9rem, 2.5vw, 1rem);
    font-weight: bold;
    margin-bottom: 0.5rem;
    letter-spacing: 1px;
}

.fac-university {
    font-size: clamp(0.6rem, 2vw, 0.7rem);
    text-transform: uppercase;
    letter-spacing: 2px;
    opacity: 0.9;
}

.logo-book {
    width: clamp(60px, 15vw, 80px);
    height: auto;
    margin: 10px auto;
    display: block;
}

.cover-body {
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.document-title {
    font-size: clamp(1.2rem, 3vw, 1.6rem);
    font-weight: bold;
    margin-bottom: clamp(1rem, 3vw, 2rem);
    line-height: 1.2;
    text-transform: capitalize;
}

.cover-footer {
    text-align: center;
    margin-top: clamp(1rem, 3vw, 2rem);
}

/* Navigation */
.navigation {
    position: fixed;
    bottom: clamp(15px, 4vw, 20px);
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: clamp(8px, 2vw, 15px);
    padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px);
    border-radius: 30px;
    background: rgba(44, 62, 80, 0.95);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    animation: navAppear 0.5s ease 0.3s both;
    z-index: 1002;
}

.nav-btn {
    width: clamp(32px, 5vw, 45px);
    height: clamp(32px, 5vw, 45px);
    border-radius: 50%;
    border: none;
    background: #34495e;
    color: #f1c40f;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.nav-btn svg {
    width: clamp(16px, 3vw, 24px);
    height: clamp(16px, 3vw, 24px);
}

.page-info {
    color: white;
    font-size: clamp(12px, 2.5vw, 16px);
    min-width: clamp(25px, 4vw, 30px);
    text-align: center;
    font-weight: 500;
    background: #34495e;
    border-radius: 100%;
    padding: 5px clamp(8px, 2vw, 10px);
}

/* Slide Content */
.slide-content {
    width: 100%;
    height: 100%;
    padding: clamp(1rem, 3vw, 2rem);
    background: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.slide-number {
    font-size: clamp(0.7rem, 2vw, 0.8rem);
    color: #666;
    margin-bottom: clamp(0.5rem, 2vw, 1rem);
}

.slide-text {
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    color: #333;
    line-height: 1.4;
}

/* Additional Responsive Adjustments */
@media (max-width: 480px) {
    .close-modal {
        width: 32px;
        height: 32px;
        font-size: 20px;
        right: 8px;
        top: 8px;
    }

    .navigation {
        padding: 6px 10px;
        gap: 6px;
    }

    .nav-btn {
        width: 28px;
        height: 28px;
    }

    .nav-btn svg {
        width: 14px;
        height: 14px;
    }

    .document-title {
        font-size: 1rem;
    }

    .progress-bar {
        width: 85%;
    }
}

@media (min-width: 769px) {
    .close-modal:hover {
        transform: scale(1.1);
        background: var(--secondary);
        color: white;
    }

    .nav-btn:hover {
        background: #f1c40f;
        color: #34495e;
        transform: scale(1.1);
    }

    .flipbook {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
}

/* Animations */
@keyframes modalFadeIn {
    from { background: rgba(0,0,0,0); }
    to { background: rgba(0,0,0,0.8); }
}

@keyframes navAppear {
    from {
        transform: translate(-50%, 20px);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

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

/* Ajout des styles manquants au code précédent */

/* Document Meta Styles */
.document-meta {
    display: flex;
    flex-direction: column;
    gap: clamp(0.5rem, 2vw, 1rem);
    font-size: clamp(0.8rem, 2.5vw, 1.1rem);
}

.auteur-name {
    font-size: clamp(0.7rem, 2vw, 0.9rem);
    font-weight: 400;
    margin-bottom: 0.2rem;
}

.document-page {
    font-size: clamp(0.5rem, 1.5vw, 0.6rem);
    opacity: 0.8;
}

.doc-info {
    font-size: clamp(10px, 2vw, 12px);
    opacity: 0.8;
}

/* Error States */
.error-page, .error-slide {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: clamp(1rem, 3vw, 2rem);
    text-align: center;
    background: #fff5f5;
    color: #e53e3e;
}

.error-content {
    max-width: 90%;
}

.error-content h3 {
    font-size: clamp(0.9rem, 2.5vw, 1.2rem);
    margin-bottom: clamp(0.5rem, 2vw, 1rem);
}

.error-content p {
    font-size: clamp(0.7rem, 2vw, 0.9rem);
    color: #666;
}

/* End Content */
.end-content {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: clamp(1rem, 3vw, 2rem);
    text-align: center;
    color: white;
}

.end-text {
    font-size: clamp(1rem, 3vw, 1.6rem);
    font-weight: 500;
    margin-bottom: clamp(0.8rem, 2vw, 1rem);
}

/* PDF Page Specific */
.flipbook .page img {
    max-width: 100%;
    height: auto;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}

/* Loading Indicator Responsiveness */
#loadingIndicator {
    width: clamp(280px, 90%, 400px);
    padding: clamp(15px, 4vw, 20px);
}

.spinner {
    font-size: clamp(14px, 3vw, 16px);
    gap: clamp(8px, 2vw, 10px);
}

.spinner::before {
    width: clamp(16px, 3vw, 20px);
    height: clamp(16px, 3vw, 20px);
}

/* Touch Device Optimizations */
@media (hover: none) and (pointer: coarse) {
    .nav-btn {
        min-width: 40px;
        min-height: 40px;
        padding: 10px;
    }

    .close-modal {
        min-width: 44px;
        min-height: 44px;
    }
}

/* Portrait Mobile Adjustments */
@media (max-width: 480px) and (orientation: portrait) {
    .flipbook {
        height: calc(100vh - 160px);
    }

    .navigation {
        bottom: 10px;
    }
}

/* Landscape Mobile Adjustments */
@media (max-height: 480px) and (orientation: landscape) {
    .flipbook {
        height: calc(100vh - 100px);
    }

    .cover-container {
        padding: 1rem;
    }

    .logo-book {
        width: 40px;
    }
}

/* High DPI Screens */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .flipbook .page img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Print Prevention */
@media print {
    .modal, .flipbook, .navigation {
        display: none !important;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .modal,
    .navigation,
    .close-modal,
    .nav-btn,
    .loading-spinner,
    .progress-fill {
        animation: none;
        transition: none;
    }
}

/* Ajout des derniers styles manquants */

/* View Transitions */
.view-transition {
    transition: transform 0.3s ease-in-out;
}

/* Page Content Styles */
.page-content {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    padding: clamp(0.5rem, 2vw, 1rem);
}

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: clamp(1rem, 3vw, 1.5rem);
}

/* Page Numbers */
.page-number {
    position: absolute;
    bottom: clamp(0.5rem, 2vw, 1rem);
    right: clamp(0.5rem, 2vw, 1rem);
    font-size: clamp(0.6rem, 1.5vw, 0.8rem);
    color: #666;
}

/* Double Page Layout */
.double-page {
    display: flex;
    justify-content: space-between;
    gap: clamp(1rem, 3vw, 2rem);
}

.page-left, .page-right {
    flex: 1;
    min-width: 0;
}

/* Image Handling in Pages */
.page-image {
    max-width: 100%;
    height: auto;
    margin: clamp(0.5rem, 2vw, 1rem) auto;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.image-caption {
    text-align: center;
    font-size: clamp(0.7rem, 1.5vw, 0.9rem);
    color: #666;
    margin-top: 0.5rem;
}

/* Page Edge Effect */
.page-edge {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
}

.page-edge-right {
    right: 0;
    transform: scaleX(-1);
}

/* Zoom Controls */
.zoom-controls {
    position: fixed;
    right: clamp(10px, 3vw, 20px);
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 8px;
    z-index: 1002;
}

.zoom-btn {
    width: clamp(32px, 5vw, 40px);
    height: clamp(32px, 5vw, 40px);
    border-radius: 50%;
    background: rgba(44, 62, 80, 0.9);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

/* Touch Gestures */
.touch-hint {
    position: fixed;
    bottom: clamp(70px, 10vh, 100px);
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: clamp(12px, 2.5vw, 14px);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 1003;
}

.touch-hint.visible {
    opacity: 1;
}

/* Accessibility */
.screen-reader-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

/* Loading States */
.page-loading {
    position: relative;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.page-loading::after {
    content: '';
    width: clamp(30px, 5vw, 40px);
    height: clamp(30px, 5vw, 40px);
    border: 3px solid #eee;
    border-top-color: #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Error States Enhancement */
.page-error {
    padding: clamp(1rem, 3vw, 2rem);
    text-align: center;
    background: #fff5f5;
    border: 1px solid #fed7d7;
    border-radius: 8px;
    margin: clamp(0.5rem, 2vw, 1rem);
}

.page-error-icon {
    color: #e53e3e;
    font-size: clamp(24px, 5vw, 32px);
    margin-bottom: clamp(0.5rem, 2vw, 1rem);
}

/* Performance Optimizations */
.hardware-accelerated {
    transform: translateZ(0);
    backface-visibility: hidden;
    perspective: 1000px;
}

/* Mobile-specific Enhancements */
@media (max-width: 480px) {
    .zoom-controls {
        display: none;
    }
    
    .touch-hint {
        padding: 6px 12px;
        font-size: 12px;
    }

    .page-loading {
        min-height: 150px;
    }
}

/* Tablet Optimizations */
@media (min-width: 481px) and (max-width: 768px) {
    .double-page {
        flex-direction: column;
    }
}

/* Orientation Changes */
@media screen and (orientation: landscape) {
    .navigation {
        bottom: 5px;
    }
    
    .touch-hint {
        bottom: 50px;
    }
}

/* Dark Mode Enhancements */
@media (prefers-color-scheme: dark) {
    .page-loading {
        background: #2d3748;
    }
    
    .page-error {
        background: #2a2f45;
        border-color: #e53e3e;
    }
    
    .image-caption {
        color: #a0aec0;
    }
}

/* Keyboard Navigation */
.keyboard-navigation:focus {
    outline: 2px solid #3498db;
    outline-offset: 2px;
}

/* Print Styles Enhancement */
@media print {
    .no-print {
        display: none !important;
    }
    
    .page-break-after {
        page-break-after: always;
    }
}

</style>
@endpush