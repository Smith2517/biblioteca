<?php
/**
 * app/controllers/UsersController.php
 * CRUD de usuarios + gestión de permisos (can_read / can_download).
 */
class UsersController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin');
        require_once BASE_PATH . 'app/models/User.php';
        $this->userModel = new User();
    }

    /** GET /admin/users — Lista de usuarios */
    public function index(): void
    {
        $roles = $this->db->query("SELECT * FROM tb_roles ORDER BY id_role")->fetchAll();
        $this->render('admin/users', [
            'title' => 'Gestión de Usuarios',
            'users' => $this->userModel->getAllWithRole(),
            'roles' => $roles,
        ], 'admin');
    }

    /** POST /admin/users/store — Crear o actualizar usuario */
    public function store(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $id      = (int) ($_POST['id_user'] ?? 0);
        $names   = trim($_POST['names']    ?? '');
        $surnames= trim($_POST['surnames'] ?? '');
        $email   = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $roleId  = (int) ($_POST['role_id'] ?? 3);
        $status  = isset($_POST['status']) ? 1 : 0;

        // Validaciones
        if (!$names || !$surnames || !$email) {
            $this->json(['success' => false, 'message' => 'Completa todos los campos obligatorios.'], 422);
        }

        if ($this->userModel->emailExists($email, $id)) {
            $this->json(['success' => false, 'message' => 'El email ya está registrado.'], 422);
        }

        // Manejar foto
        $photo = 'default.png';
        if ($id) {
            $existing = $this->userModel->find($id);
            $photo    = $existing['photo'] ?? 'default.png';
        }

        if (!empty($_FILES['photo']['name'])) {
            $upload = $this->uploadPhoto($_FILES['photo']);
            if ($upload['success']) {
                $photo = $upload['filename'];
                // Eliminar foto anterior si no es la default
                if ($id && $existing['photo'] !== 'default.png') {
                    @unlink(BASE_PATH . 'public/uploads/covers/' . $existing['photo']);
                }
            }
        }

        $userData = [
            'names'    => $names,
            'surnames' => $surnames,
            'email'    => $email,
            'role_id'  => $roleId,
            'photo'    => $photo,
            'status'   => $status,
        ];

        if ($id) {
            // Actualizar
            if (!empty($_POST['password'])) {
                $userData['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            }
            $this->userModel->update($id, $userData);
            $this->json(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
        } else {
            // Crear
            if (empty($_POST['password'])) {
                $this->json(['success' => false, 'message' => 'La contraseña es obligatoria para nuevos usuarios.'], 422);
            }
            $userData['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $newId = $this->userModel->insert($userData);
            $this->json(['success' => true, 'message' => 'Usuario creado correctamente.', 'id' => $newId]);
        }
    }

    /** POST /admin/users/edit — Obtiene datos de un usuario para editar (AJAX) */
    public function edit(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $user = $this->userModel->findWithRole($id);
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
        }
        unset($user['password'], $user['reset_token']);
        $this->json(['success' => true, 'data' => $user]);
    }

    /** POST /admin/users/delete — Eliminar usuario */
    public function delete(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $id = (int) ($_POST['id_user'] ?? 0);
        if ($id === (int) Session::userId()) {
            $this->json(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta.'], 403);
        }

        $this->userModel->delete($id);
        $this->json(['success' => true, 'message' => 'Usuario eliminado.']);
    }

    /** POST /admin/users/toggle — Activa/inactiva un usuario */
    public function toggleStatus(): void
    {
        $this->requirePost();
        $id = (int) ($_POST['id_user'] ?? 0);
        $this->userModel->toggleStatus($id);
        $this->json(['success' => true, 'message' => 'Estado actualizado.']);
    }

    /**
     * POST /admin/users/perms — Actualiza permisos can_read / can_download
     * El administrador puede dar permisos específicos por usuario.
     */
    public function updatePermissions(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $id          = (int) ($_POST['id_user']      ?? 0);
        $canRead     = isset($_POST['can_read'])     ? true : false;
        $canDownload = isset($_POST['can_download']) ? true : false;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID de usuario inválido.'], 422);
        }

        $this->userModel->updatePermissions($id, $canRead, $canDownload);
        $this->json([
            'success' => true,
            'message' => 'Permisos actualizados correctamente.',
            'can_read'     => $canRead,
            'can_download' => $canDownload,
        ]);
    }

    /** Sube una foto de perfil — retorna ['success'=>bool, 'filename'=>string] */
    private function uploadPhoto(array $file): array
    {
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize     = 2 * 1024 * 1024; // 2 MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Error al subir el archivo.'];
        }
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'La foto no debe superar 2 MB.'];
        }

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowedMime, true)) {
            return ['success' => false, 'message' => 'Formato de imagen no permitido.'];
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . uniqid() . '.' . strtolower($ext);
        $dest     = BASE_PATH . 'public/uploads/covers/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'message' => 'No se pudo guardar la foto.'];
        }

        return ['success' => true, 'filename' => $filename];
    }
}
