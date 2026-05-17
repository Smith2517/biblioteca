/**
 * public/js/reader.js
 * Visor de PDF con PDF.js — controles de página, zoom y pantalla completa.
 * Requiere que PDF_URL y BOOK_ID estén definidos en la vista.
 */
'use strict';

// Worker de PDF.js (desde CDN)
pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let pdfDoc     = null;
let currentPage = 1;
let totalPages  = 0;
let scale       = 1.2;
let rendering   = false;

const canvas    = document.getElementById('pdfCanvas');
const ctx       = canvas?.getContext('2d');
const loading   = document.getElementById('pdfLoading');

/** Renderiza una página */
function renderPage(num) {
    if (!pdfDoc || rendering) return;
    rendering = true;

    pdfDoc.getPage(num).then(page => {
        const viewport = page.getViewport({ scale });
        canvas.width   = viewport.width;
        canvas.height  = viewport.height;

        page.render({ canvasContext: ctx, viewport }).promise.then(() => {
            rendering = false;
            document.getElementById('pageNum').textContent = num;
        });
    });
}

/** Carga el PDF desde la URL */
function loadPdf(url) {
    pdfjsLib.getDocument(url).promise.then(pdf => {
        pdfDoc     = pdf;
        totalPages = pdf.numPages;
        document.getElementById('pageCount').textContent = totalPages;
        loading.style.display = 'none';
        canvas.style.display  = 'block';
        renderPage(currentPage);

        // Registrar vista al cargar
        fetch(BASE_URL + 'api/books/view', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `book_id=${BOOK_ID}&csrf_token=${CSRF_TOKEN}`,
        });
    }).catch(err => {
        if (loading) loading.innerHTML = `<p class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar el PDF: ${err.message}</p>`;
    });
}

// Inicializar
if (canvas && typeof PDF_URL !== 'undefined') {
    canvas.style.display = 'none';
    loadPdf(PDF_URL);
}

// Controles de navegación
document.getElementById('prevPage')?.addEventListener('click', () => {
    if (currentPage <= 1) return;
    currentPage--;
    renderPage(currentPage);
});

document.getElementById('nextPage')?.addEventListener('click', () => {
    if (currentPage >= totalPages) return;
    currentPage++;
    renderPage(currentPage);
});

// Zoom
document.getElementById('zoomIn')?.addEventListener('click', () => {
    if (scale >= 3) return;
    scale += 0.2;
    renderPage(currentPage);
});

document.getElementById('zoomOut')?.addEventListener('click', () => {
    if (scale <= 0.5) return;
    scale -= 0.2;
    renderPage(currentPage);
});

// Pantalla completa
document.getElementById('fullscreenBtn')?.addEventListener('click', () => {
    const wrap = document.getElementById('pdf-canvas-container');
    if (!document.fullscreenElement) {
        wrap.requestFullscreen?.();
    } else {
        document.exitFullscreen?.();
    }
});

// Navegar con teclado
document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
        if (currentPage < totalPages) { currentPage++; renderPage(currentPage); }
    }
    if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
        if (currentPage > 1) { currentPage--; renderPage(currentPage); }
    }
});
