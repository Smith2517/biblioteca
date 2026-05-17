<?php
/**
 * app/controllers/TeacherController.php
 * Dashboard y funcionalidades del Docente.
 */
class TeacherController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('teacher');
        require_once BASE_PATH . 'app/models/Book.php';
        require_once BASE_PATH . 'app/models/Category.php';
        require_once BASE_PATH . 'app/models/Author.php';
        require_once BASE_PATH . 'app/models/Favorite.php';
        require_once BASE_PATH . 'app/models/History.php';
        require_once BASE_PATH . 'app/models/Comment.php';
    }

    public function dashboard(): void
    {
        $bookModel = new Book();
        $this->render('teacher/dashboard', [
            'title'       => 'Mi Biblioteca — Docente',
            'recentBooks' => $bookModel->getRecent(8),
            'mostViewed'  => $bookModel->getMostViewed(4),
        ], 'teacher');
    }

    public function catalog(): void
    {
        $this->render('teacher/catalog', [
            'title'      => 'Catálogo de Libros',
            'books'      => (new Book())->getAllWithDetails(),
            'categories' => (new Category())->allActive(),
            'authors'    => (new Author())->allActive(),
        ], 'teacher');
    }

    public function favorites(): void
    {
        $this->render('teacher/favorites', [
            'title'     => 'Mis Favoritos',
            'favorites' => (new Favorite())->getByUser(Session::userId()),
        ], 'teacher');
    }

    public function history(): void
    {
        $this->render('teacher/history', [
            'title'   => 'Historial de Lectura',
            'history' => (new History())->getByUser(Session::userId()),
        ], 'teacher');
    }

    public function profile(): void
    {
        require_once BASE_PATH . 'app/models/User.php';
        $user = (new User())->findWithRole(Session::userId());
        $this->render('teacher/profile', ['title' => 'Mi Perfil', 'user' => $user], 'teacher');
    }

    public function read(): void
    {
        $bookId    = (int) ($_GET['id'] ?? 0);
        $bookModel = new Book();
        $book      = $bookModel->findWithDetails($bookId);

        if (!$book || !$book['status']) {
            Session::setFlash('error', 'Libro no encontrado.');
            $this->redirect('teacher/catalog');
        }

        // Verificar permiso de lectura
        if (!$_SESSION['can_read']) {
            Session::setFlash('error', 'No tienes permiso para leer libros.');
            $this->redirect('teacher/catalog');
        }

        $bookModel->incrementViews($bookId);
        (new History())->addEntry(Session::userId(), $bookId, 'read');

        $this->render('teacher/read-book', [
            'title'    => 'Leyendo: ' . $book['title'],
            'book'     => $book,
            'comments' => (new Comment())->getByBook($bookId),
            'isFav'    => (new Favorite())->isFavorite(Session::userId(), $bookId),
            'avgRating'=> (new Comment())->avgRating($bookId),
        ], 'teacher');
    }

    public function download(): void
    {
        $bookId = (int) ($_GET['id'] ?? 0);

        if (!$_SESSION['can_download']) {
            Session::setFlash('error', 'No tienes permiso para descargar libros.');
            $this->redirect('teacher/catalog');
        }

        $book = (new Book())->find($bookId);
        if (!$book) {
            Session::setFlash('error', 'Libro no encontrado.');
            $this->redirect('teacher/catalog');
        }

        $filePath = BOOKS_PATH . $book['pdf_file'];
        if (!file_exists($filePath)) {
            Session::setFlash('error', 'Archivo no disponible.');
            $this->redirect('teacher/catalog');
        }

        (new Book())->incrementDownloads($bookId);
        (new History())->addEntry(Session::userId(), $bookId, 'download');

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($book['pdf_file']) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
