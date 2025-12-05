<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'onlinecourse'; // Tên CSDL đã được yêu cầu
    private $username = 'root'; // Thay thế bằng username CSDL của bạn
    private $password = ''; // Thay thế bằng password CSDL của bạn
    public $conn;

    /**
     * Lấy kết nối CSDL
     * @return PDO|null Kết nối PDO hoặc null nếu kết nối thất bại
     */
    public function getConnection() {
        $this->conn = null;
        try {
            // Chuỗi kết nối DSN
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            
            // Tùy chọn cho PDO
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Bật chế độ báo lỗi
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Chế độ lấy dữ liệu mặc định là mảng kết hợp
                PDO::ATTR_EMULATE_PREPARES   => false,                // Tắt chế độ giả lập Prepared Statements
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            // Thiết lập múi giờ cho kết nối, quan trọng cho DATETIME
            $this->conn->exec("SET time_zone = '+07:00';"); // Đặt theo múi giờ Việt Nam
        } catch(PDOException $exception) {
            echo "Lỗi kết nối CSDL: " . $exception->getMessage();
            // Trong môi trường production, bạn có thể ghi log thay vì in ra lỗi
        }
        return $this->conn;
    }
}