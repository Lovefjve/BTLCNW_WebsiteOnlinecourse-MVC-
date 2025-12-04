<?php
/**
 * Auth Controller
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    public function login() {
        // Nếu đã login, redirect
        session_start();
        if (isset($_SESSION['user_id'])) {
            $this->redirectByRole($_SESSION['role']);
            exit;
        }
        
        // Xử lý login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $userModel = new User();
            $user = $userModel->login($email, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fullname'] = $user['fullname'];
                
                $this->redirectByRole($user['role']);
                exit;
            } else {
                $_SESSION['error'] = 'Email hoặc mật khẩu không đúng';
            }
        }
        
        include __DIR__ . '/../views/auth/login.php';
    }
    
    public function register() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: /btl/');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'fullname' => $_POST['fullname'] ?? '',
                'role' => 0 // Mặc định là student
            ];
            
            $userModel = new User();
            
            // Kiểm tra username/email đã tồn tại
            if ($userModel->getByEmail($data['email'])) {
                $_SESSION['error'] = 'Email đã được sử dụng';
            } else {
                if ($userModel->create($data)) {
                    $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    header('Location: /btl/auth/login');
                    exit;
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi đăng ký';
                }
            }
        }
        
        include __DIR__ . '/../views/auth/register.php';
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /btl/');
        exit;
    }
    
    private function redirectByRole($role) {
        switch ($role) {
            case 0: // Student
                header('Location: /btl/student/dashboard');
                break;
            case 1: // Instructor
                header('Location: /btl/instructor/course/manage');
                break;
            case 2: // Admin
                header('Location: /btl/admin/dashboard');
                break;
            default:
                header('Location: /btl/');
        }
    }
}
?>