<?php
// controllers/MaterialController.php

class MaterialController {
    
    public function __construct() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?c=auth&a=login');
            exit;
        }
        
        // Kiểm tra quyền instructor
        if (($_SESSION['role'] ?? 0) != 1) {
            header('Location: ?c=auth&a=login');
            exit;
        }
    }

    // ========== THÊM PHƯƠNG THỨC RENDER VÀO ĐÂY ==========
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
        
        // Lấy lesson_id từ URL
        $lesson_id = $_GET['lesson_id'] ?? 0;
        
        if (!$lesson_id) {
            $_SESSION['error'] = "Không tìm thấy bài học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Load models
        require_once 'models/Course.php';
        require_once 'models/Lesson.php';
        require_once 'models/Material.php';
        
        $courseModel = new Course();
        $lessonModel = new Lesson();
        $materialModel = new Material();
        
        try {
            // Lấy thông tin bài học
            $lesson = $lessonModel->getById($lesson_id);
            
            if (!$lesson) {
                $_SESSION['error'] = "Bài học không tồn tại";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy thông tin khóa học
            $course = $courseModel->getById($lesson['course_id']);
            
            if (!$course) {
                $_SESSION['error'] = "Khóa học không tồn tại";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Kiểm tra quyền sở hữu
            if ($course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền quản lý tài liệu bài học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Lấy danh sách tài liệu
            $materials = $materialModel->getByLesson($lesson_id);
            
            // Thống kê
            $total_materials = count($materials);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=lesson&a=index&course_id=' . ($lesson['course_id'] ?? 0));
            exit;
        }
        
        // Hiển thị view - TRUYỀN BIẾN ĐẦY ĐỦ theo đúng tên biến view cần
        
        $this->render('instructor/materials/upload', [
            'course' => $course,
            'course_title' => $course['title'] ?? 'Khóa học',
            'course_id' => $course['id'],
            'lesson' => $lesson,
            'lesson_title' => $lesson['title'] ?? 'Bài học',
            'lesson_id' => $lesson_id,
            'materials' => $materials,
            'total_materials' => $total_materials,
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
        
        $lesson_id = (int)($_POST['lesson_id'] ?? 0);
        
        if (!$lesson_id) {
            $_SESSION['error'] = "Không tìm thấy bài học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Validation
        $errors = $this->validateMaterialData($_FILES);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ?c=material&a=index&lesson_id=' . $lesson_id); // SỬA: về index thay vì upload
            exit;
        }
        
        // Load models
        require_once 'models/Course.php';
        require_once 'models/Lesson.php';
        require_once 'models/Material.php';
        
        $courseModel = new Course();
        $lessonModel = new Lesson();
        $materialModel = new Material();
        
        try {
            // Lấy thông tin bài học
            $lesson = $lessonModel->getById($lesson_id);
            
            if (!$lesson) {
                $_SESSION['error'] = "Bài học không tồn tại";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Kiểm tra quyền sở hữu (qua course)
            $course = $courseModel->getById($lesson['course_id']);
            
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền upload tài liệu cho bài học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Xử lý upload
            $uploaded_count = 0;
            $upload_errors = [];
            
            if (isset($_FILES['materials']) && count($_FILES['materials']['name']) > 0) {
                for ($i = 0; $i < count($_FILES['materials']['name']); $i++) {
                    if ($_FILES['materials']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_data = [
                            'name' => $_FILES['materials']['name'][$i],
                            'tmp_name' => $_FILES['materials']['tmp_name'][$i],
                            'size' => $_FILES['materials']['size'][$i],
                            'type' => $_FILES['materials']['type'][$i]
                        ];
                        
                        try {
                            if ($materialModel->upload($lesson_id, $file_data)) {
                                $uploaded_count++;
                            } else {
                                $upload_errors[] = "Không thể upload file: " . $file_data['name'];
                            }
                        } catch (Exception $e) {
                            $upload_errors[] = "Lỗi upload file " . $file_data['name'] . ": " . $e->getMessage();
                        }
                    } elseif ($_FILES['materials']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $upload_errors[] = "Lỗi upload file: " . $_FILES['materials']['name'][$i];
                    }
                }
            } else {
                $_SESSION['error'] = "Vui lòng chọn ít nhất một file để upload";
                header('Location: ?c=material&a=index&lesson_id=' . $lesson_id);
                exit;
            }
            
            if ($uploaded_count > 0) {
                $_SESSION['success'] = "Đã upload thành công $uploaded_count tài liệu!";
            }
            
            if (!empty($upload_errors)) {
                $_SESSION['error'] = implode("<br>", $upload_errors);
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header('Location: ?c=material&a=index&lesson_id=' . $lesson_id);
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
        
        $material_id = (int)($_POST['material_id'] ?? 0);
        $lesson_id = (int)($_POST['lesson_id'] ?? 0);
        
        if (!$material_id || !$lesson_id) {
            $_SESSION['error'] = "Không tìm thấy tài liệu";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        // Load models
        require_once 'models/Course.php';
        require_once 'models/Lesson.php';
        require_once 'models/Material.php';
        
        $courseModel = new Course();
        $lessonModel = new Lesson();
        $materialModel = new Material();
        
        try {
            // Lấy thông tin bài học
            $lesson = $lessonModel->getById($lesson_id);
            
            if (!$lesson) {
                $_SESSION['error'] = "Bài học không tồn tại";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Kiểm tra quyền sở hữu (qua course)
            $course = $courseModel->getById($lesson['course_id']);
            
            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xóa tài liệu này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            // Kiểm tra tài liệu tồn tại và thuộc bài học
            $material = $materialModel->getById($material_id);
            
            if (!$material) {
                $_SESSION['error'] = "Tài liệu không tồn tại";
                header('Location: ?c=material&a=index&lesson_id=' . $lesson_id);
                exit;
            }
            
            if ($material['lesson_id'] != $lesson_id) {
                $_SESSION['error'] = "Tài liệu không thuộc bài học này";
                header('Location: ?c=material&a=index&lesson_id=' . $lesson_id);
                exit;
            }
            
            // Xóa tài liệu
            if ($materialModel->delete($material_id)) {
                $_SESSION['success'] = "Xóa tài liệu thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi xóa tài liệu";
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header('Location: ?c=material&a=index&lesson_id=' . $lesson_id);
        exit;
    }
    
    public function download() {
        // Kiểm tra quyền (cho phép học viên download nếu đã enrolled)
        // Ở đây tôi chỉ kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để download tài liệu";
            header('Location: ?c=auth&a=login');
            exit;
        }
        
        $material_id = $_GET['id'] ?? 0;
        
        if (!$material_id) {
            $_SESSION['error'] = "Không tìm thấy tài liệu";
            header('Location: ?c=instructor&a=courses');
            exit;
        }
        
        require_once 'models/Material.php';
        $materialModel = new Material();
        
        try {
            $material = $materialModel->getById($material_id);
            
            if (!$material) {
                $_SESSION['error'] = "Tài liệu không tồn tại";
                header('Location: ?c=instructor&a=courses');
                exit;
            }
            
            $file_path = $material['file_path'];
            
            if (!file_exists($file_path)) {
                $_SESSION['error'] = "File không tồn tại trên server";
                header('Location: ?c=material&a=index&lesson_id=' . $material['lesson_id']);
                exit;
            }
            
            // Thiết lập headers để download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($material['filename']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=material&a=index&lesson_id=' . ($material['lesson_id'] ?? 0));
            exit;
        }
    }
    
    // VALIDATE DỮ LIỆU MATERIAL
    private function validateMaterialData($files) {
        $errors = [];
        
        if (!isset($files['materials']) || count($files['materials']['name']) == 0) {
            $errors['materials'] = "Vui lòng chọn ít nhất một file";
            return $errors;
        }
        
        // Kiểm tra từng file
        $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 50 * 1024 * 1024; // 50MB
        
        for ($i = 0; $i < count($files['materials']['name']); $i++) {
            if ($files['materials']['error'][$i] !== UPLOAD_ERR_OK) {
                if ($files['materials']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $errors['materials'] = "Có lỗi xảy ra khi upload file: " . $files['materials']['name'][$i];
                }
                continue;
            }
            
            // Kiểm tra kích thước
            if ($files['materials']['size'][$i] > $max_file_size) {
                $errors['materials'] = "File '" . $files['materials']['name'][$i] . "' vượt quá kích thước 50MB cho phép";
            }
            
            // Kiểm tra định dạng
            $file_extension = strtolower(pathinfo($files['materials']['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($file_extension, $allowed_extensions)) {
                $errors['materials'] = "File '" . $files['materials']['name'][$i] . "' có định dạng không được hỗ trợ";
            }
        }
        
        return $errors;
    }
}