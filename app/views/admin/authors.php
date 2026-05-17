<?php /* app/views/admin/authors.php */ ?>
<div class="page-header">
    <h1 class="page-title">Autores</h1>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalAuthor">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Autor
    </button>
</div>
<div class="card">
    <div class="card-body">
        <table id="tblAuthors" class="table table-hover w-100">
            <thead><tr><th>#</th><th>Nombre</th><th>Biografía</th><th>Libros</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($authors as $a): ?>
            <tr>
                <td><?= $a['id_author'] ?></td>
                <td class="fw-500"><?= htmlspecialchars($a['name']) ?></td>
                <td class="text-muted small" style="max-width:300px"><?= htmlspecialchars(mb_substr($a['bio']??'',0,80)) ?>...</td>
                <td><span class="badge bg-primary"><?= $a['book_count'] ?></span></td>
                <td><span class="badge <?= $a['status'] ? 'bg-success' : 'bg-secondary' ?>"><?= $a['status'] ? 'Activo' : 'Inactivo' ?></span></td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary" onclick='editAuthor(<?= json_encode($a) ?>)'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAuthor(<?= $a['id_author'] ?>)"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalAuthor" tabindex="-1"><div class="modal-dialog"><div class="modal-content bg-card border-custom">
    <div class="modal-header border-custom">
        <h5 class="modal-title fw-600" id="modalAuthorTitle">Nuevo Autor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <form id="formAuthor">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">
            <input type="hidden" name="id_author" id="authorId" value="0">
            <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="name" id="authorName" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Biografía</label><textarea name="bio" id="authorBio" class="form-control" rows="3"></textarea></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="status" id="authorStatus" value="1" checked><label class="form-check-label" for="authorStatus">Activo</label></div>
        </div>
        <div class="modal-footer border-custom">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-accent"><i class="bi bi-save me-1"></i>Guardar</button>
        </div>
    </form>
</div></div></div>
<script>
$('#tblAuthors').DataTable({language:{url:'//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'}});
$('#formAuthor').on('submit',function(e){
    e.preventDefault();
    const fd=new FormData(this);
    if(!$('#authorStatus').is(':checked'))fd.delete('status');
    $.post(BASE_URL+'admin/authors/store',Object.fromEntries(fd),r=>{
        if(r.success){Swal.fire({icon:'success',title:r.message,timer:1500,showConfirmButton:false}).then(()=>location.reload());}
        else Swal.fire({icon:'error',title:r.message});
    });
});
function editAuthor(a){
    document.getElementById('modalAuthorTitle').textContent='Editar Autor';
    document.getElementById('authorId').value=a.id_author;
    document.getElementById('authorName').value=a.name;
    document.getElementById('authorBio').value=a.bio||'';
    document.getElementById('authorStatus').checked=a.status==1;
    new bootstrap.Modal(document.getElementById('modalAuthor')).show();
}
function deleteAuthor(id){
    Swal.fire({title:'¿Eliminar autor?',icon:'warning',showCancelButton:true,confirmButtonText:'Sí',cancelButtonText:'No',confirmButtonColor:'#ef4444'}).then(r=>{
        if(r.isConfirmed)$.post(BASE_URL+'admin/authors/delete',{id_author:id,csrf_token:CSRF_TOKEN},res=>{ if(res.success)location.reload(); });
    });
}
</script>
