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

        // KIỂM TRA: Nếu session cũ không phải ID muốn test
        $desired_test_id = 3; // ID muốn test

        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $desired_test_id) {
            // Reset session về ID muốn test
            $_SESSION['user_id'] = $desired_test_id;
            $_SESSION['role'] = 1;
            $_SESSION['is_fake'] = true;
            $_SESSION['info'] = "Đã chuyển sang test với ID=" . $desired_test_id;
        }
        // if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
        //     header('Location: ?c=auth&a=login');
        //     exit;
        // }
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
