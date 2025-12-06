<?php
// models/Course.php
require_once './config/Database.php';

class Course {
    private $conn;
    private $table = 'courses';
    
    public $id;
    public $title;
    public $description;
    public $instructor_id;
    public $category_id;
    public $price;
    public $duration_weeks;
    public $level;
    public $image;
    public $status;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Tạo khóa học mới
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET title = :title,
                    description = :description,
                    instructor_id = :instructor_id,
                    category_id = :category_id,
                    price = :price,
                    duration_weeks = :duration_weeks,
                    level = :level,
                    image = :image,
                    status = :status,
                    created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Làm sạch dữ liệu
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Bind parameters
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":instructor_id", $this->instructor_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":duration_weeks", $this->duration_weeks);
        $stmt->bindParam(":level", $this->level);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":status", $this->status);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Lấy tất cả khóa học của giảng viên (có phân trang)
    public function getByInstructor($instructor_id, $limit = 10, $offset = 0) {
        $query = "SELECT c.*, cat.name as category_name, 
                         (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
                  FROM " . $this->table . " c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE c.instructor_id = :instructor_id
                  ORDER BY c.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Đếm tổng số khóa học của giảng viên
    public function countByInstructor($instructor_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE instructor_id = :instructor_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Lấy thống kê của giảng viên
    public function getInstructorStats($instructor_id) {
        $stats = [
            'total_courses' => 0,
            'published_courses' => 0,
            'pending_courses' => 0,
            'total_students' => 0
        ];
        
        // Đếm tổng khóa học
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE instructor_id = :instructor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_courses'] = $result['total'] ?? 0;
        
        // Đếm khóa học theo trạng thái
        $query = "SELECT status, COUNT(*) as count FROM " . $this->table . " 
                  WHERE instructor_id = :instructor_id 
                  GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['status'] == 'published') {
                $stats['published_courses'] = $row['count'];
            } elseif ($row['status'] == 'pending') {
                $stats['pending_courses'] = $row['count'];
            }
        }
        
        // Đếm tổng học viên
        $query = "SELECT COUNT(DISTINCT e.student_id) as total_students
                  FROM enrollments e
                  JOIN courses c ON e.course_id = c.id
                  WHERE c.instructor_id = :instructor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_students'] = $result['total_students'] ?? 0;
        
        return $stats;
    }
    
    // Lấy khóa học theo ID
    public function getById($id) {
        $query = "SELECT c.*, cat.name as category_name,
                         (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
                  FROM " . $this->table . " c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE c.id = :id
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Gán giá trị cho object
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->instructor_id = $row['instructor_id'];
            $this->category_id = $row['category_id'];
            $this->price = $row['price'];
            $this->duration_weeks = $row['duration_weeks'];
            $this->level = $row['level'];
            $this->image = $row['image'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
        
        return $row;
    }
    
    // Cập nhật khóa học
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET title = :title,
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
        
        // Làm sạch dữ liệu
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Bind parameters
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":duration_weeks", $this->duration_weeks);
        $stmt->bindParam(":level", $this->level);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    
    // Xóa khóa học
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Lấy tất cả khóa học (cho admin)
    public function getAll($limit = 20, $offset = 0) {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name,
                         (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  ORDER BY c.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Tìm kiếm khóa học
    public function search($keyword, $category_id = null, $level = null, $limit = 20) {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name,
                         (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE c.status = 'published'";
        
        $params = [];
        
        if (!empty($keyword)) {
            $query .= " AND (c.title LIKE :keyword OR c.description LIKE :keyword)";
            $params[':keyword'] = "%$keyword%";
        }
        
        if (!empty($category_id)) {
            $query .= " AND c.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        if (!empty($level)) {
            $query .= " AND c.level = :level";
            $params[':level'] = $level;
        }
        
        $query .= " ORDER BY c.created_at DESC LIMIT :limit";
        $params[':limit'] = $limit;
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            if ($key == ':limit') {
                $stmt->bindParam($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindParam($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . "
                SET status = :status,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    

}
?>