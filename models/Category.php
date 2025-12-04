<?php
/**
 * Category Model - Quản lý danh mục (hỗ trợ dropdown)
 */

require_once __DIR__ . '/../config/Database.php';

class Category {
    private $db;
    private $table = 'categories';

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY name ASC";
        
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Category Model - GetAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh mục theo ID
     */
    public function getById($category_id) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Category Model - GetById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh mục có khóa học (active)
     */
    public function getActiveCategories() {
        $sql = "SELECT DISTINCT c.* 
                FROM {$this->table} c
                JOIN courses ON c.id = courses.category_id
                WHERE courses.status = 'published'
                GROUP BY c.id
                ORDER BY c.name ASC";
        
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Category Model - GetActiveCategories Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số khóa học theo danh mục
     */
    public function countCourses($category_id) {
        $sql = "SELECT COUNT(*) as total FROM courses 
                WHERE category_id = :category_id AND status = 'published'";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Category Model - CountCourses Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>