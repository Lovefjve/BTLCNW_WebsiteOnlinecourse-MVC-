<?php
// controllers/InstructorController.php

class InstructorController
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

        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }



    // ========== DASHBOARD ==========
    public function dashboard()
    {
        // Lấy instructor_id từ session (đã được fake nếu chưa có)
        $instructorId = $_SESSION['user_id'];

        // DEBUG
        error_log("=== DASHBOARD LOADING ===");
        error_log("Instructor ID: " . $instructorId);
        error_log("Is fake login: " . ($_SESSION['is_fake'] ?? 'false'));

        try {
            // Load model Course
            require_once 'models/Course.php';
            $courseModel = new Course();

            // 1. Lấy thống kê từ CSDL
            $stats = $courseModel->getInstructorStats($instructorId);
            error_log("Stats từ DB: total_courses = " . ($stats['total_courses'] ?? 0));

            // 2. Lấy khóa học gần đây từ CSDL
            $recentCourses = $courseModel->getByInstructor($instructorId, 5, 0);
            error_log("Số courses từ DB: " . count($recentCourses));

            // QUAN TRỌNG: Kiểm tra xem có dữ liệu từ DB không
            $hasDataFromDB = !empty($recentCourses) && ($stats['total_courses'] ?? 0) > 0;

            if ($hasDataFromDB) {
                // CÓ dữ liệu từ DB - sử dụng dữ liệu THẬT
                error_log("✅ Sử dụng dữ liệu THẬT từ database");


                // DEBUG thông tin dữ liệu thật
                foreach ($recentCourses as $index => $course) {
                    error_log("Course " . ($index + 1) . ": " . $course['title'] . " (Status: " . $course['status'] . ")");
                }
            } else {
                // KHÔNG có dữ liệu từ DB - hiển thị dữ liệu MẪU
                error_log("⚠️ Không có dữ liệu từ DB - hiển thị dữ liệu mẫu");

                $recentCourses = [
                    [
                        'id' => 1,
                        'title' => 'Lập trình PHP cơ bản (Dữ liệu mẫu)',
                        'image' => '',
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => 'published',
                        'student_count' => 25,
                        'price' => 500000,
                        'category_name' => 'Lập trình',
                        'level' => 'Beginner'
                    ],
                    [
                        'id' => 2,
                        'title' => 'MySQL Database (Dữ liệu mẫu)',
                        'image' => '',
                        'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                        'status' => 'published',
                        'student_count' => 18,
                        'price' => 600000,
                        'category_name' => 'Database',
                        'level' => 'Intermediate'
                    ]
                ];

                // Stats cho dữ liệu mẫu
                $stats = [
                    'total_courses' => 2,
                    'published_courses' => 2,
                    'pending_courses' => 0,
                    'total_students' => 43,
                ];

                if (!isset($_SESSION['sample_data_shown'])) {
                    $_SESSION['info'] = "Chưa có khóa học. Hãy tạo khóa học đầu tiên của bạn!";
                    $_SESSION['sample_data_shown'] = true;
                }
            }
        } catch (Exception $e) {
            error_log("❌ Dashboard ERROR: " . $e->getMessage());

            // Nếu có lỗi, hiển thị dashboard với dữ liệu mặc định
            $stats = [
                'total_courses' => 0,
                'published_courses' => 0,
                'pending_courses' => 0,
                'total_students' => 0,
            ];
            $recentCourses = [];
        }

        error_log("=== DASHBOARD FINAL DATA ===");
        error_log("Số courses hiển thị: " . count($recentCourses));
        error_log("Stats: " . print_r($stats, true));

        // Hiển thị view dashboard
        $this->render('instructor/dashboard', [
            'stats' => $stats,
            'recentCourses' => $recentCourses,
            'totalCourses' => $stats['total_courses']
        ]);
    }

    // ========== PROFILE ==========
    public function profile()
    {
        // Tạm thời chưa phát triển - redirect về dashboard với thông báo
        $_SESSION['info'] = "Tính năng cập nhật hồ sơ sẽ được phát triển trong tương lai";
        header('Location: ?c=instructor&a=dashboard');
        exit;
    }

    // ========== MY COURSES ==========
    public function myCourses()
    {
        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $instructorId = $_SESSION['user_id'];

        $courses = $courseModel->getByInstructor($instructorId);

        $this->render('instructor/my_courses', [
            'courses' => $courses,
            'instructor_id' => $instructorId
        ]);
    }

    // ========== MANAGE COURSES ==========
    public function manageCourses()
    {
        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $instructorId = $_SESSION['user_id'];

        $courses = $courseModel->getByInstructor($instructorId);
        $stats = $courseModel->getInstructorStats($instructorId);
        $totalCourses = count($courses);

        $this->render('instructor/course/manage', [
            'courses' => $courses,
            'stats' => $stats,
            'totalCourses' => $totalCourses,
            'instructor_id' => $instructorId
        ]);
    }

    // ========== CREATE COURSE ==========
    public function createCourse()
    {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        // Render create form
        $this->render('instructor/course/create', [
            'categories' => $categories,
            'errors' => $_SESSION['errors'] ?? [],
            'old_input' => $_SESSION['old_input'] ?? []
        ]);
        // Clear session
        unset($_SESSION['errors'], $_SESSION['old_input']);
    }

    // ========== STORE COURSE ==========
    public function storeCourse()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'price' => (float)($_POST['price'] ?? 0),
            'duration_weeks' => (int)($_POST['duration_weeks'] ?? 0),
            'level' => trim($_POST['level'] ?? ''),
            'instructor_id' => $_SESSION['user_id']
        ];

        // Validation
        $errors = [];
        if (empty($data['title'])) $errors['title'] = 'Tên khóa học là bắt buộc';
        if (empty($data['description'])) $errors['description'] = 'Mô tả khóa học là bắt buộc';
        if ($data['category_id'] <= 0) $errors['category_id'] = 'Vui lòng chọn danh mục';
        if ($data['price'] < 0) $errors['price'] = 'Giá không hợp lệ';
        if ($data['duration_weeks'] <= 0) $errors['duration_weeks'] = 'Thời lượng phải lớn hơn 0';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . BASE_URL . '/instructor/course/create');
            exit;
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'assets/uploads/courses/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $data['image'] = $file_path;
            }
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        
        if ($courseModel->create($data)) {
            $_SESSION['success'] = 'Khóa học đã được tạo thành công!';
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo khóa học';
            header('Location: ' . BASE_URL . '/instructor/course/create');
            exit;
        }
    }

    // ========== UPDATE COURSE ==========
    public function updateCourse()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $course_id = (int)($_POST['course_id'] ?? 0);
        if (!$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'price' => (float)($_POST['price'] ?? 0),
            'duration_weeks' => (int)($_POST['duration_weeks'] ?? 0),
            'level' => trim($_POST['level'] ?? ''),
            'status' => trim($_POST['status'] ?? 'pending')
        ];

        // Validation
        $errors = [];
        if (empty($data['title'])) $errors['title'] = 'Tên khóa học là bắt buộc';
        if ($data['category_id'] <= 0) $errors['category_id'] = 'Vui lòng chọn danh mục';
        if ($data['price'] < 0) $errors['price'] = 'Giá không hợp lệ';
        if ($data['duration_weeks'] <= 0) $errors['duration_weeks'] = 'Thời lượng phải lớn hơn 0';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . BASE_URL . '/instructor/course/edit?id=' . $course_id);
            exit;
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'assets/uploads/courses/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                $data['image'] = $file_path;
            }
        }

        if ($courseModel->update($course_id, $data)) {
            $_SESSION['success'] = 'Khóa học đã được cập nhật thành công!';
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật khóa học';
            header('Location: ' . BASE_URL . '/instructor/course/edit?id=' . $course_id);
            exit;
        }
    }

    // ========== DELETE COURSE ==========
    public function deleteCourse()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $course_id = (int)($_POST['course_id'] ?? 0);
        if (!$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        if ($courseModel->delete($course_id)) {
            $_SESSION['success'] = 'Khóa học đã được xóa thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa khóa học';
        }

        header('Location: ' . BASE_URL . '/instructor/course/manage');
        exit;
    }

    // ========== EDIT COURSE ==========
    public function editCourse()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $this->render('instructor/course/edit', [
            'course' => $course,
            'categories' => $categories,
            'errors' => $_SESSION['errors'] ?? [],
            'old_input' => $_SESSION['old_input'] ?? []
        ]);
        unset($_SESSION['errors'], $_SESSION['old_input']);
    }

    // ========== MANAGE LESSONS ==========
    public function manageLessons()
    {
        $course_id = $_GET['course_id'] ?? null;
        if (!$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Lesson.php';
        $lessonModel = new Lesson();
        $lessons = $lessonModel->getByCourse($course_id);

        $this->render('instructor/lessons/manage', [
            'course' => $course,
            'lessons' => $lessons
        ]);
    }

    // ========== LIST STUDENTS ==========
    public function listStudents()
    {
        $course_id = $_GET['course_id'] ?? null;
        if (!$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Enrollment.php';
        $enrollmentModel = new Enrollment();
        $students = $enrollmentModel->getStudentsByCourse($course_id);

        $this->render('instructor/students/list', [
            'course' => $course,
            'students' => $students
        ]);
    }

    // ========== CREATE LESSON ==========
    public function createLesson()
    {
        $course_id = $_GET['course_id'] ?? null;
        if (!$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $this->render('instructor/lessons/create', [
            'course' => $course,
            'errors' => $_SESSION['errors'] ?? [],
            'old_input' => $_SESSION['old_input'] ?? []
        ]);
        unset($_SESSION['errors'], $_SESSION['old_input']);
    }

    // ========== STORE LESSON ==========
    public function storeLesson()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $course_id = (int)($_POST['course_id'] ?? 0);
        if (!$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $data = [
            'course_id' => $course_id,
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'video_url' => trim($_POST['video_url'] ?? ''),
            'order' => (int)($_POST['order'] ?? 1)
        ];

        // Validation
        $errors = [];
        if (empty($data['title'])) $errors['title'] = 'Tiêu đề bài học là bắt buộc';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . BASE_URL . '/instructor/lessons/create?course_id=' . $course_id);
            exit;
        }

        require_once __DIR__ . '/../models/Lesson.php';
        $lessonModel = new Lesson();

        if ($lessonModel->create($data)) {
            $_SESSION['success'] = 'Bài học đã được tạo thành công!';
            header('Location: ' . BASE_URL . '/instructor/lessons/manage?course_id=' . $course_id);
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo bài học';
            header('Location: ' . BASE_URL . '/instructor/lessons/create?course_id=' . $course_id);
            exit;
        }
    }

    // ========== EDIT LESSON ==========
    public function editLesson()
    {
        $id = $_GET['id'] ?? null;
        $course_id = $_GET['course_id'] ?? null;
        if (!$id || !$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Lesson.php';
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($id);

        if (!$lesson || $lesson['course_id'] != $course_id) {
            header('Location: ' . BASE_URL . '/instructor/lessons/manage?course_id=' . $course_id);
            exit;
        }

        $this->render('instructor/lessons/edit', [
            'course' => $course,
            'lesson' => $lesson,
            'errors' => $_SESSION['errors'] ?? [],
            'old_input' => $_SESSION['old_input'] ?? []
        ]);
        unset($_SESSION['errors'], $_SESSION['old_input']);
    }

    // ========== UPDATE LESSON ==========
    public function updateLesson()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $lesson_id = (int)($_POST['lesson_id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);
        if (!$lesson_id || !$course_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($course_id);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Lesson.php';
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($lesson_id);

        if (!$lesson || $lesson['course_id'] != $course_id) {
            header('Location: ' . BASE_URL . '/instructor/lessons/manage?course_id=' . $course_id);
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'video_url' => trim($_POST['video_url'] ?? ''),
            'order' => (int)($_POST['order'] ?? $lesson['order'])
        ];

        // Validation
        $errors = [];
        if (empty($data['title'])) $errors['title'] = 'Tiêu đề bài học là bắt buộc';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . BASE_URL . '/instructor/lessons/edit?id=' . $lesson_id . '&course_id=' . $course_id);
            exit;
        }

        // Update lesson
        if ($lessonModel->update($lesson_id, $data)) {
            $_SESSION['success'] = 'Bài học đã được cập nhật thành công!';
            header('Location: ' . BASE_URL . '/instructor/lessons/manage?course_id=' . $course_id);
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật bài học';
            header('Location: ' . BASE_URL . '/instructor/lessons/edit?id=' . $lesson_id . '&course_id=' . $course_id);
            exit;
        }
    }

    // ========== DELETE LESSON ==========
    public function deleteLesson()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        $lesson_id = (int)($_POST['lesson_id'] ?? 0);
        if (!$lesson_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Lesson.php';
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($lesson_id);

        if (!$lesson) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($lesson['course_id']);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        if ($lessonModel->delete($lesson_id)) {
            $_SESSION['success'] = 'Bài học đã được xóa thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa bài học';
        }

        header('Location: ' . BASE_URL . '/instructor/lessons/manage?course_id=' . $lesson['course_id']);
        exit;
    }

    // ========== UPLOAD MATERIAL ==========
    public function uploadMaterial()
    {
        $lesson_id = $_GET['lesson_id'] ?? null;
        if (!$lesson_id) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Lesson.php';
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($lesson_id);

        if (!$lesson) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $course = $courseModel->getById($lesson['course_id']);

        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL . '/instructor/course/manage');
            exit;
        }

        require_once __DIR__ . '/../models/Material.php';
        $materialModel = new Material();
        try {
            $materials = $materialModel->getByLesson($lesson_id);
        } catch (Exception $e) {
            $materials = [];
            error_log('Error getting materials: ' . $e->getMessage());
        }

        $this->render('instructor/materials/upload', [
            'course' => $course,
            'lesson' => $lesson,
            'materials' => $materials
        ]);
    }

    // ========== STORE MATERIAL ==========
    public function storeMaterial()
    {
        // Similar to MaterialController->store()
        // For now, redirect back
        $lesson_id = $_POST['lesson_id'] ?? null;
        header('Location: ' . BASE_URL . '/instructor/materials/upload?lesson_id=' . $lesson_id);
        exit;
    }

    // ========== LOGOUT ==========
    public function logout()
    {
        // Xóa session
        session_destroy();

        // Tạo session mới
        session_start();
        $_SESSION['info'] = "Đã đăng xuất thành công";

        // Về trang chủ (không về dashboard ngay)
        header('Location: ?c=home&a=index');
        exit;
    }
}
