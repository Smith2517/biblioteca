/**
 * public/js/users.js
 * CRUD de usuarios + gestión de permisos can_read / can_download.
 */
$(function () {

    // DataTable
    $('#tblUsers').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [0, 4, 5, 7] }],
    });

    // Limpiar modal al abrir para nuevo usuario
    document.getElementById('modalUser')?.addEventListener('show.bs.modal', function (e) {
        if (!e.relatedTarget) return;
        document.getElementById('modalUserTitle').textContent = 'Nuevo Usuario';
        document.getElementById('formUser').reset();
        document.getElementById('userId').value    = '0';
        document.getElementById('pwdHint').textContent = '(obligatoria)';
        document.getElementById('uPassword').setAttribute('required', '');
    });

    // Submit formulario usuario
    $('#formUser').on('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);

        // Checkboxes no marcados no se envían — asegurar valor correcto
        if (!$('#uStatus').is(':checked'))     fd.delete('status');
        if (!$('#uCanRead').is(':checked'))    fd.delete('can_read');
        if (!$('#uCanDownload').is(':checked'))fd.delete('can_download');

        const btn = $(this).find('[type=submit]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');

        $.ajax({
            url:         BASE_URL + 'admin/users/store',
            method:      'POST',
            data:        fd,
            processData: false,
            contentType: false,
            success: function (r) {
                if (r.success) {
                    Swal.fire({ icon: 'success', title: r.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: r.message });
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Guardar');
                }
            },
            error: () => {
                Swal.fire({ icon: 'error', title: 'Error inesperado.' });
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Guardar');
            }
        });
    });
});

/** Cargar datos de usuario en el modal de edición */
function editUser(id) {
    fetch(BASE_URL + 'admin/users/edit?id=' + id)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { Swal.fire({ icon: 'error', title: data.message }); return; }
            const u = data.data;
            document.getElementById('modalUserTitle').textContent = 'Editar Usuario';
            document.getElementById('userId').value    = u.id_user;
            document.getElementById('uNames').value    = u.names;
            document.getElementById('uSurnames').value = u.surnames;
            document.getElementById('uEmail').value    = u.email;
            document.getElementById('uRole').value     = u.role_id;
            document.getElementById('uStatus').checked     = u.status == 1;
            document.getElementById('uCanRead').checked    = u.can_read == 1;
            document.getElementById('uCanDownload').checked= u.can_download == 1;
            document.getElementById('pwdHint').textContent = '(dejar vacío para no cambiar)';
            document.getElementById('uPassword').removeAttribute('required');
            new bootstrap.Modal(document.getElementById('modalUser')).show();
        });
}

/** Eliminar usuario */
function deleteUser(id) {
    Swal.fire({
        title: '¿Eliminar este usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
    }).then(r => {
        if (r.isConfirmed) {
            $.post(BASE_URL + 'admin/users/delete', { id_user: id, csrf_token: CSRF_TOKEN }, res => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: res.message });
                }
            });
        }
    });
}

/** Activar / Inactivar usuario */
function toggleStatus(id) {
    $.post(BASE_URL + 'admin/users/toggle', { id_user: id }, res => {
        if (res.success) location.reload();
        else Swal.fire({ icon: 'error', title: res.message });
    });
}
