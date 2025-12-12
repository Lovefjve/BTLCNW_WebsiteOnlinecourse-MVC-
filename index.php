<?php
session_start();

// ===== THÊM 2 DÒNG NÀY VÀO ĐẦU (bắt buộc) =====
require_once 'models/Model.php';
require_once 'controllers/StudentController.php';   // ← DÒNG QUAN TRỌNG NHẤT
// =============================================

require_once 'config/ConnectDb.php';
require_once 'models/Course.php';
require_once 'models/Category.php';
require_once 'models/Enrollment.php';
require_once 'controllers/CourseController.php';

// ... phần router còn lại của bạn giữ nguyên
// TEST NHANH (xóa 4 dòng này khi nộp bài)
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 0;
$_SESSION['fullname'] = 'Học viên test';

// Router đơn giản
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$segments = explode('/', $url);

$controller = new CourseController();

if ($segments[0] === '' || $segments[0] === 'course') {
    if (!isset($segments[1]) || $segments[1] === 'index' || $segments[1] === '') {
        $controller->index();
    } elseif ($segments[1] === 'detail' && isset($segments[2])) {
        $controller->detail($segments[2]);
    } elseif ($segments[1] === 'enroll' && isset($segments[2])) {
        $controller->enroll($segments[2]);
    } else {
        $controller->index();
    }
} else {
    $controller->index();
}
?>