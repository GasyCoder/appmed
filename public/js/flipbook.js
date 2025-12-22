pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let pdfDoc = null;

async function initPDF() {
    try {
        // Récupérer le contenu du PDF
        const response = await fetch("{{ route('pdf.content', ['filename' => $filename]) }}");
        const data = await response.json();

        // Convertir le base64 en Uint8Array
        const pdfData = atob(data.data);
        const pdfArray = new Uint8Array(pdfData.length);
        for (let i = 0; i < pdfData.length; i++) {
            pdfArray[i] = pdfData.charCodeAt(i);
        }

        // Charger le PDF avec PDF.js
        pdfDoc = await pdfjsLib.getDocument({data: pdfArray}).promise;
        const numPages = pdfDoc.numPages;

        initFlipbook(numPages);

    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('loading').innerHTML = 'Erreur de chargement';
    }
}

function initFlipbook(numPages) {
    const flipbook = $('#flipbook');

    // Ajouter toutes les pages
    flipbook.append('<div class="hard"><div class="cover-content"><h1>{{ $filename }}</h1></div></div>');
    for (let i = 1; i <= numPages; i++) {
        flipbook.append(`<div class="page" id="page-${i}"><canvas id="canvas-${i}"></canvas></div>`);
    }
    flipbook.append('<div class="hard"></div>');

    // Initialiser Turn.js
    flipbook.turn({
        width: 800,
        height: 600,
        autoCenter: true,
        acceleration: true,
        gradients: true,
        elevation: 50,
        when: {
            turning: function(e, page, view) {
                renderPages(view);
            }
        }
    });

    // Rendre les premières pages
    renderPages([1, 2]);

    // Cacher le loader
    document.getElementById('loading').style.display = 'none';
}

async function renderPages(pageNumbers) {
    for (let pageNum of pageNumbers) {
        if (pageNum < 1 || pageNum > pdfDoc.numPages) continue;

        try {
            const page = await pdfDoc.getPage(pageNum);
            const canvas = document.getElementById(`canvas-${pageNum}`);
            if (!canvas) continue;

            const viewport = page.getViewport({scale: 1.5});
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            await page.render({
                canvasContext: canvas.getContext('2d'),
                viewport: viewport
            }).promise;

        } catch (error) {
            console.error(`Erreur rendu page ${pageNum}:`, error);
        }
    }
}

$(document).ready(function() {
    initPDF();

    $('#prev').click(() => $('#flipbook').turn('previous'));
    $('#next').click(() => $('#flipbook').turn('next'));
});
