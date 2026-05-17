<?php /* app/views/admin/dashboard.php */ ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard <small>Resumen del sistema</small></h1>
    </div>
    <span class="badge bg-success">Sistema activo</span>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="bi bi-book-fill"></i></div>
            <div>
                <div class="stat-value"><?= number_format($totalBooks) ?></div>
                <div class="stat-label">Libros publicados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card green">
            <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value"><?= number_format($totalUsers) ?></div>
                <div class="stat-label">Usuarios activos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card blue">
            <div class="stat-icon blue"><i class="bi bi-eye-fill"></i></div>
            <div>
                <div class="stat-value"><?= number_format($bookStats['total_views'] ?? 0) ?></div>
                <div class="stat-label">Lecturas totales</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="bi bi-download"></i></div>
            <div>
                <div class="stat-value"><?= number_format($bookStats['total_downloads'] ?? 0) ?></div>
                <div class="stat-label">Descargas totales</div>
            </div>
        </div>
    </div>
</div>

<!-- Roles -->
<div class="row g-3 mb-4">
    <?php foreach ($usersByRole as $r): ?>
    <div class="col-4">
        <div class="card p-3 text-center">
            <div class="fw-700 fs-4"><?= $r['total'] ?></div>
            <div class="text-muted small"><?= $r['name'] ?>s</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Charts + Top Books -->
<div class="row g-4">
    <!-- Libros más vistos -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-fire me-2 text-accent"></i>Libros más vistos</span>
                <a href="<?= BASE_URL ?>admin/reports" class="btn btn-sm btn-outline-accent">Ver todo</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="tblMostViewed">
                    <thead><tr>
                        <th class="px-3">#</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th class="text-end pe-3">Vistas</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($mostViewed as $i => $b): ?>
                    <tr>
                        <td class="px-3 fw-600 text-accent"><?= $i+1 ?></td>
                        <td>
                            <div class="fw-500"><?= htmlspecialchars($b['title']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($b['author_name']) ?></div>
                        </td>
                        <td>
                            <span class="category-badge"
                                  style="background:<?= $b['category_color'] ?>22;color:<?= $b['category_color'] ?>">
                                <?= htmlspecialchars($b['category_name']) ?>
                            </span>
                        </td>
                        <td class="text-end pe-3 fw-600"><?= number_format($b['views']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráfico por categoría -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-pie-chart-fill me-2 text-accent"></i>Libros por categoría
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="categoryChart" style="max-height:260px"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Libros recientes -->
<div class="card mt-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-accent"></i>Libros recientes</span>
        <a href="<?= BASE_URL ?>admin/books" class="btn btn-sm btn-outline-accent">Gestionar libros</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
        <?php foreach ($recentBooks as $b): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="book-card">
                    <div class="book-cover-wrap">
                        <img src="<?= COVERS_URL . htmlspecialchars($b['cover_image']) ?>"
                             alt="<?= htmlspecialchars($b['title']) ?>"
                             onerror="this.src='<?= BASE_URL ?>public/img/no-cover.png'">
                    </div>
                    <div class="book-info">
                        <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
                        <div class="book-author"><?= htmlspecialchars($b['author_name']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Chart.js — Libros por categoría
const catData = <?= json_encode($booksByCategory, JSON_UNESCAPED_UNICODE) ?>;
if (catData.length) {
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: catData.map(c => c.name),
            datasets: [{
                data: catData.map(c => c.total),
                backgroundColor: catData.map(c => c.color + '99'),
                borderColor: catData.map(c => c.color),
                borderWidth: 2
            }]
        },
        options: {
            plugins: { legend: { labels: { color: '#94a3b8', font: { family: 'Inter' } } } },
            cutout: '65%'
        }
    });
}
</script>
