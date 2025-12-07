<?php
require_once 'core/Auth.php';
require_once 'models/User.php';

class AdminController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Dashboard cho quản trị viên
    public function dashboard() {
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Chỉ cho phép role = 2 (quản trị viên)
        if (!Auth::hasRole(2)) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $user = Auth::getUser();
        require_once 'views/admin/dashboard.php';
    }

    // Quản lý người dùng - danh sách
    public function manageUsers() {
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        if (!Auth::hasRole(2)) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $user = Auth::getUser();
        $users = $this->userModel->getAllUsers();
        require_once 'views/admin/users/manage.php';
    }

    // Cập nhật trạng thái người dùng
    public function updateUserStatus() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            http_response_code(403);
            echo 'Unauthorized';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
            $status = isset($_POST['status']) ? $_POST['status'] : 'active';

            // Chỉ cho phép status = 'active' hoặc 'inactive'
            if (!in_array($status, ['active', 'inactive'])) {
                http_response_code(400);
                echo 'Invalid status';
                exit;
            }

            if ($this->userModel->updateUserStatus($userId, $status)) {
                header('Location: ' . BASE_URL . '/admin/users');
                exit;
            }
        }

        http_response_code(400);
        echo 'Bad Request';
        exit;
    }

    // Tạo người dùng mới
    public function createUser() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'fullname' => trim($_POST['fullname'] ?? ''),
                'role' => isset($_POST['role']) ? (int)$_POST['role'] : 0,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validate
            if (empty($data['username'])) {
                $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            } elseif ($this->userModel->findUserByUsername($data['username'])) {
                $errors['username'] = 'Tên đăng nhập đã tồn tại';
            }

            if (empty($data['email'])) {
                $errors['email'] = 'Vui lòng nhập email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                $errors['email'] = 'Email đã tồn tại';
            }

            if (empty($data['password'])) {
                $errors['password'] = 'Vui lòng nhập mật khẩu';
            } elseif (strlen($data['password']) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            if (empty($data['fullname'])) {
                $errors['fullname'] = 'Vui lòng nhập họ tên';
            }

            // Nếu không có lỗi, tạo user
            if (empty($errors)) {
                if ($this->userModel->createUser($data)) {
                    header('Location: ' . BASE_URL . '/admin/users?success=created');
                    exit;
                } else {
                    $errors['db'] = 'Tạo tài khoản thất bại. Vui lòng thử lại.';
                }
            }
        }

        $user = Auth::getUser();
        require_once 'views/admin/users/create.php';
    }

    // Chỉnh sửa người dùng
    public function editUser() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($userId <= 0) {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $editUser = $this->userModel->getUserById($userId);
        if (!$editUser) {
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $errors = [];
        $data = $editUser;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'fullname' => trim($_POST['fullname'] ?? ''),
                'role' => isset($_POST['role']) ? (int)$_POST['role'] : 0,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validate username (chỉ nếu thay đổi)
            if ($data['username'] !== $editUser['username']) {
                if (empty($data['username'])) {
                    $errors['username'] = 'Vui lòng nhập tên đăng nhập';
                } elseif ($this->userModel->findUserByUsername($data['username'])) {
                    $errors['username'] = 'Tên đăng nhập đã tồn tại';
                }
            }

            // Validate email (chỉ nếu thay đổi)
            if ($data['email'] !== $editUser['email']) {
                if (empty($data['email'])) {
                    $errors['email'] = 'Vui lòng nhập email';
                } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Email không hợp lệ';
                } elseif ($this->userModel->findUserByEmail($data['email'])) {
                    $errors['email'] = 'Email đã tồn tại';
                }
            }

            // Validate password (nếu để trống, không thay đổi)
            if (!empty($data['password']) && strlen($data['password']) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            } elseif (empty($data['password'])) {
                $data['password'] = $editUser['password']; // Giữ mật khẩu cũ
            }

            if (empty($data['fullname'])) {
                $errors['fullname'] = 'Vui lòng nhập họ tên';
            }

            // Nếu không có lỗi, cập nhật user
            if (empty($errors)) {
                if ($this->userModel->updateUser($userId, $data)) {
                    header('Location: ' . BASE_URL . '/admin/users?success=updated');
                    exit;
                } else {
                    $errors['db'] = 'Cập nhật tài khoản thất bại. Vui lòng thử lại.';
                }
            }
        }

        $user = Auth::getUser();
        require_once 'views/admin/users/edit.php';
    }

    // Xóa người dùng
    public function deleteUser() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            http_response_code(403);
            echo 'Unauthorized';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

            if ($userId > 0 && $this->userModel->deleteUser($userId)) {
                header('Location: ' . BASE_URL . '/admin/users');
                exit;
            }
        }

        http_response_code(400);
        echo 'Bad Request';
        exit;
    }
}

