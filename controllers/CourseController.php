<?php
// controllers/CourseController.php

class CourseController
{
    private function render($viewPath, $data = [])
    {
        extract($data);
        $fullPath = "views/{$viewPath}.php";

        if (!file_exists($fullPath)) {
            die("Lỗi: Không tìm thấy view '{$viewPath}'");
        }

        require_once $fullPath;
    }

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // KIỂM TRA ĐĂNG NHẬP VÀ QUYỀN GIẢNG VIÊN
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            header('Location: ?c=auth&a=login');
            exit;
        }
    }

    // ========== DANH SÁCH KHÓA HỌC ==========
    public function index()
    {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=home&a=index');
            exit;
        }

        $instructor_id = $_SESSION['user_id'];

        // Load model
        require_once 'models/Course.php';
        $courseModel = new Course();

        try {
            // Lấy thống kê
            $stats = $courseModel->getInstructorStats($instructor_id);
            
            // Lấy danh sách khóa học
            $courses = $courseModel->getByInstructor($instructor_id, 10, 0);
            
            // Lấy tổng số khóa học
            $totalCourses = $courseModel->countByInstructor($instructor_id);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            $stats = [
                'total_courses' => 0,
                'published_courses' => 0,
                'pending_courses' => 0,
                'total_students' => 0
            ];
            $courses = [];
            $totalCourses = 0;
        }

        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $totalPages = ceil($totalCourses / $limit);

        // Gọi view
        $this->render('instructor/course/manage', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'page' => $page,
            'totalPages' => $totalPages,
            'stats' => $stats
        ]);
    }

    // ========== XEM CHI TIẾT KHÓA HỌC ==========
    public function show()
    {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=home&a=index');
            exit;
        }

        $course_id = $_GET['id'] ?? 0;
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=course&a=index');
            exit;
        }

        require_once 'models/Course.php';
        $courseModel = new Course();

        try {
            $course = $courseModel->getById($course_id);
            
            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=course&a=index');
                exit;
            }

            // Kiểm tra quyền sở hữu
            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xem khóa học này";
                header('Location: ?c=course&a=index');
                exit;
            }

        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=course&a=index');
            exit;
        }

        $this->render('instructor/course/show', [
            'course' => $course
        ]);
    }

    // ========== TẠO KHÓA HỌC ==========
    public function create()
    {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=course&a=index');
            exit;
        }

        // Lấy danh mục từ Model
        require_once 'models/Category.php';
        $categoryModel = new Category();
        
        try {
            $categories = $categoryModel->getAll();
        } catch (Exception $e) {
            $categories = [];
        }

        // Lấy errors và old input từ session
        $errors = $_SESSION['errors'] ?? [];
        $old_input = $_SESSION['old_input'] ?? [];

        // Xóa session data sau khi dùng
        unset($_SESSION['errors']);
        unset($_SESSION['old_input']);

        $this->render('instructor/course/create', [
            'categories' => $categories,
            'errors' => $errors,
            'old_input' => $old_input
        ]);
    }

    // ========== LƯU KHÓA HỌC ==========
    public function store()
    {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=course&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=course&a=create');
            exit;
        }

        // Validation
        $errors = $this->validateCourseData($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=course&a=create');
            exit;
        }

        // Xử lý upload ảnh
        $image_name = $this->handleImageUpload();

        require_once 'models/Course.php';
        $courseModel = new Course();

        try {
            $courseData = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'instructor_id' => $_SESSION['user_id'],
                'category_id' => (int)$_POST['category_id'],
                'price' => (float)$_POST['price'],
                'duration_weeks' => (int)$_POST['duration_weeks'],
                'level' => $_POST['level'],
                'image' => $image_name,
                'status' => 'pending'
            ];

            if ($courseModel->create($courseData)) {
                $_SESSION['success'] = "Tạo khóa học thành công! Đang chờ duyệt.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi lưu khóa học";
            }

        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }

        header('Location: ?c=course&a=index');
        exit;
    }

    // ========== SỬA KHÓA HỌC ==========
    public function edit()
    {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=course&a=index');
            exit;
        }

        $course_id = $_GET['id'] ?? 0;

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=course&a=index');
            exit;
        }

        require_once 'models/Course.php';
        require_once 'models/Category.php';

        $courseModel = new Course();
        $categoryModel = new Category();

        try {
            $course = $courseModel->getById($course_id);

            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=course&a=index');
                exit;
            }

            // Kiểm tra quyền sở hữu
            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền chỉnh sửa khóa học này";
                header('Location: ?c=course&a=index');
                exit;
            }

            $categories = $categoryModel->getAll();

            // Lấy errors và old input từ session
            $errors = $_SESSION['errors'] ?? [];
            $old_input = $_SESSION['old_input'] ?? [];

            // Xóa session data sau khi dùng
            unset($_SESSION['errors']);
            unset($_SESSION['old_input']);

        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=course&a=index');
            exit;
        }

        $this->render('instructor/course/edit', [
            'course' => $course,
            'categories' => $categories,
            'errors' => $errors,
            'old_input' => $old_input
        ]);
    }

    // ========== CẬP NHẬT KHÓA HỌC ==========
    public function update()
    {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=course&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=course&a=index');
            exit;
        }

        $course_id = (int)($_POST['id'] ?? 0);

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=course&a=index');
            exit;
        }

        // Validation
        $errors = $this->validateCourseData($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=course&a=edit&id=' . $course_id);
            exit;
        }

        require_once 'models/Course.php';
        $courseModel = new Course();

        try {
            // Kiểm tra quyền sở hữu
            $course = $courseModel->getById($course_id);

            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=course&a=index');
                exit;
            }

            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền chỉnh sửa khóa học này";
                header('Location: ?c=course&a=index');
                exit;
            }

            // Xử lý upload ảnh mới
            $image_name = $course['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $new_image = $this->handleImageUpload();
                if (!empty($new_image)) {
                    $image_name = $new_image;
                }
            }

            $updateData = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'category_id' => (int)$_POST['category_id'],
                'price' => (float)$_POST['price'],
                'duration_weeks' => (int)$_POST['duration_weeks'],
                'level' => $_POST['level'],
                'image' => $image_name,
                'status' => $_POST['status']
            ];

            if ($courseModel->update($course_id, $updateData)) {
                $_SESSION['success'] = "Cập nhật khóa học thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật khóa học";
            }

        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }

        header('Location: ?c=course&a=index');
        exit;
    }

    // ========== XÓA KHÓA HỌC ==========
    public function delete()
    {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=course&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=course&a=index');
            exit;
        }

        $course_id = (int)($_POST['id'] ?? 0);

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=course&a=index');
            exit;
        }

        require_once 'models/Course.php';
        $courseModel = new Course();

        try {
            // Kiểm tra quyền sở hữu
            $course = $courseModel->getById($course_id);

            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=course&a=index');
                exit;
            }

            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xóa khóa học này";
                header('Location: ?c=course&a=index');
                exit;
            }

            if ($courseModel->delete($course_id)) {
                $_SESSION['success'] = "Xóa khóa học thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi xóa khóa học";
            }

        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }

        header('Location: ?c=course&a=index');
        exit;
    }

    // ========== VALIDATION ==========
    private function validateCourseData($data)
    {
        $errors = [];

        if (empty(trim($data['title'] ?? '')) || strlen(trim($data['title'])) < 5) {
            $errors['title'] = "Tên khóa học phải có ít nhất 5 ký tự";
        }

        if (empty(trim($data['description'] ?? '')) || strlen(trim($data['description'])) < 20) {
            $errors['description'] = "Mô tả phải có ít nhất 20 ký tự";
        }

        if (!is_numeric($data['price'] ?? 0) || $data['price'] < 0) {
            $errors['price'] = "Giá không hợp lệ";
        }

        if (!is_numeric($data['duration_weeks'] ?? 0) || $data['duration_weeks'] < 1 || $data['duration_weeks'] > 52) {
            $errors['duration_weeks'] = "Thời lượng phải từ 1 đến 52 tuần";
        }

        if (empty($data['category_id'] ?? 0) || !is_numeric($data['category_id'])) {
            $errors['category_id'] = "Vui lòng chọn danh mục";
        }

        if (empty($data['level'] ?? '')) {
            $errors['level'] = "Vui lòng chọn cấp độ";
        }

        return $errors;
    }

    // ========== HANDLE IMAGE UPLOAD ==========
    private function handleImageUpload()
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return '';
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            return '';
        }

        if ($file_size > $max_size) {
            return '';
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = 'course_' . time() . '_' . uniqid() . '.' . strtolower($ext);
        $upload_dir = 'assets/uploads/courses/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $upload_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            return $image_name;
        }

        return '';
    }
}