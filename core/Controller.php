<?php
/**
 * core/Controller.php
 * Controlador base — todos los controladores extienden esta clase.
 */
class Controller
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Carga un modelo y lo retorna.
     *
     * @param  string $model Nombre de la clase del modelo (ej: 'User')
     * @return object
     */
    protected function model(string $model): object
    {
        $file = BASE_PATH . 'app' . DS . 'models' . DS . $model . '.php';
        require_once $file;
        return new $model();
    }

    /**
     * Renderiza una vista con datos opcionales.
     *
     * @param  string $view  Ruta relativa dentro de /app/views/ (ej: 'admin/dashboard')
     * @param  array  $data  Variables a extraer en la vista
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = BASE_PATH . 'app' . DS . 'views' . DS . str_replace('/', DS, $view) . '.php';

        if (!file_exists($viewFile)) {
            die("Vista no encontrada: {$viewFile}");
        }

        require_once $viewFile;
    }

    /**
     * Renderiza una vista envuelta en el layout completo (header + sidebar + footer).
     *
     * @param  string $view     Ruta relativa a la vista de contenido
     * @param  array  $data     Variables para la vista
     * @param  string $layout   Layout a usar: 'admin', 'teacher', 'student', 'auth'
     */
    protected function render(string $view, array $data = [], string $layout = 'admin'): void
    {
        extract($data);
        $data['currentView'] = $view;
        $data['layout']      = $layout;

        $viewFile = BASE_PATH . 'app' . DS . 'views' . DS . str_replace('/', DS, $view) . '.php';
        if (!file_exists($viewFile)) {
            die("Vista no encontrada: {$viewFile}");
        }

        // Capturar el contenido de la vista
        ob_start();
        extract($data);
        require $viewFile;
        $content = ob_get_clean();

        // Cargar el layout correspondiente
        extract($data);
        require BASE_PATH . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';

        if ($layout === 'auth') {
            echo $content;
        } else {
            require BASE_PATH . 'app' . DS . 'views' . DS . 'layouts' . DS . 'navbar.php';
            echo '<div class="wrapper d-flex">';
            require BASE_PATH . 'app' . DS . 'views' . DS . 'layouts' . DS . 'sidebar_' . $layout . '.php';
            echo '<div class="main-content flex-grow-1">' . $content . '</div>';
            echo '</div>';
        }

        require BASE_PATH . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php';
    }

    /**
     * Redirige a una URL (relativa a BASE_URL).
     *
     * @param string $path  Ruta relativa, ej: 'auth/login'
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . ltrim($path, '/'));
        exit;
    }

    /**
     * Responde con JSON (para peticiones AJAX).
     *
     * @param mixed $data
     * @param int   $statusCode
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Verifica que el usuario esté autenticado; si no, redirige al login.
     */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            Session::setFlash('error', 'Debes iniciar sesión para acceder.');
            $this->redirect('auth/login');
        }
    }

    /**
     * Verifica que el usuario tenga el rol requerido.
     *
     * @param string|array $roles  Rol(es) permitidos: 'admin', 'teacher', 'student'
     */
    protected function requireRole(string|array $roles): void
    {
        $this->requireAuth();
        $roles = (array) $roles;

        if (!in_array($_SESSION['user_role'], $roles, true)) {
            Session::setFlash('error', 'No tienes permiso para acceder a esta sección.');
            $this->redirect('auth/login');
        }
    }

    /**
     * Valida el token CSRF de un formulario POST.
     */
    protected function validateCsrf(): void
    {
        if (!Session::validateCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token de seguridad inválido. Intenta nuevamente.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '');
        }
    }

    /**
     * Verifica que la petición sea POST.
     */
    protected function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
        }
    }
}
