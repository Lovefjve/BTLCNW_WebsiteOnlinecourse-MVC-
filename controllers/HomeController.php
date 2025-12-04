<?php
/**
 * Home Controller
 */

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController {
    
    public function index() {
        $courseModel = new Course();
        $categoryModel = new Category();
        
        // Lấy dữ liệu
        $featuredCourses = $courseModel->getAll('published');
        $categories = $categoryModel->getAll();
        
        // Include view
        include __DIR__ . '/../views/home/index.php';
    }
}
?>