<?php /* app/views/teacher/catalog.php */ ?>
<?php $role = $_SESSION['user_role']; ?>
<div class="page-header">
    <h1 class="page-title">Catálogo <small><?= count($books) ?> libros disponibles</small></h1>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-500">Buscar</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Título, autor, descripción...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-500">Categoría</label>
                <select id="filterCategory" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id_category'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-500">Autor</label>
                <select id="filterAuthor" class="form-select">
                    <option value="">Todos los autores</option>
                    <?php foreach ($authors as $a): ?>
                    <option value="<?= $a['id_author'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Grid de libros -->
<div id="booksGrid" class="row g-3">
<?php foreach ($books as $b): ?>
<div class="col-6 col-md-4 col-lg-3 col-xl-2 book-item"
     data-title="<?= strtolower(htmlspecialchars($b['title'])) ?>"
     data-author="<?= strtolower(htmlspecialchars($b['author_name'])) ?>"
     data-category="<?= $b['category_id'] ?>"
     data-authorid="<?= $b['author_id'] ?>">
    <div class="book-card animate-in">
        <div class="book-cover-wrap">
            <img src="<?= COVERS_URL . htmlspecialchars($b['cover_image']) ?>"
                 alt="<?= htmlspecialchars($b['title']) ?>"
                 onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
            <div class="book-overlay">
                <a href="<?= BASE_URL . $role ?>/read?id=<?= $b['id_book'] ?>" class="btn btn-accent btn-sm"><i class="bi bi-book-open me-1"></i>Leer</a>
                <?php if ($_SESSION['can_download']): ?>
                <a href="<?= BASE_URL . $role ?>/download?id=<?= $b['id_book'] ?>" class="btn btn-sm btn-outline-light"><i class="bi bi-download"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="book-info">
            <span class="category-badge" style="background:<?= $b['category_color'] ?>22;color:<?= $b['category_color'] ?>"><?= htmlspecialchars($b['category_name']) ?></span>
            <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
            <div class="book-author"><?= htmlspecialchars($b['author_name']) ?></div>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="text-muted x-small"><i class="bi bi-eye me-1"></i><?= number_format($b['views']) ?></span>
                <button class="btn-heart" onclick="toggleFav(event,<?= $b['id_book'] ?>,this)">
                    <i class="bi bi-heart"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<div id="noResults" class="text-center text-muted py-5 d-none">
    <i class="bi bi-search fs-1 mb-3 d-block opacity-50"></i>
    No se encontraron libros con esos filtros.
</div>

<script>
// Filtro cliente
function applyFilters(){
    const q   = document.getElementById('searchInput').value.toLowerCase().trim();
    const cat = document.getElementById('filterCategory').value;
    const aut = document.getElementById('filterAuthor').value;
    let   cnt = 0;
    document.querySelectorAll('.book-item').forEach(el=>{
        const matchQ   = !q   || el.dataset.title.includes(q) || el.dataset.author.includes(q);
        const matchCat = !cat || el.dataset.category === cat;
        const matchAut = !aut || el.dataset.authorid === aut;
        const show = matchQ && matchCat && matchAut;
        el.style.display = show ? '' : 'none';
        if(show) cnt++;
    });
    document.getElementById('noResults').classList.toggle('d-none', cnt > 0);
}
function clearFilters(){
    document.getElementById('searchInput').value='';
    document.getElementById('filterCategory').value='';
    document.getElementById('filterAuthor').value='';
    applyFilters();
}
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterCategory').addEventListener('change', applyFilters);
document.getElementById('filterAuthor').addEventListener('change', applyFilters);
</script>
