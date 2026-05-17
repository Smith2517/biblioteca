<?php /* app/views/teacher/history.php */ ?>
<?php $role = $_SESSION['user_role']; ?>
<div class="page-header">
    <h1 class="page-title">Historial de Lectura</h1>
</div>
<?php if(empty($history)): ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-clock-history fs-1 d-block mb-3 opacity-50"></i>
    <p>Aún no has leído ningún libro. ¡Explora el catálogo!</p>
    <a href="<?= BASE_URL . $role ?>/catalog" class="btn btn-accent mt-2">Ir al catálogo</a>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <table id="tblHistory" class="table table-hover mb-0">
            <thead><tr><th class="px-3">Portada</th><th>Libro</th><th>Categoría</th><th>Acción</th><th>Fecha</th></tr></thead>
            <tbody>
            <?php foreach($history as $h): ?>
            <tr>
                <td class="px-3">
                    <img src="<?= COVERS_URL . htmlspecialchars($h['cover_image']) ?>"
                         width="32" height="44" style="object-fit:cover;border-radius:4px"
                         onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
                </td>
                <td>
                    <a href="<?= BASE_URL . $role ?>/read?id=<?= $h['book_id'] ?>" class="fw-500 text-decoration-none">
                        <?= htmlspecialchars($h['title']) ?>
                    </a>
                    <div class="text-muted x-small"><?= htmlspecialchars($h['author_name']) ?></div>
                </td>
                <td><span class="category-badge" style="background:<?= $h['category_color'] ?>22;color:<?= $h['category_color'] ?>"><?= htmlspecialchars($h['category_name']) ?></span></td>
                <td>
                    <span class="badge <?= $h['action']==='read' ? 'bg-primary' : 'bg-warning text-dark' ?>">
                        <i class="bi bi-<?= $h['action']==='read' ? 'book' : 'download' ?> me-1"></i>
                        <?= $h['action']==='read' ? 'Lectura' : 'Descarga' ?>
                    </span>
                </td>
                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>$('#tblHistory').DataTable({order:[[4,'desc']],language:{url:'//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'}});</script>
<?php endif; ?>
