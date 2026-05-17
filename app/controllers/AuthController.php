<?php
/**
 * app/controllers/AuthController.php
 * Maneja login, logout y recuperación de contraseña.
 */
class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        require_once BASE_PATH . 'app/models/User.php';
        $this->userModel = new User();
    }

    /** GET /auth/login — Muestra el formulario de login */
    public function index(): void
    {
        // Si ya está autenticado, redirigir a su dashboard
        if (Session::isLoggedIn()) {
            $this->redirectByRole(Session::userRole());
        }
        $this->render('auth/login', ['title' => 'Iniciar Sesión'], 'auth');
    }

    /** POST /auth/doLogin — Procesa el login */
    public function doLogin(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $email    = filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            Session::setFlash('error', 'Completa todos los campos.');
            $this->redirect('auth/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::setFlash('error', 'Credenciales incorrectas. Verifica tu email y contraseña.');
            $this->redirect('auth/login');
        }

        if (!$user['status']) {
            Session::setFlash('error', 'Tu cuenta está desactivada. Contacta al administrador.');
            $this->redirect('auth/login');
        }

        // Iniciar sesión
        Session::login($user);
        Session::setFlash('success', '¡Bienvenido, ' . $user['names'] . '!');
        $this->redirectByRole($user['role_slug']);
    }

    /** GET /auth/logout — Cierra la sesión */
    public function logout(): void
    {
        Session::logout();
        $this->redirect('auth/login');
    }

    /** GET /auth/forgot — Formulario de recuperación */
    public function forgot(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirectByRole(Session::userRole());
        }
        $this->render('auth/forgot-password', ['title' => 'Recuperar Contraseña'], 'auth');
    }

    /** POST /auth/doForgot — Envía el email de recuperación */
    public function doForgot(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        if (!$email) {
            Session::setFlash('error', 'Ingresa un email válido.');
            $this->redirect('auth/forgot');
        }

        $user = $this->userModel->findByEmail($email);

        // Siempre mostrar el mismo mensaje para no revelar si el email existe
        Session::setFlash('info', 'Si el email existe en el sistema, recibirás un enlace de recuperación.');

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->userModel->setResetToken($email, $token, $expires);
            $resetUrl = BASE_URL . 'auth/reset?token=' . $token;

            // TODO: Configurar PHPMailer con tus credenciales SMTP en config/config.php
            // $this->sendResetEmail($user, $resetUrl);
            // Por ahora, mostrar el enlace en modo debug
            if (DEBUG_MODE) {
                Session::setFlash('info', 'DEBUG — Enlace de reset: <a href="' . $resetUrl . '">' . $resetUrl . '</a>');
            }
        }

        $this->redirect('auth/forgot');
    }

    /** GET /auth/reset — Formulario de nueva contraseña */
    public function reset(): void
    {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            $this->redirect('auth/login');
        }

        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            Session::setFlash('error', 'El enlace de recuperación es inválido o ha expirado.');
            $this->redirect('auth/login');
        }

        $this->render('auth/reset-password', ['title' => 'Nueva Contraseña', 'token' => $token], 'auth');
    }

    /** POST /auth/doReset — Guarda la nueva contraseña */
    public function doReset(): void
    {
        $this->requirePost();
        $this->validateCsrf();

        $token    = $_POST['token']    ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';

        if (strlen($password) < 8) {
            Session::setFlash('error', 'La contraseña debe tener al menos 8 caracteres.');
            $this->redirect('auth/reset?token=' . $token);
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Las contraseñas no coinciden.');
            $this->redirect('auth/reset?token=' . $token);
        }

        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            Session::setFlash('error', 'Token inválido o expirado.');
            $this->redirect('auth/login');
        }

        $this->userModel->changePassword($user['id_user'], $password);
        $this->userModel->clearResetToken($user['id_user']);

        Session::setFlash('success', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
        $this->redirect('auth/login');
    }

    /** Redirige al dashboard según el rol */
    private function redirectByRole(string $role): void
    {
        match ($role) {
            'admin'   => $this->redirect('admin/dashboard'),
            'teacher' => $this->redirect('teacher/dashboard'),
            'student' => $this->redirect('student/dashboard'),
            default   => $this->redirect('auth/login'),
        };
    }
}
