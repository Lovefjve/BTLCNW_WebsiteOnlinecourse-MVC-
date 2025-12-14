<?php
// models/Material.php

class Material {
    private $conn;
    private $upload_dir = 'assets/uploads/materials/';
    
    public function __construct() {
        require_once 'config/database.php';
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Tạo thư mục upload nếu chưa tồn tại
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }
    
    // Lấy tài liệu theo ID
    public function getById($id) {
        $sql = "SELECT * FROM materials WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Lấy tài liệu theo bài học
    public function getByLesson($lesson_id) {
        $sql = "SELECT * FROM materials 
                WHERE lesson_id = ? 
                ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$lesson_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Upload tài liệu
    public function upload($lesson_id, $file_data) {
        // Kiểm tra file size (max 50MB)
        $max_size = 50 * 1024 * 1024;
        if ($file_data['size'] > $max_size) {
            throw new Exception('File quá lớn. Kích thước tối đa 50MB');
        }
        
        // Kiểm tra file type
        $allowed_extensions = [
            'pdf', 'doc', 'docx', 'ppt', 'pptx', 
            'xls', 'xlsx', 'zip', 'rar', '7z',
            'txt', 'jpg', 'jpeg', 'png', 'gif'
        ];
        
        $file_ext = strtolower(pathinfo($file_data['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_extensions)) {
            throw new Exception('Loại file không được hỗ trợ. Chỉ chấp nhận: ' . implode(', ', $allowed_extensions));
        }
        
        // Tạo tên file unique
        $original_name = basename($file_data['name']);
        $safe_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original_name);
        $new_filename = uniqid() . '_' . time() . '_' . $safe_name;
        $destination = $this->upload_dir . $new_filename;
        
        // Di chuyển file
        if (!move_uploaded_file($file_data['tmp_name'], $destination)) {
            throw new Exception('Không thể lưu file');
        }
        
        // Xác định file type
        $file_type = $this->getFileType($file_ext);
        
        // Lưu vào database
        $sql = "INSERT INTO materials 
                (lesson_id, filename, file_path, file_type, uploaded_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $lesson_id,
            $original_name,
            $destination,
            $file_type
        ]);
    }
    
    // Xóa tài liệu
    public function delete($id) {
        // Lấy thông tin file trước khi xóa
        $material = $this->getById($id);
        
        if ($material) {
            // Xóa file vật lý
            $file_path = $material['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // Xóa record trong database
        $sql = "DELETE FROM materials WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Đếm số tài liệu theo bài học
    public function countByLesson($lesson_id) {
        $sql = "SELECT COUNT(*) as count FROM materials WHERE lesson_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$lesson_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    // Đếm tổng tài liệu theo khóa học
    public function countByCourse($course_id) {
        $sql = "SELECT COUNT(*) as total 
                FROM materials m
                JOIN lessons l ON m.lesson_id = l.id
                WHERE l.course_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Helper: Xác định file type
    private function getFileType($extension) {
        $types = [
            'pdf' => 'document',
            'doc' => 'document',
            'docx' => 'document',
            'ppt' => 'presentation',
            'pptx' => 'presentation',
            'xls' => 'spreadsheet',
            'xlsx' => 'spreadsheet',
            'zip' => 'archive',
            'rar' => 'archive',
            '7z' => 'archive',
            'txt' => 'text',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image'
        ];
        
        return $types[$extension] ?? 'other';
    }
    
    // Lấy icon cho file type
    public function getFileIcon($file_type) {
        $icons = [
            'document' => 'fas fa-file-word',
            'presentation' => 'fas fa-file-powerpoint',
            'spreadsheet' => 'fas fa-file-excel',
            'archive' => 'fas fa-file-archive',
            'text' => 'fas fa-file-alt',
            'image' => 'fas fa-file-image',
            'pdf' => 'fas fa-file-pdf',
            'other' => 'fas fa-file'
        ];
        
        return $icons[$file_type] ?? 'fas fa-file';
    }
    
    // Lấy màu cho file type
    public function getFileColor($file_type) {
        $colors = [
            'document' => '#0d6efd',
            'presentation' => '#fd7e14',
            'spreadsheet' => '#198754',
            'archive' => '#6f42c1',
            'text' => '#6c757d',
            'image' => '#20c997',
            'pdf' => '#dc3545',
            'other' => '#6c757d'
        ];
        
        return $colors[$file_type] ?? '#6c757d';
    }
    
    // Format file size
    public function formatFileSize($bytes) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
?>