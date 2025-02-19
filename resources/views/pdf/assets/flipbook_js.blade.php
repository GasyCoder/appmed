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
    // Ajuster les limites de zoom pour maintenir la lisibilité
    currentScale = Math.max(0.5, Math.min(1.2, currentScale + delta));
    updateZoomDisplay();
    renderPages([
        $('#flipbook').turn('page'),
        $('#flipbook').turn('page') + 1
    ]);
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

    // Calculer les dimensions optimales
    const pageWidth = Math.min(containerWidth * 0.90, 1100);
    const pageHeight = Math.min(containerHeight * 0.95, 1000);

    console.log('Dimensions du flipbook:', pageWidth, 'x', pageHeight);

    // Initialiser Turn.js
    flipbook.turn({
        width: pageWidth,
        height: pageHeight,
        autoCenter: true,
        acceleration: false,
        gradients: true,
        elevation: 50,
        display: window.innerWidth <= 768 ? 'single' : 'double',
        when: {
            turning: function(event, page, view) {
                if (isPowerPoint) {
                    renderSlides(zipSource, view);
                } else {
                    renderPages(view);
                }

                // Jouer le son
                if (pageSound && pageSound.readyState >= 2) {
                    pageSound.currentTime = 0;
                    pageSound.play().catch(error => console.log("Erreur son:", error));
                }

                // Mettre à jour la pagination
                $('#current-page').text(page);
                $('#total-pages').text(numPages);
            },
            turned: function(event, page) {
                if (isPowerPoint) {
                    renderSlides(zipSource, [page, page + 1]);
                } else {
                    renderPages([page, page + 1]);
                }
            }
        }
    });

    // Rendre les premières pages
    if (isPowerPoint) {
        await renderSlides(zipSource, [1, 2]);
    } else {
        await renderPages([1, 2]);
    }
}


// Fonction pour rendre les slides PowerPoint
async function renderSlides(zipSource, slideNumbers) {
    if (!zipSource) {
        console.error('Source ZIP non définie');
        return;
    }

    for (let slideNum of slideNumbers) {
        if (!slideNum || slideNum < 1) continue;

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

            // Détecter les titres basés sur la position (généralement, le premier paragraphe est un titre)
            if (paragraphTexts.length > 0) {
                const firstText = paragraphTexts[0];
                if (firstText.length > 50) {
                    slideHTML += `<div class="large-title">${firstText}</div>`;
                } else {
                    slideHTML += `<h2 class="slide-title">${firstText}</h2>`;
                }
                hasTitle = true;

                // Ajouter les autres paragraphes
                for (let i = 1; i < paragraphTexts.length; i++) {
                    if (paragraphTexts[i].length < 30 && paragraphTexts[i].endsWith(':')) {
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

            // Ajouter des placeholders pour les images
            if (images.length > 0) {
                for (let i = 0; i < images.length; i++) {
                    slideHTML += `
                        <div class="slide-image-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" class="image-icon" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="none" stroke="currentColor" stroke-width="2" d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z M12 17l-5-5h10z M17 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                            <div class="image-text">Image</div>
                        </div>
                    `;
                }
            }

            // Afficher le contenu ou un message si vide
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
    for (let pageNum of pageNumbers) {
        if (!pageNum || pageNum < 1 || pageNum > pdfDoc.numPages) continue;

        try {
            const page = await pdfDoc.getPage(pageNum);
            const canvas = document.getElementById(`canvas-${pageNum}`);

            if (!canvas) continue;

            // Ajuster l'échelle pour avoir ce rendu exact
            const viewport = page.getViewport({
                scale: currentScale
            });

            const context = canvas.getContext('2d');

            // Optimiser la résolution sans exagérer
            const outputScale = Math.min(window.devicePixelRatio || 1, 1.25);
            canvas.width = Math.floor(viewport.width * outputScale);
            canvas.height = Math.floor(viewport.height * outputScale);

            canvas.style.width = Math.floor(viewport.width) + "px";
            canvas.style.height = Math.floor(viewport.height) + "px";

            context.scale(outputScale, outputScale);

            await page.render({
                canvasContext: context,
                viewport: viewport,
                enableWebGL: true,
            }).promise;

        } catch (error) {
            console.error(`Erreur lors du rendu de la page ${pageNum}:`, error);
        }
    }
}

function toggleSound() {
    if (pageSound) {
        pageSound.muted = !pageSound.muted;
        $('#soundToggle').html(pageSound.muted ?
            '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path></svg>' :
            '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15.536 8.464a5 5 0 0 1 0 7.072m2.828-9.9a9 9 0 0 1 0 12.728M7.5 13.5 4 17H2v-4H0v-2h2V7h2l3.5 3.5z"></path></svg>'
        );
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

// Initialisation
$(document).ready(function() {
    console.log('Démarrage de l\'application');
    initializeSound();
    initPDF();
    enableDocumentProtection();

    // Navigation
    $('#prev').click(() => $('#flipbook').turn('previous'));
    $('#next').click(() => $('#flipbook').turn('next'));

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
});
</script>
@endpush
