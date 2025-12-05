<?php

// Yêu cầu class Database
require_once __DIR__ . '/../config/Database.php';

class Course {
    // Thuộc tính kết nối CSDL và tên bảng
    private $conn;
    private $table_name = "courses";

    // Thuộc tính của đối tượng Course
    public $id;
    public $title;
    public $description;
    public $instructor_id; // Khóa ngoại liên kết với bảng users
    public $category_id; // Khóa ngoại liên kết với bảng categories
    public $price;
    public $duration_weeks;
    public $level;
    public $image;
    public $created_at;
    public $updated_at;

    // Constructor với tham số kết nối CSDL
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Tạo khóa học mới
     * @return bool
     */
    public function create() {
        // Ghi chú: Cần check quyền Giảng viên (instructor_id) ở Controller trước khi gọi
        
        // Truy vấn INSERT
        $query = "INSERT INTO " . $this->table_name . "
                  SET title = :title, description = :description, instructor_id = :instructor_id,
                      category_id = :category_id, price = :price, duration_weeks = :duration_weeks,
                      level = :level, image = :image, created_at = NOW(), updated_at = NOW()";

        // Chuẩn bị truy vấn
        $stmt = $this->conn->prepare($query);

        // Làm sạch và gán giá trị
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->instructor_id = htmlspecialchars(strip_tags($this->instructor_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->duration_weeks = htmlspecialchars(strip_tags($this->duration_weeks));
        $this->level = htmlspecialchars(strip_tags($this->level));
        $this->image = htmlspecialchars(strip_tags($this->image));

        // Ràng buộc giá trị
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':instructor_id', $this->instructor_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':duration_weeks', $this->duration_weeks);
        $stmt->bindParam(':level', $this->level);
        $stmt->bindParam(':image', $this->image);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            // Lấy ID của bản ghi vừa tạo
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Cập nhật khóa học
     * @return bool
     */
    public function update() {
        // Ghi chú: Cần check quyền Giảng viên VÀ đảm bảo instructor_id khớp với người tạo ở Controller
        
        // Truy vấn UPDATE
        $query = "UPDATE " . $this->table_name . "
                  SET title = :title, description = :description, category_id = :category_id,
                      price = :price, duration_weeks = :duration_weeks, level = :level,
                      image = :image, updated_at = NOW()
                  WHERE id = :id AND instructor_id = :instructor_id"; // Thêm điều kiện instructor_id để bảo mật

        // Chuẩn bị truy vấn
        $stmt = $this->conn->prepare($query);

        // Làm sạch và gán giá trị
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        // ... (làm sạch các thuộc tính khác tương tự trong create()) ...
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->duration_weeks = htmlspecialchars(strip_tags($this->duration_weeks));
        $this->level = htmlspecialchars(strip_tags($this->level));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->instructor_id = htmlspecialchars(strip_tags($this->instructor_id)); // Dùng để check quyền

        // Ràng buộc giá trị
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':duration_weeks', $this->duration_weeks);
        $stmt->bindParam(':level', $this->level);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':instructor_id', $this->instructor_id);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Xóa khóa học
     * @return bool
     */
    public function delete() {
        // Ghi chú: Cần check quyền Giảng viên VÀ đảm bảo instructor_id khớp với người tạo ở Controller
        
        // Truy vấn DELETE
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND instructor_id = :instructor_id"; // Thêm điều kiện instructor_id để bảo mật

        // Chuẩn bị truy vấn
        $stmt = $this->conn->prepare($query);

        // Làm sạch và gán giá trị
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->instructor_id = htmlspecialchars(strip_tags($this->instructor_id));

        // Ràng buộc giá trị
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':instructor_id', $this->instructor_id);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            // Ghi chú: Cần xóa các bài học (lessons), tài liệu (materials), và đăng ký (enrollments) liên quan
            // Có thể dùng ON DELETE CASCADE trong CSDL hoặc gọi thêm hàm xóa trong Model Lesson, Material, Enrollment
            return true;
        }

        return false;
    }
    
    // ... Thêm các hàm khác như readOne(), readByInstructor() ...
}