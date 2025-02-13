@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('js/turn.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@3.12.0/libs/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@3.12.0/dist/pptxgen.min.js"></script>

<script>
    document.addEventListener('livewire:initialized', () => {
    // Configuration de PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    class PDFFlipBook {
        constructor() {
            this.pdfDoc = null;
            this.currentPage = 1;
            this.pageRendering = false;
            this.scale = 1.5;
            this.flipbook = $('#flipbook');
            this.loadingIndicator = document.getElementById('loadingIndicator');
            this.progressBar = this.loadingIndicator.querySelector('.progress-fill');
            this.progressText = this.loadingIndicator.querySelector('.progress-text');
        }

        async init(pdfUrl) {
            try {
                this.showLoading();

                // Charger le document PDF
                this.pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
                const totalPages = this.pdfDoc.numPages;

                // Nettoyer le flipbook existant
                if (this.flipbook.turn('is')) {
                    this.flipbook.turn('destroy');
                }
                this.flipbook.empty();

                // Ajouter la première page (couverture)
                await this.addCoverPage();

                // Charger et ajouter toutes les pages
                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    this.updateProgress(pageNum, totalPages);
                    await this.addPage(pageNum);
                }

                // Ajouter la dernière page (dos)
                await this.addBackCover();

                // Initialiser turn.js
                this.initializeTurnJs();
                this.hideLoading();

            } catch (error) {
                console.error('Erreur lors du chargement du PDF:', error);
                this.hideLoading();
                throw error;
            }
        }

        showLoading() {
            this.loadingIndicator.style.display = 'flex';
            this.progressBar.style.width = '0%';
            this.progressText.textContent = 'Chargement du document...';
        }

        hideLoading() {
            this.loadingIndicator.style.display = 'none';
        }

        updateProgress(current, total) {
            const percentage = (current / total * 100).toFixed(0);
            this.progressBar.style.width = `${percentage}%`;
            this.progressText.textContent = `Chargement de la page ${current}/${total}`;
        }

        async addPage(pageNum) {
            try {
                const page = await this.pdfDoc.getPage(pageNum);
                const viewport = page.getViewport({ scale: this.scale });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({
                    canvasContext: context,
                    viewport: viewport,
                    enableWebGL: true
                }).promise;

                const pageDiv = document.createElement('div');
                pageDiv.className = 'page';
                pageDiv.appendChild(canvas);
                this.flipbook.append(pageDiv);

            } catch (error) {
                console.error(`Erreur lors du rendu de la page ${pageNum}:`, error);
                this.flipbook.append(`
                    <div class="page error-page">
                        <div class="error-content">
                            <h3>Erreur de chargement</h3>
                            <p>La page ${pageNum} n'a pas pu être chargée</p>
                        </div>
                    </div>
                `);
            }
        }

        initializeTurnJs() {
            const width = Math.min(window.innerWidth * 0.8, 1000);
            const height = (width / 1.41); // Ratio A4

            this.flipbook.turn({
                width: width,
                height: height,
                autoCenter: true,
                gradients: true,
                acceleration: true,
                elevation: 50,
                page: 1,
                display: this.isMobile() ? 'single' : 'double',
                when: {
                    turned: (e, page) => {
                        this.updatePageNumber(page);
                    }
                }
            });

            this.initializeNavigation();
            this.handleResize();
        }

        isMobile() {
            return window.innerWidth <= 768;
        }

        handleResize() {
            const baseWidth = Math.min(window.innerWidth * 0.8, 1000);
            const baseHeight = (baseWidth / 1.41);

            this.flipbook.turn('size', baseWidth, baseHeight);

            // Ajuster le zoom sur mobile
            if (this.isMobile()) {
                this.flipbook.turn('display', 'single');
                this.flipbook.turn('zoom', 0.8);
            } else {
                this.flipbook.turn('display', 'double');
                this.flipbook.turn('zoom', 1);
            }
        }

        initializeNavigation() {
            $('#firstPage').on('click', () => this.flipbook.turn('page', 1));
            $('#lastPage').on('click', () => this.flipbook.turn('page', this.pdfDoc.numPages));
            $('#prev').on('click', () => this.flipbook.turn('previous'));
            $('#next').on('click', () => this.flipbook.turn('next'));

            // Navigation clavier
            $(document).on('keydown', (e) => {
                if (!this.flipbook.turn('is')) return;

                switch(e.keyCode) {
                    case 37: // gauche
                        this.flipbook.turn('previous');
                        e.preventDefault();
                        break;
                    case 39: // droite
                        this.flipbook.turn('next');
                        e.preventDefault();
                        break;
                }
            });
        }

        updatePageNumber(page) {
            $('#currentPage').text(page);
        }

        destroy() {
            if (this.flipbook.turn('is')) {
                this.flipbook.turn('destroy');
            }
            this.flipbook.empty();
        }
    }

    // Gestionnaire global du PDF viewer
    let pdfViewer = null;

    Livewire.on('documentReady', async (data) => {
        if (!pdfViewer) {
            pdfViewer = new PDFFlipBook();
        }
        try {
            await pdfViewer.init(data.url);
        } catch (error) {
            console.error('Erreur lors de l\'initialisation du viewer:', error);
        }
    });

    // Nettoyage lors de la fermeture
    Livewire.on('closeFlipbook', () => {
        if (pdfViewer) {
            pdfViewer.destroy();
        }
    });

    // Gestion du redimensionnement
    window.addEventListener('resize', _.debounce(() => {
        if (pdfViewer && pdfViewer.flipbook.turn('is')) {
            pdfViewer.handleResize();
        }
    }, 200));
});
</script>
@endpush
