<?php
class Enrollment extends Model {
    
    // Đăng ký học
    public function enroll($course_id, $student_id) {
        try {
            $sql = "INSERT INTO enrollments (course_id, student_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$course_id, $student_id]);
        } catch (Exception $e) {
            return false; // đã đăng ký rồi (do UNIQUE constraint)
        }
    }

    // Kiểm tra đã đăng ký chưa
    public function isEnrolled($course_id, $student_id) {
        $sql = "SELECT id FROM enrollments WHERE course_id = ? AND student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$course_id, $student_id]);
        return $stmt->rowCount() > 0;
    }
}
?>