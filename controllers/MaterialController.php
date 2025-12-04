<?php
/**
 * Instructor Material Controller
 */

require_once __DIR__ . '/../../models/Material.php';
require_once __DIR__ . '/../../models/Lesson.php';
require_once __DIR__ . '/../../models/Course.php';

class MaterialController {
    
    /**
     * Form upload tài liệu
     */
    public function upload($lesson_id) {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header('Location: /btl/auth/login.php');
            exit;
        }
        
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($lesson_id);
        
        if (!$lesson) {
            header('Location: /btl/instructor/course/manage');
            exit;
        }
        
        // Kiểm tra quyền sở hữu
        $courseModel = new Course();
        $course = $courseModel->getById($lesson['course_id']);
        
        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: /btl/instructor/course/manage');
            exit;
        }
        
        // Lấy danh sách tài liệu hiện có
        $materialModel = new Material();
        $materials = $materialModel->getByLesson($lesson_id);
        
        include __DIR__ . '/../../views/instructor/materials/upload.php';
    }
    
    /**
     * Xử lý upload tài liệu
     */
    public function store($lesson_id) {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header('Location: /btl/auth/login.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /btl/instructor/materials/upload/' . $lesson_id);
            exit;
        }
        
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($lesson_id);
        
        if (!$lesson) {
            header('Location: /btl/instructor/course/manage');
            exit;
        }
        
        // Kiểm tra quyền sở hữu
        $courseModel = new Course();
        $course = $courseModel->getById($lesson['course_id']);
        
        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: /btl/instructor/course/manage');
            exit;
        }
        
        // Kiểm tra file upload
        if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Vui lòng chọn file để upload';
            header('Location: /btl/instructor/materials/upload/' . $lesson_id);
            exit;
        }
        
        $file = $_FILES['material_file'];
        
        // Kiểm tra file type
        $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip', 'jpg', 'png'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['error'] = 'Loại file không được hỗ trợ. Chỉ chấp nhận: ' . implode(', ', $allowed_types);
            header('Location: /btl/instructor/materials/upload/' . $lesson_id);
            exit;
        }
        
        // Kiểm tra kích thước (max 50MB)
        if ($file['size'] > 50 * 1024 * 1024) {
            $_SESSION['error'] = 'File quá lớn. Kích thước tối đa: 50MB';
            header('Location: /btl/instructor/materials/upload/' . $lesson_id);
            exit;
        }
        
        // Tạo thư mục nếu chưa tồn tại
        $upload_dir = __DIR__ . '/../../assets/uploads/materials/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Tạo tên file unique
        $filename = 'material_' . time() . '_' . uniqid() . '.' . $file_ext;
        $filepath = $upload_dir . $filename;
        
        // Di chuyển file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $materialModel = new Material();
            
            // Lưu vào database
            if ($materialModel->upload($lesson_id, $filename, $filepath, $file_ext)) {
                $_SESSION['success'] = 'Upload tài liệu thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi lưu thông tin tài liệu';
                // Xóa file đã upload
                unlink($filepath);
            }
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi upload file';
        }
        
        header('Location: /btl/instructor/materials/upload/' . $lesson_id);
        exit;
    }
    
    /**
     * Xóa tài liệu
     */
    public function destroy($material_id) {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header('Location: /btl/auth/login.php');
            exit;
        }
        
        $materialModel = new Material();
        $material = $materialModel->getById($material_id);
        
        if (!$material) {
            header('Location: /btl/instructor/course/manage');
            exit;
        }
        
        // Kiểm tra quyền sở hữu qua lesson -> course
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($material['lesson_id']);
        
        if (!$lesson) {
            header('Location: /btl/instructor/course/manage');
            exit;
        }
        
        $courseModel = new Course();
        $course = $courseModel->getById($lesson['course_id']);
        
        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
            header('Location: /btl/instructor/course/manage');
            exit;
        }
        
        // Xóa file vật lý
        if (file_exists($material['file_path'])) {
            unlink($material['file_path']);
        }
        
        // Xóa trong database
        if ($materialModel->delete($material_id)) {
            $_SESSION['success'] = 'Xóa tài liệu thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tài liệu';
        }
        
        header('Location: /btl/instructor/materials/upload/' . $lesson['id']);
        exit;
    }
}
?>