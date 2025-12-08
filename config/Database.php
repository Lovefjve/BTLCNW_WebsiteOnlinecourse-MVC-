<?php
// config/Database.php
class Database {
    private $host = "localhost";
    private $db_name = "onlinecourse";
    private $username = "root";
    private $password = "";
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Test query
            $this->conn->query("SELECT 1");
            
        } catch(PDOException $e) {
            // Hiển thị lỗi chi tiết
            die("
                <div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin: 20px;'>
                    <h3>❌ Lỗi kết nối database</h3>
                    <p><strong>Chi tiết:</strong> " . $e->getMessage() . "</p>
                    <p><strong>Database:</strong> {$this->db_name}</p>
                    <p><strong>Username:</strong> {$this->username}</p>
                    <p>Hãy kiểm tra:</p>
                    <ol>
                        <li>XAMPP có đang chạy MySQL không?</li>
                        <li>Database '{$this->db_name}' có tồn tại không?</li>
                        <li>Tên database có đúng không?</li>
                    </ol>
                </div>
            ");
        }
        
        return $this->conn;
    }
}
?>