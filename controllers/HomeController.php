<?php
require_once 'core/Auth.php';

class HomeController {
    // Hiển thị trang chủ
    public function index() {
        require_once 'views/home/index.php';
    }
}

