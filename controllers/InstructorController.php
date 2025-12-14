<?php
require_once 'core/Auth.php';

class InstructorController {
    // Dashboard cho giảng viên
    public function dashboard() {
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Chỉ cho phép role = 1 (giảng viên)
        if (!Auth::hasRole(1)) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $user = Auth::getUser();
        require_once 'views/instructor/dashboard.php';
    }
}

