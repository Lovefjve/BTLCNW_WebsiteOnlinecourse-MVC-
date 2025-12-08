<?php
// controllers/InstructorController.php

class InstructorController {
    
    public function courses() {
        $instructor_id = $_GET['instructor_id'] ?? null;
    
    if ($instructor_id) {
        // Xem courses của giảng viên khác
        $instructor_id = (int)$instructor_id;
    } else {
        // Xem courses của chính mình (giảng viên đang đăng nhập)
        $instructor_id = $this->getFirstInstructorId();
        
        // Set session cho giảng viên đang đăng nhập
        $_SESSION['user_id'] = $instructor_id;
        $_SESSION['role'] = 1;
        $_SESSION['fullname'] = "Giảng viên";
    }
    
    // Load model
    require_once 'models/Course.php';
    $courseModel = new Course();
    
    // Lấy courses
    $courses = $courseModel->getByInstructor($instructor_id, 10, 0);
    $totalCourses = $courseModel->countByInstructor($instructor_id);
    $stats = $courseModel->getInstructorStats($instructor_id);
        
        // Nếu không có courses, lấy trực tiếp từ database (loại bỏ draft)
        if (empty($courses) && $totalCourses > 0) {
            try {
                require_once 'config/Database.php';
                $database = new Database();
                $conn = $database->getConnection();
                
                // Lấy courses trực tiếp, không bao gồm draft
                $query = "
                    SELECT c.*, cat.name as category_name,
                           (SELECT COUNT(DISTINCT student_id) 
                            FROM enrollments 
                            WHERE course_id = c.id) as student_count
                    FROM courses c
                    LEFT JOIN categories cat ON c.category_id = cat.id
                    WHERE c.instructor_id = ?
                    AND c.status != 'draft'  -- Loại bỏ courses có trạng thái draft
                    ORDER BY c.created_at DESC
                    LIMIT 10 OFFSET 0
                ";
                
                $stmt = $conn->prepare($query);
                $stmt->execute([$instructor_id]);
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (Exception $e) {
                error_log("Direct SQL error: " . $e->getMessage());
                $courses = [];
            }
        }
        
        // Nếu vẫn không có data, tạo mẫu
        if (empty($courses)) {
            $courses = [
                [
                    'id' => 1,
                    'title' => 'Lập trình PHP cơ bản (Mẫu)',
                    'category_name' => 'Lập trình',
                    'price' => 500000,
                    'level' => 'Beginner',
                    'image' => '',
                    'status' => 'published',
                    'created_at' => date('Y-m-d H:i:s'),
                    'student_count' => 15
                ],
                [
                    'id' => 2,
                    'title' => 'MySQL Database (Mẫu)',
                    'category_name' => 'Lập trình',
                    'price' => 600000,
                    'level' => 'Intermediate',
                    'image' => '',
                    'status' => 'published',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'student_count' => 10
                ]
            ];
            
            $totalCourses = 2;
            $stats = [
                'total_courses' => 2,
                'published_courses' => 2,
                'pending_courses' => 0,
                'total_students' => 25
            ];
            
            $_SESSION['info'] = "Đang hiển thị dữ liệu mẫu. Hãy tạo khóa học thật của bạn!";
        }
        
        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $totalPages = ceil($totalCourses / $limit);
        
        // Gọi view
        $data = [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'page' => $page,
            'totalPages' => $totalPages,
            'stats' => $stats
        ];
        
        extract($data);
        require_once 'views/instructor/course/manage.php';
    }
    
    // Helper method: Lấy ID của giảng viên đầu tiên có courses
    private function getFirstInstructorId() {
        try {
            require_once 'config/Database.php';
            $database = new Database();
            $conn = $database->getConnection();
            
            // Ưu tiên giảng viên có courses
            $query = "SELECT u.id, u.fullname 
                      FROM users u 
                      WHERE u.role = 1 
                      AND EXISTS (SELECT 1 FROM courses c WHERE c.instructor_id = u.id)
                      LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $_SESSION['fullname'] = $result['fullname'];
                return $result['id'];
            }
            
            // Nếu không có, lấy giảng viên đầu tiên
            $query = "SELECT id, fullname FROM users WHERE role = 1 LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $_SESSION['fullname'] = $result['fullname'];
                return $result['id'];
            }
            
        } catch (Exception $e) {
            error_log("Error getting instructor ID: " . $e->getMessage());
        }
        
        return 1; // Fallback
    }
    
    public function createCourse() {
        // Kiểm tra quyền
        if (($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Lấy danh mục từ Model
        require_once 'models/Category.php';
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        
        // Lấy errors và old input từ session
        $errors = $_SESSION['errors'] ?? [];
        $old_input = $_SESSION['old_input'] ?? [];
        
        // Xóa session data sau khi dùng
        unset($_SESSION['errors']);
        unset($_SESSION['old_input']);
        
        // Truyền data cho View
        $data = [
            'categories' => $categories,
            'errors' => $errors,
            'old_input' => $old_input
        ];
        
        extract($data);
        require_once 'views/instructor/course/create.php';
    }
    
    public function storeCourse() {
        // Xử lý POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=createCourse');
            exit;
        }
        
        // Validation
        $errors = $this->validateCourseData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=instructor&a=createCourse');
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
            'instructor_id' => $_SESSION['user_id'],
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
        
        header('Location: ?c=instructor&a=courses');
        exit;
    }
    
    private function validateCourseData($data) {
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
    
    private function handleImageUpload() {
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
    
    public function editCourse() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
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
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Kiểm tra quyền sở hữu
            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền chỉnh sửa khóa học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy danh mục
            $categories = $categoryModel->getAll();
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Hiển thị form chỉnh sửa
        require_once 'views/instructor/course/edit.php';
    }
    
    public function updateCourse() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $course_id = (int)($_POST['course_id'] ?? 0);
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
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
        $status = $_POST['status'] ?? 'peding';
        
        if (empty($title) || strlen($title) < 5) {
            $errors['title'] = "Tên khóa học phải có ít nhất 5 ký tự";
        }
        
        if (empty($description) || strlen($description) < 20) {
            $errors['description'] = "Mô tả phải có ít nhất 20 ký tự";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=instructor&a=edit&id=' . $course_id);
            exit;
        }
        
        // Load model
        require_once 'models/Course.php';
        $courseModel = new Course();
        
        try {
            // Kiểm tra quyền sở hữu
            $course = $courseModel->getById($course_id);
            
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền chỉnh sửa khóa học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
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
        
        header('Location: ?c=instructor&a=courses');
        exit;
    }
    
    public function deleteCourse() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Phương thức không hợp lệ";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        $course_id = (int)($_POST['course_id'] ?? 0);
        
        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Load model
        require_once 'models/Course.php';
        $courseModel = new Course();
        
        try {
            // Kiểm tra quyền sở hữu
            $course = $courseModel->getById($course_id);
            
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xóa khóa học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Xóa khóa học
            if ($courseModel->delete($course_id)) {
                $_SESSION['success'] = "Xóa khóa học thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi xóa khóa học";
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header('Location: ?c=instructor&a=courses');
        exit;
    }
}