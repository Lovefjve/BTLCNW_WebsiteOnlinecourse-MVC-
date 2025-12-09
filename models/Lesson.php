<?php
// models/Lesson.php
class Lesson {
    private $conn;
    
    public function __construct() {
        require_once 'config/database.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Lấy bài học theo khóa học
    public function getByCourse($course_id) {
        $sql = "SELECT * FROM lessons 
                WHERE course_id = ? 
                ORDER BY `order` ASC, created_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy bài học theo ID
    public function getById($id) {
        $sql = "SELECT * FROM lessons WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Tạo bài học mới
    public function create($data) {
        $sql = "INSERT INTO lessons 
                (course_id, title, content, video_url, `order`, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['course_id'],
            $data['title'],
            $data['content'],
            $data['video_url'] ?? '',
            $data['order'] ?? 1
        ]);
    }
    
    // Cập nhật bài học
    public function update($id, $data) {
        $sql = "UPDATE lessons 
                SET title = ?, content = ?, video_url = ?, `order` = ?
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['video_url'] ?? '',
            $data['order'] ?? 1,
            $id
        ]);
    }
    
    // Xóa bài học
    public function delete($id) {
        $sql = "DELETE FROM lessons WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Đếm số bài học
    public function countByCourse($course_id) {
        $sql = "SELECT COUNT(*) as total FROM lessons WHERE course_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
?>