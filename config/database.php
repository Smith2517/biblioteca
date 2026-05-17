<?php
/**
 * config/database.php
 * Conexión PDO Singleton — una sola instancia durante toda la petición.
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Retorna la instancia única de PDO.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST
                     . ';dbname=' . DB_NAME
                     . ';charset=' . DB_CHARSET;

                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    die('<div style="font-family:monospace;padding:20px;background:#1a1a2e;color:#e94560;border-radius:8px;">
                        <strong>❌ Error de conexión a la base de datos:</strong><br>' 
                        . htmlspecialchars($e->getMessage()) . '</div>');
                } else {
                    die('Error de conexión. Contacte al administrador.');
                }
            }
        }
        return self::$instance;
    }

    // Prevenir clonación y deserialización
    private function __construct() {}
    private function __clone() {}
}
