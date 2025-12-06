<?php
// config/Database.php

class Database {
    private $host = "localhost";
    private $db_name = "onlinecourse";    // TÊN DATABASE CỦA BẠN
    private $username = "root";           // USERNAME XAMPP
    private $password = "";               // PASSWORD XAMPP (để trống)
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
            
            return $this->conn;
            
        } catch(PDOException $exception) {
            // Hiển thị lỗi chi tiết để debug
            echo "<h3>LỖI KẾT NỐI DATABASE:</h3>";
            echo "<p><strong>Database:</strong> " . $this->db_name . "</p>";
            echo "<p><strong>Lỗi:</strong> " . $exception->getMessage() . "</p>";
            echo "<p><strong>Chi tiết:</strong> " . $exception->getTraceAsString() . "</p>";
            
            // Tạo database tự động nếu chưa có
            $this->createDatabase();
            
            return null;
        }
    }
    
    private function createDatabase() {
        try {
            // Kết nối không chọn database
            $temp_conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            
            // Tạo database
            $temp_conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $temp_conn->exec("USE " . $this->db_name);
            
            echo "<p>✅ Đã tạo database: " . $this->db_name . "</p>";
            
            // Tạo các bảng cơ bản
            $this->createTables($temp_conn);
            
            // Đóng kết nối tạm
            $temp_conn = null;
            
            // Thử kết nối lại
            return $this->getConnection();
            
        } catch(PDOException $e) {
            echo "<p>❌ Lỗi tạo database: " . $e->getMessage() . "</p>";
            return null;
        }
    }
    
    private function createTables($conn) {
        $tables = [
            "users" => "
                CREATE TABLE IF NOT EXISTS users (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    username VARCHAR(255) UNIQUE NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    fullname VARCHAR(255),
                    role INT DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB
            ",
            
            "categories" => "
                CREATE TABLE IF NOT EXISTS categories (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB
            ",
            
            "courses" => "
                CREATE TABLE IF NOT EXISTS courses (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    instructor_id INT NOT NULL,
                    category_id INT DEFAULT 1,
                    price DECIMAL(10,2) DEFAULT 0,
                    duration_weeks INT DEFAULT 4,
                    level VARCHAR(50) DEFAULT 'Beginner',
                    image VARCHAR(255),
                    status VARCHAR(50) DEFAULT 'draft',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
                ) ENGINE=InnoDB
            ",
            
            "enrollments" => "
                CREATE TABLE IF NOT EXISTS enrollments (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    course_id INT NOT NULL,
                    student_id INT NOT NULL,
                    enrolled_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                    status VARCHAR(50) DEFAULT 'active',
                    progress INT DEFAULT 0,
                    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
                    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB
            "
        ];
        
        foreach ($tables as $name => $sql) {
            try {
                $conn->exec($sql);
                echo "<p>✅ Đã tạo bảng: " . $name . "</p>";
            } catch(PDOException $e) {
                echo "<p>❌ Lỗi tạo bảng " . $name . ": " . $e->getMessage() . "</p>";
            }
        }
        
        // Thêm dữ liệu mẫu
        $this->insertSampleData($conn);
    }
    
    private function insertSampleData($conn) {
        // Thêm user giảng viên
        $conn->exec("
            INSERT IGNORE INTO users (id, username, email, password, fullname, role) 
            VALUES 
            (1, 'instructor', 'instructor@example.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'Giảng viên Mẫu', 1),
            (2, 'student1', 'student1@example.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'Học viên 1', 0)
        ");
        
        // Thêm danh mục
        $conn->exec("
            INSERT IGNORE INTO categories (id, name) 
            VALUES 
            (1, 'Lập trình'),
            (2, 'Thiết kế'),
            (3, 'Kinh doanh'),
            (4, 'Marketing')
        ");
        
        // Thêm khóa học
        $conn->exec("
            INSERT IGNORE INTO courses (id, title, description, instructor_id, category_id, price, level, status) 
            VALUES 
            (1, 'Lập trình PHP cơ bản', 'Khóa học PHP cho người mới bắt đầu', 1, 1, 500000, 'Beginner', 'published'),
            (2, 'MySQL Database', 'Học quản lý cơ sở dữ liệu MySQL', 1, 1, 0, 'Intermediate', 'draft'),
            (3, 'Thiết kế Web với Figma', 'Học thiết kế UI/UX chuyên nghiệp', 1, 2, 300000, 'Beginner', 'published')
        ");
        
        // Thêm enrollment
        $conn->exec("
            INSERT IGNORE INTO enrollments (course_id, student_id) 
            VALUES (1, 2)
        ");
        
        echo "<p>✅ Đã thêm dữ liệu mẫu</p>";
    }
    
    // Kiểm tra kết nối (cho debug)
    public function checkConnection() {
        $conn = $this->getConnection();
        if ($conn) {
            echo "<p>✅ Kết nối database thành công!</p>";
            echo "<p>Database: " . $this->db_name . "</p>";
            echo "<p>Host: " . $this->host . "</p>";
            return true;
        }
        return false;
    }
    
    // Test query
    public function testQuery() {
        $conn = $this->getConnection();
        if ($conn) {
            try {
                $stmt = $conn->query("SELECT 1 as test");
                $result = $stmt->fetch();
                return $result['test'] == 1;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }
}

// Test ngay khi load file (chỉ khi debug)
if (isset($_GET['test']) && $_GET['test'] == 'db') {
    $db = new Database();
    $db->checkConnection();
}
?>