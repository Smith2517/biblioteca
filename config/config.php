<?php
/**
 * config/config.php
 * Configuración global del sistema — rutas dinámicas, constantes de app.
 * Funciona tanto en localhost (XAMPP) como en servidor de producción sin cambios.
 */

// ─── BASE URL DINÁMICA ───────────────────────────────────────────────────────
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
// dirname del script para calcular el subdirectorio (ej: /biblioteca)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base      = rtrim($scriptDir, '/');
define('BASE_URL',  $protocol . '://' . $host . $base . '/');

// ─── CONSTANTES BASE (definir primero para usar en rutas) ───────────────────
define('DS',          DIRECTORY_SEPARATOR);
define('APP_NAME',    'BiblioVirtual');
define('APP_VERSION', '1.0.0');

// Ruta absoluta del proyecto en el sistema de archivos (directorio raíz = padre de /config)
define('BASE_PATH', dirname(__DIR__) . DS);
// Equivale a: c:\xampp\htdocs\biblioteca\  en local
// En producción sería: /var/www/html/biblioteca/  (se ajusta automáticamente)

// ─── RUTAS DE UPLOADS ────────────────────────────────────────────────────────
define('UPLOAD_PATH',  BASE_PATH . 'public' . DS . 'uploads' . DS);
define('BOOKS_PATH',   UPLOAD_PATH . 'books' . DS);
define('COVERS_PATH',  UPLOAD_PATH . 'covers' . DS);

// URLs públicas de uploads
define('UPLOAD_URL',  BASE_URL . 'public/uploads/');
define('BOOKS_URL',   UPLOAD_URL . 'books/');
define('COVERS_URL',  UPLOAD_URL . 'covers/');

// ─── CONFIGURACIÓN DE BASE DE DATOS ──────────────────────────────────────────
define('DB_HOST',     'localhost');
define('DB_NAME',     'bd_biblioteca');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_CHARSET',  'utf8mb4');

// ─── CONFIGURACIÓN DE CORREO (PHPMailer) ─────────────────────────────────────
// Completar con tus credenciales SMTP antes de usar la recuperación de contraseña
define('MAIL_HOST',       'smtp.tudominio.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'no-reply@tudominio.com');
define('MAIL_PASSWORD',   'tu_password_smtp');
define('MAIL_FROM_NAME',  APP_NAME);
define('MAIL_ENCRYPTION', 'tls'); // 'tls' o 'ssl'

// ─── ZONA HORARIA ────────────────────────────────────────────────────────────
date_default_timezone_set('America/Lima');

// ─── MODO DEBUG (desactivar en producción) ───────────────────────────────────
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
