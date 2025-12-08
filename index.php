<?php
// index.php

// Bật lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bật session (CHỈ Ở ĐÂY)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xử lý routing
$controller = $_GET['c'] ?? 'instructor';
$action = $_GET['a'] ?? 'courses';

if ($controller === 'instructor') {
    // Include controller
    require_once 'controllers/InstructorController.php';
    
    // Tạo instance
    $instructorController = new InstructorController();
    
    // Map actions
    $actionMap = [
        'courses' => 'courses',
        'createCourse' => 'createCourse',
        'storeCourse' => 'storeCourse',
        'edit' => 'editCourse',
        'update' => 'updateCourse',
        'delete' => 'deleteCourse'
    ];
    
    // Gọi phương thức
    $method = $actionMap[$action] ?? 'courses';
    
    if (method_exists($instructorController, $method)) {
        $instructorController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại";
    }
} else {
    echo "Lỗi: Controller '$controller' không tồn tại";
}
?>