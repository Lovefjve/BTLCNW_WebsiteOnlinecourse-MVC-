<?php
require_once 'core/Auth.php';

class StudentController {
    // Dashboard cho học viên
    public function dashboard() {
        // Kiểm tra đã đăng nhập
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Chỉ cho phép role = 0 (học viên)
        if (!Auth::hasRole(0)) {
            // Nếu không có quyền, chuyển về trang chủ
            header('Location: ' . BASE_URL);
            exit;
        }

        $user = Auth::getUser();
        require_once 'views/student/dashboard.php';
    }
}

