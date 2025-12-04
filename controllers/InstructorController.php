<?php
namespace Controllers;

use Models\Course;
use Models\Lesson;
use Models\Material;
use Models\Enrollment;

class InstructorController
{
    private $courseModel;
    private $lessonModel;
    private $materialModel;
    private $enrollmentModel;
    
    public function __construct()
    {
        // Khởi tạo session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra đăng nhập và quyền giảng viên
        $this->checkInstructorAccess();
        
        // Khởi tạo models
        $this->courseModel = new Course();
        $this->lessonModel = new Lesson();
        $this->materialModel = new Material();
        $this->enrollmentModel = new Enrollment();
    }
    
    private function checkInstructorAccess()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header('Location: /auth/login');
            exit();
        }
    }
    
    // Dashboard giảng viên
    public function dashboard()
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Lấy thống kê
        $courses = $this->courseModel->getByInstructor($instructor_id);
        $enrollmentStats = $this->enrollmentModel->getEnrollmentStats($instructor_id);
        
        // Tính tổng số học viên
        $totalStudents = 0;
        foreach ($enrollmentStats as $stat) {
            $totalStudents += $stat['total_students'];
        }
        
        $data = [
            'title' => 'Bảng điều khiển giảng viên',
            'courses' => $courses,
            'enrollmentStats' => $enrollmentStats,
            'totalStudents' => $totalStudents,
            'totalCourses' => count($courses)
        ];
        
        $this->view('instructor/dashboard', $data);
    }
    
    // Quản lý khóa học
    public function courses()
    {
        $instructor_id = $_SESSION['user_id'];
        $courses = $this->courseModel->getByInstructor($instructor_id);
        
        $data = [
            'title' => 'Quản lý khóa học',
            'courses' => $courses
        ];
        
        $this->view('instructor/courses/manage', $data);
    }
    
    // Tạo khóa học mới - Hiển thị form
    public function createCourse()
    {
        $categories = $this->courseModel->getCategories();
        
        $data = [
            'title' => 'Tạo khóa học mới',
            'categories' => $categories,
            'levels' => ['Beginner' => 'Beginner', 'Intermediate' => 'Intermediate', 'Advanced' => 'Advanced']
        ];
        
        $this->view('instructor/courses/create', $data);
    }
    
    // Xử lý tạo khóa học
    public function storeCourse()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /instructor/courses/create');
            exit();
        }
        
        // Validate input
        $errors = [];
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $duration_weeks = intval($_POST['duration_weeks'] ?? 0);
        $level = $_POST['level'] ?? '';
        
        if (empty($title)) {
            $errors[] = 'Tiêu đề không được để trống';
        }
        
        if (empty($description)) {
            $errors[] = 'Mô tả không được để trống';
        }
        
        if ($category_id <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }
        
        if (!in_array($level, ['Beginner', 'Intermediate', 'Advanced'])) {
            $errors[] = 'Cấp độ không hợp lệ';
        }
        
        // Xử lý upload ảnh
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['image']['type'], $allowed_types) && 
                $_FILES['image']['size'] <= $max_size) {
                
                $upload_dir = 'assets/uploads/courses/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $image = $destination;
                }
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        if (!empty($errors)) {
            $categories = $this->courseModel->getCategories();
            
            $data = [
                'title' => 'Tạo khóa học mới',
                'categories' => $categories,
                'levels' => ['Beginner' => 'Beginner', 'Intermediate' => 'Intermediate', 'Advanced' => 'Advanced'],
                'errors' => $errors,
                'old' => $_POST
            ];
            
            $this->view('instructor/courses/create', $data);
            return;
        }
        
        // Tạo khóa học
        $this->courseModel->title = $title;
        $this->courseModel->description = $description;
        $this->courseModel->instructor_id = $_SESSION['user_id'];
        $this->courseModel->category_id = $category_id;
        $this->courseModel->price = $price;
        $this->courseModel->duration_weeks = $duration_weeks;
        $this->courseModel->level = $level;
        $this->courseModel->image = $image;
        
        if ($this->courseModel->create()) {
            $_SESSION['success'] = 'Tạo khóa học thành công!';
            header('Location: /instructor/courses/' . $this->courseModel->id);
            exit();
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo khóa học';
            header('Location: /instructor/courses/create');
            exit();
        }
    }
    
    // Xem chi tiết khóa học
    public function showCourse($id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập khóa học này';
            header('Location: /instructor/courses');
            exit();
        }
        
        $course = $this->courseModel->getById($id);
        $lessons = $this->lessonModel->getByCourse($id, $instructor_id);
        
        $data = [
            'title' => $course['title'],
            'course' => $course,
            'lessons' => $lessons
        ];
        
        $this->view('instructor/courses/detail', $data);
    }
    
    // Sửa khóa học - Hiển thị form
    public function editCourse($id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền sửa khóa học này';
            header('Location: /instructor/courses');
            exit();
        }
        
        $course = $this->courseModel->getById($id);
        $categories = $this->courseModel->getCategories();
        
        $data = [
            'title' => 'Sửa khóa học: ' . $course['title'],
            'course' => $course,
            'categories' => $categories,
            'levels' => ['Beginner' => 'Beginner', 'Intermediate' => 'Intermediate', 'Advanced' => 'Advanced']
        ];
        
        $this->view('instructor/courses/edit', $data);
    }
    
    // Xử lý cập nhật khóa học
    public function updateCourse($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /instructor/courses');
            exit();
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền sửa khóa học này';
            header('Location: /instructor/courses');
            exit();
        }
        
        // Validate input
        $errors = [];
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $duration_weeks = intval($_POST['duration_weeks'] ?? 0);
        $level = $_POST['level'] ?? '';
        
        if (empty($title)) {
            $errors[] = 'Tiêu đề không được để trống';
        }
        
        if (empty($description)) {
            $errors[] = 'Mô tả không được để trống';
        }
        
        if ($category_id <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }
        
        if (!in_array($level, ['Beginner', 'Intermediate', 'Advanced'])) {
            $errors[] = 'Cấp độ không hợp lệ';
        }
        
        // Xử lý upload ảnh mới (nếu có)
        $image = $_POST['current_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['image']['type'], $allowed_types) && 
                $_FILES['image']['size'] <= $max_size) {
                
                $upload_dir = 'assets/uploads/courses/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Xóa ảnh cũ nếu có
                if (!empty($image) && file_exists($image)) {
                    unlink($image);
                }
                
                $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $destination = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $image = $destination;
                }
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        if (!empty($errors)) {
            $course = $this->courseModel->getById($id);
            $categories = $this->courseModel->getCategories();
            
            $data = [
                'title' => 'Sửa khóa học: ' . $course['title'],
                'course' => $course,
                'categories' => $categories,
                'levels' => ['Beginner' => 'Beginner', 'Intermediate' => 'Intermediate', 'Advanced' => 'Advanced'],
                'errors' => $errors,
                'old' => $_POST
            ];
            
            $this->view('instructor/courses/edit', $data);
            return;
        }
        
        // Cập nhật khóa học
        $this->courseModel->id = $id;
        $this->courseModel->title = $title;
        $this->courseModel->description = $description;
        $this->courseModel->instructor_id = $instructor_id;
        $this->courseModel->category_id = $category_id;
        $this->courseModel->price = $price;
        $this->courseModel->duration_weeks = $duration_weeks;
        $this->courseModel->level = $level;
        $this->courseModel->image = $image;
        
        if ($this->courseModel->update()) {
            $_SESSION['success'] = 'Cập nhật khóa học thành công!';
            header('Location: /instructor/courses/' . $id);
            exit();
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật khóa học';
            header('Location: /instructor/courses/' . $id . '/edit');
            exit();
        }
    }
    
    // Xóa khóa học
    public function deleteCourse($id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền xóa khóa học này';
            header('Location: /instructor/courses');
            exit();
        }
        
        if ($this->courseModel->delete($id, $instructor_id)) {
            $_SESSION['success'] = 'Xóa khóa học thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa khóa học';
        }
        
        header('Location: /instructor/courses');
        exit();
    }
    
    // QUẢN LÝ BÀI HỌC
    
    // Danh sách bài học của khóa học
    public function lessons($course_id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập';
            header('Location: /instructor/courses');
            exit();
        }
        
        $course = $this->courseModel->getById($course_id);
        $lessons = $this->lessonModel->getByCourse($course_id, $instructor_id);
        
        $data = [
            'title' => 'Quản lý bài học: ' . $course['title'],
            'course' => $course,
            'lessons' => $lessons
        ];
        
        $this->view('instructor/lessons/manage', $data);
    }
    
    // Tạo bài học mới - Hiển thị form
    public function createLesson($course_id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền thêm bài học';
            header('Location: /instructor/courses');
            exit();
        }
        
        $course = $this->courseModel->getById($course_id);
        $lessonCount = $this->lessonModel->countByCourse($course_id);
        
        $data = [
            'title' => 'Thêm bài học mới',
            'course' => $course,
            'nextOrder' => $lessonCount + 1
        ];
        
        $this->view('instructor/lessons/create', $data);
    }
    
    // Xử lý tạo bài học
    public function storeLesson($course_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /instructor/courses/' . $course_id . '/lessons/create');
            exit();
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền thêm bài học';
            header('Location: /instructor/courses');
            exit();
        }
        
        // Validate input
        $errors = [];
        
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        $order = intval($_POST['order'] ?? 0);
        
        if (empty($title)) {
            $errors[] = 'Tiêu đề không được để trống';
        }
        
        if (empty($content)) {
            $errors[] = 'Nội dung không được để trống';
        }
        
        // Nếu có lỗi, hiển thị lại form
        if (!empty($errors)) {
            $course = $this->courseModel->getById($course_id);
            $lessonCount = $this->lessonModel->countByCourse($course_id);
            
            $data = [
                'title' => 'Thêm bài học mới',
                'course' => $course,
                'nextOrder' => $lessonCount + 1,
                'errors' => $errors,
                'old' => $_POST
            ];
            
            $this->view('instructor/lessons/create', $data);
            return;
        }
        
        // Tạo bài học
        $this->lessonModel->course_id = $course_id;
        $this->lessonModel->title = $title;
        $this->lessonModel->content = $content;
        $this->lessonModel->video_url = $video_url;
        $this->lessonModel->order = $order;
        
        if ($this->lessonModel->create()) {
            $_SESSION['success'] = 'Thêm bài học thành công!';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit();
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm bài học';
            header('Location: /instructor/courses/' . $course_id . '/lessons/create');
            exit();
        }
    }
    
    // Sửa bài học - Hiển thị form
    public function editLesson($course_id, $lesson_id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền sửa bài học';
            header('Location: /instructor/courses');
            exit();
        }
        
        $course = $this->courseModel->getById($course_id);
        $lesson = $this->lessonModel->getById($lesson_id, $instructor_id);
        
        if (!$lesson) {
            $_SESSION['error'] = 'Bài học không tồn tại';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit();
        }
        
        $data = [
            'title' => 'Sửa bài học: ' . $lesson['title'],
            'course' => $course,
            'lesson' => $lesson
        ];
        
        $this->view('instructor/lessons/edit', $data);
    }
    
    // Xử lý cập nhật bài học
    public function updateLesson($course_id, $lesson_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit();
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền sửa bài học';
            header('Location: /instructor/courses');
            exit();
        }
        
        // Validate input
        $errors = [];
        
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        $order = intval($_POST['order'] ?? 0);
        
        if (empty($title)) {
            $errors[] = 'Tiêu đề không được để trống';
        }
        
        if (empty($content)) {
            $errors[] = 'Nội dung không được để trống';
        }
        
        // Nếu có lỗi, hiển thị lại form
        if (!empty($errors)) {
            $course = $this->courseModel->getById($course_id);
            $lesson = $this->lessonModel->getById($lesson_id, $instructor_id);
            
            $data = [
                'title' => 'Sửa bài học: ' . $lesson['title'],
                'course' => $course,
                'lesson' => $lesson,
                'errors' => $errors,
                'old' => $_POST
            ];
            
            $this->view('instructor/lessons/edit', $data);
            return;
        }
        
        // Cập nhật bài học
        $this->lessonModel->id = $lesson_id;
        $this->lessonModel->title = $title;
        $this->lessonModel->content = $content;
        $this->lessonModel->video_url = $video_url;
        $this->lessonModel->order = $order;
        
        if ($this->lessonModel->update()) {
            $_SESSION['success'] = 'Cập nhật bài học thành công!';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit();
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật bài học';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/edit');
            exit();
        }
    }
    
    // Xóa bài học
    public function deleteLesson($course_id, $lesson_id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền xóa bài học';
            header('Location: /instructor/courses');
            exit();
        }
        
        if ($this->lessonModel->delete($lesson_id, $instructor_id)) {
            $_SESSION['success'] = 'Xóa bài học thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa bài học';
        }
        
        header('Location: /instructor/courses/' . $course_id . '/lessons');
        exit();
    }
    
    // QUẢN LÝ TÀI LIỆU
    
    // Upload tài liệu - Hiển thị form
    public function uploadMaterial($course_id, $lesson_id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền upload tài liệu';
            header('Location: /instructor/courses');
            exit();
        }
        
        $course = $this->courseModel->getById($course_id);
        $lesson = $this->lessonModel->getById($lesson_id, $instructor_id);
        
        if (!$lesson) {
            $_SESSION['error'] = 'Bài học không tồn tại';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit();
        }
        
        $data = [
            'title' => 'Upload tài liệu cho: ' . $lesson['title'],
            'course' => $course,
            'lesson' => $lesson
        ];
        
        $this->view('instructor/materials/upload', $data);
    }
    
    // Xử lý upload tài liệu
    public function storeMaterial($course_id, $lesson_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/materials/upload');
            exit();
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền upload tài liệu';
            header('Location: /instructor/courses');
            exit();
        }
        
        // Kiểm tra file upload
        if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Vui lòng chọn file để upload';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/materials/upload');
            exit();
        }
        
        $file = $_FILES['material_file'];
        
        // Kiểm tra loại file
        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'application/zip',
            'application/x-rar-compressed',
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
        
        $max_size = 50 * 1024 * 1024; // 50MB
        
        if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
            $_SESSION['error'] = 'File không hợp lệ hoặc quá lớn (tối đa 50MB)';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/materials/upload');
            exit();
        }
        
        // Tạo thư mục upload nếu chưa tồn tại
        $upload_dir = 'assets/uploads/materials/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Tạo tên file unique
        $filename = uniqid() . '_' . basename($file['name']);
        $destination = $upload_dir . $filename;
        
        // Di chuyển file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi upload file';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/materials/upload');
            exit();
        }
        
        // Lưu thông tin vào database
        $this->materialModel->lesson_id = $lesson_id;
        $this->materialModel->filename = $file['name'];
        $this->materialModel->file_path = $destination;
        $this->materialModel->file_type = $this->materialModel->getFileType($file['name']);
        
        if ($this->materialModel->create()) {
            $_SESSION['success'] = 'Upload tài liệu thành công!';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit();
        } else {
            // Xóa file vật lý nếu lưu database thất bại
            if (file_exists($destination)) {
                unlink($destination);
            }
            
            $_SESSION['error'] = 'Có lỗi xảy ra khi lưu thông tin tài liệu';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/materials/upload');
            exit();
        }
    }
    
    // Xóa tài liệu
    public function deleteMaterial($course_id, $lesson_id, $material_id)
    {
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu
        if (!$this->courseModel->isInstructorCourse($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền xóa tài liệu';
            header('Location: /instructor/courses');
            exit();
        }
        
        if ($this->materialModel->delete($material_id, $instructor_id)) {
            $_SESSION['success'] = 'Xóa tài liệu thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tài liệu';
        }
        
        header('Location: /instructor/courses/' . $course_id . '/lessons');
        exit();
    }
    
    // QUẢN LÝ HỌC VIÊN
    
    // Danh sách học viên
    public function students($course_id = null)
    {
        $instructor_id = $_SESSION['user_id'];
        
        $students = $this->enrollmentModel->getStudentsByInstructor($instructor_id, $course_id);
        
        if ($course_id) {
            $course = $this->courseModel->getById($course_id);
            $title = 'Danh sách học viên: ' . $course['title'];
        } else {
            $courses = $this->courseModel->getByInstructor($instructor_id);
            $title = 'Danh sách học viên';
        }
        
        $data = [
            'title' => $title,
            'students' => $students,
            'course_id' => $course_id,
            'courses' => $courses ?? null
        ];
        
        $this->view('instructor/students/list', $data);
    }
    
    // Cập nhật tiến độ học viên
    public function updateStudentProgress($enrollment_id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /instructor/students');
            exit();
        }
        
        $instructor_id = $_SESSION['user_id'];
        $progress = intval($_POST['progress'] ?? 0);
        
        // Validate progress
        if ($progress < 0 || $progress > 100) {
            $_SESSION['error'] = 'Tiến độ phải từ 0 đến 100';
            header('Location: /instructor/students');
            exit();
        }
        
        if ($this->enrollmentModel->updateProgress($enrollment_id, $progress, $instructor_id)) {
            $_SESSION['success'] = 'Cập nhật tiến độ thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật tiến độ';
        }
        
        header('Location: /instructor/students');
        exit();
    }
    
    // Helper function để render view
    private function view($view, $data = [])
    {
        // Extract data để sử dụng trong view
        extract($data);
        
        // Include header
        require_once 'views/layouts/header.php';
        
        // Include sidebar
        require_once 'views/layouts/sidebar.php';
        
        // Include main content
        $viewPath = 'views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "<div class='alert alert-danger'>View không tồn tại: $viewPath</div>";
        }
        
        // Include footer
        require_once 'views/layouts/footer.php';
    }
}