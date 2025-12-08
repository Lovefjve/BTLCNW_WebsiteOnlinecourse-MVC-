<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die("View không tồn tại: " . htmlspecialchars($view));
        }
        
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once $viewPath;
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}
?>