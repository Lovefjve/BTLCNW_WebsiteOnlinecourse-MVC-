<?php
// models/Enrollment.php

class Enrollment {
    private $conn;
    
    public function __construct() {
        require_once 'config/database.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Lấy danh sách học viên theo khóa học
    public function getStudentsByCourse($course_id) {
        $sql = "SELECT u.*, e.enrolled_at, e.progress, e.last_accessed, 
                       e.status, e.completed_lessons
                FROM users u
                JOIN enrollments e ON u.id = e.user_id
                WHERE e.course_id = ? AND u.role = 0
                ORDER BY e.enrolled_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Đếm số học viên theo khóa học
    public function countStudentsByCourse($course_id) {
        $sql = "SELECT COUNT(*) as total 
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                WHERE e.course_id = ? AND u.role = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Kiểm tra user có phải là học viên của khóa học không
    public function isStudentEnrolled($user_id, $course_id) {
        $sql = "SELECT COUNT(*) as enrolled 
                FROM enrollments 
                WHERE user_id = ? AND course_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id, $course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['enrolled'] > 0;
    }
    
    // Lấy tiến độ học tập của học viên
    public function getStudentProgress($student_id, $course_id) {
        $sql = "SELECT e.progress, e.completed_lessons, 
                       e.last_accessed, e.enrolled_at,
                       (SELECT COUNT(*) FROM lessons WHERE course_id = ?) as total_lessons
                FROM enrollments e
                WHERE e.user_id = ? AND e.course_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id, $student_id, $course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return [
                'progress' => $result['progress'] ?? 0,
                'completed_lessons' => $result['completed_lessons'] ?? 0,
                'total_lessons' => $result['total_lessons'] ?? 0,
                'last_accessed' => $result['last_accessed'],
                'enrolled_at' => $result['enrolled_at']
            ];
        }
        
        return null;
    }
    
    // Kiểm tra bài học đã hoàn thành chưa
    public function isLessonCompleted($student_id, $lesson_id) {
        $sql = "SELECT COUNT(*) as completed 
                FROM lesson_completions 
                WHERE user_id = ? AND lesson_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id, $lesson_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['completed'] > 0;
    }
    
    // Lấy ngày đăng ký
    public function getEnrollmentDate($student_id, $course_id) {
        $sql = "SELECT enrolled_at FROM enrollments 
                WHERE user_id = ? AND course_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id, $course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['enrolled_at'] ?? null;
    }
    
    // Thêm học viên vào khóa học
    public function enrollStudent($student_id, $course_id) {
        // Kiểm tra đã đăng ký chưa
        if ($this->isStudentEnrolled($student_id, $course_id)) {
            return false;
        }
        
        $sql = "INSERT INTO enrollments (user_id, course_id, enrolled_at, status) 
                VALUES (?, ?, NOW(), 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$student_id, $course_id]);
    }
    
    // Xóa học viên khỏi khóa học
    public function unenrollStudent($student_id, $course_id) {
        $sql = "DELETE FROM enrollments WHERE user_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$student_id, $course_id]);
    }
    
    // Cập nhật tiến độ học tập
    public function updateProgress($student_id, $course_id, $progress, $completed_lessons) {
        $sql = "UPDATE enrollments 
                SET progress = ?, completed_lessons = ?, last_accessed = NOW() 
                WHERE user_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$progress, $completed_lessons, $student_id, $course_id]);
    }
    
    // Lấy tất cả khóa học của học viên
    public function getCoursesByStudent($student_id) {
        $sql = "SELECT c.*, e.enrolled_at, e.progress, e.completed_lessons
                FROM courses c
                JOIN enrollments e ON c.id = e.course_id
                WHERE e.user_id = ?
                ORDER BY e.enrolled_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy thông tin enrollment cụ thể
    public function getEnrollment($student_id, $course_id) {
        $sql = "SELECT * FROM enrollments 
                WHERE user_id = ? AND course_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id, $course_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Thống kê enrollment theo tháng
    public function getMonthlyStats($course_id, $year = null) {
        if ($year === null) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                    MONTH(enrolled_at) as month,
                    COUNT(*) as enrollments,
                    SUM(CASE WHEN progress >= 100 THEN 1 ELSE 0 END) as completed
                FROM enrollments 
                WHERE course_id = ? AND YEAR(enrolled_at) = ?
                GROUP BY MONTH(enrolled_at)
                ORDER BY month";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id, $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Đánh dấu bài học đã hoàn thành
    public function markLessonCompleted($student_id, $lesson_id) {
        // Kiểm tra đã hoàn thành chưa
        if ($this->isLessonCompleted($student_id, $lesson_id)) {
            return false;
        }
        
        $sql = "INSERT INTO lesson_completions (user_id, lesson_id, completed_at) 
                VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$student_id, $lesson_id]);
    }
}
?>