<?php
class Category extends Model {
    protected $table = 'categories';

    public function getAll() {
        $stmt = $this->db->query("SELECT id, name FROM categories ORDER BY name");
        return $stmt->fetchAll();
    }
}
?>