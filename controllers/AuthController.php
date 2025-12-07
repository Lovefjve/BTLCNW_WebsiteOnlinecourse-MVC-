<?php
// controllers/AuthController.php
require_once "./core/Controller.php";

class AuthController extends Controller {

    public function login() {
        // Khởi tạo mảng dữ liệu để gửi ra View
        $data = [];

        // Kiểm tra xem người dùng có submit form không (POST request)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // 1. Lấy dữ liệu từ form
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // 2. Gọi Model User
            $userModel = $this->model("User");
            $user = $userModel->login($username, $password);

            // 3. Xử lý kết quả trả về từ Model
            if ($user) {
                // Đăng nhập thành công -> Lưu session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_fullname'] = $user['fullname'];

                // Điều hướng về trang chủ (hoặc dashboard sau này)
                header("Location: /onlinecourse/index.php");
                exit();
            } else {
                // Đăng nhập thất bại -> Gán lỗi để hiển thị ra View
                $data['error'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }

        // Gọi View login (kèm theo biến $data chứa lỗi nếu có)
        $this->view("auth/login", $data);
    }
    
    // Hàm đăng xuất (đơn giản để test)
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /onlinecourse/index.php?url=auth/login");
    }
}
?>