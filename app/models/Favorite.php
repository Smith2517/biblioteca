<?php
/** app/models/Favorite.php */
class Favorite extends Model
{
    protected string $table = 'tb_favorites';
    protected string $pk    = 'id_favorite';

    public function getByUser(int $userId): array
    {
        return $this->query(
            "SELECT f.*, b.title, b.cover_image, b.year,
                    a.name AS author_name, c.name AS category_name, c.color AS category_color
             FROM tb_favorites f
             INNER JOIN tb_books      b ON b.id_book      = f.book_id
             INNER JOIN tb_authors    a ON a.id_author    = b.author_id
             INNER JOIN tb_categories c ON c.id_category  = b.category_id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC",
            [$userId]
        );
    }

    public function isFavorite(int $userId, int $bookId): bool
    {
        $row = $this->queryOne(
            "SELECT id_favorite FROM tb_favorites WHERE user_id = ? AND book_id = ?",
            [$userId, $bookId]
        );
        return (bool) $row;
    }

    public function toggle(int $userId, int $bookId): bool
    {
        if ($this->isFavorite($userId, $bookId)) {
            $this->execute(
                "DELETE FROM tb_favorites WHERE user_id = ? AND book_id = ?",
                [$userId, $bookId]
            );
            return false; // Removido
        } else {
            $this->insert(['user_id' => $userId, 'book_id' => $bookId]);
            return true;  // Agregado
        }
    }
}
