<?php /* app/views/admin/categories.php */ ?>
<div class="page-header">
    <h1 class="page-title">Categorías</h1>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalCat">
        <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
    </button>
</div>
<div class="card">
    <div class="card-body">
        <table id="tblCats" class="table table-hover w-100">
            <thead><tr><th>#</th><th>Nombre</th><th>Descripción</th><th>Color</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td><?= $c['id_category'] ?></td>
                <td><span class="category-badge" style="background:<?= $c['color'] ?>22;color:<?= $c['color'] ?>"><?= htmlspecialchars($c['name']) ?></span></td>
                <td class="text-muted small"><?= htmlspecialchars($c['description'] ?? '') ?></td>
                <td><span class="badge" style="background:<?= $c['color'] ?>"><?= $c['color'] ?></span></td>
                <td><span class="badge <?= $c['status'] ? 'bg-success' : 'bg-secondary' ?>"><?= $c['status'] ? 'Activa' : 'Inactiva' ?></span></td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick='editCat(<?= json_encode($c) ?>)'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCat(<?= $c['id_category'] ?>)"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCat" tabindex="-1"><div class="modal-dialog"><div class="modal-content bg-card border-custom">
    <div class="modal-header border-custom">
        <h5 class="modal-title fw-600" id="modalCatTitle">Nueva Categoría</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <form id="formCat">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">
            <input type="hidden" name="id_category" id="catId" value="0">
            <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="name" id="catName" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Descripción</label><input type="text" name="description" id="catDesc" class="form-control"></div>
            <div class="mb-3 row align-items-center">
                <div class="col"><label class="form-label">Color</label><input type="color" name="color" id="catColor" class="form-control form-control-color" value="#6366f1"></div>
                <div class="col d-flex align-items-end pb-1"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="status" id="catStatus" value="1" checked><label class="form-check-label" for="catStatus">Activa</label></div></div>
            </div>
        </div>
        <div class="modal-footer border-custom">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-accent"><i class="bi bi-save me-1"></i>Guardar</button>
        </div>
    </form>
</div></div></div>

<script>
$('#tblCats').DataTable({language:{url:'//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'}});

$('#formCat').on('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    if($('#catStatus').is(':checked')) fd.set('status','1'); else fd.delete('status');
    $.post(BASE_URL+'admin/categories/store', Object.fromEntries(fd), r=>{
        if(r.success){ Swal.fire({icon:'success',title:r.message,timer:1500,showConfirmButton:false}).then(()=>location.reload()); }
        else Swal.fire({icon:'error',title:r.message});
    });
});

function editCat(c){
    document.getElementById('modalCatTitle').textContent='Editar Categoría';
    document.getElementById('catId').value=c.id_category;
    document.getElementById('catName').value=c.name;
    document.getElementById('catDesc').value=c.description||'';
    document.getElementById('catColor').value=c.color||'#6366f1';
    document.getElementById('catStatus').checked=c.status==1;
    new bootstrap.Modal(document.getElementById('modalCat')).show();
}
function deleteCat(id){
    Swal.fire({title:'¿Eliminar categoría?',icon:'warning',showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',confirmButtonColor:'#ef4444'}).then(r=>{
        if(r.isConfirmed) $.post(BASE_URL+'admin/categories/delete',{id_category:id,csrf_token:CSRF_TOKEN},res=>{
            if(res.success) location.reload(); else Swal.fire({icon:'error',title:res.message});
        });
    });
}
</script>
