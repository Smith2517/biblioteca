<?php
/**
 * app/controllers/FavoritesController.php
 * Maneja los favoritos de los usuarios.
 */
class FavoritesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    /** POST /api/favorites/toggle */
    public function toggle(): void
    {
        $this->requirePost();
        $bookId = (int) ($_POST['book_id'] ?? 0);
        if (!$bookId) {
            $this->json(['success' => false, 'message' => 'ID de libro inválido.'], 422);
        }
        require_once BASE_PATH . 'app/models/Favorite.php';
        $favModel = new Favorite();
        $added    = $favModel->toggle(Session::userId(), $bookId);
        $this->json([
            'success' => true,
            'added'   => $added,
            'message' => $added ? 'Agregado a favoritos.' : 'Eliminado de favoritos.',
        ]);
    }
}
