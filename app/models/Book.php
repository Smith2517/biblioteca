<?php
/**
 * app/models/Book.php
 * Modelo de libros — CRUD + búsqueda + estadísticas.
 */
class Book extends Model
{
    protected string $table = 'tb_books';
    protected string $pk    = 'id_book';

    /**
     * Obtiene todos los libros con datos relacionados (autor, categoría, editorial).
     */
    public function getAllWithDetails(): array
    {
        return $this->query(
            "SELECT b.*,
                    a.name  AS author_name,
                    c.name  AS category_name,
                    c.color AS category_color,
                    e.name  AS editorial_name
             FROM tb_books b
             INNER JOIN tb_authors    a ON a.id_author    = b.author_id
             INNER JOIN tb_categories c ON c.id_category  = b.category_id
             INNER JOIN tb_editorials e ON e.id_editorial = b.editorial_id
             WHERE b.status = 1
             ORDER BY b.created_at DESC"
        );
    }

    /**
     * Obtiene un libro con todos sus detalles.
     */
    public function findWithDetails(int $id): array|false
    {
        return $this->queryOne(
            "SELECT b.*,
                    a.name  AS author_name,
                    c.name  AS category_name,
                    c.color AS category_color,
                    e.name  AS editorial_name
             FROM tb_books b
             INNER JOIN tb_authors    a ON a.id_author    = b.author_id
             INNER JOIN tb_categories c ON c.id_category  = b.category_id
             INNER JOIN tb_editorials e ON e.id_editorial = b.editorial_id
             WHERE b.id_book = ?",
            [$id]
        );
    }

    /**
     * Búsqueda de libros por texto, categoría, autor.
     */
    public function search(string $term = '', int $categoryId = 0, int $authorId = 0): array
    {
        $sql    = "SELECT b.*,
                          a.name  AS author_name,
                          c.name  AS category_name,
                          c.color AS category_color,
                          e.name  AS editorial_name
                   FROM tb_books b
                   INNER JOIN tb_authors    a ON a.id_author    = b.author_id
                   INNER JOIN tb_categories c ON c.id_category  = b.category_id
                   INNER JOIN tb_editorials e ON e.id_editorial = b.editorial_id
                   WHERE b.status = 1";
        $params = [];

        if ($term) {
            $sql     .= " AND (b.title LIKE ? OR b.description LIKE ? OR a.name LIKE ?)";
            $like     = "%{$term}%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($categoryId) {
            $sql     .= " AND b.category_id = ?";
            $params[] = $categoryId;
        }
        if ($authorId) {
            $sql     .= " AND b.author_id = ?";
            $params[] = $authorId;
        }

        $sql .= " ORDER BY b.views DESC LIMIT 100";
        return $this->query($sql, $params);
    }

    /**
     * Libros más vistos.
     */
    public function getMostViewed(int $limit = 8): array
    {
        return $this->query(
            "SELECT b.*, a.name AS author_name, c.name AS category_name, c.color AS category_color
             FROM tb_books b
             INNER JOIN tb_authors a ON a.id_author = b.author_id
             INNER JOIN tb_categories c ON c.id_category = b.category_id
             WHERE b.status = 1
             ORDER BY b.views DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Libros más recientes.
     */
    public function getRecent(int $limit = 8): array
    {
        return $this->query(
            "SELECT b.*, a.name AS author_name, c.name AS category_name, c.color AS category_color
             FROM tb_books b
             INNER JOIN tb_authors a ON a.id_author = b.author_id
             INNER JOIN tb_categories c ON c.id_category = b.category_id
             WHERE b.status = 1
             ORDER BY b.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Incrementa el contador de vistas.
     */
    public function incrementViews(int $bookId): void
    {
        $this->execute(
            "UPDATE tb_books SET views = views + 1 WHERE id_book = ?",
            [$bookId]
        );
    }

    /**
     * Incrementa el contador de descargas.
     */
    public function incrementDownloads(int $bookId): void
    {
        $this->execute(
            "UPDATE tb_books SET downloads = downloads + 1 WHERE id_book = ?",
            [$bookId]
        );
    }

    /**
     * Estadísticas para el dashboard.
     */
    public function getStats(): array
    {
        return $this->queryOne(
            "SELECT
                COUNT(*) AS total,
                SUM(views) AS total_views,
                SUM(downloads) AS total_downloads
             FROM tb_books WHERE status = 1"
        );
    }

    /**
     * Libros por categoría (para el gráfico donut).
     */
    public function countByCategory(): array
    {
        return $this->query(
            "SELECT c.name, c.color, COUNT(b.id_book) AS total
             FROM tb_categories c
             LEFT JOIN tb_books b ON b.category_id = c.id_category AND b.status = 1
             WHERE c.status = 1
             GROUP BY c.id_category
             ORDER BY total DESC"
        );
    }
}
