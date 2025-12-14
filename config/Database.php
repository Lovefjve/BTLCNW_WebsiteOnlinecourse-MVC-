<?php
// file_chung/Database.php -- canonical Database helper
// Provides connect() (used by core/Model) and getConnection() (used by snapshots)
class Database {
    private $host = '127.0.0.1';
    private $db_name = 'onlinecourse';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $conn;

    public function connect() {
        if ($this->conn) return $this->conn;

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            return $this->conn;
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            // For developer convenience, rethrow so failures are visible.
            throw $e;
        }
    }

    // alias expected by some snapshots
    public function getConnection() {
        return $this->connect();
    }
}

// Backwards-compatible alias class name used in one snapshot
if (!class_exists('ConnectDb')) {
    class ConnectDb extends Database {}
}

?>
