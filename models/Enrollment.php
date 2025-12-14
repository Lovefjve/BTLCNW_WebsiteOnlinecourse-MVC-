<?php
// models/Enrollment.php

class Enrollment
{
    private $conn;

    public function __construct()
    {
        require_once 'config/database.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy danh sách học viên theo khóa học với phân trang
    public function getStudentsByCourse($course_id, $offset = 0, $limit = 10)
    {
        // Vì có UNIQUE KEY trong database, chúng ta chỉ cần DISTINCT hoặc GROUP BY u.id
        $sql = "SELECT 
                u.id, 
                u.username, 
                u.email, 
                u.fullname,
                u.created_at as user_created_at,
                e.enrolled_date, 
                e.progress, 
                e.status
            FROM users u
            INNER JOIN enrollments e ON u.id = e.student_id
            WHERE e.course_id = ? AND u.role = 0
            GROUP BY u.id  -- Đảm bảo mỗi học viên chỉ xuất hiện 1 lần
            ORDER BY e.enrolled_date DESC
            LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $course_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách học viên theo khóa học (không phân trang)
    // Phương thức getAllStudentsByCourse() cũng cần DISTINCT
    public function getAllStudentsByCourse($course_id)
    {
        $sql = "SELECT DISTINCT u.*, e.enrolled_date, e.progress, e.status
            FROM users u
            JOIN enrollments e ON u.id = e.student_id
            WHERE e.course_id = ? AND u.role = 0
            ORDER BY e.enrolled_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm số học viên theo khóa học
    public function countStudentsByCourse($course_id)
    {
        $sql = "SELECT COUNT(DISTINCT u.id) as total 
            FROM users u
            INNER JOIN enrollments e ON u.id = e.student_id
            WHERE e.course_id = ? AND u.role = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Kiểm tra user có phải là học viên của khóa học không
    public function isStudentEnrolled($student_id, $course_id)
    {
        $sql = "SELECT COUNT(*) as enrolled 
                FROM enrollments 
                WHERE student_id = ? AND course_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id, $course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['enrolled'] > 0;
    }

    // Lấy tiến độ học tập của học viên
    public function getStudentProgress($student_id, $course_id)
    {
        $sql = "SELECT e.progress, e.status, e.enrolled_date,
                       (SELECT COUNT(*) FROM lessons WHERE course_id = ?) as total_lessons
                FROM enrollments e
                WHERE e.student_id = ? AND e.course_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id, $student_id, $course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return [
                'progress' => $result['progress'] ?? 0,
                'status' => $result['status'] ?? 'active',
                'total_lessons' => $result['total_lessons'] ?? 0,
                'enrolled_date' => $result['enrolled_date']
            ];
        }

        return null;
    }

    // Lấy ngày đăng ký
    public function getEnrollmentDate($student_id, $course_id)
    {
        $sql = "SELECT enrolled_date FROM enrollments 
                WHERE student_id = ? AND course_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id, $course_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['enrolled_date'] ?? null;
    }

    // Thêm học viên vào khóa học
    public function enrollStudent($student_id, $course_id)
    {
        // Kiểm tra đã đăng ký chưa
        if ($this->isStudentEnrolled($student_id, $course_id)) {
            return false;
        }

        $sql = "INSERT INTO enrollments (student_id, course_id, enrolled_date, status, progress) 
                VALUES (?, ?, NOW(), 'active', 0)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$student_id, $course_id]);
    }

    // Xóa học viên khỏi khóa học
    public function unenrollStudent($student_id, $course_id)
    {
        $sql = "DELETE FROM enrollments WHERE student_id = ? AND course_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$student_id, $course_id]);
    }

    // Cập nhật tiến độ học tập
    public function updateProgress($student_id, $course_id, $progress, $status = null)
    {
        $sql = "UPDATE enrollments 
                SET progress = ?";

        $params = [$progress];

        if ($status !== null) {
            $sql .= ", status = ?";
            $params[] = $status;
        }

        $sql .= " WHERE student_id = ? AND course_id = ?";
        $params[] = $student_id;
        $params[] = $course_id;

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    // Lấy tất cả khóa học của học viên
    public function getCoursesByStudent($student_id)
    {
        $sql = "SELECT c.*, e.enrolled_date, e.progress, e.status
                FROM courses c
                JOIN enrollments e ON c.id = e.course_id
                WHERE e.student_id = ?
                ORDER BY e.enrolled_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin enrollment cụ thể
    public function getEnrollment($student_id, $course_id)
    {
        $sql = "SELECT * FROM enrollments 
                WHERE student_id = ? AND course_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$student_id, $course_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thống kê enrollment theo tháng
    public function getMonthlyStats($course_id, $year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $sql = "SELECT 
                    MONTH(enrolled_date) as month,
                    COUNT(*) as enrollments,
                    SUM(CASE WHEN progress >= 100 THEN 1 ELSE 0 END) as completed
                FROM enrollments 
                WHERE course_id = ? AND YEAR(enrolled_date) = ?
                GROUP BY MONTH(enrolled_date)
                ORDER BY month";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id, $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm học viên theo trạng thái
    public function countStudentsByStatus($course_id, $status = null)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM enrollments e
                JOIN users u ON e.student_id = u.id
                WHERE e.course_id = ? AND u.role = 0";

        $params = [$course_id];

        if ($status !== null) {
            $sql .= " AND e.status = ?";
            $params[] = $status;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Lấy học viên đã hoàn thành khóa học (progress >= 100)
    public function getCompletedStudents($course_id)
    {
        $sql = "SELECT u.*, e.enrolled_date, e.progress, e.status
                FROM users u
                JOIN enrollments e ON u.id = e.student_id
                WHERE e.course_id = ? AND u.role = 0 AND e.progress >= 100
                ORDER BY e.enrolled_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
