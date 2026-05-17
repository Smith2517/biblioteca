<?php
/** app/controllers/AuthorsController.php */
class AuthorsController extends Controller
{
    public function __construct() { parent::__construct(); $this->requireRole('admin'); }

    public function index(): void
    {
        require_once BASE_PATH . 'app/models/Author.php';
        $this->render('admin/authors', [
            'title'   => 'Autores',
            'authors' => (new Author())->withBookCount(),
        ], 'admin');
    }

    public function store(): void
    {
        $this->requirePost(); $this->validateCsrf();
        require_once BASE_PATH . 'app/models/Author.php';
        $author = new Author();
        $id     = (int) ($_POST['id_author'] ?? 0);
        $data   = [
            'name'   => trim($_POST['name'] ?? ''),
            'bio'    => trim($_POST['bio']  ?? ''),
            'status' => isset($_POST['status']) ? 1 : 0,
        ];
        if (!$data['name']) { $this->json(['success'=>false,'message'=>'El nombre es obligatorio.'],422); }
        $id ? $author->update($id, $data) : $author->insert($data);
        $this->json(['success'=>true,'message'=> $id ? 'Autor actualizado.' : 'Autor creado.']);
    }

    public function edit(): void
    {
        require_once BASE_PATH . 'app/models/Author.php';
        $row = (new Author())->find((int)($_GET['id']??0));
        $row ? $this->json(['success'=>true,'data'=>$row]) : $this->json(['success'=>false,'message'=>'No encontrado.'],404);
    }

    public function delete(): void
    {
        $this->requirePost(); $this->validateCsrf();
        require_once BASE_PATH . 'app/models/Author.php';
        (new Author())->delete((int)($_POST['id_author']??0));
        $this->json(['success'=>true,'message'=>'Autor eliminado.']);
    }
}
