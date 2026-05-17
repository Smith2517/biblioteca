<?php
/** app/controllers/EditorialsController.php */
class EditorialsController extends Controller
{
    public function __construct() { parent::__construct(); $this->requireRole('admin'); }

    public function index(): void
    {
        require_once BASE_PATH . 'app/models/Editorial.php';
        $this->render('admin/editorials', [
            'title'      => 'Editoriales',
            'editorials' => (new Editorial())->withBookCount(),
        ], 'admin');
    }

    public function store(): void
    {
        $this->requirePost(); $this->validateCsrf();
        require_once BASE_PATH . 'app/models/Editorial.php';
        $editorial = new Editorial();
        $id        = (int) ($_POST['id_editorial'] ?? 0);
        $data      = [
            'name'    => trim($_POST['name']    ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'website' => trim($_POST['website'] ?? ''),
            'status'  => isset($_POST['status']) ? 1 : 0,
        ];
        if (!$data['name']) { $this->json(['success'=>false,'message'=>'El nombre es obligatorio.'],422); }
        $id ? $editorial->update($id, $data) : $editorial->insert($data);
        $this->json(['success'=>true,'message'=> $id ? 'Editorial actualizada.' : 'Editorial creada.']);
    }

    public function edit(): void
    {
        require_once BASE_PATH . 'app/models/Editorial.php';
        $row = (new Editorial())->find((int)($_GET['id']??0));
        $row ? $this->json(['success'=>true,'data'=>$row]) : $this->json(['success'=>false,'message'=>'No encontrado.'],404);
    }

    public function delete(): void
    {
        $this->requirePost(); $this->validateCsrf();
        require_once BASE_PATH . 'app/models/Editorial.php';
        (new Editorial())->delete((int)($_POST['id_editorial']??0));
        $this->json(['success'=>true,'message'=>'Editorial eliminada.']);
    }
}
