<?php
// controllers/StudentController.php

class StudentController {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            header('Location: ?c=auth&a=login');
            exit;
        }
    }
    
    // Danh sách học viên
    public function index() {
        $course_id = $_GET['course_id'] ?? 0;
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        require_once 'models/Course.php';
        require_once 'models/Enrollment.php';
        require_once 'models/Lesson.php';
        
        $courseModel = new Course();
        $enrollmentModel = new Enrollment();
        $lessonModel = new Lesson();
        
        try {
            $course = $courseModel->getById($course_id);
            
            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xem học viên của khóa học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy danh sách học viên
            $students = $enrollmentModel->getStudentsByCourse($course_id);
            
            // Lấy danh sách bài học
            $lessons = $lessonModel->getByCourse($course_id);
            $total_lessons = count($lessons);
            
            // Thống kê
            $total_students = count($students);
            $active_students = 0;
            $completed_students = 0;
            
            foreach ($students as &$student) {
                // Đảm bảo có các trường cần thiết
                $student['progress'] = $student['progress'] ?? 0;
                $student['completed_lessons'] = $student['completed_lessons'] ?? 0;
                
                if ($student['progress'] >= 100) {
                    $completed_students++;
                } elseif ($student['progress'] > 0) {
                    $active_students++;
                }
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Hiển thị view
        $data = [
            'course' => $course,
            'students' => $students,
            'lessons' => $lessons,
            'total_lessons' => $total_lessons,
            'total_students' => $total_students,
            'active_students' => $active_students,
            'completed_students' => $completed_students
        ];
        
        extract($data);
        
        $view_file = 'views/instructor/students/list.php';
        if (!file_exists($view_file)) {
            die("Không tìm thấy file view: $view_file");
        }
        
        require_once $view_file;
    }
    
    // Xem tiến độ chi tiết của học viên
    public function progress() {
        $course_id = $_GET['course_id'] ?? 0;
        $student_id = $_GET['student_id'] ?? 0;
        
        if (!$course_id || !$student_id) {
            $_SESSION['error'] = "Thiếu thông tin yêu cầu";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        require_once 'models/Course.php';
        require_once 'models/User.php';
        require_once 'models/Lesson.php';
        require_once 'models/Enrollment.php';
        
        $courseModel = new Course();
        $userModel = new User();
        $lessonModel = new Lesson();
        $enrollmentModel = new Enrollment();
        
        try {
            // Lấy thông tin khóa học
            $course = $courseModel->getById($course_id);
            
            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Kiểm tra quyền sở hữu
            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xem tiến độ học viên này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy thông tin học viên
            $student = $userModel->getById($student_id);
            
            if (!$student || $student['role'] != 0) {
                $_SESSION['error'] = "Học viên không tồn tại";
                header('Location: ?c=student&a=index&course_id=' . $course_id);
                exit;
            }
            
            // Kiểm tra học viên có đăng ký khóa học không
            if (!$enrollmentModel->isStudentEnrolled($student_id, $course_id)) {
                $_SESSION['error'] = "Học viên chưa đăng ký khóa học này";
                header('Location: ?c=student&a=index&course_id=' . $course_id);
                exit;
            }
            
            // Lấy tiến độ học tập
            $student_progress = $enrollmentModel->getStudentProgress($student_id, $course_id);
            
            // Lấy danh sách bài học với trạng thái hoàn thành
            $lessons = $lessonModel->getByCourse($course_id);
            foreach ($lessons as &$lesson) {
                $lesson['completed'] = $enrollmentModel->isLessonCompleted($student_id, $lesson['id']);
            }
            
            // Tính toán thêm thông tin
            $total_lessons = count($lessons);
            $completed_lessons = $student_progress['completed_lessons'] ?? 0;
            $progress_percentage = $student_progress['progress'] ?? 0;
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=student&a=index&course_id=' . $course_id);
            exit;
        }
        
        // Hiển thị view
        $data = [
            'course' => $course,
            'student' => $student,
            'student_progress' => $student_progress,
            'lessons' => $lessons,
            'total_lessons' => $total_lessons,
            'completed_lessons' => $completed_lessons,
            'progress_percentage' => $progress_percentage
        ];
        
        extract($data);
        
        $view_file = 'views/instructor/students/progress.php';
        if (!file_exists($view_file)) {
            die("Không tìm thấy file view: $view_file");
        }
        
        require_once $view_file;
    }
    
    // Xuất danh sách học viên
    public function export() {
        $course_id = $_GET['course_id'] ?? 0;
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        require_once 'models/Course.php';
        require_once 'models/Enrollment.php';
        require_once 'models/Lesson.php';
        
        $courseModel = new Course();
        $enrollmentModel = new Enrollment();
        $lessonModel = new Lesson();
        
        try {
            $course = $courseModel->getById($course_id);
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xuất dữ liệu";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            $students = $enrollmentModel->getStudentsByCourse($course_id);
            $lessons = $lessonModel->getByCourse($course_id);
            $total_lessons = count($lessons);
            
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="hoc_vien_khoa_' . $course_id . '_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');
            
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
            echo '<h3>Danh sách học viên - ' . htmlspecialchars($course['title']) . '</h3>';
            echo '<table border="1" style="width:100%;">';
            echo '<tr style="background:#4a6cf7;color:white;font-weight:bold;">';
            echo '<th>STT</th>';
            echo '<th>Họ tên</th>';
            echo '<th>Email</th>';
            echo '<th>Ngày đăng ký</th>';
            echo '<th>Tiến độ</th>';
            echo '<th>Bài học hoàn thành</th>';
            echo '<th>Trạng thái</th>';
            echo '<th>Truy cập gần nhất</th>';
            echo '</tr>';
            
            $stt = 1;
            foreach ($students as $student) {
                $progress = $student['progress'] ?? 0;
                $completed_lessons = $student['completed_lessons'] ?? 0;
                
                // Xác định trạng thái
                if ($progress >= 100) {
                    $status = 'Đã hoàn thành';
                } elseif ($progress > 0) {
                    $status = 'Đang học';
                } else {
                    $status = 'Chưa bắt đầu';
                }
                
                echo '<tr>';
                echo '<td>' . $stt++ . '</td>';
                echo '<td>' . htmlspecialchars($student['fullname'] ?? $student['username'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($student['email'] ?? 'N/A') . '</td>';
                echo '<td>' . (!empty($student['enrolled_at']) ? date('d/m/Y', strtotime($student['enrolled_at'])) : 'N/A') . '</td>';
                echo '<td>' . $progress . '%</td>';
                echo '<td>' . $completed_lessons . '/' . $total_lessons . '</td>';
                echo '<td>' . $status . '</td>';
                echo '<td>' . (!empty($student['last_accessed']) ? date('d/m/Y H:i', strtotime($student['last_accessed'])) : 'Chưa truy cập') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</body></html>';
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi xuất file: " . $e->getMessage();
            header('Location: ?c=student&a=index&course_id=' . $course_id);
            exit;
        }
    }
}