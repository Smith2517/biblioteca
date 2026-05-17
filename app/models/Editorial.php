<?php
/** app/models/Editorial.php */
class Editorial extends Model
{
    protected string $table = 'tb_editorials';
    protected string $pk    = 'id_editorial';

    public function allActive(): array
    {
        return $this->query("SELECT * FROM tb_editorials WHERE status = 1 ORDER BY name ASC");
    }

    public function withBookCount(): array
    {
        return $this->query(
            "SELECT e.*, COUNT(b.id_book) AS book_count
             FROM tb_editorials e
             LEFT JOIN tb_books b ON b.editorial_id = e.id_editorial AND b.status = 1
             GROUP BY e.id_editorial
             ORDER BY e.name ASC"
        );
    }
}
