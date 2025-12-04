<?php
/**
 * Material Model - Quản lý tài liệu đính kèm
 */

require_once __DIR__ . '/../config/Database.php';

class Material {
    private $db;
    private $table = 'materials';

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Upload tài liệu mới
     */
    public function upload($lesson_id, $file_info) {
        $sql = "INSERT INTO {$this->table} 
                (lesson_id, filename, file_path, file_type, uploaded_at) 
                VALUES (:lesson_id, :filename, :file_path, :file_type, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                ':lesson_id' => $lesson_id,
                ':filename' => $file_info['filename'] ?? '',
                ':file_path' => $file_info['file_path'] ?? '',
                ':file_type' => $file_info['file_type'] ?? ''
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Material Model - Upload Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tài liệu theo ID
     */
    public function getById($material_id) {
        $sql = "SELECT m.*, l.title as lesson_title, l.course_id 
                FROM {$this->table} m
                JOIN lessons l ON m.lesson_id = l.id
                WHERE m.id = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $material_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Material Model - GetById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả tài liệu của bài học
     */
    public function getByLesson($lesson_id) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE lesson_id = :lesson_id 
                ORDER BY uploaded_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Material Model - GetByLesson Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Xóa tài liệu
     */
    public function delete($material_id) {
        try {
            // Lấy thông tin file trước khi xóa
            $material = $this->getById($material_id);
            
            if (!$material) {
                return false;
            }
            
            // Xóa trong database
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $material_id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            // Xóa file vật lý nếu thành công
            if ($result && isset($material['file_path']) && file_exists($material['file_path'])) {
                unlink($material['file_path']);
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Material Model - Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật số lượt download
     */
    public function incrementDownload($material_id) {
        $sql = "UPDATE {$this->table} SET download_count = COALESCE(download_count, 0) + 1 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $material_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Material Model - IncrementDownload Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số tài liệu của bài học
     */
    public function countByLesson($lesson_id) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE lesson_id = :lesson_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':lesson_id', $lesson_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Material Model - CountByLesson Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy tất cả tài liệu của khóa học
     */
    public function getByCourse($course_id) {
        $sql = "SELECT m.*, l.title as lesson_title, l.`order` as lesson_order 
                FROM {$this->table} m
                JOIN lessons l ON m.lesson_id = l.id
                WHERE l.course_id = :course_id
                ORDER BY l.`order` ASC, m.uploaded_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Material Model - GetByCourse Error: " . $e->getMessage());
            return [];
        }
    }
}
?>