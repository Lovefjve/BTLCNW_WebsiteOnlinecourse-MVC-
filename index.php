<?php
// index.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = $_GET['c'] ?? 'instructor';
$action = $_GET['a'] ?? 'dashboard';

// ========== InstructorController (DASHBOARD) ==========
if ($controller === 'instructor') {
    require_once 'controllers/InstructorController.php';
    $instructorController = new InstructorController();

    $actionMap = [
        'dashboard' => 'dashboard',   // Trang chủ instructor
        'profile' => 'profile',       // Hồ sơ giảng viên
        'logout' => 'logout',         // Đăng xuất - THÊM VÀO ĐÂY
        'settings' => 'settings'      // Cài đặt
    ];

    $method = $actionMap[$action] ?? 'dashboard';
    
    if (method_exists($instructorController, $method)) {
        $instructorController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại trong InstructorController";
    }
}

// ========== CourseController ==========
elseif ($controller === 'course') {
    require_once 'controllers/CourseController.php';
    $courseController = new CourseController();

    $actionMap = [
        'index' => 'index',         // Danh sách khóa học
        'create' => 'create',       // Form tạo khóa học
        'store' => 'store',         // Lưu khóa học mới
        'edit' => 'edit',           // Form sửa khóa học
        'update' => 'update',       // Cập nhật khóa học
        'delete' => 'delete',       // Xóa khóa học
        'viewAll' => 'viewAll'      // Xem tất cả khóa học (cho testing) - THÊM NẾU CẦN
    ];

    $method = $actionMap[$action] ?? 'index';
    
    if (method_exists($courseController, $method)) {
        $courseController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại trong CourseController";
    }
}

// ========== LessonController ==========
elseif ($controller === 'lesson') {
    require_once 'controllers/LessonController.php';
    $lessonController = new LessonController();

    $actionMap = [
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'edit' => 'edit',
        'update' => 'update',
        'delete' => 'delete'
    ];

    $method = $actionMap[$action] ?? 'index';
    
    if (method_exists($lessonController, $method)) {
        $lessonController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại trong LessonController";
    }
}

// ========== MaterialController ==========
elseif ($controller === 'material') {
    require_once 'controllers/MaterialController.php';
    $materialController = new MaterialController();

    $actionMap = [
        'index' => 'index',
        'store' => 'store',
        'delete' => 'delete',
        'download' => 'download'
    ];

    $method = $actionMap[$action] ?? 'index';
    
    if (method_exists($materialController, $method)) {
        $materialController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại trong MaterialController";
    }
}

// ========== StudentController ==========
elseif ($controller === 'student') {
    require_once 'controllers/StudentController.php';
    $studentController = new StudentController();

    $actionMap = [
        'index' => 'index',
        'export' => 'export'
    ];

    $method = $actionMap[$action] ?? 'index';
    
    if (method_exists($studentController, $method)) {
        $studentController->$method();
    } else {
        echo "Lỗi: Phương thức '$method' không tồn tại trong StudentController";
    }
}

// ========== AuthController (nếu có) ==========
elseif ($controller === 'auth') {
    
}

// ========== CategoryController (nếu có) ==========
elseif ($controller === 'category') {
    
}

// ========== DEFAULT/ERROR ==========
else {
    header('Location: ?c=instructor&a=dashboard');
    exit;
}