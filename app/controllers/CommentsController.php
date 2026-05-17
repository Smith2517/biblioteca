<?php
/** app/controllers/CommentsController.php */
class CommentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    public function store(): void
    {
        $this->requirePost();
        require_once BASE_PATH . 'app/models/Comment.php';
        $bookId  = (int) ($_POST['book_id'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $rating  = isset($_POST['rating']) ? (int) $_POST['rating'] : null;

        if (!$bookId || !$comment) {
            $this->json(['success' => false, 'message' => 'Datos incompletos.'], 422);
        }
        if ($rating !== null && ($rating < 1 || $rating > 5)) {
            $rating = null;
        }

        (new Comment())->addComment(Session::userId(), $bookId, $comment, $rating);
        $this->json(['success' => true, 'message' => 'Comentario publicado.']);
    }

    public function delete(): void
    {
        $this->requirePost();
        require_once BASE_PATH . 'app/models/Comment.php';
        $id = (int) ($_POST['id_comment'] ?? 0);
        // Solo admin puede eliminar cualquier comentario
        if (!Session::hasRole('admin')) {
            $this->json(['success' => false, 'message' => 'Sin permiso.'], 403);
        }
        (new Comment())->delete($id);
        $this->json(['success' => true, 'message' => 'Comentario eliminado.']);
    }
}
