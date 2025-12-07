<?php
session_start();

class Auth {
    // Khởi tạo session
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Đăng nhập
    public static function login($user) {
        self::init();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
    }

    // Đăng xuất
    public static function logout() {
        self::init();
        session_destroy();
    }

    // Kiểm tra đã đăng nhập chưa
    public static function isLoggedIn() {
        self::init();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Lấy thông tin người dùng hiện tại
    public static function getUser() {
        self::init();
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    // Kiểm tra quyền
    public static function hasRole($role) {
        $user = self::getUser();
        if ($user) {
            return $user['role'] == $role;
        }
        return false;
    }
}
