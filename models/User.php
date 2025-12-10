<?php
// models/User.php

class User {
    private $conn;
    
    public function __construct() {
        require_once 'config/database.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Lấy thông tin user theo ID
    public function getById($user_id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Lấy thông tin user theo email
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Lấy tất cả user theo role
    public function getByRole($role) {
        $sql = "SELECT * FROM users WHERE role = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Tạo mới user
    public function create($data) {
        $sql = "INSERT INTO users (username, email, password, fullname, role, avatar, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['fullname'] ?? '',
            $data['role'] ?? 0,
            $data['avatar'] ?? '',
            $data['status'] ?? 1
        ]);
    }
    
    // Cập nhật user
    public function update($user_id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key === 'password' && !empty($value)) {
                $fields[] = "password = ?";
                $values[] = password_hash($value, PASSWORD_DEFAULT);
            } elseif ($key !== 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $user_id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($values);
    }
    
    // Xóa user
    public function delete($user_id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id]);
    }
    
    // Đếm tổng số user theo role
    public function countByRole($role) {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$role]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Tìm kiếm user
    public function search($keyword, $role = null) {
        $sql = "SELECT * FROM users 
                WHERE (username LIKE ? OR email LIKE ? OR fullname LIKE ?)";
        
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($role !== null) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy tất cả users (có phân trang)
    public function getAll($page = 1, $limit = 10, $role = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM users";
        $params = [];
        
        if ($role !== null) {
            $sql .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>