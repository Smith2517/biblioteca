<?php /* app/views/admin/editorials.php */ ?>
<div class="page-header">
    <h1 class="page-title">Editoriales</h1>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalEd">
        <i class="bi bi-plus-lg me-1"></i> Nueva Editorial
    </button>
</div>
<div class="card">
    <div class="card-body">
        <table id="tblEds" class="table table-hover w-100">
            <thead><tr><th>#</th><th>Nombre</th><th>País</th><th>Sitio web</th><th>Libros</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($editorials as $e): ?>
            <tr>
                <td><?= $e['id_editorial'] ?></td>
                <td class="fw-500"><?= htmlspecialchars($e['name']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($e['country']??'') ?></td>
                <td><?php if($e['website']): ?><a href="<?= htmlspecialchars($e['website']) ?>" target="_blank" class="text-accent small"><?= htmlspecialchars($e['website']) ?></a><?php endif; ?></td>
                <td><span class="badge bg-primary"><?= $e['book_count'] ?></span></td>
                <td><span class="badge <?= $e['status'] ? 'bg-success' : 'bg-secondary' ?>"><?= $e['status'] ? 'Activa' : 'Inactiva' ?></span></td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick='editEd(<?= json_encode($e) ?>)'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteEd(<?= $e['id_editorial'] ?>)"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalEd" tabindex="-1"><div class="modal-dialog"><div class="modal-content bg-card border-custom">
    <div class="modal-header border-custom"><h5 class="modal-title fw-600" id="modalEdTitle">Nueva Editorial</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <form id="formEd">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">
            <input type="hidden" name="id_editorial" id="edId" value="0">
            <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="name" id="edName" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">País</label><input type="text" name="country" id="edCountry" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Sitio web</label><input type="url" name="website" id="edWebsite" class="form-control" placeholder="https://..."></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="status" id="edStatus" value="1" checked><label class="form-check-label" for="edStatus">Activa</label></div>
        </div>
        <div class="modal-footer border-custom"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-accent"><i class="bi bi-save me-1"></i>Guardar</button></div>
    </form>
</div></div></div>
<script>
$('#tblEds').DataTable({language:{url:'//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'}});
$('#formEd').on('submit',function(e){
    e.preventDefault();
    const fd=new FormData(this);
    if(!$('#edStatus').is(':checked'))fd.delete('status');
    $.post(BASE_URL+'admin/editorials/store',Object.fromEntries(fd),r=>{
        if(r.success){Swal.fire({icon:'success',title:r.message,timer:1500,showConfirmButton:false}).then(()=>location.reload());}
        else Swal.fire({icon:'error',title:r.message});
    });
});
function editEd(e){
    document.getElementById('modalEdTitle').textContent='Editar Editorial';
    document.getElementById('edId').value=e.id_editorial;
    document.getElementById('edName').value=e.name;
    document.getElementById('edCountry').value=e.country||'';
    document.getElementById('edWebsite').value=e.website||'';
    document.getElementById('edStatus').checked=e.status==1;
    new bootstrap.Modal(document.getElementById('modalEd')).show();
}
function deleteEd(id){
    Swal.fire({title:'¿Eliminar editorial?',icon:'warning',showCancelButton:true,confirmButtonText:'Sí',cancelButtonText:'No',confirmButtonColor:'#ef4444'}).then(r=>{
        if(r.isConfirmed)$.post(BASE_URL+'admin/editorials/delete',{id_editorial:id,csrf_token:CSRF_TOKEN},res=>{ if(res.success)location.reload(); });
    });
}
</script>
