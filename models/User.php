<?php
// models/User.php
require_once "./config/Database.php";

class User {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Hàm kiểm tra đăng nhập
    public function login($username_or_email, $password) {
        // 1. Chuẩn bị câu lệnh SQL
        $query = "SELECT id, username, password, role, fullname 
                  FROM " . $this->table_name . " 
                  WHERE username = :u OR email = :e 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);

        // 2. Gán dữ liệu vào tham số (tránh SQL Injection) [cite: 101]
        $stmt->bindParam(':u', $username_or_email);
        $stmt->bindParam(':e', $username_or_email);
        
        // 3. Thực thi
        $stmt->execute();

        // 4. Kiểm tra kết quả
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Kiểm tra mật khẩu đã mã hóa (password_hash) [cite: 99]
            if (password_verify($password, $row['password'])) {
                // Trả về mảng thông tin user nếu đúng
                return $row;
            }
        }
        
        // Trả về false nếu sai username hoặc password
        return false;
    }
}
?>