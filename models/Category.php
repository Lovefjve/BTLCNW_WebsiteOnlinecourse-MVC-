<?php
// models/Category.php
require_once 'config/Database.php';

class Category {
    private $conn;
    private $table = 'categories';
    
    public $id;
    public $name;
    public $description;
    public $created_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET name = :name, description = :description, created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        
        return $stmt->execute();
    }
}
?>