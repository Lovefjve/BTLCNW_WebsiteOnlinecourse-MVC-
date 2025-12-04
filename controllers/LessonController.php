<?php
/**
 * Lesson Controller (Public - cho học viên xem bài học)
 */

require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../models/Enrollment.php';

class LessonController {
    
    public function view($lesson_id) {
        session_start();
        
        $lessonModel = new Lesson();
        $materialModel = new Material();
        $courseModel = new Course();
        
        $lesson = $lessonModel->getById($lesson_id);
        
        if (!$lesson) {
            header('Location: /btl/');
            exit;
        }
        
        $course = $courseModel->getById($lesson['course_id']);
        
        // Kiểm tra enrollment
        $canView = false;
        if (isset($_SESSION['user_id'])) {
            $enrollmentModel = new Enrollment();
            $enrollments = $enrollmentModel->getByStudent($_SESSION['user_id']);
            
            foreach ($enrollments as $enrollment) {
                if ($enrollment['course_id'] == $course['id']) {
                    $canView = true;
                    break;
                }
            }
            
            // Nếu là instructor của course
            if ($_SESSION['role'] == 1 && $_SESSION['user_id'] == $course['instructor_id']) {
                $canView = true;
            }
        }
        
        if (!$canView) {
            $_SESSION['error'] = 'Bạn cần đăng ký khóa học để xem bài học';
            header('Location: /btl/courses/detail/' . $course['id']);
            exit;
        }
        
        // Lấy materials
        $materials = $materialModel->getByLesson($lesson_id);
        
        // Lấy danh sách lessons trong course
        $lessons = $lessonModel->getByCourse($course['id']);
        
        include __DIR__ . '/../views/lessons/view.php';
    }
}
?>