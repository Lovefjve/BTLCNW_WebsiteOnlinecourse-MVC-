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

// Routing cho InstructorController
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
        echo "Lỗi: Phương thức '$method' không tồn tại trong InstructorController";
    }
} 
// Routing cho LessonController
elseif ($controller === 'lesson') {
    // Include controller
    require_once 'controllers/LessonController.php';
    
    // Tạo instance
    $lessonController = new LessonController();
    
    // Map actions
    $actionMap = [
        'index' => 'index',      // Danh sách bài học
        'create' => 'create',    // Form tạo bài học
        'store' => 'store',      // Xử lý tạo bài học
        'edit' => 'edit',        // Form sửa bài học
        'update' => 'update',    // Xử lý cập nhật
        'delete' => 'delete'     // Xử lý xóa
    ];
    
    // Gọi phương thức
    $method = $actionMap[$action] ?? 'index';
    
    if (method_exists($lessonController, $method)) {
        $lessonController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại trong LessonController";
    }
} 
// Routing cho MaterialController
elseif ($controller === 'material') {
    // Include controller
    require_once 'controllers/MaterialController.php';
    
    // Tạo instance
    $materialController = new MaterialController();
    
    // Map actions
    $actionMap = [
        'index' => 'index',        // Danh sách tài liệu
        'upload' => 'upload',      // Form upload tài liệu
        'store' => 'store',        // Xử lý upload tài liệu
        'delete' => 'delete'       // Xử lý xóa tài liệu
    ];
    
    // Gọi phương thức
    $method = $actionMap[$action] ?? 'index';
    
    if (method_exists($materialController, $method)) {
        $materialController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại trong MaterialController";
    }
}
// Thêm các controller khác ở đây
// elseif ($controller === 'home') { ... }
// elseif ($controller === 'auth') { ... }
// elseif ($controller === 'student') { ... }
else {
    echo "Lỗi: Controller '$controller' không tồn tại";
    
    // Hoặc redirect về trang mặc định
    // header('Location: ?c=instructor&a=courses');
    // exit;
}
?>