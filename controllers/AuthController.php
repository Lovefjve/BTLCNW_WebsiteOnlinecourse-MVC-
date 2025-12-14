<?php
require_once 'models/User.php';
require_once 'core/Auth.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Hiển thị form đăng ký
    public function register() {
        require_once 'views/auth/register.php';
    }

    // Xử lý đăng ký
    public function postRegister() {
        $data = [];
        $errors = [];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'fullname' => trim($_POST['fullname'] ?? ''),
                'role' => isset($_POST['role']) ? (int)$_POST['role'] : 0
            ];

            // Validate
            if(empty($data['username'])) {
                $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            } else if($this->userModel->findUserByUsername($data['username'])) {
                $errors['username'] = 'Tên đăng nhập đã tồn tại';
            }

            if(empty($data['email'])) {
                $errors['email'] = 'Vui lòng nhập email';
            } else if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            } else if($this->userModel->findUserByEmail($data['email'])) {
                $errors['email'] = 'Email đã tồn tại';
            }

            if(empty($data['password'])) {
                $errors['password'] = 'Vui lòng nhập mật khẩu';
            } else if(strlen($data['password']) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            if(empty($data['fullname'])) {
                $errors['fullname'] = 'Vui lòng nhập họ tên';
            }

            // Chỉ cho phép role = 0 hoặc 1 khi đăng ký từ form (ngăn đăng ký thành admin)
            $data['role'] = ($data['role'] === 1) ? 1 : 0;

            // Nếu không có lỗi, thực hiện đăng ký
            if(empty($errors)) {
                if($this->userModel->register($data)) {
                    // Đăng ký thành công, chuyển hướng đến trang đăng nhập
                    header('location: ' . BASE_URL . '/auth/login?register=success');
                    exit;
                } else {
                    // Lỗi khi thêm vào CSDL
                    $errors['db'] = 'Đăng ký thất bại. Vui lòng thử lại.';
                }
            }
        }
        
        // Hiển thị form với dữ liệu và lỗi
        require_once 'views/auth/register.php';
    }

    // Hiển thị form đăng nhập
    public function login() {
        require_once 'views/auth/login.php';
    }

    // Xử lý đăng nhập
    public function postLogin() {
        $errors = [];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if(empty($username)) {
                $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            }
            if(empty($password)) {
                $errors['password'] = 'Vui lòng nhập mật khẩu';
            }

            if(empty($errors)) {
                $user = $this->userModel->login($username, $password);
                if($user) {
                    // Đăng nhập thành công
                    Auth::login($user);

                    // Lấy role từ user và ép kiểu int để so sánh chính xác
                    $role = isset($user['role']) ? (int)$user['role'] : 0;

                    // Chuyển hướng theo role (dùng if để tránh bất đồng kiểu)
                    if ($role === 1) {
                        header('Location: ' . BASE_URL . '/instructor/dashboard', true, 302);
                        exit;
                    } elseif ($role === 2) {
                        header('Location: ' . BASE_URL . '/admin/dashboard', true, 302);
                        exit;
                    } else {
                        header('Location: ' . BASE_URL . '/student/dashboard', true, 302);
                        exit;
                    }
                } else {
                    $errors['login'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
                }
            }
        }
        
        // Hiển thị form với lỗi nếu có
        require_once 'views/auth/login.php';
    }

    // Đăng xuất
    public function logout() {
        Auth::logout();
        header('Location: ' . BASE_URL . '/auth/login', true, 302);
        exit;
    }
}
