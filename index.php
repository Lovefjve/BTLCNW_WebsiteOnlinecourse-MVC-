<?php
// btl/index.php - VERSION ĐƠN GIẢN
session_start();

// BẬT DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== DEBUG MODE ===<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Current Dir: " . __DIR__ . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";

// Lấy controller và action
$controller = $_GET['c'] ?? 'home';
$action = $_GET['a'] ?? 'index';

echo "Controller: $controller, Action: $action<br>";

// Chỉ xử lý instructor
if ($controller == 'instructor') {
    echo "=== PROCESSING INSTRUCTOR ===<br>";
    
    $controllerFile = __DIR__ . '/controllers/InstructorController.php';
    echo "Controller file: $controllerFile<br>";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        echo "✅ File loaded<br>";
        
        if (class_exists('InstructorController')) {
            $inst = new InstructorController();
            echo "✅ Instance created<br>";
            
            if ($action == 'courses') {
                echo "Calling manageCourses()<br>";
                $inst->manageCourses();
            } elseif ($action == 'courses/create') {
                echo "Calling createCourse()<br>";
                $inst->createCourse();
            } else {
                echo "❌ Action not found: $action<br>";
            }
        } else {
            echo "❌ Class InstructorController not defined<br>";
        }
    } else {
        echo "❌ Controller file not found<br>";
    }
    
    exit;
}

// HOME PAGE ĐƠN GIẢN
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trang chủ</title>
</head>
<body>
    <h1>TEST PAGE</h1>
    <a href="index.php?c=instructor&a=courses">Test Quản lý khóa học</a><br>
    <a href="index.php?c=instructor&a=courses/create">Test Tạo khóa học</a>
</body>
</html>