<?php
/**
 * app/controllers/AdminController.php
 * Dashboard y estadísticas del administrador.
 */
class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin');
    }

    /** GET /admin/dashboard */
    public function dashboard(): void
    {
        require_once BASE_PATH . 'app/models/Book.php';
        require_once BASE_PATH . 'app/models/User.php';

        $bookModel = new Book();
        $userModel = new User();

        $data = [
            'title'         => 'Dashboard — Admin',
            'bookStats'     => $bookModel->getStats(),
            'usersByRole'   => $userModel->countByRole(),
            'mostViewed'    => $bookModel->getMostViewed(5),
            'recentBooks'   => $bookModel->getRecent(5),
            'booksByCategory' => $bookModel->countByCategory(),
            'totalUsers'    => $userModel->count('status = 1'),
            'totalBooks'    => $bookModel->count('status = 1'),
        ];

        $this->render('admin/dashboard', $data, 'admin');
    }

    /** GET /admin/reports */
    public function reports(): void
    {
        require_once BASE_PATH . 'app/models/Book.php';
        $bookModel = new Book();

        $data = [
            'title'         => 'Reportes',
            'mostViewed'    => $bookModel->getMostViewed(20),
            'booksByCategory' => $bookModel->countByCategory(),
        ];
        $this->render('admin/reports', $data, 'admin');
    }

    /** GET /api/stats — Datos JSON para Chart.js */
    public function stats(): void
    {
        $this->requireRole('admin');
        require_once BASE_PATH . 'app/models/Book.php';
        $bookModel = new Book();

        $this->json([
            'byCategory' => $bookModel->countByCategory(),
            'mostViewed' => $bookModel->getMostViewed(10),
        ]);
    }
}
