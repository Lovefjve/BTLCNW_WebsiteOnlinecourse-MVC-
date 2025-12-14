<?php
// controllers/Controller.php - FIX VĨNH VIỄN ĐƯỜNG DẪN VIEW
class Controller
{
    protected function view($viewPath, $data = [])
    {
        extract($data);

        // ĐƯỜNG DẪN TUYỆT ĐỐI – CHẠY ĐÚNG 100% MỌI LÚC
        $file = __DIR__ . "/../views/{$viewPath}.php";

        if (file_exists($file)) {
            require $file;
        } else {
            die("View không tồn tại: $file");
        }
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}
?>