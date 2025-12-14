<?php
// controllers/LessonController.php

class LessonController {

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            header('Location: ?c=auth&a=login');
            exit;
        }
    }
    
    private function render($viewPath, $data = [])
    {
        extract($data);
        $fullPath = "views/{$viewPath}.php";
        
        if (!file_exists($fullPath)) {
            die("Lỗi: Không tìm thấy view '{$viewPath}'");
        }
        
        require_once $fullPath;
    }

    public function index() {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Lấy course_id từ URL
        $course_id = $_GET['course_id'] ?? 0;
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Load models
        require_once 'models/Course.php';
        require_once 'models/Lesson.php';
        
        $courseModel = new Course();
        $lessonModel = new Lesson();
        
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
                $_SESSION['error'] = "Bạn không có quyền quản lý bài học khóa học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy danh sách bài học
            $lessons = $lessonModel->getByCourse($course_id);
            
            // Thống kê
            $total_lessons = count($lessons);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // DEBUG: Kiểm tra biến (có thể bỏ khi chạy ổn)
        // echo "<pre style='display:none'>";
        // echo "Course ID: " . $course_id . "\n";
        // echo "Course Title: " . ($course['title'] ?? 'N/A') . "\n";
        // echo "Lessons count: " . count($lessons) . "\n";
        // echo "</pre>";
        
        // Hiển thị view - TRUYỀN BIẾN ĐẦY ĐỦ theo đúng tên biến view cần
        
        $this->render('instructor/lessons/manage', [
            'course' => $course,
            'course_title' => $course['title'] ?? 'Khóa học',
            'course_id' => $course_id,
            'lessons' => $lessons,
            'total_lessons' => $total_lessons,
        ]);
    }
    
    public function create() {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Lấy course_id từ URL
        $course_id = $_GET['course_id'] ?? 0;
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Load models
        require_once 'models/Course.php';
        require_once 'models/Lesson.php';
        
        $courseModel = new Course();
        $lessonModel = new Lesson();
        
        // KHỞI TẠO BIẾN
        $errors = [];
        $old_input = [];
        
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
                $_SESSION['error'] = "Bạn không có quyền thêm bài học cho khóa học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy thứ tự bài học tiếp theo
            $lessons = $lessonModel->getByCourse($course_id);
            $next_order = count($lessons) + 1;
            
            // Lấy errors và old input từ session
            $errors = $_SESSION['errors'] ?? [];
            $old_input = $_SESSION['old_input'] ?? [];
            
            // Xóa session data sau khi dùng
            unset($_SESSION['errors']);
            unset($_SESSION['old_input']);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=lesson&a=index&course_id=' . $course_id);
            exit;
        }
        
        // Hiển thị view
        $this->render('instructor/lessons/create', [
            'course' => $course,
            'next_order' => $next_order,
            'errors' => $errors,
            'old_input' => $old_input
        ]);
    }
    
    public function store() {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Validation
        $errors = $this->validateLessonData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=lesson&a=create&course_id=' . ($_POST['course_id'] ?? 0));
            exit;
        }
        
        // Load model
        require_once 'models/Lesson.php';
        $lessonModel = new Lesson();
        
        try {
            // Tạo dữ liệu bài học
            $lessonData = [
                'course_id' => (int)$_POST['course_id'],
                'title' => trim($_POST['title']),
                'content' => trim($_POST['content']),
                'video_url' => trim($_POST['video_url'] ?? ''),
                'order' => (int)($_POST['order'] ?? 1)
            ];
            
            if ($lessonModel->create($lessonData)) {
                $_SESSION['success'] = "Tạo bài học thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi lưu bài học";
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header('Location: ?c=lesson&a=index&course_id=' . ($_POST['course_id'] ?? 0));
        exit;
    }
    
    public function edit() {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $lesson_id = $_GET['id'] ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        
        if (!$lesson_id || !$course_id) {
            $_SESSION['error'] = "Không tìm thấy bài học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Load models
        require_once 'models/Course.php';
        require_once 'models/Lesson.php';
        
        $courseModel = new Course();
        $lessonModel = new Lesson();
        
        // KHỞI TẠO BIẾN
        $errors = [];
        $old_input = [];
        
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
                $_SESSION['error'] = "Bạn không có quyền chỉnh sửa bài học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy thông tin bài học
            $lesson = $lessonModel->getById($lesson_id);
            
            if (!$lesson) {
                $_SESSION['error'] = "Bài học không tồn tại";
                header('Location: ?c=lesson&a=index&course_id=' . $course_id);
                exit;
            }
            
            // Kiểm tra bài học thuộc khóa học
            if ($lesson['course_id'] != $course_id) {
                $_SESSION['error'] = "Bài học không thuộc khóa học này";
                header('Location: ?c=lesson&a=index&course_id=' . $course_id);
                exit;
            }
            
            // Lấy errors và old input từ session
            $errors = $_SESSION['errors'] ?? [];
            $old_input = $_SESSION['old_input'] ?? [];
            
            // Xóa session data sau khi dùng
            unset($_SESSION['errors']);
            unset($_SESSION['old_input']);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=lesson&a=index&course_id=' . $course_id);
            exit;
        }
        
        // Hiển thị view
        $this->render('instructor/lessons/edit', [
            'course' => $course,
            'lesson' => $lesson,
            'errors' => $errors,
            'old_input' => $old_input
        ]);
    }
    
    public function update() {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $lesson_id = (int)($_POST['id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);
        
        if (!$lesson_id || !$course_id) {
            $_SESSION['error'] = "Không tìm thấy bài học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Validation
        $errors = $this->validateLessonData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=lesson&a=edit&id=' . $lesson_id . '&course_id=' . $course_id);
            exit;
        }
        
        // Load model
        require_once 'models/Lesson.php';
        $lessonModel = new Lesson();
        
        try {
            // Kiểm tra quyền sở hữu (qua course)
            require_once 'models/Course.php';
            $courseModel = new Course();
            $course = $courseModel->getById($course_id);
            
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền chỉnh sửa bài học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Cập nhật dữ liệu - SỬA LẠI CHO ĐÚNG VỚI CSDL
            $lessonData = [
                'title' => trim($_POST['title']),
                'content' => trim($_POST['content']),
                'video_url' => trim($_POST['video_url'] ?? ''),
                'order' => (int)($_POST['order'] ?? 1)
            ];
            
            if ($lessonModel->update($lesson_id, $lessonData)) {
                $_SESSION['success'] = "Cập nhật bài học thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật bài học";
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header('Location: ?c=lesson&a=index&course_id=' . $course_id);
        exit;
    }
    
    public function delete() {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $lesson_id = (int)($_POST['id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);
        
        if (!$lesson_id || !$course_id) {
            $_SESSION['error'] = "Không tìm thấy bài học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Load model
        require_once 'models/Lesson.php';
        $lessonModel = new Lesson();
        
        try {
            // Kiểm tra quyền sở hữu
            require_once 'models/Course.php';
            $courseModel = new Course();
            $course = $courseModel->getById($course_id);
            
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xóa bài học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Xóa bài học
            if ($lessonModel->delete($lesson_id)) {
                $_SESSION['success'] = "Xóa bài học thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi xóa bài học";
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header('Location: ?c=lesson&a=index&course_id=' . $course_id);
        exit;
    }
    
    // VALIDATE ĐÚNG THEO YÊU CẦU CỦA BẠN
    private function validateLessonData($data) {
        $errors = [];
        
        if (empty(trim($data['title'] ?? '')) || strlen(trim($data['title'])) < 3) {
            $errors['title'] = "Tiêu đề bài học phải có ít nhất 3 ký tự";
        }
        
        if (empty(trim($data['content'] ?? '')) || strlen(trim($data['content'])) < 10) {
            $errors['content'] = "Nội dung bài học phải có ít nhất 10 ký tự";
        }
        
        if (!is_numeric($data['order'] ?? 0) || $data['order'] < 1) {
            $errors['order'] = "Thứ tự bài học không hợp lệ";
        }
        
        if (!empty($data['video_url'] ?? '') && !filter_var($data['video_url'], FILTER_VALIDATE_URL)) {
            $errors['video_url'] = "URL video không hợp lệ";
        }
        
        return $errors;
    }
}
?>