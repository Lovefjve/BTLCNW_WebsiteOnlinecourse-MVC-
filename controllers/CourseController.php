<?php
require_once 'core/Auth.php';
require_once 'models/Course.php';
require_once 'models/User.php';
require_once 'models/Category.php';

class CourseController {
    private $courseModel;
    private $userModel;
    private $categoryModel;

    public function __construct() {
        $this->courseModel = new Course();
        $this->userModel = new User();
        $this->categoryModel = new Category();
    }

    // Admin: danh sách cần duyệt (pending)
    public function manageCourses() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $user = Auth::getUser();
    // Show both pending and rejected so admin can review/re-open decisions if needed
    $courses = $this->courseModel->getByStatuses(['pending']);

        // Attach instructor username and category name for display
        foreach ($courses as &$c) {
            $instr = $this->userModel->getUserById($c['instructor_id']);
            $c['instructor_name'] = $instr ? ($instr['fullname'] ?: $instr['username']) : 'N/A';

            $cat = $this->categoryModel->getById($c['category_id']);
            $c['category_name'] = $cat ? $cat['name'] : 'N/A';

            // map status label for view convenience
            $labels = ['pending' => 'Chờ', 'published' => 'Duyệt', 'rejected' => 'Từ chối', 'draft' => 'Nháp'];
            $c['status_label'] = $labels[$c['status']] ?? $c['status'];
        }
        unset($c);

        require_once 'views/admin/courses/manage.php';
    }

    // Admin: chi tiết 1 khóa học
    public function courseDetail() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '/admin/courses');
            exit;
        }

        $course = $this->courseModel->getById($id);
        if ($course) {
            $instr = $this->userModel->getUserById($course['instructor_id']);
            $course['instructor_name'] = $instr ? ($instr['fullname'] ?: $instr['username']) : 'N/A';

            $cat = $this->categoryModel->getById($course['category_id']);
            $course['category_name'] = $cat ? $cat['name'] : 'N/A';

            $labels = ['pending' => 'Chờ', 'published' => 'Duyệt', 'rejected' => 'Từ chối', 'draft' => 'Nháp'];
            $course['status_label'] = $labels[$course['status']] ?? $course['status'];
        }

        require_once 'views/admin/courses/detail.php';
    }

    // Admin: phê duyệt
    public function approveCourse() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            http_response_code(403);
            echo 'Unauthorized';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
            if ($id > 0 && $this->courseModel->setStatus($id, 'published')) {
                header('Location: ' . BASE_URL . '/admin/courses?success=approved');
                exit;
            }
        }

        http_response_code(400);
        echo 'Bad Request';
        exit;
    }

    // Admin: từ chối
    public function rejectCourse() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            http_response_code(403);
            echo 'Unauthorized';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
            if ($id > 0 && $this->courseModel->setStatus($id, 'rejected')) {
                header('Location: ' . BASE_URL . '/admin/courses?success=rejected');
                exit;
            }
        }

        http_response_code(400);
        echo 'Bad Request';
        exit;
    }
}

?>
