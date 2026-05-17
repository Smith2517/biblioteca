<?php /* app/views/admin/reports.php */ ?>
<div class="page-header">
    <h1 class="page-title">Reportes <small>Estadísticas del sistema</small></h1>
</div>

<div class="row g-4">
    <!-- Gráfico barras — más vistos -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3"><i class="bi bi-bar-chart-fill me-2 text-accent"></i>Top 10 libros más vistos</div>
            <div class="card-body"><canvas id="viewsChart" height="100"></canvas></div>
        </div>
    </div>
    <!-- Gráfico donut — por categoría -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3"><i class="bi bi-pie-chart-fill me-2 text-accent"></i>Por categoría</div>
            <div class="card-body d-flex justify-content-center align-items-center"><canvas id="catChart" height="200"></canvas></div>
        </div>
    </div>
</div>

<!-- Tabla completa -->
<div class="card mt-4">
    <div class="card-header py-3"><i class="bi bi-table me-2 text-accent"></i>Libros más visitados</div>
    <div class="card-body">
        <table id="tblReports" class="table table-hover w-100">
            <thead><tr><th>#</th><th>Título</th><th>Autor</th><th>Categoría</th><th>Vistas</th><th>Descargas</th></tr></thead>
            <tbody>
            <?php foreach ($mostViewed as $i => $b): ?>
            <tr>
                <td class="fw-600 text-accent"><?= $i+1 ?></td>
                <td class="fw-500"><?= htmlspecialchars($b['title']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($b['author_name']) ?></td>
                <td><span class="category-badge" style="background:<?= $b['category_color'] ?>22;color:<?= $b['category_color'] ?>"><?= htmlspecialchars($b['category_name']) ?></span></td>
                <td><i class="bi bi-eye me-1 text-accent"></i><?= number_format($b['views']) ?></td>
                <td><i class="bi bi-download me-1 text-warning"></i><?= number_format($b['downloads']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const mv  = <?= json_encode(array_slice($mostViewed, 0, 10), JSON_UNESCAPED_UNICODE) ?>;
const cat = <?= json_encode($booksByCategory, JSON_UNESCAPED_UNICODE) ?>;

new Chart(document.getElementById('viewsChart'),{
    type:'bar',
    data:{
        labels: mv.map(b=>b.title.substring(0,25)+'…'),
        datasets:[{label:'Vistas',data:mv.map(b=>b.views),backgroundColor:'rgba(99,102,241,.7)',borderColor:'#6366f1',borderWidth:1,borderRadius:6}]
    },
    options:{plugins:{legend:{display:false}},scales:{x:{ticks:{color:'#94a3b8'}},y:{ticks:{color:'#94a3b8'}}}}
});

new Chart(document.getElementById('catChart'),{
    type:'doughnut',
    data:{
        labels:cat.map(c=>c.name),
        datasets:[{data:cat.map(c=>c.total),backgroundColor:cat.map(c=>c.color+'99'),borderColor:cat.map(c=>c.color),borderWidth:2}]
    },
    options:{plugins:{legend:{labels:{color:'#94a3b8',font:{family:'Inter'}}}},cutout:'65%'}
});

$('#tblReports').DataTable({language:{url:'//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'}});
</script>
