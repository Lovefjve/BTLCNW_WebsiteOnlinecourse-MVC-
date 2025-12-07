<?php
// controllers/InstructorController.php

class InstructorController {
    
    public function courses() {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Fake login for testing
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['role'] = 1;
            $_SESSION['fullname'] = "Giảng viên Test";
        }
        
        // Check permission
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=auth&a=login');
            exit;
        }
        
        // Sample data
        $courses = [
            [
                'id' => 1,
                'title' => 'Lập trình PHP cơ bản',
                'category_name' => 'Lập trình',
                'price' => 500000,
                'level' => 'Beginner',
                'image' => '',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'student_count' => 15
            ],
            [
                'id' => 2,
                'title' => 'MySQL Database',
                'category_name' => 'Lập trình',
                'price' => 0,
                'level' => 'Intermediate',
                'image' => '',
                'status' => 'draft',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'student_count' => 10
            ],
            [
                'id' => 3,
                'title' => 'JavaScript nâng cao',
                'category_name' => 'Lập trình',
                'price' => 750000,
                'level' => 'Advanced',
                'image' => '',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'student_count' => 8
            ]
        ];
        
        $stats = [
            'total_courses' => 3,
            'published_courses' => 1,
            'pending_courses' => 1,
            'total_students' => 33
        ];
        
        $totalCourses = 3;
        $page = 1;
        $totalPages = 1;
        
        // Include view
        require_once 'views/instructor/course/manage.php';
    }
    
    public function createCourse() {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check permission
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Get categories (sample data)
        $categories = [
            ['id' => 1, 'name' => 'Lập trình'],
            ['id' => 2, 'name' => 'Thiết kế'],
            ['id' => 3, 'name' => 'Kinh doanh'],
            ['id' => 4, 'name' => 'Marketing'],
            ['id' => 5, 'name' => 'Ngoại ngữ'],
            ['id' => 6, 'name' => 'Kỹ năng mềm']
        ];
        
        // Clear old session data
        $errors = $_SESSION['errors'] ?? [];
        $old_input = $_SESSION['old_input'] ?? [];
        unset($_SESSION['errors']);
        unset($_SESSION['old_input']);
        
        // Pass data to view
        require_once 'views/instructor/course/create.php';
    }
    
    public function storeCourse() {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=createCourse');
            exit;
        }
        
        // Check permission
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $duration_weeks = (int)($_POST['duration_weeks'] ?? 4);
        $level = $_POST['level'] ?? 'Beginner';
        
        // Validate
        $errors = [];
        
        if (empty($title) || strlen($title) < 5) {
            $errors['title'] = "Tên khóa học phải có ít nhất 5 ký tự";
        }
        
        if (empty($description) || strlen($description) < 20) {
            $errors['description'] = "Mô tả phải có ít nhất 20 ký tự";
        }
        
        if ($price < 0) {
            $errors['price'] = "Giá không hợp lệ";
        }
        
        if ($duration_weeks < 1 || $duration_weeks > 52) {
            $errors['duration_weeks'] = "Thời lượng phải từ 1 đến 52 tuần";
        }
        
        // Handle image upload
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            
            if (in_array($file_type, $allowed_types)) {
                if ($file_size <= $max_size) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image_name = 'course_' . time() . '.' . $ext;
                    $upload_dir = 'assets/uploads/courses/';
                    
                    // Create directory if not exists
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $upload_path = $upload_dir . $image_name;
                    
                    // Move uploaded file
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        // File uploaded successfully
                    } else {
                        $errors['image'] = "Không thể upload file ảnh";
                    }
                } else {
                    $errors['image'] = "File ảnh không được lớn hơn 5MB";
                }
            } else {
                $errors['image'] = "Chỉ chấp nhận file ảnh (JPEG, PNG, JPG, GIF)";
            }
        }
        
        // If there are errors, redirect back with errors and old input
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=instructor&a=createCourse');
            exit;
        }
        
        // Save course data (for now, just show success message)
        // In real application, you would save to database here
        
        // Success
        $_SESSION['success'] = "Tạo khóa học '" . htmlspecialchars($title) . "' thành công!";
        header('Location: ?c=instructor&a=courses');
        exit;
    }
    
    public function editCourse() {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check permission
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $course_id = $_GET['id'] ?? 0;
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Sample course data
        $course = [
            'id' => $course_id,
            'title' => 'Lập trình PHP cơ bản',
            'description' => 'Khóa học lập trình PHP từ cơ bản đến nâng cao',
            'category_id' => 1,
            'price' => 500000,
            'duration_weeks' => 8,
            'level' => 'Beginner',
            'status' => 'published'
        ];
        
        // Sample categories
        $categories = [
            ['id' => 1, 'name' => 'Lập trình'],
            ['id' => 2, 'name' => 'Thiết kế'],
            ['id' => 3, 'name' => 'Kinh doanh']
        ];
        
        // Include edit view (you need to create this file)
        require_once 'views/instructor/course/edit.php';
    }
    
    public function deleteCourse() {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check permission and method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $course_id = $_POST['course_id'] ?? 0;
        
        if ($course_id) {
            // Delete course logic here
            $_SESSION['success'] = "Đã xóa khóa học thành công";
        } else {
            $_SESSION['error'] = "Không tìm thấy khóa học";
        }
        
        header('Location: ?c=instructor&a=courses');
        exit;
    }
    
    public function updateCourse() {
        // Similar to storeCourse but for updating
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $course_id = $_POST['course_id'] ?? 0;
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Validation and update logic here
        
        $_SESSION['success'] = "Cập nhật khóa học thành công";
        header('Location: ?c=instructor&a=courses');
        exit;
    }
}
?>