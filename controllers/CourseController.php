<?php

class Controller
{
    protected function view($viewPath, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: " . $viewFile);
        }
    }
    
    protected function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }
    
    protected function isLoggedIn()
    {
        // LƯU Ý: Lập trình viên A sẽ implement đầy đủ
        return isset($_SESSION['user_id']);
    }
    
    protected function isInstructor()
    {
        // LƯU Ý: Lập trình viên A sẽ implement đầy đủ
        return isset($_SESSION['role']) && $_SESSION['role'] == 1;
    }
}