<?php
/** app/controllers/HistoryController.php */
class HistoryController extends Controller
{
    public function __construct() { parent::__construct(); $this->requireAuth(); }

    public function add(): void
    {
        $this->requirePost();
        require_once BASE_PATH . 'app/models/History.php';
        $bookId = (int) ($_POST['book_id'] ?? 0);
        $action = in_array($_POST['action'] ?? '', ['read','download']) ? $_POST['action'] : 'read';
        if ($bookId) {
            (new History())->addEntry(Session::userId(), $bookId, $action);
        }
        $this->json(['success' => true]);
    }
}
