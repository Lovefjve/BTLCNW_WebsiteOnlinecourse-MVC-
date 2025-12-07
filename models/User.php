<?php
require_once 'core/Model.php';

class User extends Model {
    // Đăng ký người dùng mới
    public function register($data) {
        $query = 'INSERT INTO users (username, email, password, fullname, role) VALUES (:username, :email, :password, :fullname, :role)';
        $stmt = $this->db->prepare($query);

        // Bind values (không mã hóa mật khẩu)
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':fullname', $data['fullname']);
        $stmt->bindParam(':role', $data['role']);

        // Execute
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Đăng nhập
    public function login($username, $password) {
        $query = 'SELECT * FROM users WHERE username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && $password === $row['password']) {
            return $row;
        }

        return false;
    }

    // Kiểm tra username đã tồn tại chưa
    public function findUserByUsername($username) {
        $query = 'SELECT * FROM users WHERE username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    // Kiểm tra email đã tồn tại chưa
    public function findUserByEmail($email) {
        $query = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    // Lấy tất cả người dùng
    public function getAllUsers() {
        $query = 'SELECT id, username, email, fullname, role, created_at FROM users ORDER BY created_at DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy người dùng theo ID
    public function getUserById($id) {
        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái người dùng (active/inactive)
    public function updateUserStatus($id, $status) {
        // Hàm này không dùng vì bảng chưa có cột status
        return false;
    }

    // Xóa người dùng
    public function deleteUser($id) {
        $query = 'DELETE FROM users WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Tạo người dùng (admin tạo)
    public function createUser($data) {
        $query = 'INSERT INTO users (username, email, password, fullname, role, created_at) 
                  VALUES (:username, :email, :password, :fullname, :role, NOW())';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':fullname', $data['fullname']);
        $stmt->bindParam(':role', $data['role'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cập nhật thông tin người dùng
    public function updateUser($id, $data) {
        $query = 'UPDATE users SET email = :email, password = :password, 
                  fullname = :fullname, role = :role WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':fullname', $data['fullname']);
        $stmt->bindParam(':role', $data['role'], PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Database error in updateUser: ' . $e->getMessage());
            return false;
        }
    }
}
