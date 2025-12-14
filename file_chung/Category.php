<?php
// Canonical Category model merged. Require the project's Database helper from config.
require_once __DIR__ . '/../config/Database.php';

class Category {
    private $pdo;
    private $table = 'categories';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    // Return all categories (ordered by name)
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, description FROM {$this->table} ORDER BY name ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Category::getAll error: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Category::getById error: ' . $e->getMessage());
            return null;
        }
    }

    public function findByName($name) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE name = :name LIMIT 1");
            $stmt->execute([':name' => $name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Category::findByName error: ' . $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (name, description, created_at) VALUES (:name, :description, NOW())");
            return $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log('Category::create error: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET name = :name, description = :description, updated_at = NOW() WHERE id = :id");
            return $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? '',
                ':id' => (int)$id
            ]);
        } catch (PDOException $e) {
            error_log('Category::update error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => (int)$id]);
        } catch (PDOException $e) {
            error_log('Category::delete error: ' . $e->getMessage());
            return false;
        }
    }
}

?>
