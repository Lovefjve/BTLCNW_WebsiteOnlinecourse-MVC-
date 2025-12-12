<?php
class Course extends Model {
    protected $table = 'courses';

    // Lấy tất cả khóa học (có tìm kiếm + lọc danh mục)
    public function getAll($keyword = '', $category_id = '') {
        $sql = "SELECT c.*, cat.name as category_name, u.fullname as instructor_name 
                FROM courses c
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN users u ON c.instructor_id = u.id
                WHERE 1=1";

        $params = [];
        if ($keyword !== '') {
            $sql .= " AND c.title LIKE ?";
            $params[] = "%$keyword%";
        }
        if ($category_id !== '' && $category_id !== null) {
            $sql .= " AND c.category_id = ?";
            $params[] = $category_id;
        }
        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // === THÊM HÀM NÀY – CHI TIẾT KHÓA HỌC ===
    public function getById($id) {
        $sql = "SELECT c.*, cat.name as category_name, u.fullname as instructor_name 
                FROM courses c
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN users u ON c.instructor_id = u.id
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // === THÊM HÀM NÀY – ĐẾM HỌC VIÊN ===
    public function countEnrollments($course_id) {
        $sql = "SELECT COUNT(*) FROM enrollments WHERE course_id = ? AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$course_id]);
        return $stmt->fetchColumn();
    }
}
?>