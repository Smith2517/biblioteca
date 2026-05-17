/**
 * public/js/books.js
 * CRUD de libros para el panel administrador.
 */
$(function () {

    // DataTable
    $('#tblBooks').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [0, 7] }],
    });

    // Limpiar modal al abrir para nuevo libro
    document.getElementById('modalBook')?.addEventListener('show.bs.modal', function (e) {
        if (!e.relatedTarget) return;
        document.getElementById('modalBookTitle').textContent = 'Nuevo Libro';
        document.getElementById('formBook').reset();
        document.getElementById('bookId').value = '0';
        document.getElementById('pdfCurrentLabel').textContent = '';
    });

    // Submit formulario libro
    $('#formBook').on('submit', function (e) {
        e.preventDefault();
        const fd  = new FormData(this);
        if (!$('#bStatus').is(':checked')) fd.delete('status');

        // Validar que haya PDF si es nuevo
        const bookId = parseInt($('#bookId').val());
        if (!bookId && !fd.get('pdf_file').size) {
            Swal.fire({ icon: 'warning', title: 'Debes seleccionar un archivo PDF.' });
            return;
        }

        const btn = $(this).find('[type=submit]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');

        $.ajax({
            url:         BASE_URL + 'admin/books/store',
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
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error inesperado. Intenta de nuevo.' });
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Guardar');
            }
        });
    });
});

/** Editar libro — carga datos en el modal */
function editBook(id) {
    fetch(BASE_URL + 'admin/books/edit?id=' + id)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { Swal.fire({ icon: 'error', title: data.message }); return; }
            const b = data.data;
            document.getElementById('modalBookTitle').textContent = 'Editar Libro';
            document.getElementById('bookId').value       = b.id_book;
            document.getElementById('bTitle').value       = b.title;
            document.getElementById('bIsbn').value        = b.isbn  || '';
            document.getElementById('bYear').value        = b.year  || '';
            document.getElementById('bPages').value       = b.pages || '';
            document.getElementById('bDescription').value = b.description || '';
            document.getElementById('bAuthor').value      = b.author_id;
            document.getElementById('bCategory').value    = b.category_id;
            document.getElementById('bEditorial').value   = b.editorial_id;
            document.getElementById('bStatus').checked    = b.status == 1;
            document.getElementById('pdfCurrentLabel').textContent = 'PDF actual: ' + b.pdf_file;
            document.getElementById('bPdf').removeAttribute('required');
            new bootstrap.Modal(document.getElementById('modalBook')).show();
        });
}

/** Eliminar libro */
function deleteBook(id) {
    Swal.fire({
        title: '¿Eliminar este libro?',
        text: 'Se eliminarán el PDF y la portada del servidor.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
    }).then(r => {
        if (r.isConfirmed) {
            $.post(BASE_URL + 'admin/books/delete', { id_book: id, csrf_token: CSRF_TOKEN }, res => {
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
