<?php
/**
 * app/models/User.php
 * Modelo de usuarios — CRUD + permisos + autenticación.
 */
class User extends Model
{
    protected string $table = 'tb_users';
    protected string $pk    = 'id_user';

    /**
     * Obtiene todos los usuarios con su rol.
     */
    public function getAllWithRole(): array
    {
        return $this->query(
            "SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM tb_users u
             INNER JOIN tb_roles r ON r.id_role = u.role_id
             ORDER BY u.created_at DESC"
        );
    }

    /**
     * Busca un usuario por email (para login).
     */
    public function findByEmail(string $email): array|false
    {
        return $this->queryOne(
            "SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM tb_users u
             INNER JOIN tb_roles r ON r.id_role = u.role_id
             WHERE u.email = ? AND u.status = 1",
            [$email]
        );
    }

    /**
     * Busca un usuario por ID con datos del rol.
     */
    public function findWithRole(int $id): array|false
    {
        return $this->queryOne(
            "SELECT u.*, r.name AS role_name, r.slug AS role_slug
             FROM tb_users u
             INNER JOIN tb_roles r ON r.id_role = u.role_id
             WHERE u.id_user = ?",
            [$id]
        );
    }

    /**
     * Crea un nuevo usuario con contraseña hasheada.
     */
    public function createUser(array $data): string
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->insert($data);
    }

    /**
     * Actualiza los permisos de lectura/descarga de un usuario.
     */
    public function updatePermissions(int $userId, bool $canRead, bool $canDownload): int
    {
        return $this->execute(
            "UPDATE tb_users SET can_read = ?, can_download = ? WHERE id_user = ?",
            [(int) $canRead, (int) $canDownload, $userId]
        );
    }

    /**
     * Activa o desactiva un usuario.
     */
    public function toggleStatus(int $userId): int
    {
        return $this->execute(
            "UPDATE tb_users SET status = IF(status=1,0,1) WHERE id_user = ?",
            [$userId]
        );
    }

    /**
     * Cambia la contraseña de un usuario.
     */
    public function changePassword(int $userId, string $newPassword): int
    {
        return $this->execute(
            "UPDATE tb_users SET password = ? WHERE id_user = ?",
            [password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $userId]
        );
    }

    /**
     * Guarda el token de restablecimiento de contraseña.
     */
    public function setResetToken(string $email, string $token, string $expires): int
    {
        return $this->execute(
            "UPDATE tb_users SET reset_token = ?, reset_expires = ? WHERE email = ?",
            [$token, $expires, $email]
        );
    }

    /**
     * Busca usuario por token de reset válido (no expirado).
     */
    public function findByResetToken(string $token): array|false
    {
        return $this->queryOne(
            "SELECT * FROM tb_users
             WHERE reset_token = ? AND reset_expires > NOW() AND status = 1",
            [$token]
        );
    }

    /**
     * Limpia el token de reset tras usarlo.
     */
    public function clearResetToken(int $userId): int
    {
        return $this->execute(
            "UPDATE tb_users SET reset_token = NULL, reset_expires = NULL WHERE id_user = ?",
            [$userId]
        );
    }

    /**
     * Estadísticas para el dashboard admin.
     */
    public function countByRole(): array
    {
        return $this->query(
            "SELECT r.name, r.slug, COUNT(u.id_user) AS total
             FROM tb_roles r
             LEFT JOIN tb_users u ON u.role_id = r.id_role AND u.status = 1
             GROUP BY r.id_role"
        );
    }

    /**
     * Verifica si el email ya existe (excluyendo un ID).
     */
    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $row = $this->queryOne(
            "SELECT id_user FROM tb_users WHERE email = ? AND id_user != ?",
            [$email, $excludeId]
        );
        return (bool) $row;
    }
}
