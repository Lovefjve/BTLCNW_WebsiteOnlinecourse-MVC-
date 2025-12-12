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

        // FAKE LOGIN - Tự động tạo session nếu chưa có
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            $_SESSION['user_id'] = 2; // Giảng viên ID = 2
            $_SESSION['full_name'] = 'Nguyễn Văn An'; // Sửa tên thật từ DB
            $_SESSION['email'] = 'gv1@example.com'; // Sửa email thật từ DB
            $_SESSION['role'] = 1; // 1 = giảng viên
        }
    }

    // ========== DASHBOARD ==========
    // ========== DASHBOARD ==========
    // ========== DASHBOARD ==========
    public function dashboard()
    {
        // Lấy instructor_id từ session (mặc định là 2)
        $instructorId = $_SESSION['user_id'] ?? 2;

        // DEBUG
        error_log("=== DASHBOARD LOADING ===");
        error_log("Instructor ID: " . $instructorId);

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

                // Đảm bảo stats có đủ fields
                if (!isset($stats['total_revenue'])) {
                    $stats['total_revenue'] = 0;
                }

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
                    'total_revenue' => 0
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
                'total_revenue' => 0
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

    // ========== LOGOUT ==========
    public function logout()
    {
        // Xóa tất cả session
        session_destroy();

        // Xóa tất cả session variables
        $_SESSION = [];

        // Tạo session mới để chứa thông báo
        session_start();
        $_SESSION['info'] = "Đã đăng xuất thành công";

        // Chuyển hướng đến trang dashboard (sẽ tự động fake login lại)
        header('Location: ?c=instructor&a=dashboard');
        exit;
    }
}
