<?php
/**
 * Course Model - Quản lý khóa học
 * File đầu tiên trong phần models
 */

// Load Database class
require_once __DIR__ . '/../config/Database.php';

class Course {
    private $db;
    private $table = 'courses';

    /**
     * Constructor - Khởi tạo kết nối database
     */
    public function __construct() {
        // Lấy instance của Database
        $database = Database::getInstance();
        
        // Lấy connection từ Database
        $this->db = $database->getConnection();
        
        // Kiểm tra xem bảng có tồn tại không (optional)
        // $this->checkTableExists();
    }

    /**
     * CREATE - Tạo khóa học mới
     * @param int $instructor_id - ID của giảng viên
     * @param array $data - Dữ liệu khóa học
     * @return int|false - ID của khóa học mới tạo hoặc false nếu thất bại
     */
    public function create($instructor_id, $data) {
        $sql = "INSERT INTO {$this->table} 
                (title, description, instructor_id, category_id, price, 
                 duration_weeks, level, image, status, created_at, updated_at) 
                VALUES (:title, :description, :instructor_id, :category_id, :price, 
                        :duration_weeks, :level, :image, :status, NOW(), NOW())";
        
        try {
            // Chuẩn bị statement
            $stmt = $this->db->prepare($sql);
            
            // Thực thi với parameters
            $success = $stmt->execute([
                ':title' => trim($data['title']),
                ':description' => trim($data['description']),
                ':instructor_id' => $instructor_id,
                ':category_id' => $data['category_id'] ?? null,
                ':price' => floatval($data['price'] ?? 0),
                ':duration_weeks' => intval($data['duration_weeks'] ?? 1),
                ':level' => $data['level'] ?? 'Beginner',
                ':image' => $data['image'] ?? null,
                ':status' => $data['status'] ?? 'draft'
            ]);
            
            // Trả về ID nếu thành công
            return $success ? $this->db->lastInsertId() : false;
            
        } catch (PDOException $e) {
            // Ghi log lỗi
            error_log("Course Model - Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * READ - Lấy thông tin khóa học theo ID
     * @param int $course_id - ID khóa học
     * @return array|false - Thông tin khóa học hoặc false nếu không tìm thấy
     */
    public function getById($course_id) {
        $sql = "SELECT c.*, 
                       u.fullname as instructor_name, 
                       u.email as instructor_email,
                       cat.name as category_name 
                FROM {$this->table} c
                LEFT JOIN users u ON c.instructor_id = u.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE c.id = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Course Model - GetById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * READ - Lấy danh sách khóa học của một giảng viên
     * @param int $instructor_id - ID giảng viên
     * @param int $page - Trang hiện tại
     * @param int $limit - Số item mỗi trang
     * @return array - Danh sách khóa học
     */
    public function getByInstructor($instructor_id, $page = 1, $limit = 10) {
        // Tính offset
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, cat.name as category_name 
                FROM {$this->table} c 
                LEFT JOIN categories cat ON c.category_id = cat.id 
                WHERE c.instructor_id = :instructor_id 
                ORDER BY c.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Course Model - GetByInstructor Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * UPDATE - Cập nhật thông tin khóa học
     * @param int $course_id - ID khóa học
     * @param array $data - Dữ liệu cập nhật
     * @return bool - Thành công hay thất bại
     */
    public function update($course_id, $data) {
        // Xây dựng SQL động cho các field có giá trị
        $fields = [];
        $params = [':id' => $course_id];
        
        if (isset($data['title'])) {
            $fields[] = "title = :title";
            $params[':title'] = $data['title'];
        }
        
        if (isset($data['description'])) {
            $fields[] = "description = :description";
            $params[':description'] = $data['description'];
        }
        
        if (isset($data['category_id'])) {
            $fields[] = "category_id = :category_id";
            $params[':category_id'] = $data['category_id'];
        }
        
        if (isset($data['price'])) {
            $fields[] = "price = :price";
            $params[':price'] = floatval($data['price']);
        }
        
        if (isset($data['duration_weeks'])) {
            $fields[] = "duration_weeks = :duration_weeks";
            $params[':duration_weeks'] = intval($data['duration_weeks']);
        }
        
        if (isset($data['level'])) {
            $fields[] = "level = :level";
            $params[':level'] = $data['level'];
        }
        
        if (isset($data['image'])) {
            $fields[] = "image = :image";
            $params[':image'] = $data['image'];
        }
        
        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $params[':status'] = $data['status'];
        }
        
        // Luôn cập nhật updated_at
        $fields[] = "updated_at = NOW()";
        
        // Nếu không có field nào để update
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Course Model - Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * DELETE - Xóa khóa học
     * @param int $course_id - ID khóa học
     * @return bool - Thành công hay thất bại
     */
    public function delete($course_id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Course Model - Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm tổng số khóa học của một giảng viên
     */
    public function countByInstructor($instructor_id) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE instructor_id = :instructor_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Course Model - CountByInstructor Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy thống kê khóa học của giảng viên
     */
    public function getInstructorStats($instructor_id) {
        $sql = "SELECT 
                    COUNT(*) as total_courses,
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_courses,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_courses,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_courses,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_courses,
                    COALESCE(SUM(total_students), 0) as total_students
                FROM {$this->table} 
                WHERE instructor_id = :instructor_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $stats = $stmt->fetch();
            
            // Đảm bảo luôn có giá trị mặc định
            return [
                'total_courses' => (int)($stats['total_courses'] ?? 0),
                'published_courses' => (int)($stats['published_courses'] ?? 0),
                'pending_courses' => (int)($stats['pending_courses'] ?? 0),
                'draft_courses' => (int)($stats['draft_courses'] ?? 0),
                'rejected_courses' => (int)($stats['rejected_courses'] ?? 0),
                'total_students' => (int)($stats['total_students'] ?? 0)
            ];
        } catch (PDOException $e) {
            error_log("Course Model - GetInstructorStats Error: " . $e->getMessage());
            return [
                'total_courses' => 0,
                'published_courses' => 0,
                'pending_courses' => 0,
                'draft_courses' => 0,
                'rejected_courses' => 0,
                'total_students' => 0
            ];
        }
    }

    /**
     * Kiểm tra xem bảng có tồn tại không (helper)
     */
    private function checkTableExists() {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            $exists = $stmt->rowCount() > 0;
            
            if (!$exists) {
                error_log("Warning: Table '{$this->table}' does not exist!");
            }
            
            return $exists;
        } catch (PDOException $e) {
            error_log("Check table exists error: " . $e->getMessage());
            return false;
        }
    }
}
?>