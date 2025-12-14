<?php
// controllers/StudentController.php

class StudentController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? 0) != 1) {
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

    // ========== KẾT THÚC PHƯƠNG THỨC RENDER ==========

    // ========== PHƯƠNG THỨC REDIRECT ==========
    private function redirect($url)
    {
        header("Location: $url");
        exit;
    }
    // ========== KẾT THÚC PHƯƠNG THỨC RENDER ==========

    public function index()
    {
        $course_id = $_GET['course_id'] ?? 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }

        // Load models
        require_once 'models/Course.php';
        require_once 'models/Enrollment.php';
        require_once 'models/Lesson.php';

        $courseModel = new Course();
        $enrollmentModel = new Enrollment();
        $lessonModel = new Lesson();

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
                $_SESSION['error'] = "Bạn không có quyền xem học viên của khóa học này";
                header('Location: ?c=instructor&a=courses');
                exit;
            }

            // Lấy tổng số học viên
            $total_students = $enrollmentModel->countStudentsByCourse($course_id);
            $total_pages = ceil($total_students / $limit);

            // Lấy danh sách học viên
            $students = $enrollmentModel->getStudentsByCourse($course_id, $offset, $limit);

            // Lấy tổng số bài học
            $total_lessons = $lessonModel->countByCourse($course_id);

            // Thống kê
            $active_students = 0;
            $completed_students = 0;
            $inactive_students = 0;
            $dropped_students = 0;

            // Xử lý dữ liệu học viên
            foreach ($students as &$student) {
                // Format dữ liệu
                $student['progress'] = (int) ($student['progress'] ?? 0);
                $student['status'] = $student['status'] ?? 'active';
                $student['enrolled_date'] = $student['enrolled_date'] ?? null;

                // Tính toán số bài học đã hoàn thành
                if ($total_lessons > 0) {
                    $student['completed_lessons'] = round(($student['progress'] / 100) * $total_lessons);
                } else {
                    $student['completed_lessons'] = 0;
                }

                // Format ngày đăng ký
                if (!empty($student['enrolled_date'])) {
                    $student['enrolled_date_formatted'] = date('d/m/Y', strtotime($student['enrolled_date']));
                } else {
                    $student['enrolled_date_formatted'] = 'N/A';
                }

                // Format tên hiển thị
                $student['display_name'] = !empty($student['fullname']) ? $student['fullname'] : (!empty($student['username']) ? $student['username'] : 'Không có tên');
                $student['display_email'] = $student['email'] ?? 'Không có email';

                // Map status text
                $status_map = [
                    'active' => 'Đang học',
                    'completed' => 'Đã hoàn thành',
                    'dropped' => 'Đã hủy'
                ];
                $student['enrollment_status_text'] = $status_map[$student['status']] ?? 'Đang học';

                // Xác định trạng thái học tập
                if ($student['progress'] >= 100) {
                    $completed_students++;
                    $student['learning_status'] = 'completed';
                    $student['learning_status_text'] = 'Đã hoàn thành';
                } elseif ($student['progress'] > 0) {
                    $active_students++;
                    $student['learning_status'] = 'active';
                    $student['learning_status_text'] = 'Đang học';
                } else {
                    $inactive_students++;
                    $student['learning_status'] = 'inactive';
                    $student['learning_status_text'] = 'Chưa bắt đầu';
                }

                // Đếm dropped students
                if ($student['status'] === 'dropped') {
                    $dropped_students++;
                }
            }
            unset($student); // Hủy tham chiếu

        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            header('Location: ?c=instructor&a=courses');
            exit;
        }

        // ========== SỬA DÒNG NÀY ==========
        //view rendering
        $this->render('instructor/students/list', [
            'course' => $course,
            'students' => $students,
            'total_lessons' => $total_lessons,
            'total_students' => $total_students,
            'active_students' => $active_students,
            'completed_students' => $completed_students,
            'inactive_students' => $inactive_students,
            'dropped_students' => $dropped_students,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'limit' => $limit
        ]);
        // ========== KẾT THÚC SỬA ==========
    }

    // Xuất danh sách học viên
    public function export()
    {
        $course_id = $_GET['course_id'] ?? 0;

        if (!$course_id) {
            $_SESSION['error'] = "Không tìm thấy khóa học";
            header('Location: ?c=instructor&a=courses');
            exit;
        }

        // Load models
        require_once 'models/Course.php';
        require_once 'models/Enrollment.php';
        require_once 'models/Lesson.php';

        $courseModel = new Course();
        $enrollmentModel = new Enrollment();
        $lessonModel = new Lesson();

        try {
            // Lấy thông tin khóa học
            $course = $courseModel->getById($course_id);

            if (!$course || $course['instructor_id'] != $_SESSION['user_id']) {
                $_SESSION['error'] = "Bạn không có quyền xuất dữ liệu";
                header('Location: ?c=instructor&a=courses');
                exit;
            }

            // Lấy tất cả học viên (không phân trang)
            $students = $enrollmentModel->getAllStudentsByCourse($course_id);
            $total_lessons = $lessonModel->countByCourse($course_id);

            // Tạo file Excel
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="danh_sach_hoc_vien_' . $course_id . '_' . date('Y-m-d_H-i') . '.xls"');
            header('Cache-Control: max-age=0');

            // HTML cho Excel
            echo '<!DOCTYPE html>';
            echo '<html>';
            echo '<head>';
            echo '<meta charset="UTF-8">';
            echo '<title>Danh sách học viên - ' . htmlspecialchars($course['title']) . '</title>';
            echo '<style>';
            echo 'table { border-collapse: collapse; width: 100%; }';
            echo 'th { background-color: #4a6cf7; color: white; padding: 10px; text-align: center; font-weight: bold; }';
            echo 'td { border: 1px solid #ddd; padding: 8px; }';
            echo 'tr:nth-child(even) { background-color: #f9f9f9; }';
            echo '.center { text-align: center; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';

            // Tiêu đề
            echo '<h2>DANH SÁCH HỌC VIÊN</h2>';
            echo '<h3>Khóa học: ' . htmlspecialchars($course['title']) . '</h3>';
            echo '<p>Ngày xuất: ' . date('d/m/Y H:i:s') . '</p>';

            // Bảng dữ liệu
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>STT</th>';
            echo '<th>Họ và tên</th>';
            echo '<th>Email</th>';
            echo '<th>Ngày đăng ký</th>';
            echo '<th>Tiến độ</th>';
            echo '<th>Bài học đã hoàn thành</th>';
            echo '<th>Trạng thái học tập</th>';
            echo '<th>Trạng thái đăng ký</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            $stt = 1;
            $total_students = count($students);
            $active_count = 0;
            $completed_count = 0;
            $inactive_count = 0;
            $dropped_count = 0;

            foreach ($students as $student) {
                $progress = (int) ($student['progress'] ?? 0);
                $status = $student['status'] ?? 'active';

                // Tính số bài học đã hoàn thành
                $completed_lessons = $total_lessons > 0 ?
                    round(($progress / 100) * $total_lessons) : 0;

                // Xác định trạng thái học tập
                $learning_status = '';
                if ($progress >= 100) {
                    $learning_status = 'Đã hoàn thành';
                    $completed_count++;
                } elseif ($progress > 0) {
                    $learning_status = 'Đang học';
                    $active_count++;
                } else {
                    $learning_status = 'Chưa bắt đầu';
                    $inactive_count++;
                }

                // Trạng thái đăng ký
                $status_map = [
                    'active' => 'Đang học',
                    'completed' => 'Đã hoàn thành',
                    'dropped' => 'Đã hủy'
                ];
                $enrollment_status = $status_map[$status] ?? 'Đang học';

                if ($status === 'dropped') {
                    $dropped_count++;
                }

                // Format ngày đăng ký
                if (!empty($student['enrolled_date'])) {
                    $enrolled_date = date('d/m/Y', strtotime($student['enrolled_date']));
                } else {
                    $enrolled_date = 'N/A';
                }

                // Thông tin học viên
                $display_name = $student['fullname'] ?? $student['username'] ?? 'N/A';
                $display_email = $student['email'] ?? 'N/A';

                // Xuất hàng
                echo '<tr>';
                echo '<td class="center">' . $stt++ . '</td>';
                echo '<td>' . htmlspecialchars($display_name) . '</td>';
                echo '<td>' . htmlspecialchars($display_email) . '</td>';
                echo '<td class="center">' . $enrolled_date . '</td>';
                echo '<td class="center">' . $progress . '%</td>';
                echo '<td class="center">' . $completed_lessons . '/' . $total_lessons . '</td>';
                echo '<td class="center">' . $learning_status . '</td>';
                echo '<td class="center">' . $enrollment_status . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            // Thống kê
            echo '<br>';
            echo '<h4>THỐNG KÊ</h4>';
            echo '<p><strong>Tổng học viên:</strong> ' . $total_students . '</p>';
            echo '<p><strong>Đang học:</strong> ' . $active_count . '</p>';
            echo '<p><strong>Đã hoàn thành:</strong> ' . $completed_count . '</p>';
            echo '<p><strong>Chưa bắt đầu:</strong> ' . $inactive_count . '</p>';
            echo '<p><strong>Đã hủy:</strong> ' . $dropped_count . '</p>';
            echo '<p><strong>Tổng bài học:</strong> ' . $total_lessons . '</p>';

            echo '</body>';
            echo '</html>';
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi xuất file: " . $e->getMessage();
            header('Location: ?c=student&a=index&course_id=' . $course_id);
            exit;
        }
    }
    
}
