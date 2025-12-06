<?php
// controllers/InstructorController.php
require_once 'models/Course.php';
require_once 'models/Category.php';

class InstructorController {
    
    // PHƯƠNG THỨC QUAN TRỌNG BẠN ĐANG THIẾU
    public function manageCourses() {
        // Kiểm tra session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra đăng nhập và quyền
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            // Tạm fake login để test
            $_SESSION['user_id'] = 1;
            $_SESSION['role'] = 1;
            $_SESSION['fullname'] = "Giảng viên Test";
            // header('Location: ?c=auth&a=login');
            // exit;
        }
        
        $instructor_id = $_SESSION['user_id'];
        $courseModel = new Course();
        
        // Lấy tham số phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Lấy dữ liệu từ Model
        $courses = $courseModel->getByInstructor($instructor_id, $limit, $offset);
        $totalCourses = $courseModel->countByInstructor($instructor_id);
        $totalPages = ceil($totalCourses / $limit);
        $stats = $courseModel->getInstructorStats($instructor_id);
        
        // Nếu không có dữ liệu, tạo dữ liệu mẫu
        if (empty($courses)) {
            $courses = $this->getSampleCourses($instructor_id);
            $totalCourses = count($courses);
            $stats = [
                'total_courses' => $totalCourses,
                'published_courses' => 1,
                'pending_courses' => 0,
                'total_students' => 25
            ];
        }
        
        // Truyền biến cho View
        $GLOBALS['stats'] = $stats;
        $GLOBALS['totalCourses'] = $totalCourses;
        $GLOBALS['courses'] = $courses;
        $GLOBALS['page'] = $page;
        $GLOBALS['totalPages'] = $totalPages;
        
        // Hiển thị View
        require_once 'views/instructor/courses/manage.php';
    }
    
    // Dữ liệu mẫu để test
    private function getSampleCourses($instructor_id) {
        return [
            [
                'id' => 1,
                'title' => 'Lập trình PHP cơ bản',
                'description' => 'Khóa học lập trình PHP cho người mới bắt đầu',
                'instructor_id' => $instructor_id,
                'category_id' => 1,
                'category_name' => 'Lập trình',
                'price' => 500000,
                'duration_weeks' => 8,
                'level' => 'Beginner',
                'image' => '',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'student_count' => 15
            ],
            [
                'id' => 2,
                'title' => 'MySQL Database',
                'description' => 'Học quản lý cơ sở dữ liệu MySQL từ cơ bản đến nâng cao',
                'instructor_id' => $instructor_id,
                'category_id' => 1,
                'category_name' => 'Lập trình',
                'price' => 0,
                'duration_weeks' => 6,
                'level' => 'Intermediate',
                'image' => '',
                'status' => 'draft',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'student_count' => 10
            ]
        ];
    }
    
    public function createCourse() {
        // Kiểm tra session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra đăng nhập và quyền giảng viên
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            // Tạm fake login để test
            $_SESSION['user_id'] = 1;
            $_SESSION['role'] = 1;
            // $_SESSION['error'] = "Bạn cần đăng nhập với quyền giảng viên";
            // header('Location: ?c=auth&a=login');
            // exit;
        }
        
        // Lấy danh mục từ Model
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        
        // Nếu không có danh mục, tạo danh mục mẫu
        if (empty($categories)) {
            $categories = [
                ['id' => 1, 'name' => 'Lập trình'],
                ['id' => 2, 'name' => 'Thiết kế'],
                ['id' => 3, 'name' => 'Kinh doanh'],
                ['id' => 4, 'name' => 'Marketing']
            ];
        }
        
        // Truyền biến cho View
        $GLOBALS['categories'] = $categories;
        
        // Hiển thị View
        require_once 'views/instructor/courses/create.php';
    }
    
    public function storeCourse() {
        // Kiểm tra session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=courses/create');
            exit;
        }
        
        // Kiểm tra quyền
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=auth&a=login');
            exit;
        }
        
        // Validate dữ liệu
        $errors = $this->validateCourseData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=instructor&a=courses/create');
            exit;
        }
        
        // Tạo đối tượng Course
        $courseModel = new Course();
        
        // Gán giá trị
        $courseModel->title = trim($_POST['title']);
        $courseModel->description = trim($_POST['description']);
        $courseModel->instructor_id = $_SESSION['user_id'];
        $courseModel->category_id = (int)$_POST['category_id'];
        $courseModel->price = (float)$_POST['price'];
        $courseModel->duration_weeks = (int)$_POST['duration_weeks'];
        $courseModel->level = $_POST['level'];
        $courseModel->status = 'draft';
        
        // Xử lý upload ảnh
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadImage($_FILES['image']);
            if ($uploadResult['success']) {
                $courseModel->image = $uploadResult['filename'];
            } else {
                $_SESSION['error'] = $uploadResult['error'];
                header('Location: ?c=instructor&a=courses/create');
                exit;
            }
        }
        
        // Lưu vào database
        if ($courseModel->create()) {
            $_SESSION['success'] = "Tạo khóa học '" . htmlspecialchars($courseModel->title) . "' thành công!";
            header('Location: ?c=instructor&a=courses');
            exit;
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi lưu khóa học. Vui lòng thử lại.";
            header('Location: ?c=instructor&a=courses/create');
            exit;
        }
    }
    
    private function validateCourseData($data) {
        $errors = [];
        
        // Title validation
        if (empty(trim($data['title'] ?? ''))) {
            $errors['title'] = "Tên khóa học không được để trống";
        } elseif (strlen(trim($data['title'])) < 5) {
            $errors['title'] = "Tên khóa học phải có ít nhất 5 ký tự";
        }
        
        // Description validation
        if (empty(trim($data['description'] ?? ''))) {
            $errors['description'] = "Mô tả không được để trống";
        } elseif (strlen(trim($data['description'])) < 20) {
            $errors['description'] = "Mô tả phải có ít nhất 20 ký tự";
        }
        
        // Price validation
        if (!is_numeric($data['price'] ?? 0) || $data['price'] < 0) {
            $errors['price'] = "Giá không hợp lệ";
        }
        
        // Duration validation
        if (!is_numeric($data['duration_weeks'] ?? 0) || $data['duration_weeks'] < 1 || $data['duration_weeks'] > 52) {
            $errors['duration_weeks'] = "Thời lượng phải từ 1 đến 52 tuần";
        }
        
        return $errors;
    }
    
    private function uploadImage($file) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Lỗi upload file'];
        }
        
        // Kiểm tra loại file
        $file_type = mime_content_type($file['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            return ['success' => false, 'error' => 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF)'];
        }
        
        // Kiểm tra kích thước
        if ($file['size'] > $max_size) {
            return ['success' => false, 'error' => 'File ảnh không được vượt quá 5MB'];
        }
        
        // Tạo thư mục nếu chưa có
        $upload_dir = 'assets/uploads/courses/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Tạo tên file mới
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'course_' . time() . '_' . uniqid() . '.' . strtolower($ext);
        $target_path = $upload_dir . $new_filename;
        
        // Di chuyển file
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return ['success' => true, 'filename' => $new_filename];
        }
        
        return ['success' => false, 'error' => 'Không thể lưu file'];
    }
}
?>