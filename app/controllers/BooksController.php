<?php
/**
 * app/controllers/BooksController.php
 * CRUD de libros con subida de PDF y portada.
 */
class BooksController extends Controller
{
    private Book     $bookModel;
    private Category $categoryModel;
    private Author   $authorModel;
    private Editorial $editorialModel;

    public function __construct()
    {
        parent::__construct();
        require_once BASE_PATH . 'app/models/Book.php';
        require_once BASE_PATH . 'app/models/Category.php';
        require_once BASE_PATH . 'app/models/Author.php';
        require_once BASE_PATH . 'app/models/Editorial.php';
        $this->bookModel      = new Book();
        $this->categoryModel  = new Category();
        $this->authorModel    = new Author();
        $this->editorialModel = new Editorial();
    }

    /** GET /admin/books */
    public function index(): void
    {
        $this->requireRole('admin');
        $this->render('admin/books', [
            'title'      => 'Gestión de Libros',
            'books'      => $this->bookModel->getAllWithDetails(),
            'categories' => $this->categoryModel->allActive(),
            'authors'    => $this->authorModel->allActive(),
            'editorials' => $this->editorialModel->allActive(),
        ], 'admin');
    }

    /** POST /admin/books/store — Crear o actualizar libro */
    public function store(): void
    {
        $this->requireRole('admin');
        $this->requirePost();
        $this->validateCsrf();

        $id          = (int) ($_POST['id_book']       ?? 0);
        $title       = trim($_POST['title']           ?? '');
        $isbn        = trim($_POST['isbn']            ?? '');
        $description = trim($_POST['description']     ?? '');
        $pages       = (int) ($_POST['pages']         ?? 0);
        $year        = (int) ($_POST['year']          ?? 0);
        $language    = trim($_POST['language']        ?? 'Español');
        $authorId    = (int) ($_POST['author_id']     ?? 0);
        $categoryId  = (int) ($_POST['category_id']  ?? 0);
        $editorialId = (int) ($_POST['editorial_id'] ?? 0);
        $status      = isset($_POST['status']) ? 1 : 0;

        if (!$title || !$authorId || !$categoryId || !$editorialId) {
            $this->json(['success' => false, 'message' => 'Completa todos los campos obligatorios.'], 422);
        }

        $existing    = $id ? $this->bookModel->find($id) : [];
        $pdfFile     = $existing['pdf_file']    ?? '';
        $coverImage  = $existing['cover_image'] ?? 'no-cover.png';

        // Subir PDF
        if (!empty($_FILES['pdf_file']['name'])) {
            $upload = $this->uploadPdf($_FILES['pdf_file']);
            if (!$upload['success']) {
                $this->json(['success' => false, 'message' => $upload['message']], 422);
            }
            // Eliminar PDF anterior
            if ($pdfFile && file_exists(BOOKS_PATH . $pdfFile)) {
                @unlink(BOOKS_PATH . $pdfFile);
            }
            $pdfFile = $upload['filename'];
        }

        if (!$pdfFile) {
            $this->json(['success' => false, 'message' => 'Debes subir un archivo PDF.'], 422);
        }

        // Subir portada
        if (!empty($_FILES['cover_image']['name'])) {
            $upload = $this->uploadCover($_FILES['cover_image']);
            if ($upload['success']) {
                if ($coverImage !== 'no-cover.png' && file_exists(COVERS_PATH . $coverImage)) {
                    @unlink(COVERS_PATH . $coverImage);
                }
                $coverImage = $upload['filename'];
            }
        }

        $bookData = [
            'title'        => $title,
            'isbn'         => $isbn,
            'description'  => $description,
            'pdf_file'     => $pdfFile,
            'cover_image'  => $coverImage,
            'pages'        => $pages ?: null,
            'year'         => $year  ?: null,
            'language'     => $language,
            'author_id'    => $authorId,
            'category_id'  => $categoryId,
            'editorial_id' => $editorialId,
            'status'       => $status,
        ];

        if ($id) {
            $this->bookModel->update($id, $bookData);
            $this->json(['success' => true, 'message' => 'Libro actualizado correctamente.']);
        } else {
            $newId = $this->bookModel->insert($bookData);
            $this->json(['success' => true, 'message' => 'Libro registrado correctamente.', 'id' => $newId]);
        }
    }

    /** GET /admin/books/edit?id=N — Datos para el modal de edición */
    public function edit(): void
    {
        $this->requireRole('admin');
        $id   = (int) ($_GET['id'] ?? 0);
        $book = $this->bookModel->findWithDetails($id);
        if (!$book) {
            $this->json(['success' => false, 'message' => 'Libro no encontrado.'], 404);
        }
        $this->json(['success' => true, 'data' => $book]);
    }

    /** POST /admin/books/delete */
    public function delete(): void
    {
        $this->requireRole('admin');
        $this->requirePost();
        $this->validateCsrf();

        $id   = (int) ($_POST['id_book'] ?? 0);
        $book = $this->bookModel->find($id);
        if ($book) {
            @unlink(BOOKS_PATH  . $book['pdf_file']);
            if ($book['cover_image'] !== 'no-cover.png') {
                @unlink(COVERS_PATH . $book['cover_image']);
            }
            $this->bookModel->delete($id);
        }
        $this->json(['success' => true, 'message' => 'Libro eliminado.']);
    }

    /** POST /api/books/view — Registrar vista (AJAX) */
    public function registerView(): void
    {
        $this->requireAuth();
        $bookId = (int) ($_POST['book_id'] ?? 0);
        if ($bookId) {
            $this->bookModel->incrementViews($bookId);
            require_once BASE_PATH . 'app/models/History.php';
            (new History())->addEntry(Session::userId(), $bookId, 'read');
        }
        $this->json(['success' => true]);
    }

    /** GET /api/books/search — Búsqueda AJAX */
    public function search(): void
    {
        $this->requireAuth();
        $term       = trim($_GET['q']           ?? '');
        $categoryId = (int) ($_GET['category']  ?? 0);
        $authorId   = (int) ($_GET['author']    ?? 0);
        $books      = $this->bookModel->search($term, $categoryId, $authorId);
        $this->json(['success' => true, 'data' => $books]);
    }

    // ─── HELPERS PRIVADOS ────────────────────────────────────────────────────

    private function uploadPdf(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Error al subir el PDF.'];
        }
        if ($file['size'] > 50 * 1024 * 1024) { // 50 MB
            return ['success' => false, 'message' => 'El PDF no debe superar 50 MB.'];
        }
        $mime = mime_content_type($file['tmp_name']);
        if ($mime !== 'application/pdf') {
            return ['success' => false, 'message' => 'Solo se permiten archivos PDF.'];
        }
        $filename = 'book_' . uniqid() . '.pdf';
        if (!is_dir(BOOKS_PATH)) {
            mkdir(BOOKS_PATH, 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], BOOKS_PATH . $filename)) {
            return ['success' => false, 'message' => 'No se pudo guardar el PDF.'];
        }
        return ['success' => true, 'filename' => $filename];
    }

    private function uploadCover(array $file): array
    {
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false];
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false];
        }
        if (!in_array(mime_content_type($file['tmp_name']), $allowedMime, true)) {
            return ['success' => false];
        }
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'cover_' . uniqid() . '.' . $ext;
        if (!is_dir(COVERS_PATH)) {
            mkdir(COVERS_PATH, 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], COVERS_PATH . $filename)) {
            return ['success' => false];
        }
        return ['success' => true, 'filename' => $filename];
    }
}
