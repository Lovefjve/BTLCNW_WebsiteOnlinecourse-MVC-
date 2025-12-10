<?php
require_once 'core/Model.php';

class Category extends Model {
    // Lấy tất cả danh mục
    public function getAll() {
        $query = 'SELECT * FROM categories ORDER BY created_at DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh mục theo id
    public function getById($id) {
        $query = 'SELECT * FROM categories WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm theo tên
    public function findByName($name) {
        $query = 'SELECT * FROM categories WHERE name = :name LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Tạo danh mục mới
    public function create($data) {
        $query = 'INSERT INTO categories (name, description) VALUES (:name, :description)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        return $stmt->execute();
    }

    // Cập nhật danh mục
    public function update($id, $data) {
        $query = 'UPDATE categories SET name = :name, description = :description WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Xóa danh mục
    public function delete($id) {
        $query = 'DELETE FROM categories WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // (No slugify needed with current schema)
}
