<?php /* app/views/teacher/dashboard.php — reutilizable para student */ ?>
<?php $role = $_SESSION['user_role']; ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Bienvenido, <?= htmlspecialchars(explode(' ',$_SESSION['user_name'])[0]) ?> 👋
            <small>Tu biblioteca digital personal</small>
        </h1>
    </div>
</div>

<!-- Libros más vistos -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-600 mb-0"><i class="bi bi-fire me-2 text-accent"></i>Libros populares</h5>
    <a href="<?= BASE_URL . $role ?>/catalog" class="btn btn-sm btn-outline-accent">Ver catálogo →</a>
</div>

<div class="row g-3 mb-5">
<?php foreach ($mostViewed as $b): ?>
    <div class="col-6 col-md-3 col-lg-3">
        <div class="book-card animate-in" onclick="window.location='<?= BASE_URL . $role ?>/read?id=<?= $b['id_book'] ?>'">
            <div class="book-cover-wrap">
                <img src="<?= COVERS_URL . htmlspecialchars($b['cover_image']) ?>"
                     alt="<?= htmlspecialchars($b['title']) ?>"
                     onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
                <div class="book-overlay">
                    <button class="btn btn-accent btn-sm"><i class="bi bi-book-open me-1"></i>Leer</button>
                </div>
            </div>
            <div class="book-info">
                <span class="category-badge" style="background:<?= $b['category_color'] ?>22;color:<?= $b['category_color'] ?>"><?= htmlspecialchars($b['category_name']) ?></span>
                <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                <div class="book-author"><?= htmlspecialchars($b['author_name']) ?></div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- Recientes -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-600 mb-0"><i class="bi bi-clock me-2 text-accent"></i>Agregados recientemente</h5>
</div>
<div class="row g-3">
<?php foreach ($recentBooks as $b): ?>
    <div class="col-6 col-md-3 col-lg-2">
        <div class="book-card animate-in" onclick="window.location='<?= BASE_URL . $role ?>/read?id=<?= $b['id_book'] ?>'">
            <div class="book-cover-wrap">
                <img src="<?= COVERS_URL . htmlspecialchars($b['cover_image']) ?>"
                     alt="<?= htmlspecialchars($b['title']) ?>"
                     onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
                <div class="book-overlay">
                    <button class="btn btn-accent btn-sm"><i class="bi bi-book-open"></i></button>
                </div>
            </div>
            <div class="book-info">
                <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                <div class="book-author"><?= htmlspecialchars($b['author_name']) ?></div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
