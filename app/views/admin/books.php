<?php /* app/views/admin/books.php */ ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Libros <small>Gestión de catálogo</small></h1>
    </div>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalBook">
        <i class="bi bi-plus-lg me-1"></i> Agregar Libro
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table id="tblBooks" class="table table-hover w-100">
            <thead><tr>
                <th>Portada</th><th>Título</th><th>Autor</th><th>Categoría</th>
                <th>Año</th><th>Vistas</th><th>Estado</th><th>Acciones</th>
            </tr></thead>
            <tbody>
            <?php foreach ($books as $b): ?>
            <tr>
                <td>
                    <img src="<?= COVERS_URL . htmlspecialchars($b['cover_image']) ?>"
                         width="36" height="50" style="object-fit:cover;border-radius:4px"
                         onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
                </td>
                <td>
                    <div class="fw-500"><?= htmlspecialchars($b['title']) ?></div>
                    <?php if ($b['isbn']): ?><div class="text-muted x-small">ISBN: <?= $b['isbn'] ?></div><?php endif; ?>
                </td>
                <td class="text-muted small"><?= htmlspecialchars($b['author_name']) ?></td>
                <td>
                    <span class="category-badge"
                          style="background:<?= $b['category_color'] ?>22;color:<?= $b['category_color'] ?>">
                        <?= htmlspecialchars($b['category_name']) ?>
                    </span>
                </td>
                <td><?= $b['year'] ?? '—' ?></td>
                <td><i class="bi bi-eye me-1 text-accent"></i><?= number_format($b['views']) ?></td>
                <td>
                    <span class="badge <?= $b['status'] ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $b['status'] ? 'Publicado' : 'Oculto' ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-secondary" onclick="editBook(<?= $b['id_book'] ?>)"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBook(<?= $b['id_book'] ?>)"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Libro -->
<div class="modal fade" id="modalBook" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content bg-card border-custom">
    <div class="modal-header border-custom">
        <h5 class="modal-title fw-600" id="modalBookTitle">Nuevo Libro</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <form id="formBook" enctype="multipart/form-data">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">
            <input type="hidden" name="id_book" id="bookId" value="0">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Título *</label>
                    <input type="text" name="title" id="bTitle" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" id="bIsbn" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Año de publicación</label>
                    <input type="number" name="year" id="bYear" class="form-control" min="1900" max="<?= date('Y') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Páginas</label>
                    <input type="number" name="pages" id="bPages" class="form-control" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Autor *</label>
                    <select name="author_id" id="bAuthor" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        <?php foreach ($authors as $a): ?>
                        <option value="<?= $a['id_author'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Categoría *</label>
                    <select name="category_id" id="bCategory" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id_category'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Editorial *</label>
                    <select name="editorial_id" id="bEditorial" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        <?php foreach ($editorials as $e): ?>
                        <option value="<?= $e['id_editorial'] ?>"><?= htmlspecialchars($e['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" id="bDescription" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Archivo PDF * <span class="text-muted small">(máx. 50 MB)</span></label>
                    <input type="file" name="pdf_file" id="bPdf" class="form-control" accept=".pdf">
                    <div id="pdfCurrentLabel" class="text-muted small mt-1"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Portada <span class="text-muted small">(JPG/PNG/WebP)</span></label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="bStatus" value="1" checked>
                        <label class="form-check-label" for="bStatus">Publicado</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer border-custom">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-accent"><i class="bi bi-save me-1"></i>Guardar</button>
        </div>
    </form>
</div>
</div>
</div>

<script src="<?= BASE_URL ?>public/js/books.js"></script>
