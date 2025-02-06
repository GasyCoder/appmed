import * as pdfjsLib from 'pdfjs-dist';
pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.js';

export default class PDFViewer {
    constructor(url, container) {
        this.url = url;
        this.container = container;
        this.currentPage = 1;
        this.zoom = 1;
        this.pdf = null;

        this.init();
    }

    async init() {
        try {
            this.pdf = await pdfjsLib.getDocument(this.url).promise;
            this.updatePageCount();
            this.renderCurrentPage();
        } catch (error) {
            console.error('Error loading PDF:', error);
        }
    }

    async renderCurrentPage() {
        if (!this.pdf) return;

        try {
            const page = await this.pdf.getPage(this.currentPage);
            const viewport = page.getViewport({ scale: this.zoom });

            const canvas = document.getElementById('pdf-canvas');
            const context = canvas.getContext('2d');

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({
                canvasContext: context,
                viewport: viewport
            }).promise;

            this.updatePageNumber();
        } catch (error) {
            console.error('Error rendering page:', error);
        }
    }

    updatePageCount() {
        const totalPages = this.pdf.numPages;
        document.getElementById('total-pages').textContent = totalPages;
    }

    updatePageNumber() {
        document.getElementById('current-page').textContent = this.currentPage;
    }

    previousPage() {
        if (this.currentPage <= 1) return;
        this.currentPage--;
        this.renderCurrentPage();
    }

    nextPage() {
        if (this.currentPage >= this.pdf.numPages) return;
        this.currentPage++;
        this.renderCurrentPage();
    }

    zoomIn() {
        if (this.zoom >= 3) return;
        this.zoom += 0.25;
        this.updateZoomLevel();
        this.renderCurrentPage();
    }

    zoomOut() {
        if (this.zoom <= 0.25) return;
        this.zoom -= 0.25;
        this.updateZoomLevel();
        this.renderCurrentPage();
    }

    updateZoomLevel() {
        document.getElementById('zoom-level').textContent = `${Math.round(this.zoom * 100)}%`;
    }
}
