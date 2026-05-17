<?php /* app/views/teacher/read-book.php */ ?>
<?php $role = $_SESSION['user_role']; ?>
<div class="page-header">
    <div>
        <a href="<?= BASE_URL . $role ?>/catalog" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="page-title d-inline"><?= htmlspecialchars($book['title']) ?>
            <small><?= htmlspecialchars($book['author_name']) ?></small>
        </h1>
    </div>
    <div class="d-flex gap-2">
        <button class="btn-heart <?= $isFav ? 'active' : '' ?>" id="favBtn" onclick="toggleFav(event,<?= $book['id_book'] ?>,this)">
            <i class="bi bi-heart<?= $isFav ? '-fill' : '' ?>"></i>
        </button>
        <?php if ($_SESSION['can_download']): ?>
        <a href="<?= BASE_URL . $role ?>/download?id=<?= $book['id_book'] ?>" class="btn btn-sm btn-outline-accent">
            <i class="bi bi-download me-1"></i>Descargar
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Lector PDF -->
    <div class="col-lg-8">
        <div class="pdf-reader-wrap">
            <div class="pdf-toolbar">
                <button class="btn btn-sm btn-outline-secondary" id="prevPage"><i class="bi bi-chevron-left"></i></button>
                <span class="text-muted small">Página <span id="pageNum">1</span> de <span id="pageCount">?</span></span>
                <button class="btn btn-sm btn-outline-secondary" id="nextPage"><i class="bi bi-chevron-right"></i></button>
                <div class="ms-auto d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="zoomOut"><i class="bi bi-zoom-out"></i></button>
                    <button class="btn btn-sm btn-outline-secondary" id="zoomIn"><i class="bi bi-zoom-in"></i></button>
                    <button class="btn btn-sm btn-outline-secondary" id="fullscreenBtn"><i class="bi bi-fullscreen"></i></button>
                </div>
            </div>
            <div id="pdf-canvas-container">
                <canvas id="pdfCanvas"></canvas>
                <div id="pdfLoading" class="text-center py-5 text-muted">
                    <div class="spinner-border text-accent" role="status"></div>
                    <p class="mt-3">Cargando PDF...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info + Comentarios -->
    <div class="col-lg-4">
        <!-- Info del libro -->
        <div class="card mb-3">
            <div class="card-body">
                <img src="<?= COVERS_URL . htmlspecialchars($book['cover_image']) ?>"
                     class="w-100 rounded-xl mb-3" style="max-height:200px;object-fit:cover"
                     onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
                <h6 class="fw-600"><?= htmlspecialchars($book['title']) ?></h6>
                <p class="text-muted small mb-2"><?= htmlspecialchars($book['description'] ?? '') ?></p>
                <div class="d-flex flex-wrap gap-2 small text-muted">
                    <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($book['author_name']) ?></span>
                    <span><i class="bi bi-calendar me-1"></i><?= $book['year'] ?? '—' ?></span>
                    <span><i class="bi bi-file-earmark-text me-1"></i><?= $book['pages'] ?? '—' ?> págs.</span>
                </div>
                <?php if ($avgRating): ?>
                <div class="mt-2">
                    <?php for($s=1;$s<=5;$s++): ?>
                    <i class="bi bi-star<?= $s <= $avgRating ? '-fill text-warning' : ' text-muted' ?>"></i>
                    <?php endfor; ?>
                    <span class="text-muted small ms-1">(<?= $avgRating ?>)</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comentarios -->
        <div class="card">
            <div class="card-header py-3 fw-600"><i class="bi bi-chat-dots me-2 text-accent"></i>Comentarios</div>
            <div class="card-body">
                <form id="commentForm" class="mb-3">
                    <input type="hidden" name="book_id" value="<?= $book['id_book'] ?>">
                    <div class="mb-2">
                        <div class="d-flex gap-1 mb-2" id="ratingStars">
                            <?php for($s=1;$s<=5;$s++): ?>
                            <i class="bi bi-star text-warning fs-5" style="cursor:pointer" data-val="<?= $s ?>" onclick="setRating(<?= $s ?>)"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput">
                    </div>
                    <textarea name="comment" class="form-control mb-2" rows="2" placeholder="Escribe tu comentario..." required></textarea>
                    <button type="submit" class="btn btn-accent btn-sm w-100">
                        <i class="bi bi-send me-1"></i>Publicar
                    </button>
                </form>
                <div id="commentsList">
                <?php foreach ($comments as $c): ?>
                <div class="d-flex gap-2 mb-3">
                    <img src="<?= BASE_URL ?>public/uploads/covers/<?= htmlspecialchars($c['user_photo']) ?>"
                         class="rounded-circle" width="32" height="32" style="object-fit:cover"
                         onerror="this.src='<?= BASE_URL ?>public/img/default.png'">
                    <div class="flex-grow-1">
                        <div class="fw-500 small"><?= htmlspecialchars($c['user_name']) ?></div>
                        <div class="text-muted x-small mb-1"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></div>
                        <div class="small"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($comments)): ?><p class="text-muted small text-center">Sé el primero en comentar.</p><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const PDF_URL = '<?= BOOKS_URL . htmlspecialchars($book['pdf_file']) ?>';
const BOOK_ID = <?= $book['id_book'] ?>;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="<?= BASE_URL ?>public/js/reader.js"></script>
