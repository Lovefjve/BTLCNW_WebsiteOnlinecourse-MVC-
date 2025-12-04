<?php
/**
 * Enrollment Controller
 */

require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Course.php';

class EnrollmentController {
    
    public function enroll($course_id) {
        session_start();
        
        // Chỉ student mới được enroll
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
            $_SESSION['error'] = 'Bạn cần đăng nhập với tư cách học viên';
            header('Location: /btl/auth/login');
            exit;
        }
        
        $enrollmentModel = new Enrollment();
        $student_id = $_SESSION['user_id'];
        
        if ($enrollmentModel->enroll($course_id, $student_id)) {
            $_SESSION['success'] = 'Đăng ký khóa học thành công!';
        } else {
            $_SESSION['error'] = 'Bạn đã đăng ký khóa học này rồi';
        }
        
        header('Location: /btl/courses/detail/' . $course_id);
        exit;
    }
    
    public function myCourses() {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
            header('Location: /btl/auth/login');
            exit;
        }
        
        $enrollmentModel = new Enrollment();
        $courses = $enrollmentModel->getByStudent($_SESSION['user_id']);
        
        include __DIR__ . '/../views/student/my_courses.php';
    }
}
?>