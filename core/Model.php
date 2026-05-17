<?php
/**
 * core/Model.php
 * Modelo base — helpers de consultas PDO reutilizables.
 */
class Model
{
    protected PDO    $db;
    protected string $table  = '';
    protected string $pk     = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ─── HELPERS DE CONSULTA ─────────────────────────────────────────────────

    /**
     * Ejecuta una consulta y retorna todos los resultados.
     */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta una consulta y retorna el primer resultado.
     */
    protected function queryOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Ejecuta una consulta de escritura (INSERT, UPDATE, DELETE).
     * Retorna el número de filas afectadas.
     */
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Retorna el último ID insertado.
     */
    protected function lastId(): string
    {
        return $this->db->lastInsertId();
    }

    // ─── CRUD GENÉRICO ───────────────────────────────────────────────────────

    /**
     * Obtiene todos los registros de la tabla del modelo.
     */
    public function all(string $orderBy = ''): array
    {
        $order = $orderBy ? "ORDER BY {$orderBy}" : '';
        return $this->query("SELECT * FROM {$this->table} {$order}");
    }

    /**
     * Busca un registro por su PK.
     */
    public function find(int|string $id): array|false
    {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE {$this->pk} = ?",
            [$id]
        );
    }

    /**
     * Inserta un registro. Recibe un array asociativo [columna => valor].
     */
    public function insert(array $data): string
    {
        $cols   = implode(', ', array_keys($data));
        $places = implode(', ', array_fill(0, count($data), '?'));
        $this->execute(
            "INSERT INTO {$this->table} ({$cols}) VALUES ({$places})",
            array_values($data)
        );
        return $this->lastId();
    }

    /**
     * Actualiza un registro por su PK.
     */
    public function update(int|string $id, array $data): int
    {
        $sets = implode(' = ?, ', array_keys($data)) . ' = ?';
        return $this->execute(
            "UPDATE {$this->table} SET {$sets} WHERE {$this->pk} = ?",
            [...array_values($data), $id]
        );
    }

    /**
     * Elimina (o desactiva) un registro por su PK.
     */
    public function delete(int|string $id): int
    {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE {$this->pk} = ?",
            [$id]
        );
    }

    /**
     * Cuenta los registros de la tabla (con filtro opcional).
     */
    public function count(string $where = '', array $params = []): int
    {
        $sql  = "SELECT COUNT(*) as total FROM {$this->table}";
        $sql .= $where ? " WHERE {$where}" : '';
        $row  = $this->queryOne($sql, $params);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Sanitiza un string para evitar XSS.
     */
    protected function clean(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
