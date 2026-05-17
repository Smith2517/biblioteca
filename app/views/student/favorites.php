<?php /* app/views/teacher/favorites.php */ ?>
<?php $role = $_SESSION['user_role']; ?>
<div class="page-header">
    <h1 class="page-title">Mis Favoritos <small><?= count($favorites) ?> libros guardados</small></h1>
</div>
<?php if(empty($favorites)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-heart fs-1 d-block mb-3 opacity-50"></i>
    <p>Aún no tienes libros favoritos.<br>Explora el catálogo y presiona ❤️ para guardarlos aquí.</p>
    <a href="<?= BASE_URL . $role ?>/catalog" class="btn btn-accent mt-2">Ir al catálogo</a>
</div>
<?php else: ?>
<div class="row g-3">
<?php foreach($favorites as $b): ?>
<div class="col-6 col-md-4 col-lg-3 col-xl-2">
    <div class="book-card animate-in">
        <div class="book-cover-wrap">
            <img src="<?= COVERS_URL . htmlspecialchars($b['cover_image']) ?>"
                 alt="<?= htmlspecialchars($b['title']) ?>"
                 onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
            <div class="book-overlay">
                <a href="<?= BASE_URL . $role ?>/read?id=<?= $b['book_id'] ?>" class="btn btn-accent btn-sm"><i class="bi bi-book-open me-1"></i>Leer</a>
            </div>
        </div>
        <div class="book-info">
            <span class="category-badge" style="background:<?= $b['category_color'] ?>22;color:<?= $b['category_color'] ?>"><?= htmlspecialchars($b['category_name']) ?></span>
            <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
            <div class="book-author"><?= htmlspecialchars($b['author_name']) ?></div>
            <button class="btn-heart active mt-1" onclick="toggleFav(event,<?= $b['book_id'] ?>,this)">
                <i class="bi bi-heart-fill"></i>
            </button>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
