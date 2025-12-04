<?php
/**
 * Course Controller (Public)
 */

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Enrollment.php';

class CourseController {
    
    public function index() {
        $courseModel = new Course();
        $categoryModel = new Category();
        
        $keyword = $_GET['search'] ?? '';
        $category_id = $_GET['category'] ?? null;
        
        if ($keyword) {
            $courses = $courseModel->search($keyword, $category_id);
        } else {
            $courses = $courseModel->getAll('published');
        }
        
        $categories = $categoryModel->getAll();
        
        include __DIR__ . '/../views/courses/index.php';
    }
    
    public function detail($id) {
        session_start();
        
        $courseModel = new Course();
        $enrollmentModel = new Enrollment();
        
        $course = $courseModel->getById($id);
        
        if (!$course) {
            header('Location: /btl/courses');
            exit;
        }
        
        // Kiểm tra enrollment nếu user đã login
        $isEnrolled = false;
        if (isset($_SESSION['user_id']) && $_SESSION['role'] == 0) {
            $enrollments = $enrollmentModel->getByStudent($_SESSION['user_id']);
            foreach ($enrollments as $enrollment) {
                if ($enrollment['course_id'] == $id) {
                    $isEnrolled = true;
                    break;
                }
            }
        }
        
        include __DIR__ . '/../views/courses/detail.php';
    }
}
?>