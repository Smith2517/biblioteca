<?php
/** app/models/Comment.php */
class Comment extends Model
{
    protected string $table = 'tb_comments';
    protected string $pk    = 'id_comment';

    public function getByBook(int $bookId): array
    {
        return $this->query(
            "SELECT c.*, CONCAT(u.names, ' ', u.surnames) AS user_name, u.photo AS user_photo
             FROM tb_comments c
             INNER JOIN tb_users u ON u.id_user = c.user_id
             WHERE c.book_id = ? AND c.status = 1
             ORDER BY c.created_at DESC",
            [$bookId]
        );
    }

    public function addComment(int $userId, int $bookId, string $comment, ?int $rating = null): string
    {
        return $this->insert([
            'user_id' => $userId,
            'book_id' => $bookId,
            'comment' => $comment,
            'rating'  => $rating,
        ]);
    }

    public function avgRating(int $bookId): float
    {
        $row = $this->queryOne(
            "SELECT AVG(rating) AS avg FROM tb_comments WHERE book_id = ? AND status = 1 AND rating IS NOT NULL",
            [$bookId]
        );
        return round((float) ($row['avg'] ?? 0), 1);
    }
}
