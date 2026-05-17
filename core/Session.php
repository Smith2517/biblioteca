<?php
/**
 * core/Session.php
 * Manejo de sesiones PHP, flash messages y tokens CSRF.
 */
class Session
{
    /**
     * Guarda un flash message (se muestra una sola vez).
     *
     * @param string $type    'success' | 'error' | 'warning' | 'info'
     * @param string $message Texto del mensaje
     */
    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Recupera y elimina el flash message actual.
     *
     * @return array|null ['type' => ..., 'message' => ...]
     */
    public static function getFlash(): ?array
    {
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Genera un token CSRF y lo guarda en la sesión.
     */
    public static function generateCsrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida el token CSRF recibido.
     */
    public static function validateCsrf(string $token): bool
    {
        return !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Inicia la sesión de un usuario autenticado.
     */
    public static function login(array $user): void
    {
        session_regenerate_id(true); // Prevenir session fixation
        $_SESSION['user_id']       = $user['id_user'];
        $_SESSION['user_name']     = $user['names'] . ' ' . $user['surnames'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['user_role']     = $user['role_slug'];
        $_SESSION['user_photo']    = $user['photo'] ?? 'default.png';
        $_SESSION['can_read']      = (bool) $user['can_read'];
        $_SESSION['can_download']  = (bool) $user['can_download'];
        // Regenerar CSRF al iniciar sesión
        unset($_SESSION['csrf_token']);
        self::generateCsrf();
    }

    /**
     * Cierra la sesión completamente.
     */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Verifica si el usuario está autenticado.
     */
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Verifica si el usuario tiene un rol específico.
     */
    public static function hasRole(string $role): bool
    {
        return ($_SESSION['user_role'] ?? '') === $role;
    }

    /**
     * Obtiene el ID del usuario en sesión.
     */
    public static function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Obtiene el rol del usuario en sesión.
     */
    public static function userRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }
}
