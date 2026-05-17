/**
 * public/js/app.js
 * Lógica global: sidebar toggle, dark mode, flash alerts, favoritos, búsqueda global.
 */
document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar toggle ────────────────────────────────────────
    const sidebar       = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
    }

    // ── Dark / Light mode toggle ──────────────────────────────
    const themeToggle = document.getElementById('themeToggle');
    const htmlEl      = document.documentElement;
    const saved       = localStorage.getItem('bv_theme') || 'dark';
    htmlEl.setAttribute('data-bs-theme', saved);
    if (themeToggle) {
        updateThemeIcon(themeToggle, saved);
        themeToggle.addEventListener('click', () => {
            const current = htmlEl.getAttribute('data-bs-theme');
            const next    = current === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-bs-theme', next);
            localStorage.setItem('bv_theme', next);
            updateThemeIcon(themeToggle, next);
        });
    }

    // ── Flash messages via SweetAlert2 ───────────────────────
    const flashEl = document.getElementById('flash-container');
    if (flashEl) {
        const type = flashEl.dataset.type;
        const msg  = flashEl.dataset.message;
        const iconMap = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
        Swal.fire({
            icon: iconMap[type] || 'info',
            html: msg,
            timer: 3500,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
        });
    }

    // ── Búsqueda global con AJAX ──────────────────────────────
    const globalSearch  = document.getElementById('globalSearch');
    let   searchTimeout = null;

    if (globalSearch) {
        // Crear contenedor de resultados
        const wrap = globalSearch.closest('.search-bar');
        wrap.style.position = 'relative';
        const resultsBox = document.createElement('div');
        resultsBox.id = 'searchResults';
        resultsBox.style.display = 'none';
        wrap.appendChild(resultsBox);

        globalSearch.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            const q = globalSearch.value.trim();
            if (q.length < 2) { resultsBox.style.display = 'none'; return; }

            searchTimeout = setTimeout(() => {
                fetch(BASE_URL + 'api/books/search?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success || !data.data.length) {
                            resultsBox.innerHTML = '<div class="p-3 text-muted small">Sin resultados.</div>';
                        } else {
                            resultsBox.innerHTML = data.data.slice(0, 8).map(b => `
                                <a href="${BASE_URL}${USER_ROLE}/read?id=${b.id_book}" class="search-result-item">
                                    <img src="${COVERS_URL}${b.cover_image}" onerror="this.src='${BASE_URL}public/img/no-cover.png'" alt="">
                                    <div>
                                        <div class="fw-500 small">${escHtml(b.title)}</div>
                                        <div class="text-muted x-small">${escHtml(b.author_name)}</div>
                                    </div>
                                </a>`).join('');
                        }
                        resultsBox.style.display = 'block';
                    });
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!wrap.contains(e.target)) resultsBox.style.display = 'none';
        });
    }

    // ── Animación de entrada para elementos ───────────────────
    document.querySelectorAll('.animate-in').forEach((el, i) => {
        el.style.animationDelay = (i * 0.04) + 's';
    });

}); // end DOMContentLoaded

// ── Helpers globales ──────────────────────────────────────────────────────────

/** Escapa HTML para prevenir XSS en JS */
function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

/** Actualiza el ícono del botón de tema */
function updateThemeIcon(btn, theme) {
    btn.querySelector('i').className = theme === 'dark' ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
}

/**
 * Alterna un libro en favoritos (usado desde catálogo y lector).
 * @param {Event}   e
 * @param {number}  bookId
 * @param {Element} btn    Elemento botón con clase .btn-heart
 */
function toggleFav(e, bookId, btn) {
    e.stopPropagation();
    fetch(BASE_URL + 'api/favorites/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `book_id=${bookId}&csrf_token=${CSRF_TOKEN}`,
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.classList.toggle('active', data.added);
            btn.innerHTML = data.added
                ? '<i class="bi bi-heart-fill"></i>'
                : '<i class="bi bi-heart"></i>';
        }
    });
}

/** Muestra alerta de éxito tipo toast */
function toastSuccess(msg) {
    Swal.fire({ icon: 'success', html: msg, timer: 2000, timerProgressBar: true, showConfirmButton: false, toast: true, position: 'top-end' });
}

/** Muestra alerta de error tipo toast */
function toastError(msg) {
    Swal.fire({ icon: 'error', html: msg, showConfirmButton: true, toast: false });
}

/**
 * Alterna el permiso de un usuario (lectura o descarga) desde la tabla de usuarios.
 * Llamado desde admin/users.php
 */
function togglePerm(userId, type, btn) {
    const isActive  = btn.classList.contains('active');
    const canRead     = type === 'read'     ? !isActive : (document.querySelector(`button[onclick*="${userId}, 'read'"]`)?.classList.contains('active') || false);
    const canDownload = type === 'download' ? !isActive : (document.querySelector(`button[onclick*="${userId}, 'download'"]`)?.classList.contains('active') || false);

    fetch(BASE_URL + 'admin/users/perms', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_user=${userId}&${canRead?'can_read=1':''}&${canDownload?'can_download=1':''}&csrf_token=${CSRF_TOKEN}`,
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const newVal = type === 'read' ? data.can_read : data.can_download;
            btn.classList.toggle('active', newVal);
            btn.innerHTML = `<i class="bi bi-${type === 'read' ? 'book' : 'download'}"></i> ${newVal ? 'Sí' : 'No'}`;
            toastSuccess(data.message);
        } else {
            toastError(data.message);
        }
    });
}

/**
 * Sistema de calificación con estrellas (lector PDF)
 */
function setRating(val) {
    document.getElementById('ratingInput').value = val;
    document.querySelectorAll('#ratingStars i').forEach((s, i) => {
        s.className = i < val ? 'bi bi-star-fill text-warning fs-5' : 'bi bi-star text-warning fs-5';
    });
}

// Manejar envío de comentarios
document.addEventListener('DOMContentLoaded', () => {
    const cf = document.getElementById('commentForm');
    if (cf) {
        cf.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fetch(BASE_URL + 'api/comments/store', {
                method: 'POST',
                body: new URLSearchParams(fd),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toastSuccess(data.message);
                    cf.reset();
                    setRating(0);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastError(data.message);
                }
            });
        });
    }
});
