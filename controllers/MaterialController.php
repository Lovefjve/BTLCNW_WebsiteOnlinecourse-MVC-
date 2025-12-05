<?php
class MaterialController {
    private $db;
    private $materialModel;
    private $lessonModel;
    private $courseModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->materialModel = new Material($this->db);
        $this->lessonModel = new Lesson($this->db);
        $this->courseModel = new Course($this->db);
    }

    /**
     * Upload tài liệu cho bài học
     */
    public function upload($course_id, $lesson_id) {
        // LƯU Ý: Check quyền giảng viên - do lập trình viên A làm
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu bài học
        if (!$this->lessonModel->isLessonOwner($lesson_id, $instructor_id)) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền upload tài liệu cho bài học này']);
            exit;
        }
        
        // Kiểm tra file upload
        if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn file để upload']);
            exit;
        }
        
        $file = $_FILES['file'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Kiểm tra loại file
        $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
        if (!in_array($file_ext, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Loại file không được hỗ trợ. Chỉ chấp nhận: ' . implode(', ', $allowed_types)]);
            exit;
        }
        
        // Kiểm tra kích thước file (max 10MB)
        $max_size = 10 * 1024 * 1024;
        if ($file_size > $max_size) {
            echo json_encode(['success' => false, 'message' => 'Kích thước file quá lớn (tối đa 10MB)']);
            exit;
        }
        
        // Tạo thư mục upload nếu chưa tồn tại
        $upload_dir = __DIR__ . '/../assets/uploads/materials/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Tạo tên file mới để tránh trùng lặp
        $new_filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
        $upload_path = $upload_dir . $new_filename;
        
        // Di chuyển file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Lưu thông tin vào database
            $this->materialModel->lesson_id = $lesson_id;
            $this->materialModel->filename = $file_name;
            $this->materialModel->file_path = $new_filename;
            $this->materialModel->file_type = $file_ext;
            $this->materialModel->file_size = $file_size;
            
            if ($material_id = $this->materialModel->create()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Upload tài liệu thành công',
                    'material' => [
                        'id' => $material_id,
                        'filename' => $file_name,
                        'file_type' => $file_ext,
                        'file_size' => $this->formatFileSize($file_size),
                        'uploaded_at' => date('d/m/Y H:i')
                    ]
                ]);
                exit;
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi upload file']);
        exit;
    }

    /**
     * Xóa tài liệu
     */
    public function delete($material_id) {
        // LƯU Ý: Check quyền giảng viên và CSRF token - do lập trình viên A làm
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ';
            header('Location: /instructor/courses');
            exit;
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu tài liệu
        if (!$this->materialModel->isMaterialOwner($material_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền xóa tài liệu này';
            header('Location: /instructor/courses');
            exit;
        }
        
        // Lấy thông tin tài liệu
        $material = $this->materialModel->readOne($material_id);
        if (!$material) {
            $_SESSION['error'] = 'Tài liệu không tồn tại';
            header('Location: /instructor/courses');
            exit;
        }
        
        // Xóa file vật lý
        $file_path = __DIR__ . '/../assets/uploads/materials/' . $material['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Xóa record trong database
        $this->materialModel->id = $material_id;
        
        if ($this->materialModel->delete()) {
            $_SESSION['success'] = 'Xóa tài liệu thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tài liệu';
        }
        
        header('Location: /instructor/courses/' . $material['course_id'] . '/lessons/' . $material['lesson_id'] . '/edit');
        exit;
    }

    /**
     * Download tài liệu
     */
    public function download($material_id) {
        // Kiểm tra quyền truy cập tài liệu
        // Nếu là học viên: kiểm tra xem đã đăng ký khóa học chưa
        // Nếu là giảng viên: kiểm tra quyền sở hữu
        // LƯU Ý: Phần này do lập trình viên A làm
        
        $material = $this->materialModel->readOne($material_id);
        if (!$material) {
            $_SESSION['error'] = 'Tài liệu không tồn tại';
            header('Location: /');
            exit;
        }
        
        $file_path = __DIR__ . '/../assets/uploads/materials/' . $material['file_path'];
        
        if (!file_exists($file_path)) {
            $_SESSION['error'] = 'File không tồn tại trên server';
            header('Location: /');
            exit;
        }
        
        // Thiết lập headers cho download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $material['filename'] . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        readfile($file_path);
        exit;
    }

    /**
     * Format kích thước file
     */
    private function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
?>