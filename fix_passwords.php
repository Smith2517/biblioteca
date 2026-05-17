<?php
/**
 * Script de utilidad: actualiza las contraseñas de los usuarios semilla.
 * Acceder UNA SOLA VEZ en: http://localhost/biblioteca/fix_passwords.php
 * ¡ELIMINAR este archivo después de ejecutarlo!
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db   = Database::getInstance();
$hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);

$stmt = $db->prepare("UPDATE tb_users SET password = ? WHERE email IN ('admin@biblioteca.com','teacher@biblioteca.com','student@biblioteca.com')");
$stmt->execute([$hash]);

echo '<div style="font-family:monospace;padding:20px;background:#0d0d1a;color:#10b981;border-radius:8px;">';
echo '✅ Contraseñas actualizadas correctamente.<br>';
echo '<strong>Credenciales de acceso:</strong><br>';
echo '📧 admin@biblioteca.com / admin123 (Admin)<br>';
echo '📧 teacher@biblioteca.com / admin123 (Docente)<br>';
echo '📧 student@biblioteca.com / admin123 (Estudiante)<br><br>';
echo '<span style="color:#ef4444">⚠️ ELIMINA ESTE ARCHIVO INMEDIATAMENTE: /fix_passwords.php</span>';
echo '</div>';
echo '<br><a href="' . BASE_URL . 'auth/login" style="color:#6366f1">→ Ir al Login</a>';
