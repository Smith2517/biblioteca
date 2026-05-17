<?php
/** app/models/History.php */
class History extends Model
{
    protected string $table = 'tb_history';
    protected string $pk    = 'id_history';

    public function getByUser(int $userId): array
    {
        return $this->query(
            "SELECT h.*, b.title, b.cover_image, b.year,
                    a.name AS author_name, c.name AS category_name, c.color AS category_color
             FROM tb_history h
             INNER JOIN tb_books      b ON b.id_book     = h.book_id
             INNER JOIN tb_authors    a ON a.id_author   = b.author_id
             INNER JOIN tb_categories c ON c.id_category = b.category_id
             WHERE h.user_id = ?
             ORDER BY h.created_at DESC
             LIMIT 50",
            [$userId]
        );
    }

    public function addEntry(int $userId, int $bookId, string $action = 'read'): void
    {
        $this->insert([
            'user_id' => $userId,
            'book_id' => $bookId,
            'action'  => $action,
        ]);
    }
}
