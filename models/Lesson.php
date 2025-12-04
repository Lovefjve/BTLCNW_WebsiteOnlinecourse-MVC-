<?php
/**
 * Lesson Model - Quản lý bài học
 */

require_once __DIR__ . '/../config/Database.php';

class Lesson {
    private $db;
    private $table = 'lessons';

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Tạo bài học mới
     */
    public function create($course_id, $data) {
        $sql = "INSERT INTO {$this->table} 
                (course_id, title, content, video_url, `order`, created_at) 
                VALUES (:course_id, :title, :content, :video_url, :order, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                ':course_id' => $course_id,
                ':title' => $data['title'] ?? '',
                ':content' => $data['content'] ?? '',
                ':video_url' => $data['video_url'] ?? null,
                ':order' => $data['order'] ?? 1
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Lesson Model - Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy bài học theo ID
     */
    public function getById($lesson_id) {
        $sql = "SELECT l.*, c.title as course_title, c.instructor_id 
                FROM {$this->table} l
                JOIN courses c ON l.course_id = c.id
                WHERE l.id = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $lesson_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Lesson Model - GetById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả bài học của khóa học
     */
    public function getByCourse($course_id) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE course_id = :course_id 
                ORDER BY `order` ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Lesson Model - GetByCourse Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cập nhật bài học
     */
    public function update($lesson_id, $data) {
        $sql = "UPDATE {$this->table} SET 
                title = :title,
                content = :content,
                video_url = :video_url,
                `order` = :order
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':title' => $data['title'] ?? '',
                ':content' => $data['content'] ?? '',
                ':video_url' => $data['video_url'] ?? null,
                ':order' => $data['order'] ?? 1,
                ':id' => $lesson_id
            ]);
        } catch (PDOException $e) {
            error_log("Lesson Model - Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa bài học
     */
    public function delete($lesson_id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $lesson_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lesson Model - Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số bài học trong khóa học
     */
    public function countByCourse($course_id) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE course_id = :course_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lesson Model - CountByCourse Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy thứ tự tiếp theo cho bài học mới
     */
    public function getNextOrder($course_id) {
        $sql = "SELECT MAX(`order`) as max_order FROM {$this->table} 
                WHERE course_id = :course_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return ($result['max_order'] ?? 0) + 1;
        } catch (PDOException $e) {
            error_log("Lesson Model - GetNextOrder Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Sắp xếp lại thứ tự bài học
     */
    public function reorder($course_id, $order_array) {
        try {
            $this->db->beginTransaction();
            
            foreach ($order_array as $index => $lesson_id) {
                $order = $index + 1;
                $sql = "UPDATE {$this->table} SET `order` = :order 
                        WHERE id = :id AND course_id = :course_id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':order' => $order,
                    ':id' => $lesson_id,
                    ':course_id' => $course_id
                ]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Lesson Model - Reorder Error: " . $e->getMessage());
            return false;
        }
    }
}
?>