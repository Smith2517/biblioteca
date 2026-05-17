<?php
/** app/models/Author.php */
class Author extends Model
{
    protected string $table = 'tb_authors';
    protected string $pk    = 'id_author';

    public function allActive(): array
    {
        return $this->query("SELECT * FROM tb_authors WHERE status = 1 ORDER BY name ASC");
    }

    public function withBookCount(): array
    {
        return $this->query(
            "SELECT a.*, COUNT(b.id_book) AS book_count
             FROM tb_authors a
             LEFT JOIN tb_books b ON b.author_id = a.id_author AND b.status = 1
             GROUP BY a.id_author
             ORDER BY a.name ASC"
        );
    }
}
