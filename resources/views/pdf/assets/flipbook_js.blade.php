@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
<script src="{{ asset('js/turn.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@3.12.0/libs/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@3.12.0/dist/pptxgen.min.js"></script>

<script>
// Configuration de PDF.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let pdfDoc = null;
let currentScale = 0.8; // Réduit de 2.0 à 1.2
let isFullscreen = false;
let pageSound;

// Fonction pour gérer le zoom
function handleZoom(delta) {
    // Limites de zoom différentes selon le type d'appareil
    const isMobile = window.innerWidth <= 768;
    const minZoom = isMobile ? 0.4 : 0.5;
    const maxZoom = isMobile ? 1.0 : 1.2;

    // Ajuster les limites de zoom pour maintenir la lisibilité
    currentScale = Math.max(minZoom, Math.min(maxZoom, currentScale + delta));
    updateZoomDisplay();

    // Déterminer quelles pages sont visibles selon le mode d'affichage
    const currentPage = $('#flipbook').turn('page');
    const pagesToRender = isMobile ?
        [currentPage] :
        [currentPage, currentPage + 1];

    renderPages(pagesToRender);
}

function initializeSound() {
    pageSound = document.getElementById('turnPageSound');
    pageSound.volume = 1.0; // Ajustez le volume (0.0 à 1.0)
}

function getFileExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}


// Fonction pour le mode plein écran
function toggleFullscreen() {
    const container = document.querySelector('.viewer-container');
    const flipbook = $('#flipbook');

    // Sauvegarde de la page actuelle avant changement
    const currentPage = flipbook.turn('page');

    if (!isFullscreen) {
        if (container.requestFullscreen) {
            container.requestFullscreen();
        } else if (container.webkitRequestFullscreen) {
            container.webkitRequestFullscreen();
        } else if (container.msRequestFullscreen) {
            container.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    // Réajuster la taille après changement de mode plein écran
    setTimeout(() => {
        adjustFlipbookSize();
        flipbook.turn('page', currentPage);
    }, 150);
}

// Écouteurs d'événements pour le plein écran
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('mozfullscreenchange', handleFullscreenChange);
document.addEventListener('MSFullscreenChange', handleFullscreenChange);

function handleFullscreenChange() {
    isFullscreen = !!document.fullscreenElement;

    $('.fullscreen-icon').html(isFullscreen ?
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"></path></svg>' :
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5z"></path></svg>'
    );

    // Ajuster les dimensions après changement d'état plein écran
    setTimeout(adjustFlipbookSize, 100);
}


function adjustFlipbookSize() {
    const flipbook = $('#flipbook');
    const container = $('#flipbook-container');
    const containerWidth = container.width();
    const containerHeight = container.height();

    // Détecter si nous sommes sur mobile
    const isMobile = window.innerWidth <= 768;

    // Calculer les dimensions optimales en fonction de l'appareil
    let pageWidth, pageHeight;

    if (isMobile) {
        // Sur mobile, on utilise la largeur complète avec une marge minimale
        pageWidth = Math.min(containerWidth * 0.95, 600);
        pageHeight = Math.min(containerHeight * 0.90, 800);
    } else {
        // Sur desktop, dimensions plus grandes
        pageWidth = Math.min(containerWidth * 0.90, 1100);
        pageHeight = Math.min(containerHeight * 0.95, 1000);
    }

    // Appliquer les nouvelles dimensions
    flipbook.turn('size', pageWidth, pageHeight);

    // Ajuster le viewport pour le mode actuel
    flipbook.turn('display', isMobile ? 'single' : 'double');

    // Forcer le rendu des pages actuelles
    const currentPage = flipbook.turn('page');
    const pagesToRender = isMobile ?
        [currentPage] :
        [currentPage, currentPage + 1];

    renderPages(pagesToRender);
}

// Fonction pour mettre à jour l'affichage du zoom
function updateZoomDisplay() {
    $('.zoom-level').text(`${Math.round(currentScale * 100)}%`);
}

async function initPDF() {
    const loading = document.getElementById('loading');
    loading.style.display = 'flex';

    try {
        console.log('Début du chargement du PDF');
        const fileExtension = getFileExtension('{{ $filename }}');
        if (fileExtension === 'pptx' || fileExtension === 'ppt') {
            await loadPowerPoint('{{ $filename }}');
            return;
        }

        const response = await fetch(`{{ route('pdf.content', ['filename' => $filename]) }}`);

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();
        console.log('PDF récupéré, taille:', data.size);

        const pdfData = atob(data.data);
        const pdfArray = new Uint8Array(pdfData.length);
        for (let i = 0; i < pdfData.length; i++) {
            pdfArray[i] = pdfData.charCodeAt(i);
        }

        // Chargement du PDF avec support des caractères non-ASCII
        pdfDoc = await pdfjsLib.getDocument({
            data: pdfArray,
            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
            cMapPacked: true
        }).promise;

        console.log('PDF chargé, nombre de pages:', pdfDoc.numPages);
        await initFlipbook(pdfDoc.numPages);
        loading.style.display = 'none';

    } catch (error) {
        console.error('Erreur lors du chargement:', error);
        showLoadingError(error.message || 'Impossible de charger le document PDF');
    }
}

// Fonction pour charger un PowerPoint
async function loadPowerPoint(filename) {
    const loading = document.getElementById('loading');

    try {
        loading.style.display = 'flex';
        console.log('Début du chargement du PowerPoint');

        const response = await fetch(`{{ route('pdf.content', ['filename' => $filename]) }}`);
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();
        if (!data.data) {
            throw new Error('Aucune donnée reçue du serveur');
        }

        console.log('PowerPoint récupéré, décodage...');

        // Décodage base64
        const pptxData = atob(data.data);
        const pptxArray = new Uint8Array(pptxData.length);
        for (let i = 0; i < pptxData.length; i++) {
            pptxArray[i] = pptxData.charCodeAt(i);
        }

        // Création d'un blob pour JSZip
        const blob = new Blob([pptxArray], {
            type: 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        });

        console.log('Chargement du fichier ZIP...');
        // Charger le fichier avec JSZip
        const zipFile = await JSZip.loadAsync(blob);

        // Vérifier que le ZIP contient bien les slides
        const slidesPaths = Object.keys(zipFile.files)
            .filter(name => name.match(/^ppt\/slides\/slide[0-9]+\.xml$/));

        if (slidesPaths.length === 0) {
            throw new Error('Aucune slide trouvée dans le PowerPoint');
        }

        console.log(`${slidesPaths.length} slides trouvées`);

        // Initialiser le flipbook avec les slides
        await initFlipbook(slidesPaths.length, zipFile);

    } catch (error) {
        console.error('Erreur de chargement PowerPoint:', error);
        showLoadingError(error.message || 'Erreur lors du chargement du PowerPoint');
    } finally {
        loading.style.display = 'none';
    }
}

async function countSlides(zip) {
    try {
        const slidesCount = Object.keys(zip.files)
            .filter(name => name.startsWith('ppt/slides/slide'))
            .length;

        return slidesCount || 0;
    } catch (error) {
        console.error('Erreur de comptage des slides:', error);
        return 0;
    }
}


async function initFlipbook(numPages, zipSource = null) {
    console.log('Initialisation du flipbook avec', numPages, 'pages');
    const flipbook = $('#flipbook');

    // Vider le flipbook existant
    flipbook.html('');

    // Détecter si nous sommes sur mobile
    const isMobile = window.innerWidth <= 768;

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
                    <h1 class="document-title">{{ $document->title ?? $filename }}</h1>
                </div>
                <div class="teacher-info">
                    @if(isset($teacherInfo['grade']))
                        <div class="teacher-grade">{{ $teacherInfo['grade'] }}</div>
                    @endif
                    @if(isset($teacherInfo['name']))
                        <div class="teacher-name">{{ $teacherInfo['name'] }}</div>
                    @endif
                </div>
                <div class="cover-footer">
                    <div class="page-count">${numPages} diapositives</div>
                </div>
            </div>
        </div>
    `);

    // Ajouter les pages selon le type de contenu
    const isPowerPoint = zipSource !== null;

    if (isPowerPoint) {
        console.log('Ajout des pages pour PowerPoint...');
        // Pour PowerPoint
        for (let i = 1; i <= numPages; i++) {
            flipbook.append(`
                <div class="page" id="page-${i}">
                    <div id="slide-content-${i}" class="slide-content">
                        <div class="slide-loading">Chargement de la diapositive ${i}...</div>
                    </div>
                    <div class="page-number">Diapositive ${i}</div>
                </div>
            `);
        }
    } else {
        // Pour PDF
        for (let i = 1; i <= numPages; i++) {
            flipbook.append(`
                <div class="page" id="page-${i}">
                    <canvas id="canvas-${i}"></canvas>
                    <div class="page-number">${i}</div>
                </div>
            `);
        }
    }

    // Ajouter la quatrième de couverture
    flipbook.append(`
        <div class="hard">
            <div class="end-content">
                <div class="end-header">
                    <div class="university">Université de Mahajanga</div>
                    <div class="fac-university">Faculté de Médecine</div>
                </div>
                <div class="end-body">
                    <div class="end-text">Fin du document</div>
                    <h2 class="doc-title">{{ $document->title ?? $filename }}</h2>
                    @if(isset($teacherInfo['grade']) || isset($teacherInfo['name']))
                        <div class="teacher-info">
                            @if(isset($teacherInfo['grade']))
                                <div class="teacher-grade">{{ $teacherInfo['grade'] }}</div>
                            @endif
                            @if(isset($teacherInfo['name']))
                                <div class="teacher-name">{{ $teacherInfo['name'] }}</div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="end-footer">
                    <div class="page-info">${numPages} ${isPowerPoint ? 'diapositives' : 'pages'}</div>
                </div>
            </div>
        </div>
    `);

    // Obtenir les dimensions du container
    const container = $('#flipbook-container');
    const containerWidth = container.width();
    const containerHeight = container.height();

    // Calculer les dimensions optimales en fonction de l'appareil
    let pageWidth, pageHeight;

    if (isMobile) {
        // Sur mobile, on utilise la largeur complète avec une marge minimale
        pageWidth = Math.min(containerWidth * 0.95, 600);
        pageHeight = Math.min(containerHeight * 0.90, 800);
    } else {
        // Sur desktop, dimensions plus grandes
        pageWidth = Math.min(containerWidth * 0.90, 1100);
        pageHeight = Math.min(containerHeight * 0.95, 1000);
    }

    console.log('Dimensions du flipbook:', pageWidth, 'x', pageHeight);

    // Initialiser Turn.js avec mode approprié pour l'appareil
    flipbook.turn({
        width: pageWidth,
        height: pageHeight,
        autoCenter: true,
        acceleration: !isMobile, // Désactiver l'accélération sur mobile pour plus de fluidité
        gradients: !isMobile, // Désactiver les gradients sur mobile pour les performances
        elevation: isMobile ? 30 : 50, // Réduire l'élévation sur mobile
        display: isMobile ? 'single' : 'double', // Single sur mobile, double sur desktop
        when: {
            turning: function(event, page, view) {
                if (isPowerPoint) {
                    renderSlides(zipSource, view);
                } else {
                    renderPages(view);
                }

                // Jouer le son seulement si n'est pas en mode silencieux
                if (pageSound && pageSound.readyState >= 2 && !pageSound.muted) {
                    pageSound.currentTime = 0;
                    pageSound.play().catch(error => console.log("Erreur son:", error));
                }

                // Mettre à jour la pagination
                $('#current-page').text(page);
                $('#total-pages').text(numPages);
            },
            turned: function(event, page) {
                // Utiliser le mode d'affichage actuel pour déterminer quelles pages rendre
                const currentDisplay = flipbook.turn('display');
                const pagesToRender = currentDisplay === 'single' ?
                    [page] :
                    [page, page + 1];

                if (isPowerPoint) {
                    renderSlides(zipSource, pagesToRender);
                } else {
                    renderPages(pagesToRender);
                }
            }
        }
    });

    // Rendre les premières pages
    if (isPowerPoint) {
        await renderSlides(zipSource, isMobile ? [1] : [1, 2]);
    } else {
        await renderPages(isMobile ? [1] : [1, 2]);
    }

    // Ajouter gestionnaire d'événement pour le redimensionnement
    setupResizeHandler();
}

// 6. Gestionnaire d'événement pour le redimensionnement avec debounce
function setupResizeHandler() {
    $(window).off('resize.flipbook'); // Nettoyer les anciens gestionnaires

    $(window).on('resize.flipbook', _.debounce(function() {
        const isMobileNow = window.innerWidth <= 768;
        const wasMobile = $('#flipbook').turn('display') === 'single';

        // Si passage du mode desktop <-> mobile
        if (isMobileNow !== wasMobile) {
            $('#flipbook').turn('display', isMobileNow ? 'single' : 'double');
        }

        // Dans tous les cas, réajuster les dimensions
        adjustFlipbookSize();
    }, 250));
}


// Fonction pour rendre les slides PowerPoint
async function renderSlides(zipSource, slideNumbers) {
    if (!zipSource) {
        console.error('Source ZIP non définie');
        return;
    }

    // Filtrer les numéros de slide invalides
    slideNumbers = slideNumbers.filter(slideNum => slideNum && slideNum >= 1);

    if (slideNumbers.length === 0) return;

    const isMobile = window.innerWidth <= 768;

    for (let slideNum of slideNumbers) {
        const slideContainer = document.getElementById(`slide-content-${slideNum}`);
        if (!slideContainer) continue;

        try {
            const slideId = `slide${slideNum}.xml`;
            const slidePath = `ppt/slides/${slideId}`;

            if (!zipSource.files[slidePath]) {
                slideContainer.innerHTML = `<div class="slide-error">Diapositive ${slideNum} introuvable</div>`;
                continue;
            }

            const slideXml = await zipSource.files[slidePath].async('string');
            const parser = new DOMParser();
            const slideDoc = parser.parseFromString(slideXml, "application/xml");

            // Extraction simplifiée des textes
            const textNodes = slideDoc.getElementsByTagName('a:t');
            const paragraphs = slideDoc.getElementsByTagName('a:p');
            const images = slideDoc.getElementsByTagName('p:pic');

            // Générer le contenu HTML
            let slideHTML = '';
            let hasTitle = false;

            // Extraire les textes en préservant la structure des paragraphes
            const paragraphTexts = [];
            for (let i = 0; i < paragraphs.length; i++) {
                const textElements = paragraphs[i].getElementsByTagName('a:t');
                if (textElements.length === 0) continue;

                let paragraphText = '';
                for (let j = 0; j < textElements.length; j++) {
                    paragraphText += textElements[j].textContent;
                }

                if (paragraphText.trim()) {
                    paragraphTexts.push(paragraphText.trim());
                }
            }

            // Adapté pour mobile: tailles de texte relatives
            if (paragraphTexts.length > 0) {
                const firstText = paragraphTexts[0];
                // Sur mobile: limite plus stricte pour le titre
                const titleLengthLimit = isMobile ? 40 : 50;

                if (firstText.length > titleLengthLimit) {
                    slideHTML += `<div class="large-title">${firstText}</div>`;
                } else {
                    slideHTML += `<h2 class="slide-title">${firstText}</h2>`;
                }
                hasTitle = true;

                // Ajouter les autres paragraphes
                for (let i = 1; i < paragraphTexts.length; i++) {
                    // Adapté pour mobile: sous-titres plus courts
                    const subheadingLimit = isMobile ? 25 : 30;

                    if (paragraphTexts[i].length < subheadingLimit && paragraphTexts[i].endsWith(':')) {
                        // Probablement un sous-titre
                        slideHTML += `<h3 class="slide-heading">${paragraphTexts[i]}</h3>`;
                    } else {
                        // Paragraphe normal ou élément de liste
                        if (paragraphTexts[i].startsWith('•') || paragraphTexts[i].startsWith('-')) {
                            slideHTML += `<div class="slide-list-item">${paragraphTexts[i]}</div>`;
                        } else {
                            slideHTML += `<div class="slide-paragraph">${paragraphTexts[i]}</div>`;
                        }
                    }
                }
            } else if (textNodes.length > 0) {
                // Fallback: utiliser tous les nœuds de texte s'il n'y a pas de structure claire
                for (let i = 0; i < textNodes.length; i++) {
                    const text = textNodes[i].textContent.trim();
                    if (!text) continue;

                    if (!hasTitle) {
                        slideHTML += `<h2 class="slide-title">${text}</h2>`;
                        hasTitle = true;
                    } else {
                        slideHTML += `<div class="slide-paragraph">${text}</div>`;
                    }
                }
            }

            // Ajouter des placeholders pour les images - taille adaptée selon appareil
            if (images.length > 0) {
                // Sur mobile, dimensionner les images pour éviter les débordements
                const imageWidth = isMobile ? '85%' : '80%';
                const maxWidth = isMobile ? '220px' : '300px';

                for (let i = 0; i < images.length; i++) {
                    slideHTML += `
                        <div class="slide-image-placeholder" style="width: ${imageWidth}; max-width: ${maxWidth};">
                            <svg xmlns="http://www.w3.org/2000/svg" class="image-icon" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" stroke="currentColor" stroke-width="2" d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z M12 17l-5-5h10z M17 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                            <div class="image-text">Image</div>
                        </div>
                    `;
                }
            }

            // Afficher le contenu ou un message si vide - optimisé pour mobile
            if (slideHTML) {
                slideContainer.innerHTML = `
                    <div class="slide-header">Diapositive ${slideNum}</div>
                    <div class="slide-body">${slideHTML}</div>
                `;
            } else {
                slideContainer.innerHTML = `
                    <div class="slide-header">Diapositive ${slideNum}</div>
                    <div class="slide-empty">Cette diapositive ne contient pas de contenu textuel</div>
                `;
            }

        } catch (error) {
            console.error(`Erreur de rendu slide ${slideNum}:`, error);
            slideContainer.innerHTML = `
                <div class="slide-error">
                    <h3>Erreur de chargement</h3>
                    <p>Impossible d'afficher la diapositive ${slideNum}</p>
                </div>
            `;
        }
    }
}




// Fonction helper extraire les éléments de slide
function extractSlideElements(xmlDoc, slideNumber, slideRelations) {
    // Trouver les éléments textuels et images
    const slideElements = [];

    // Fonction d'aide pour obtenir un élément enfant par son nom de balise
    function getChildElementByTagName(element, tagName) {
        if (!element) return null;
        const children = element.childNodes;
        for (let i = 0; i < children.length; i++) {
            if (children[i].nodeName === tagName) {
                return children[i];
            }
        }
        return null;
    }

    // Traiter les formes (shapes) - contiennent du texte
    const shapes = xmlDoc.getElementsByTagName('p:sp');
    for (let i = 0; i < shapes.length; i++) {
        const shape = shapes[i];

        // Obtenir les informations sur la forme
        const nvSpPr = getChildElementByTagName(shape, 'p:nvSpPr');
        const spPr = getChildElementByTagName(shape, 'p:spPr');

        let shapeType = '';
        let position = { x: 0, y: 0, width: 10, height: 7 };

        // Obtenir le type/nom de la forme
        if (nvSpPr) {
            const cNvPr = getChildElementByTagName(nvSpPr, 'p:cNvPr');
            if (cNvPr && cNvPr.getAttribute) {
                shapeType = cNvPr.getAttribute('name') || '';
            }
        }

        // Obtenir la position
        if (spPr) {
            const xfrm = getChildElementByTagName(spPr, 'a:xfrm');
            if (xfrm) {
                const off = getChildElementByTagName(xfrm, 'a:off');
                const ext = getChildElementByTagName(xfrm, 'a:ext');

                if (off && off.getAttribute) {
                    position.x = parseInt(off.getAttribute('x') || '0') / 914400;
                    position.y = parseInt(off.getAttribute('y') || '0') / 914400;
                }

                if (ext && ext.getAttribute) {
                    position.width = parseInt(ext.getAttribute('cx') || '9144000') / 914400;
                    position.height = parseInt(ext.getAttribute('cy') || '6858000') / 914400;
                }
            }
        }

        // Extraire le texte dans cette forme
        const textContent = [];
        const txBody = getChildElementByTagName(shape, 'p:txBody');

        if (txBody) {
            const paragraphs = txBody.getElementsByTagName('a:p');

            for (let j = 0; j < paragraphs.length; j++) {
                const para = paragraphs[j];
                const pPr = para.getElementsByTagName('a:pPr')[0];

                let level = 0;
                let fontSize = 1800;
                let isBold = false;

                if (pPr) {
                    // Obtenir le niveau du paragraphe
                    if (pPr.getAttribute) {
                        level = parseInt(pPr.getAttribute('lvl') || '0');
                    }

                    // Obtenir le style du texte
                    const defRPr = pPr.getElementsByTagName('a:defRPr')[0];
                    if (defRPr) {
                        if (defRPr.getAttribute) {
                            fontSize = parseInt(defRPr.getAttribute('sz') || '1800');
                            isBold = defRPr.getAttribute('b') === '1';
                        }
                    }
                }

                // Collecter le texte
                const textRuns = para.getElementsByTagName('a:r');
                let paraText = '';

                if (textRuns.length > 0) {
                    for (let k = 0; k < textRuns.length; k++) {
                        const textElements = textRuns[k].getElementsByTagName('a:t');
                        for (let l = 0; l < textElements.length; l++) {
                            paraText += textElements[l].textContent + ' ';
                        }
                    }
                } else {
                    // Pour les paragraphes sans runs explicites
                    const textElements = para.getElementsByTagName('a:t');
                    for (let l = 0; l < textElements.length; l++) {
                        paraText += textElements[l].textContent + ' ';
                    }
                }

                paraText = paraText.trim();

                if (paraText) {
                    const isTitle = level === 0 || fontSize > 2400 || isBold;
                    textContent.push({
                        text: paraText,
                        isTitle: isTitle,
                        level: level
                    });
                }
            }
        }

        if (textContent.length > 0) {
            slideElements.push({
                type: 'text',
                position: position,
                content: textContent,
                isTitle: shapeType.toLowerCase().includes('title')
            });
        }
    }

    // Traiter les images
    const pictures = xmlDoc.getElementsByTagName('p:pic');

    for (let i = 0; i < pictures.length; i++) {
        const pic = pictures[i];
        let position = { x: 0, y: 0, width: 10, height: 7 };
        let relationId = '';

        // Obtenir l'ID de la relation
        const blipFill = getChildElementByTagName(pic, 'p:blipFill');
        if (blipFill) {
            const blip = getChildElementByTagName(blipFill, 'a:blip');
            if (blip && blip.getAttribute) {
                relationId = blip.getAttribute('r:embed') || '';
            }
        }

        // Obtenir la position
        const spPr = getChildElementByTagName(pic, 'p:spPr');
        if (spPr) {
            const xfrm = getChildElementByTagName(spPr, 'a:xfrm');
            if (xfrm) {
                const off = getChildElementByTagName(xfrm, 'a:off');
                const ext = getChildElementByTagName(xfrm, 'a:ext');

                if (off && off.getAttribute) {
                    position.x = parseInt(off.getAttribute('x') || '0') / 914400;
                    position.y = parseInt(off.getAttribute('y') || '0') / 914400;
                }

                if (ext && ext.getAttribute) {
                    position.width = parseInt(ext.getAttribute('cx') || '9144000') / 914400;
                    position.height = parseInt(ext.getAttribute('cy') || '6858000') / 914400;
                }
            }
        }

        slideElements.push({
            type: 'image',
            position: position,
            relationId: relationId
        });
    }

    return slideElements;
}



async function renderPages(pageNumbers) {
    // Filtrer les numéros de page invalides
    pageNumbers = pageNumbers.filter(pageNum =>
        pageNum && pageNum >= 1 && pageNum <= pdfDoc.numPages
    );

    if (pageNumbers.length === 0) return;

    // Déterminer les paramètres de rendu selon la taille de l'écran
    const isMobile = window.innerWidth <= 768;
    const isSmallPhone = window.innerWidth <= 380;

    // Ajuster l'échelle de rendu pour s'assurer que le contenu est visible
    let renderScale = currentScale;
    if (isMobile) {
        // Sur mobile, utiliser une échelle réduite pour voir plus de contenu
        renderScale = isSmallPhone ? currentScale * 0.85 : currentScale * 0.9;
    }

    // Optimiser la densité de pixels selon l'appareil
    const devicePixelRatio = window.devicePixelRatio || 1;
    const outputScale = Math.min(
        devicePixelRatio,
        isMobile ? 1.5 : 1.25 // Densité plus élevée sur mobile pour la netteté
    );

    for (let pageNum of pageNumbers) {
        try {
            const page = await pdfDoc.getPage(pageNum);
            const canvas = document.getElementById(`canvas-${pageNum}`);

            if (!canvas) continue;

            // Ajuster l'échelle pour afficher tout le contenu
            const viewport = page.getViewport({
                scale: renderScale
            });

            const context = canvas.getContext('2d');

            // Ajuster la taille du canvas
            canvas.width = Math.floor(viewport.width * outputScale);
            canvas.height = Math.floor(viewport.height * outputScale);

            // Ajuster la taille d'affichage
            canvas.style.width = Math.floor(viewport.width) + "px";
            canvas.style.height = Math.floor(viewport.height) + "px";

            context.scale(outputScale, outputScale);

            // Options de rendu optimisées pour mobile
            const renderOptions = {
                canvasContext: context,
                viewport: viewport,
                enableWebGL: !isMobile, // Désactiver WebGL sur mobile pour la stabilité
                renderInteractiveForms: false,
                textLayer: false, // Désactiver pour de meilleures performances
                canvasFactory: undefined,
                intent: isMobile ? 'display' : 'print', // Priorité à l'affichage sur mobile
            };

            await page.render(renderOptions).promise;

            // Sur mobile, ajouter une classe pour ajuster le style
            if (isMobile) {
                canvas.classList.add('mobile-canvas');
            } else {
                canvas.classList.remove('mobile-canvas');
            }

        } catch (error) {
            console.error(`Erreur lors du rendu de la page ${pageNum}:`, error);

            // Afficher un message d'erreur à l'utilisateur
            const pageElement = document.getElementById(`page-${pageNum}`);
            if (pageElement) {
                const errorMsg = document.createElement('div');
                errorMsg.className = 'page-error-message';
                errorMsg.textContent = 'Impossible d\'afficher cette page';
                pageElement.appendChild(errorMsg);
            }
        }
    }
}


// Fonction pour ajuster l'échelle de zoom selon l'appareil
function getOptimalZoomLevel() {
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;
    const isLandscape = screenWidth > screenHeight;

    // Échelle de base
    let optimalScale = 0.8;

    // Ajustements selon la taille et l'orientation
    if (screenWidth <= 380) {
        // Très petits écrans (iPhone SE, etc.)
        optimalScale = isLandscape ? 0.6 : 0.5;
    } else if (screenWidth <= 767) {
        // Smartphones standards
        optimalScale = isLandscape ? 0.7 : 0.6;
    } else if (screenWidth <= 1024) {
        // Tablettes
        optimalScale = isLandscape ? 0.9 : 0.8;
    }

    return optimalScale;
}


// Fonction pour gérer le changement d'orientation
function handleOrientationChange() {
    // Attendre que l'orientation soit complètement changée
    setTimeout(() => {
        // Réinitialiser l'échelle optimale
        currentScale = getOptimalZoomLevel();
        updateZoomDisplay();

        // Ajuster les dimensions du flipbook
        adjustFlipbookSize();

        // Recharger les pages visibles
        const currentPage = $('#flipbook').turn('page');
        const isMobile = window.innerWidth <= 768;
        const pagesToRender = isMobile ?
            [currentPage] :
            [currentPage, currentPage + 1];

        renderPages(pagesToRender);
    }, 300); // Délai pour attendre la fin de l'animation de rotation
}


// Fonction pour ajuster le flipbook après le chargement initial
function optimizeInitialDisplay() {
    // Définir l'échelle optimale pour cet appareil
    currentScale = getOptimalZoomLevel();
    updateZoomDisplay();

    // Forcer le rendu de la page courante avec la nouvelle échelle
    const currentPage = $('#flipbook').turn('page');
    const isMobile = window.innerWidth <= 768;
    const pagesToRender = isMobile ?
        [currentPage] :
        [currentPage, currentPage + 1];

    renderPages(pagesToRender);
}


// Initialisation améliorée à ajouter au ready
function initMobileOptimizations() {
    // Détecter les appareils tactiles
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    if (isTouchDevice) {
        document.body.classList.add('touch-device');

        // Écouteur d'événement pour le changement d'orientation
        window.addEventListener('orientationchange', handleOrientationChange);

        // Ajuster les contrôles pour une meilleure expérience tactile
        enhanceTouchControls();
    }

    // Définir l'échelle initiale optimale
    currentScale = getOptimalZoomLevel();

    // Observer les changements de visibilité pour optimiser le rendu
    if ('visibilityState' in document) {
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                // Recharger les pages visibles lors du retour à l'application
                const currentPage = $('#flipbook').turn('page');
                const isMobile = window.innerWidth <= 768;
                renderPages(isMobile ? [currentPage] : [currentPage, currentPage + 1]);
            }
        });
    }
}


// Améliorer les contrôles tactiles
function enhanceTouchControls() {
    const flipbookElement = document.getElementById('flipbook');

    // Variables pour la gestion du toucher
    let startX = 0;
    let startY = 0;
    let startTime = 0;
    let movedEnough = false;

    // Gestionnaire pour le début du toucher
    flipbookElement.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        startTime = new Date().getTime();
        movedEnough = false;
    }, {passive: true});

    // Gestionnaire pour le mouvement
    flipbookElement.addEventListener('touchmove', function(e) {
        const diffX = Math.abs(e.touches[0].clientX - startX);
        const diffY = Math.abs(e.touches[0].clientY - startY);

        // Marquer comme déplacé si le mouvement horizontal est significatif
        if (diffX > 20 && diffX > diffY * 2) {
            movedEnough = true;

            // Prévenir le défilement vertical pendant le balayage horizontal
            e.preventDefault();
        }
    }, {passive: false});

    // Gestionnaire pour la fin du toucher
    flipbookElement.addEventListener('touchend', function(e) {
        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;
        const endTime = new Date().getTime();
        const diffX = endX - startX;
        const diffY = Math.abs(endY - startY);
        const elapsedTime = endTime - startTime;

        // Vérifier si c'est un balayage horizontal valide
        // - Mouvement horizontal suffisant
        // - Mouvement vertical limité
        // - Temps écoulé raisonnable
        if (movedEnough && Math.abs(diffX) > 50 && diffY < 100 && elapsedTime < 500) {
            // Prévenir les actions de l'interface utilisateur par défaut
            e.preventDefault();

            if (diffX > 0) {
                // Balayage de gauche à droite (précédent)
                $('#flipbook').turn('previous');
            } else {
                // Balayage de droite à gauche (suivant)
                $('#flipbook').turn('next');
            }
        }
    }, {passive: false});

    // Améliorer les contrôles zoom pour touch
    const zoomIn = document.getElementById('zoom-in');
    const zoomOut = document.getElementById('zoom-out');

    if (zoomIn && zoomOut) {
        // Rendre les boutons de zoom plus réactifs
        zoomIn.addEventListener('touchstart', function(e) {
            e.preventDefault();
            handleZoom(0.1);
        });

        zoomOut.addEventListener('touchstart', function(e) {
            e.preventDefault();
            handleZoom(-0.1);
        });
    }
}


// Ajout pour empêcher les problèmes de défilement pendant la lecture
function preventScrollConflicts() {
    // Désactiver le défilement de la page lors de l'interaction avec le flipbook
    const flipbookElement = document.getElementById('flipbook');

    flipbookElement.addEventListener('touchmove', function(e) {
        // Empêcher le défilement de la page uniquement lors du balayage horizontal
        const touchStartX = e.touches[0].clientX;
        const touchStartY = e.touches[0].clientY;

        // Si le mouvement est plus horizontal que vertical
        if (Math.abs(touchStartX - startX) > Math.abs(touchStartY - startY)) {
            e.preventDefault();
        }
    }, {passive: false});
}

function toggleSound() {
    if (pageSound) {
        pageSound.muted = !pageSound.muted;

        // Icônes adaptatives selon la taille d'écran
        const isMobile = window.innerWidth <= 768;
        const iconSize = isMobile ? 'w-5 h-5' : 'w-6 h-6';

        $('#soundToggle').html(pageSound.muted ?
            `<svg xmlns="http://www.w3.org/2000/svg" class="${iconSize}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>` :
            `<svg xmlns="http://www.w3.org/2000/svg" class="${iconSize}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15.536 8.464a5 5 0 0 1 0 7.072m2.828-9.9a9 9 0 0 1 0 12.728M7.5 13.5 4 17H2v-4H0v-2h2V7h2l3.5 3.5z"></path></svg>`
        );

        // Ajouter une classe pour l'animation de transition
        $('#soundToggle').addClass('icon-transition');
        setTimeout(() => $('#soundToggle').removeClass('icon-transition'), 300);
    }
}

// Fonction pour désactiver le clic droit et autres événements de sécurité
function enableDocumentProtection() {
    const container = document.querySelector('.viewer-container');
    const flipbook = document.getElementById('flipbook');

    // Désactiver le clic droit
    container.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });

    // Désactiver la sélection de texte
    container.addEventListener('selectstart', function(e) {
        e.preventDefault();
        return false;
    });

    // Désactiver le glisser-déposer
    container.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
    });

    // Désactiver copier/coller
    container.addEventListener('copy', function(e) {
        e.preventDefault();
        return false;
    });

    container.addEventListener('cut', function(e) {
        e.preventDefault();
        return false;
    });

    // Désactiver les raccourcis clavier de capture d'écran et d'impression
    document.addEventListener('keydown', function(e) {
        // Ctrl + P (Impression)
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            return false;
        }
        // Ctrl + Shift + I ou F12 (Outils de développement)
        if ((e.ctrlKey && e.shiftKey && e.key === 'i') || e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        // Ctrl + C (Copier)
        if (e.ctrlKey && e.key === 'c') {
            e.preventDefault();
            return false;
        }
        // PrintScreen
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            return false;
        }
    });
}

// Ajouter le CSS pour empêcher la sélection
const style = document.createElement('style');
style.textContent = `
    .viewer-container {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .page {
        pointer-events: none;
    }

    #flipbook {
        pointer-events: auto;
    }
`;
document.head.appendChild(style);


// 10. Initialisations améliorées pour mobile
$(document).ready(function() {
    console.log('Démarrage de l\'application');

    // Détecter si l'appareil est tactile
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;

    if (isTouchDevice) {
        $('body').addClass('touch-device');
        // Augmenter la taille des zones tactiles
        $('.control-btn').css({
            'min-width': '44px',
            'min-height': '44px',
            'padding': '10px'
        });
    }

    initializeSound();
    initPDF();
    enableDocumentProtection();

    // Navigation avec gestion tactile améliorée
    $('#prev').on('click touchend', function(e) {
        e.preventDefault();
        $('#flipbook').turn('previous');
    });

    $('#next').on('click touchend', function(e) {
        e.preventDefault();
        $('#flipbook').turn('next');
    });

    // Navigation par balayage pour appareils tactiles
    if (isTouchDevice) {
        const flipbookElement = document.getElementById('flipbook');
        let startX = 0;

        flipbookElement.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        }, {passive: true});

        flipbookElement.addEventListener('touchend', function(e) {
            const endX = e.changedTouches[0].clientX;
            const diffX = endX - startX;

            // Seuil de 50px pour considérer comme un balayage
            if (Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    // Balayage de gauche à droite (précédent)
                    $('#flipbook').turn('previous');
                } else {
                    // Balayage de droite à gauche (suivant)
                    $('#flipbook').turn('next');
                }
            }
        }, {passive: true});
    }

    // Navigation clavier
    $(document).keydown(function(e) {
        switch(e.keyCode) {
            case 37: // gauche
                $('#flipbook').turn('previous');
                break;
            case 39: // droite
                $('#flipbook').turn('next');
                break;
        }
    });

    // Gestion de la rotation d'écran sur mobiles
    window.addEventListener('orientationchange', function() {
        // Attendre la fin de l'animation de rotation
        setTimeout(adjustFlipbookSize, 200);
    });
});

// Fonction pour afficher les erreurs de chargement - adaptée pour mobile
function showLoadingError(message) {
    const loading = document.getElementById('loading');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');

    loading.style.display = 'none';
    errorText.textContent = message;
    errorMessage.style.display = 'flex';

    // Adapter le message d'erreur pour les mobiles
    if (window.innerWidth <= 768) {
        const errorContainer = document.querySelector('.error-container');
        if (errorContainer) {
            errorContainer.style.width = '90%';
            errorContainer.style.maxWidth = '350px';
            errorContainer.style.padding = '1rem';
        }
    }
}
</script>
@endpush
