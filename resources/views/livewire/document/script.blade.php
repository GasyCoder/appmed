@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('js/turn.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@3.12.0/libs/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@3.12.0/dist/pptxgen.min.js"></script>

<script>
// Configuration de PDF.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// Constantes
const VIEWER_CONFIG = {
    WIDTH: 1000,
    HEIGHT: 600,
    DURATION: 800,
    BATCH_SIZE: 5,
    FETCH_TIMEOUT: 30000,
    PDF_SCALE: 1.5
};

const CURRENT_USER = 'Faculté de médecine Majunga';
const CURRENT_DATETIME = '2025-02-02 11:08:18';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

// Vérification du token CSRF
if (!CSRF_TOKEN) {
    console.error('CSRF token not found');
}

/**
 * Fetch avec timeout
 */
async function fetchWithTimeout(url, timeout = VIEWER_CONFIG.FETCH_TIMEOUT) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeout);

    try {
        const response = await fetch(url, { signal: controller.signal });
        clearTimeout(timeoutId);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response;
    } catch (error) {
        clearTimeout(timeoutId);
        throw error;
    }
}

/**
 * Détermine l'extension du fichier
 */
function getFileExtension(data) {
    if (data.originalExtension) {
        return data.originalExtension.toLowerCase();
    }

    if (data.url) {
        const urlMatch = data.url.match(/\.([^.]+)$/);
        if (urlMatch) {
            return urlMatch[1].toLowerCase();
        }
    }

    if (data.title) {
        const titleMatch = data.title.match(/\.([^.]+)$/);
        if (titleMatch) {
            return titleMatch[1].toLowerCase();
        }
    }

    return null;
}

/**
 * Initialise le flipbook avec les options optimisées
 */
function initializeFlipbook(flipbook, options = {}) {
    return flipbook.turn({
        width: VIEWER_CONFIG.WIDTH,
        height: VIEWER_CONFIG.HEIGHT,
        autoCenter: true,
        duration: VIEWER_CONFIG.DURATION,
        acceleration: true,
        gradients: true,
        elevation: 50,
        when: {
            turning: function(event, page, view) {
                $('#currentPage').text(page);
            },
            turned: function(event, page, view) {
                if (page === 1) {
                    flipbook.turn('peel', 'br');
                }
            },
            ...options
        }
    });
}

/**
 * Charge et traite un fichier PowerPoint
 */
 async function loadPowerPoint(data) {
    const flipbook = $('#flipbook');
    const loadingOverlay = document.getElementById('loadingIndicator');
    const progressBar = loadingOverlay.querySelector('.progress-fill');
    const progressText = loadingOverlay.querySelector('.progress-text');

    try {
        loadingOverlay.style.display = 'block';
        progressBar.style.width = '0%';
        progressText.textContent = 'Chargement du PowerPoint...';

        if (flipbook.turn('is')) {
            flipbook.turn('destroy');
        }
        flipbook.empty();

        // Charger le fichier PowerPoint
        const response = await fetchWithTimeout(data.url);
        const arrayBuffer = await response.arrayBuffer();

        // Ajouter la couverture
        flipbook.append(`
            <div class="hard cover-page">
                <div class="cover-container">
                    <div class="cover-header">
                        <div class="university">Université de Mahajanga</div>
                        <div class="fac-university">Faculté de Médecine</div>
                         <img src="{{ asset('assets/image/logo_med.png') }}" alt="Logo" class="logo-book" />
                    </div>
                    <div class="cover-body">
                        <h1 class="document-title">${data.title}</h1>
                    </div>
                    <div class="cover-footer">
                        <div class="auteur-name">${data.teacherName}</div>
                    </div>
                </div>
            </div>
        `);

        // Traiter le PowerPoint
        const zip = await JSZip.loadAsync(arrayBuffer);
        const slideFiles = Object.keys(zip.files)
            .filter(filename => filename.startsWith('ppt/slides/slide'))
            .sort();

        console.log('Nombre de slides trouvées:', slideFiles.length);

        // Charger les slides par lots
        for (let i = 0; i < slideFiles.length; i++) {
            const slideFile = slideFiles[i];
            progressText.textContent = `Traitement de la diapositive ${i + 1}/${slideFiles.length}`;
            progressBar.style.width = `${((i + 1) / slideFiles.length * 80) + 5}%`;

            try {
                // Lire le contenu XML de la diapositive
                const slideContent = await zip.files[slideFile].async('string');

                // Parser le XML
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(slideContent, "application/xml");

                // Extraire le texte des balises <a:t>
                const texts = Array.from(xmlDoc.getElementsByTagName('a:t')).map(node => node.textContent);
                const slideText = texts.join(' '); // Concaténer tous les textes de la diapositive

                // Créer la diapositive dans le flipbook
                const slideContainer = document.createElement('div');
                slideContainer.className = 'page';
                slideContainer.innerHTML = `
                    <div class="slide-content" style="background-color: white; padding: 20px;">
                        <div class="slide-number">Diapositive ${i + 1}</div>
                        <div class="slide-text">${slideText || 'Aucun contenu trouvé'}</div>
                    </div>
                `;
                flipbook.append(slideContainer);

            } catch (slideError) {
                console.error(`Erreur lors du traitement de la diapositive ${i + 1}:`, slideError);
                flipbook.append(`
                    <div class="page error-slide">
                        <div class="error-content">
                            <h3>Erreur de chargement</h3>
                            <p>La diapositive ${i + 1} n'a pas pu être chargée</p>
                        </div>
                    </div>
                `);
            }
        }

        // Pages de fin
        flipbook.append('<div class="hard"></div>');
        flipbook.append(`
            <div class="hard">
                <div class="end-content">
                    <div class="end-text">Fin du document</div>
                    <div class="doc-info">${data.title}</div>
                    <div class="auteur-name">${data.teacherName}</div>
                </div>
            </div>
        `);

        // Initialiser turn.js
        progressText.textContent = 'Finalisation...';
        progressBar.style.width = '100%';

        setTimeout(() => {
            initializeFlipbook(flipbook);
            initializeNavigation(flipbook);

            setTimeout(() => {
                loadingOverlay.style.opacity = '0';
                setTimeout(() => {
                    loadingOverlay.style.display = 'none';
                    loadingOverlay.style.opacity = '1';
                }, 500);
            }, 500);
        }, 500);

    } catch (error) {
        console.error('Erreur PowerPoint:', error);
        progressText.textContent = 'Erreur de chargement !';
        progressBar.style.backgroundColor = '#e74c3c';
        alert('Erreur lors du chargement du PowerPoint: ' + error.message);
        throw error;
    }
}


/**
 * Charge et affiche le PDF
 */
async function loadPDF(data) {
    const flipbook = $('#flipbook');
    const loadingOverlay = document.getElementById('loadingIndicator');
    const progressBar = loadingOverlay.querySelector('.progress-fill');
    const progressText = loadingOverlay.querySelector('.progress-text');

    try {
        loadingOverlay.style.display = 'block';
        progressBar.style.width = '0%';

        if (flipbook.turn('is')) {
            flipbook.turn('destroy');
        }
        flipbook.empty();

        const pdf = await pdfjsLib.getDocument({
            url: data.url,
            cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.12.313/cmaps/',
            cMapPacked: true
        }).promise;

        const totalPages = pdf.numPages;
        console.log('PDF chargé:', totalPages, 'pages');

        // Page de couverture
        progressText.textContent = 'Chargement de la couverture...';
        progressBar.style.width = '20%';

        flipbook.append(`
            <div class="hard cover-page">
                <div class="cover-container">
                    <div class="cover-header">
                        <div class="university">Université de Mahajanga</div>
                        <div class="fac-university">Faculté de Médecine</div>
                        <img src="{{ asset('assets/image/logo_med.png') }}" alt="Logo" class="logo-book" />
                    </div>
                    <div class="cover-body">
                        <h1 class="document-title">${data.title}</h1>
                    </div>
                    <div class="cover-footer">
                        <div class="auteur-name">${data.teacherName}</div>
                        <div class="document-page">page.${totalPages}</div>
                    </div>
                </div>
            </div>
        `);

        // Charger les pages par lots
        for (let i = 1; i <= totalPages; i += VIEWER_CONFIG.BATCH_SIZE) {
            const batch = Array.from(
                { length: Math.min(VIEWER_CONFIG.BATCH_SIZE, totalPages - i + 1) },
                (_, index) => i + index
            );

            await Promise.all(batch.map(async pageNum => {
                progressText.textContent = `Chargement de la page ${pageNum}/${totalPages}`;
                progressBar.style.width = `${(pageNum / totalPages * 80) + 20}%`;

                try {
                    const page = await pdf.getPage(pageNum);
                    const viewport = page.getViewport({ scale: VIEWER_CONFIG.PDF_SCALE });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    await page.render({
                        canvasContext: context,
                        viewport: viewport,
                        enableWebGL: true,
                        renderInteractiveForms: false
                    }).promise;

                    flipbook.append(`
                        <div class="page">
                            <img src="${canvas.toDataURL('image/png', 0.8)}" alt="Page ${pageNum}"/>
                        </div>
                    `);
                } catch (pageError) {
                    console.error(`Erreur page ${pageNum}:`, pageError);
                    flipbook.append(`
                        <div class="page error-page">
                            <div class="error-content">
                                <h3>Erreur de chargement</h3>
                                <p>La page ${pageNum} n'a pas pu être chargée</p>
                            </div>
                        </div>
                    `);
                }
            }));
        }

        // Pages de fin
        flipbook.append('<div class="hard"></div>');
        flipbook.append(`
            <div class="hard">
                <div class="end-content">
                    <div class="end-text">Fin du document</div>
                    <div class="doc-info">${data.title}</div>
                    <div class="auteur-name">${data.teacherName}</div>
                </div>
            </div>
        `);

        progressText.textContent = 'Finalisation...';
        progressBar.style.width = '100%';

        setTimeout(() => {
            initializeFlipbook(flipbook);
            initializeNavigation(flipbook);

            progressText.textContent = 'Terminé !';
            setTimeout(() => {
                loadingOverlay.style.opacity = '0';
                setTimeout(() => {
                    loadingOverlay.style.display = 'none';
                    loadingOverlay.style.opacity = '1';
                }, 500);
            }, 500);
        }, 500);

    } catch (error) {
        console.error('Erreur PDF:', error);
        progressText.textContent = 'Erreur de chargement !';
        progressBar.style.backgroundColor = '#e74c3c';
        throw error;
    }
}

/**
 * Toggle l'indicateur de chargement
 */
 function toggleLoading(show, message = '') {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (message && show) {
        loadingIndicator.querySelector('.progress-text').textContent = message;
    }
    loadingIndicator.style.display = show ? 'block' : 'none';
}

/**
 * Active les protections du document
 */
function enableDocumentProtection(modal) {
    const preventDefaultHandler = (e) => {
        e.preventDefault();
        return false;
    };

    // Désactiver le clic droit
    //modal.addEventListener('contextmenu', preventDefaultHandler);

    // Désactiver la sélection de texte
    modal.addEventListener('selectstart', preventDefaultHandler);

    // Désactiver le glisser-déposer
    modal.addEventListener('dragstart', preventDefaultHandler);

    // Désactiver copier/coller
    modal.addEventListener('copy', preventDefaultHandler);
    modal.addEventListener('cut', preventDefaultHandler);
    modal.addEventListener('paste', preventDefaultHandler);

    // Désactiver les raccourcis clavier
    document.addEventListener('keydown', function(e) {
        if (modal.style.display === 'block') {
            if ((e.ctrlKey && ['s', 'p', 'c', 'u'].includes(e.key)) ||
                (e.key === 'PrintScreen')) {
                preventDefaultHandler(e);
            }
        }
    });
}

/**
 * Initialise la navigation avancée
 */
function initializeNavigation(flipbook) {
    // Boutons de navigation
    $('#firstPage').off('click').on('click', () => flipbook.turn('page', 1));
    $('#lastPage').off('click').on('click', () => flipbook.turn('page', flipbook.turn('pages')));
    $('#prev').off('click').on('click', () => flipbook.turn('previous'));
    $('#next').off('click').on('click', () => flipbook.turn('next'));

    // Navigation clavier améliorée
    $(document).off('keydown').on('keydown', function(e) {
        if (modal.style.display === 'block') {
            switch(e.key) {
                case 'ArrowLeft':
                case 'ArrowUp':
                case 'PageUp':
                    flipbook.turn('previous');
                    e.preventDefault();
                    break;
                case 'ArrowRight':
                case 'ArrowDown':
                case 'PageDown':
                    flipbook.turn('next');
                    e.preventDefault();
                    break;
                case 'Home':
                    flipbook.turn('page', 1);
                    e.preventDefault();
                    break;
                case 'End':
                    flipbook.turn('page', flipbook.turn('pages'));
                    e.preventDefault();
                    break;
                case 'Escape':
                    closeModal();
                    e.preventDefault();
                    break;
            }
        }
    });

    // Navigation tactile
    let touchStartX = 0;
    flipbook.on('touchstart', function(e) {
        touchStartX = e.originalEvent.touches[0].pageX;
    });

    flipbook.on('touchmove', function(e) {
        if (e.originalEvent.touches.length > 0) {
            const touchEndX = e.originalEvent.touches[0].pageX;
            const diff = touchEndX - touchStartX;

            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    flipbook.turn('previous');
                } else {
                    flipbook.turn('next');
                }
                touchStartX = touchEndX;
            }
        }
    });
}

/**
 * Charge le document selon son type
 */
async function loadDocument(data) {
    try {
        toggleLoading(true, 'Analyse du document...');
        console.log('Données du document:', data);

        const fileExtension = getFileExtension(data);
        console.log('Extension détectée:', fileExtension);

        if (!fileExtension) {
            throw new Error('Extension de fichier non détectée');
        }

        const response = await fetchWithTimeout(data.url);
        if (!response.ok) {
            throw new Error('Document inaccessible');
        }

        if (fileExtension === 'pdf' || data.url.endsWith('.pdf')) {
            await loadPDF(data);
        } else if (['ppt', 'pptx'].includes(fileExtension)) {
            await loadPowerPoint(data);
        } else {
            throw new Error(`Format de fichier non supporté: ${fileExtension}`);
        }
    } catch (error) {
        console.error('Erreur de chargement:', error);
        alert(`Erreur: ${error.message}`);
    } finally {
        toggleLoading(false);
    }
}

/**
 * Ouvre le visualiseur de document
 */
async function openPdfViewer(data) {
    try {
        if (!data?.url) {
            throw new Error('Données du document invalides');
        }

        console.log('Ouverture du document:', data);

        const modal = document.getElementById('pdfModal');
        modal.style.display = 'block';
        enableDocumentProtection(modal);

        await loadDocument(data);

        // Incrémentation des vues
        try {
            const incrementSuccess = await incrementViewCount(data.id);
            if (!incrementSuccess) {
                console.warn('L\'incrémentation des vues a échoué');
            }
        } catch (error) {
            console.error('Erreur d\'incrémentation:', error);
        }

    } catch (error) {
        console.error('Erreur d\'ouverture:', error);
        alert(`Impossible d'ouvrir le document: ${error.message}`);
        document.getElementById('pdfModal').style.display = 'none';
    }
}

/**
 * Incrémente le compteur de vues
 */
async function incrementViewCount(documentId) {
    try {
        const response = await fetchWithTimeout(`/document/${documentId}/increment-view`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            const viewsElement = document.getElementById(`views-${documentId}`);
            if (viewsElement) {
                viewsElement.textContent = data.viewCount;
            }
            return true;
        }
        return false;
    } catch (error) {
        console.error('Erreur d\'incrémentation:', error);
        return false;
    }
}

// Initialisation globale
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('pdfModal');
    const closeBtn = document.querySelector('.close-modal');

    function closeModal() {
        modal.style.display = 'none';
        const flipbook = $('#flipbook');
        if (flipbook.turn('is')) {
            flipbook.turn('destroy');
        }
        flipbook.empty();
        toggleLoading(false);
    }

    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    // Désactiver l'impression
    Object.defineProperty(window, 'print', {
        value: () => {
            console.log('Impression désactivée');
            return false;
        }
    });

    // Gérer la fermeture avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
});
</script>
@endpush
