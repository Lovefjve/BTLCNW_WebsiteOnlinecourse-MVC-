<?php
class LessonController {
    private $db;
    private $lessonModel;
    private $courseModel;
    private $materialModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->lessonModel = new Lesson($this->db);
        $this->courseModel = new Course($this->db);
        $this->materialModel = new Material($this->db);
    }

    /**
     * Hiển thị trang quản lý bài học của khóa học
     */
    public function manage($course_id) {
        // LƯU Ý: Check quyền giảng viên - do lập trình viên A làm
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu khóa học
        if (!$this->courseModel->isCourseOwner($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền quản lý bài học của khóa học này';
            header('Location: /instructor/courses');
            exit;
        }
        
        // Lấy thông tin khóa học
        $course = $this->courseModel->readOne($course_id);
        if (!$course) {
            $_SESSION['error'] = 'Khóa học không tồn tại';
            header('Location: /instructor/courses');
            exit;
        }
        
        // Lấy danh sách bài học
        $lessons = $this->lessonModel->getByCourse($course_id);
        
        // Load view
        require_once __DIR__ . '/../views/instructor/lessons/manage.php';
    }

    /**
     * Hiển thị form tạo bài học
     */
    public function create($course_id) {
        // LƯU Ý: Check quyền giảng viên - do lập trình viên A làm
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu khóa học
        if (!$this->courseModel->isCourseOwner($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền tạo bài học cho khóa học này';
            header('Location: /instructor/courses');
            exit;
        }
        
        // Lấy thông tin khóa học
        $course = $this->courseModel->readOne($course_id);
        if (!$course) {
            $_SESSION['error'] = 'Khóa học không tồn tại';
            header('Location: /instructor/courses');
            exit;
        }
        
        // Lấy số lượng bài học hiện tại để xác định thứ tự
        $lesson_count = $this->lessonModel->countByCourse($course_id);
        
        // Load view
        require_once __DIR__ . '/../views/instructor/lessons/create.php';
    }

    /**
     * Xử lý tạo bài học (POST)
     */
    public function store($course_id) {
        // LƯU Ý: Check quyền giảng viên và CSRF token - do lập trình viên A làm
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ';
            header('Location: /instructor/courses/' . $course_id . '/lessons/create');
            exit;
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu khóa học
        if (!$this->courseModel->isCourseOwner($course_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền tạo bài học cho khóa học này';
            header('Location: /instructor/courses');
            exit;
        }
        
        // Validate input
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        $lesson_order = (int)($_POST['lesson_order'] ?? 1);
        
        if (empty($title)) $errors[] = 'Tiêu đề không được để trống';
        if ($lesson_order < 1) $errors[] = 'Thứ tự bài học không hợp lệ';
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /instructor/courses/' . $course_id . '/lessons/create');
            exit;
        }
        
        // Tạo bài học
        $this->lessonModel->course_id = $course_id;
        $this->lessonModel->title = $title;
        $this->lessonModel->content = $content;
        $this->lessonModel->video_url = $video_url;
        $this->lessonModel->lesson_order = $lesson_order;
        
        if ($lesson_id = $this->lessonModel->create()) {
            $_SESSION['success'] = 'Tạo bài học thành công!';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/edit');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo bài học';
            header('Location: /instructor/courses/' . $course_id . '/lessons/create');
            exit;
        }
    }

    /**
     * Hiển thị form chỉnh sửa bài học
     */
    public function edit($course_id, $lesson_id) {
        // LƯU Ý: Check quyền giảng viên - do lập trình viên A làm
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu bài học
        if (!$this->lessonModel->isLessonOwner($lesson_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa bài học này';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit;
        }
        
        // Lấy thông tin bài học
        $lesson = $this->lessonModel->readOne($lesson_id);
        if (!$lesson || $lesson['course_id'] != $course_id) {
            $_SESSION['error'] = 'Bài học không tồn tại';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit;
        }
        
        // Lấy danh sách tài liệu của bài học
        $materials = $this->materialModel->getByLesson($lesson_id);
        
        // Load view
        require_once __DIR__ . '/../views/instructor/lessons/edit.php';
    }

    /**
     * Xử lý cập nhật bài học (POST)
     */
    public function update($course_id, $lesson_id) {
        // LƯU Ý: Check quyền giảng viên và CSRF token - do lập trình viên A làm
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/edit');
            exit;
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu bài học
        if (!$this->lessonModel->isLessonOwner($lesson_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa bài học này';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit;
        }
        
        // Validate input
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        $lesson_order = (int)($_POST['lesson_order'] ?? 1);
        
        if (empty($title)) $errors[] = 'Tiêu đề không được để trống';
        if ($lesson_order < 1) $errors[] = 'Thứ tự bài học không hợp lệ';
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/edit');
            exit;
        }
        
        // Cập nhật bài học
        $this->lessonModel->id = $lesson_id;
        $this->lessonModel->title = $title;
        $this->lessonModel->content = $content;
        $this->lessonModel->video_url = $video_url;
        $this->lessonModel->lesson_order = $lesson_order;
        
        if ($this->lessonModel->update()) {
            $_SESSION['success'] = 'Cập nhật bài học thành công!';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/edit');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật bài học';
            header('Location: /instructor/courses/' . $course_id . '/lessons/' . $lesson_id . '/edit');
            exit;
        }
    }

    /**
     * Xóa bài học
     */
    public function delete($course_id, $lesson_id) {
        // LƯU Ý: Check quyền giảng viên và CSRF token - do lập trình viên A làm
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit;
        }
        
        $instructor_id = $_SESSION['user_id'];
        
        // Kiểm tra quyền sở hữu bài học
        if (!$this->lessonModel->isLessonOwner($lesson_id, $instructor_id)) {
            $_SESSION['error'] = 'Bạn không có quyền xóa bài học này';
            header('Location: /instructor/courses/' . $course_id . '/lessons');
            exit;
        }
        
        // Xóa bài học
        $this->lessonModel->id = $lesson_id;
        
        if ($this->lessonModel->delete()) {
            $_SESSION['success'] = 'Xóa bài học thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa bài học';
        }
        
        header('Location: /instructor/courses/' . $course_id . '/lessons');
        exit;
    }
}
?>