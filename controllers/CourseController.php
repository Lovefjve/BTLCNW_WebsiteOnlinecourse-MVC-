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

        // FAKE LOGIN - XÓA PHẦN KIỂM TRA QUYỀN
        // Thay vì kiểm tra quyền, tự động tạo fake session
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            $_SESSION['user_id'] = 2; // Giảng viên ID = 2
            $_SESSION['full_name'] = 'Nguyễn Văn Giảng Viên';
            $_SESSION['email'] = 'giangvien@example.com';
            $_SESSION['role'] = 1; // 1 = giảng viên

            // Hiển thị thông báo fake login lần đầu
            if (!isset($_SESSION['fake_login_shown'])) {
                $_SESSION['info'] = "Đang sử dụng tài khoản giảng viên demo ID=2";
                $_SESSION['fake_login_shown'] = true;
            }
        }

        // COMMENT PHẦN KIỂM TRA QUYỀN ĐĂNG NHẬP
        // if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
        //     header('Location: ?c=auth&a=login');
        //     exit;
        // }
    }

    // ========== DANH SÁCH KHÓA HỌC ==========
    public function index()
    {
        // Lấy instructor_id từ session (mặc định là 2)
        $instructor_id = $_SESSION['user_id'] ?? 2;

        // DEBUG: Kiểm tra session
        error_log("Instructor ID from session: " . $instructor_id);
        error_log("Session data: " . print_r($_SESSION, true));

        // Load model
        require_once 'models/Course.php';
        $courseModel = new Course();

        // Lấy courses từ CSDL thật
        $courses = [];
        $totalCourses = 0;
        $stats = [
            'total_courses' => 0,
            'published_courses' => 0,
            'pending_courses' => 0,
            'total_students' => 0
        ];

        try {
            // Thử lấy courses từ CSDL
            if (method_exists($courseModel, 'getByInstructor')) {
                $courses = $courseModel->getByInstructor($instructor_id, 10, 0) ?? [];
            }

            if (method_exists($courseModel, 'countByInstructor')) {
                $totalCourses = $courseModel->countByInstructor($instructor_id) ?? 0;
            }

            // DEBUG
            error_log("Courses from DB: " . print_r($courses, true));
            error_log("Total courses: " . $totalCourses);
        } catch (Exception $e) {
            error_log("Error getting courses: " . $e->getMessage());
        }

        // Nếu không có courses trong CSDL, tạo dữ liệu mẫu
        if (empty($courses)) {
            error_log("No courses found, showing sample data");

            $courses = [
                [
                    'id' => 1,
                    'title' => 'Lập trình PHP cơ bản (Dữ liệu mẫu)',
                    'category_name' => 'Lập trình',
                    'price' => 500000,
                    'level' => 'Beginner',
                    'image' => '',
                    'status' => 'published',
                    'created_at' => date('Y-m-d H:i:s'),
                    'student_count' => 15,
                    'duration_weeks' => 8
                ],
                [
                    'id' => 2,
                    'title' => 'MySQL Database (Dữ liệu mẫu)',
                    'category_name' => 'Database',
                    'price' => 600000,
                    'level' => 'Intermediate',
                    'image' => '',
                    'status' => 'published',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'student_count' => 10,
                    'duration_weeks' => 10
                ],
                [
                    'id' => 3,
                    'title' => 'JavaScript nâng cao (Dữ liệu mẫu)',
                    'category_name' => 'Web Development',
                    'price' => 700000,
                    'level' => 'Advanced',
                    'image' => '',
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 week')),
                    'student_count' => 5,
                    'duration_weeks' => 12
                ]
            ];

            $totalCourses = 3;
            $stats = [
                'total_courses' => 3,
                'published_courses' => 2,
                'pending_courses' => 1,
                'total_students' => 30
            ];

            if (!isset($_SESSION['sample_data_shown'])) {
                $_SESSION['info'] = "Đang hiển thị dữ liệu mẫu. Hãy tạo khóa học thật của bạn!";
                $_SESSION['sample_data_shown'] = true;
            }
        } else {
            // Sử dụng method getInstructorStats đã được định nghĩa trong model
            $courseModel = new Course();
            $stats = $courseModel->getInstructorStats($instructor_id);

            // $courses và $totalCourses vẫn giữ nguyên
            // $courses = $courseModel->getByInstructor($instructor_id, $limit, $offset);
            // $totalCourses = $courseModel->countByInstructor($instructor_id);
        }

        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $totalPages = ceil($totalCourses / $limit);

        // DEBUG: Kiểm tra dữ liệu trước khi render
        error_log("Final courses count: " . count($courses));
        error_log("Stats: " . print_r($stats, true));

        // Gọi view
        $this->render('instructor/course/manage', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'page' => $page,
            'totalPages' => $totalPages,
            'stats' => $stats
        ]);
    }

    // ========== TẠO KHÓA HỌC ==========
    public function create()
    {
        // COMMENT PHẦN KIỂM TRA QUYỀN
        // if (($_SESSION['role'] ?? 0) != 1) {
        //     $_SESSION['error'] = "Bạn không có quyền truy cập";
        //     header('Location: ?c=course&a=index');
        //     exit;
        // }

        // Lấy danh mục từ Model
        require_once 'models/Category.php';
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        // Nếu không có danh mục, tạo mẫu
        if (empty($categories)) {
            $categories = [
                ['id' => 1, 'name' => 'Lập trình'],
                ['id' => 2, 'name' => 'Database'],
                ['id' => 3, 'name' => 'Web Development']
            ];
        }

        // Lấy errors và old input từ session
        $errors = $_SESSION['errors'] ?? [];
        $old_input = $_SESSION['old_input'] ?? [];

        // Xóa session data sau khi dùng
        unset($_SESSION['errors']);
        unset($_SESSION['old_input']);

        // Truyền data cho View
        $this->render('instructor/course/create', [
            'categories' => $categories,
            'errors' => $errors,
            'old_input' => $old_input
        ]);
    }

    // ========== LƯU KHÓA HỌC ==========
    public function store()
    {
        // Xử lý POST
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

        // Lưu vào database qua Model
        require_once 'models/Course.php';
        $courseModel = new Course();

        $courseData = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'instructor_id' => $_SESSION['user_id'] ?? 2, // Lấy từ session hoặc mặc định 2
            'category_id' => (int)$_POST['category_id'],
            'price' => (float)$_POST['price'],
            'duration_weeks' => (int)$_POST['duration_weeks'],
            'level' => $_POST['level'],
            'image' => $image_name,
            'status' => 'pending'
        ];

        if ($courseModel->create($courseData)) {
            $_SESSION['success'] = "Tạo khóa học thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi lưu khóa học";
        }

        header('Location: ?c=course&a=index');
        exit;
    }

    // ========== SỬA KHÓA HỌC ==========
    public function edit()
    {
        // COMMENT PHẦN KIỂM TRA QUYỀN
        // if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
        //     $_SESSION['error'] = "Bạn không có quyền truy cập";
        //     header('Location: ?c=course&a=index');
        //     exit;
        // }

        $course_id = $_GET['id'] ?? 0;

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=course&a=index');
            exit;
        }

        // Load models
        require_once 'models/Course.php';
        require_once 'models/Category.php';

        $courseModel = new Course();
        $categoryModel = new Category();

        try {
            // Lấy thông tin khóa học
            $course = $courseModel->getById($course_id);

            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=course&a=index');
                exit;
            }

            // COMMENT PHẦN KIỂM TRA QUYỀN SỞ HỮU
            // if ($course['instructor_id'] != $_SESSION['user_id']) {
            //     $_SESSION['error'] = "Bạn không có quyền chỉnh sửa khóa học này";
            //     header('Location: ?c=course&a=index');
            //     exit;
            // }

            // Lấy danh mục
            $categories = $categoryModel->getAll();

            // Nếu không có danh mục, tạo mẫu
            if (empty($categories)) {
                $categories = [
                    ['id' => 1, 'name' => 'Lập trình'],
                    ['id' => 2, 'name' => 'Database'],
                    ['id' => 3, 'name' => 'Web Development']
                ];
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=course&a=index');
            exit;
        }

        $this->render('instructor/course/edit', [
            'course' => $course,
            'categories' => $categories
        ]);
    }

    // ========== CẬP NHẬT KHÓA HỌC ==========
    public function update()
    {
        // COMMENT PHẦN KIỂM TRA QUYỀN
        // if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
        //     $_SESSION['error'] = "Bạn không có quyền thực hiện";
        //     header('Location: ?c=course&a=index');
        //     exit;
        // }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=course&a=index');
            exit;
        }

        $course_id = (int)($_POST['course_id'] ?? 0);

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=course&a=index');
            exit;
        }

        // Validate dữ liệu
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $duration_weeks = (int)($_POST['duration_weeks'] ?? 4);
        $level = $_POST['level'] ?? 'Beginner';
        $status = $_POST['status'] ?? 'pending';

        if (empty($title) || strlen($title) < 5) {
            $errors['title'] = "Tên khóa học phải có ít nhất 5 ký tự";
        }

        if (empty($description) || strlen($description) < 20) {
            $errors['description'] = "Mô tả phải có ít nhất 20 ký tự";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=course&a=edit&id=' . $course_id);
            exit;
        }

        // Load model
        require_once 'models/Course.php';
        $courseModel = new Course();

        try {
            // Kiểm tra khóa học tồn tại
            $course = $courseModel->getById($course_id);

            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=course&a=index');
                exit;
            }

            // COMMENT PHẦN KIỂM TRA QUYỀN SỞ HỮU
            // if ($course['instructor_id'] != $_SESSION['user_id']) {
            //     $_SESSION['error'] = "Bạn không có quyền chỉnh sửa khóa học này";
            //     header('Location: ?c=course&a=index');
            //     exit;
            // }

            // Xử lý upload ảnh mới (nếu có)
            $image_name = $course['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                $max_size = 5 * 1024 * 1024;

                $file_type = $_FILES['image']['type'];
                $file_size = $_FILES['image']['size'];

                if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image_name = 'course_' . time() . '.' . $ext;
                    $upload_dir = 'assets/uploads/courses/';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $upload_path = $upload_dir . $image_name;
                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
                }
            }

            // Cập nhật CSDL
            $updateData = [
                'title' => $title,
                'description' => $description,
                'category_id' => $category_id,
                'price' => $price,
                'duration_weeks' => $duration_weeks,
                'level' => $level,
                'image' => $image_name,
                'status' => $status
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
        // COMMENT PHẦN KIỂM TRA QUYỀN
        // if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
        //     $_SESSION['error'] = "Bạn không có quyền thực hiện";
        //     header('Location: ?c=course&a=index');
        //     exit;
        // }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=course&a=index');
            exit;
        }

        $course_id = (int)($_POST['course_id'] ?? 0);

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=course&a=index');
            exit;
        }

        // Load model
        require_once 'models/Course.php';
        $courseModel = new Course();

        try {
            // Kiểm tra khóa học tồn tại
            $course = $courseModel->getById($course_id);

            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=course&a=index');
                exit;
            }

            // COMMENT PHẦN KIỂM TRA QUYỀN SỞ HỮU
            // if ($course['instructor_id'] != $_SESSION['user_id']) {
            //     $_SESSION['error'] = "Bạn không có quyền xóa khóa học này";
            //     header('Location: ?c=course&a=index');
            //     exit;
            // }

            // Xóa khóa học
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

    // ========== HELPER METHODS ==========

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

        return $errors;
    }

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
