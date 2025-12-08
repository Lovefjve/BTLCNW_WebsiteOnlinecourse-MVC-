<?php
// models/Course.php
require_once 'config/Database.php';

class Course {
    private $conn;
    private $table = 'courses';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // SỬA LẠI: Lấy khóa học của giảng viên
    public function getByInstructor($instructor_id, $limit = 10, $offset = 0) {
        try {
            // SỬA QUERY: Bỏ GROUP BY phức tạp, dùng subquery cho student_count
            $query = "SELECT 
                        c.*, 
                        cat.name as category_name,
                        (SELECT COUNT(DISTINCT student_id) 
                         FROM enrollments 
                         WHERE course_id = c.id) as student_count
                      FROM courses c
                      LEFT JOIN categories cat ON c.category_id = cat.id
                      WHERE c.instructor_id = :instructor_id
                      ORDER BY c.created_at DESC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Ghi log lỗi
            error_log("Course Model Error - getByInstructor: " . $e->getMessage());
            return [];
        }
    }
    
    // SỬA: Đếm tổng khóa học
    public function countByInstructor($instructor_id) {
        try {
            $query = "SELECT COUNT(*) as total 
                      FROM courses 
                      WHERE instructor_id = :instructor_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Course Model Error - countByInstructor: " . $e->getMessage());
            return 0;
        }
    }
    
    // SỬA: Lấy thống kê giảng viên
    public function getInstructorStats($instructor_id) {
        $stats = [
            'total_courses' => 0,
            'published_courses' => 0,
            'pending_courses' => 0,
            'total_students' => 0
        ];
        
        try {
            // 1. Tổng khóa học
            $query = "SELECT COUNT(*) as total 
                      FROM courses 
                      WHERE instructor_id = :instructor_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_courses'] = $result['total'] ?? 0;
            
            // 2. Khóa học đã xuất bản
            $query = "SELECT COUNT(*) as count 
                      FROM courses 
                      WHERE instructor_id = :instructor_id 
                      AND status = 'published'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['published_courses'] = $result['count'] ?? 0;
            
            // 3. Khóa học chờ duyệt
            $query = "SELECT COUNT(*) as count 
                      FROM courses 
                      WHERE instructor_id = :instructor_id 
                      AND status = 'pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['pending_courses'] = $result['count'] ?? 0;
            
            // 4. Tổng học viên (học viên duy nhất qua tất cả khóa học)
            $query = "SELECT COUNT(DISTINCT e.student_id) as total_students
                      FROM enrollments e
                      INNER JOIN courses c ON e.course_id = c.id
                      WHERE c.instructor_id = :instructor_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_students'] = $result['total_students'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Course Model Error - getInstructorStats: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    // Lấy khóa học theo ID
    public function getById($id) {
        try {
            $query = "SELECT c.*, cat.name as category_name 
                      FROM courses c
                      LEFT JOIN categories cat ON c.category_id = cat.id
                      WHERE c.id = :id
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Course Model Error - getById: " . $e->getMessage());
            return null;
        }
    }
    
    // Tạo khóa học mới
    public function create($data) {
        try {
            $query = "INSERT INTO courses 
                      (title, description, instructor_id, category_id, price, 
                       duration_weeks, level, image, status, created_at)
                      VALUES 
                      (:title, :description, :instructor_id, :category_id, :price,
                       :duration_weeks, :level, :image, :status, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            $params = [
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':instructor_id' => $data['instructor_id'],
                ':category_id' => $data['category_id'] ?? null,
                ':price' => $data['price'],
                ':duration_weeks' => $data['duration_weeks'],
                ':level' => $data['level'],
                ':image' => $data['image'] ?? '',
                ':status' => $data['status'] ?? 'pending'
            ];
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Course Model Error - create: " . $e->getMessage());
            return false;
        }
    }
    
    // Cập nhật khóa học
    public function update($id, $data) {
        try {
            $query = "UPDATE courses SET 
                      title = :title,
                      description = :description,
                      category_id = :category_id,
                      price = :price,
                      duration_weeks = :duration_weeks,
                      level = :level,
                      image = :image,
                      status = :status,
                      updated_at = NOW()
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $params = [
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':category_id' => $data['category_id'] ?? null,
                ':price' => $data['price'],
                ':duration_weeks' => $data['duration_weeks'],
                ':level' => $data['level'],
                ':image' => $data['image'] ?? '',
                ':status' => $data['status'],
                ':id' => $id
            ];
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Course Model Error - update: " . $e->getMessage());
            return false;
        }
    }
    
    // Xóa khóa học
    public function delete($id) {
        try {
            $query = "DELETE FROM courses WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Course Model Error - delete: " . $e->getMessage());
            return false;
        }
    }
}
?>