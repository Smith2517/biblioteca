<?php
/** app/controllers/CategoriesController.php */
class CategoriesController extends Controller
{
    public function __construct() { parent::__construct(); $this->requireRole('admin'); }

    public function index(): void
    {
        require_once BASE_PATH . 'app/models/Category.php';
        $this->render('admin/categories', [
            'title'      => 'Categorías',
            'categories' => (new Category())->all('name ASC'),
        ], 'admin');
    }

    public function store(): void
    {
        $this->requirePost(); $this->validateCsrf();
        require_once BASE_PATH . 'app/models/Category.php';
        $cat  = new Category();
        $id   = (int) ($_POST['id_category'] ?? 0);
        $data = [
            'name'        => trim($_POST['name']        ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'color'       => $_POST['color'] ?? '#6366f1',
            'status'      => isset($_POST['status']) ? 1 : 0,
        ];
        if (!$data['name']) { $this->json(['success'=>false,'message'=>'El nombre es obligatorio.'],422); }
        $id ? $cat->update($id, $data) : $cat->insert($data);
        $this->json(['success'=>true,'message'=> $id ? 'Categoría actualizada.' : 'Categoría creada.']);
    }

    public function edit(): void
    {
        require_once BASE_PATH . 'app/models/Category.php';
        $row = (new Category())->find((int)($_GET['id']??0));
        $row ? $this->json(['success'=>true,'data'=>$row]) : $this->json(['success'=>false,'message'=>'No encontrado.'],404);
    }

    public function delete(): void
    {
        $this->requirePost(); $this->validateCsrf();
        require_once BASE_PATH . 'app/models/Category.php';
        (new Category())->delete((int)($_POST['id_category']??0));
        $this->json(['success'=>true,'message'=>'Categoría eliminada.']);
    }
}
