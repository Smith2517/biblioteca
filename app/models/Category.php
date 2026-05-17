<?php
/** app/models/Category.php */
class Category extends Model
{
    protected string $table = 'tb_categories';
    protected string $pk    = 'id_category';

    public function allActive(): array
    {
        return $this->query("SELECT * FROM tb_categories WHERE status = 1 ORDER BY name ASC");
    }
}
