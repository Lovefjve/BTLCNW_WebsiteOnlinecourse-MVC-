<?php
// config/Database.php
class Database {
    private $host = "localhost";
    private $db_name = "onlinecourse";
    private $username = "root";
    private $password = "";
    private $conn;
    
    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->host;dbname=$this->db_name;charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false, // QUAN TRỌNG: tắt emulation để tăng performance
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch(PDOException $e) {
            // Hiển thị lỗi rõ ràng
            die("Lỗi kết nối database: " . $e->getMessage() . 
                "<br>Host: $this->host<br>DB: $this->db_name<br>User: $this->username");
        }
        
        return $this->conn;
    }
}