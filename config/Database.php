<?php
/**
 * Database Connection Class
 * Phiên bản đơn giản, dễ sử dụng
 */

class Database {
    private static $instance = null;
    private $connection;

    // Thông tin kết nối - SỬA THEO CẤU HÌNH CỦA BẠN
    private $host = 'localhost';        // Địa chỉ MySQL server
    private $dbname = 'onlinecourse';   // Tên database
    private $username = 'root';         // Tên đăng nhập MySQL
    private $password = '';             // Mật khẩu MySQL (để trống nếu dùng XAMPP)
    private $charset = 'utf8mb4';       // Bảng mã

    /**
     * Constructor - Private để ngăn tạo instance mới
     */
    private function __construct() {
        try {
            // Tạo DSN (Data Source Name)
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            
            // Tạo kết nối PDO
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,           // Báo lỗi chi tiết
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Trả về mảng kết hợp
                PDO::ATTR_EMULATE_PREPARES => false,                   // Dùng prepared statement thật
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]);
            
            // Thiết lập timezone Việt Nam
            $this->connection->exec("SET time_zone = '+07:00'");
            
            // echo "Kết nối database thành công!<br>"; // Bỏ comment để test
            
        } catch (PDOException $e) {
            // Hiển thị lỗi thân thiện
            die("<h3> Lỗi kết nối Database</h3>
                 <p><strong>Lỗi:</strong> " . $e->getMessage() . "</p>
                 <p><strong>Kiểm tra:</strong></p>
                 <ul>
                     <li>MySQL server có đang chạy không?</li>
                     <li>Tên database: <code>{$this->dbname}</code></li>
                     <li>Username: <code>{$this->username}</code></li>
                     <li>Password: <code>{$this->password}</code></li>
                 </ul>");
        }
    }

    /**
     * Get instance của Database (Singleton Pattern)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Prepare statement
     */
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    /**
     * Execute query (INSERT, UPDATE, DELETE)
     */
    public function execute($sql, $params = []) {
        $stmt = $this->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Fetch single row
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }

    /**
     * Close connection
     */
    public function close() {
        $this->connection = null;
        self::$instance = null;
    }

    // Ngăn chặn clone và unserialize
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Tạo alias function để dễ sử dụng
function db() {
    return Database::getInstance()->getConnection();
}
?>