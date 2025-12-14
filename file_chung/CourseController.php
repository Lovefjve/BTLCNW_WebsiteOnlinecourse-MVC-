<?php
// file_chung/CourseController.php
// Canonical controller merging admin, instructor and public actions.
require_once __DIR__ . '/../config/Database.php';// no-op if file not used; safe include

class CourseController {
    private $courseModel;

    public function __construct() {
        // Lazy-load models inside methods to avoid strict dependencies at bootstrap
    }

    // PUBLIC: list courses (site)
    public function index() {
        require_once __DIR__ . '/../models/Course.php';
        require_once __DIR__ . '/../models/Category.php';
        $course = new Course();
        $category = new Category();

        $keyword = $_GET['keyword'] ?? '';
        $catId = $_GET['category'] ?? null;

        $courses = $course->getAll($keyword, $catId);
        $categories = $category->getAll();

        if (file_exists(__DIR__ . '/../views/courses/index.php')) {
            $data = ['courses' => $courses, 'categories' => $categories, 'keyword' => $keyword, 'category' => $catId];
            require __DIR__ . '/../views/courses/index.php';
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($courses);
    }

    // PUBLIC: course detail
    public function detail($id = null) {
        require_once __DIR__ . '/../models/Course.php';
        $courseModel = new Course();
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) { http_response_code(400); echo 'Missing id'; return; }

        $course = $courseModel->getById($id);
        if (file_exists(__DIR__ . '/../views/courses/detail.php')) {
            $data = $course;
            require __DIR__ . '/../views/courses/detail.php';
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($course);
    }

    // PUBLIC: enroll (if Enrollment model exists)
    public function enroll($id = null) {
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) { http_response_code(400); echo 'Missing id'; return; }

        if (!isset($_SESSION) || session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login';
            header('Location: /auth/login');
            exit;
        }

        if (!file_exists(__DIR__ . '/../models/Enrollment.php')) {
            http_response_code(501); echo 'Enrollment not implemented'; return;
        }
        require_once __DIR__ . '/../models/Enrollment.php';
        $enr = new Enrollment();
        $ok = $enr->enroll($id, $_SESSION['user_id']);
        if ($ok) { $_SESSION['success'] = 'Enrolled'; }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    }

    // INSTRUCTOR: list/manage own courses (Duc)
    public function instructorIndex() {
        if (!isset($_SESSION) || session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login'); exit; }

        require_once __DIR__ . '/../models/Course.php';
        $course = new Course();
        $instructor_id = $_SESSION['user_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; $offset = ($page -1) * $limit;
        $courses = $course->getByInstructor($instructor_id, $limit, $offset);
        $total = $course->countByInstructor($instructor_id);

        if (file_exists(__DIR__ . '/../views/instructor/course/manage.php')) {
            $data = ['courses'=>$courses,'totalCourses'=>$total,'page'=>$page,'totalPages'=>ceil($total/$limit)];
            require __DIR__ . '/../views/instructor/course/manage.php';
            return;
        }
        header('Content-Type: application/json'); echo json_encode($courses);
    }

    // INSTRUCTOR: create/store/edit/update/delete
    public function create() {
        if (!isset($_SESSION) || session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login'); exit; }

        require_once __DIR__ . '/../models/Category.php';
        $catModel = new Category();
        $categories = $catModel->getAll();
        // show form
        if (file_exists(__DIR__ . '/../views/instructor/course/create.php')) {
            $data = ['categories' => $categories];
            require __DIR__ . '/../views/instructor/course/create.php';
            return;
        }
        echo json_encode(['categories' => $categories]);
    }

    public function store() {
        if (!isset($_SESSION) || session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /?c=course&a=index'); exit; }

        // basic validation
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;

        $errors = [];
        if ($title === '') $errors[] = 'Title is required';
        if (empty($errors)) {
            // handle image upload
            $imageName = '';
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/courses/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid('course_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
            }

            require_once __DIR__ . '/../models/Course.php';
            $course = new Course();
            $ok = $course->create([
                'title' => $title,
                'description' => $description,
                'instructor_id' => $_SESSION['user_id'],
                'category_id' => $category_id,
                'price' => $price,
                'duration_weeks' => $_POST['duration_weeks'] ?? 0,
                'level' => $_POST['level'] ?? '',
                'image' => $imageName,
                'status' => 'pending'
            ]);

            if ($ok) {
                $_SESSION['success'] = 'Course created and pending approval';
            } else {
                $_SESSION['error'] = 'Failed to create course';
            }
        } else {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
        }
        header('Location: /?c=course&a=instructorIndex');
        exit;
    }

    public function edit() {
        if (!isset($_SESSION) || session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login'); exit; }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; if (!$id) { header('Location: /?c=course&a=instructorIndex'); exit; }
        require_once __DIR__ . '/../models/Course.php'; require_once __DIR__ . '/../models/Category.php';
        $courseModel = new Course(); $catModel = new Category();
        $course = $courseModel->getById($id);
        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) { header('Location: /?c=course&a=instructorIndex'); exit; }

        $categories = $catModel->getAll();
        if (file_exists(__DIR__ . '/../views/instructor/course/edit.php')) {
            $data = ['course' => $course, 'categories' => $categories];
            require __DIR__ . '/../views/instructor/course/edit.php';
            return;
        }
        echo json_encode($course);
    }

    public function update() {
        if (!isset($_SESSION) || session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /?c=course&a=instructorIndex'); exit; }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0; if (!$id) { header('Location: /?c=course&a=instructorIndex'); exit; }
        require_once __DIR__ . '/../models/Course.php'; $courseModel = new Course();
        $course = $courseModel->getById($id);
        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) { header('Location: /?c=course&a=instructorIndex'); exit; }

        $title = trim($_POST['title'] ?? ''); $description = trim($_POST['description'] ?? ''); $category_id = (int)($_POST['category_id'] ?? 0);
        $imageName = $course['image'] ?? '';
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/courses/'; if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('course_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
        }

        $ok = $courseModel->update($id, [
            'title' => $title,
            'description' => $description,
            'category_id' => $category_id,
            'price' => $_POST['price'] ?? 0,
            'duration_weeks' => $_POST['duration_weeks'] ?? 0,
            'level' => $_POST['level'] ?? '',
            'image' => $imageName,
            'status' => $_POST['status'] ?? $course['status']
        ]);

        if ($ok) { $_SESSION['success'] = 'Course updated'; } else { $_SESSION['error'] = 'Update failed'; }
        header('Location: /?c=course&a=instructorIndex'); exit;
    }

    public function delete() {
        if (!isset($_SESSION) || session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /?c=course&a=instructorIndex'); exit; }
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0; if (!$id) { header('Location: /?c=course&a=instructorIndex'); exit; }
        require_once __DIR__ . '/../models/Course.php'; $courseModel = new Course();
        $course = $courseModel->getById($id);
        if (!$course || $course['instructor_id'] != $_SESSION['user_id']) { header('Location: /?c=course&a=instructorIndex'); exit; }
        if ($courseModel->delete($id)) { $_SESSION['success'] = 'Course deleted'; } else { $_SESSION['error'] = 'Delete failed'; }
        header('Location: /?c=course&a=instructorIndex'); exit;
    }

    // ADMIN: manage pending courses
    public function manageCourses() {
        if (!class_exists('Auth') || !Auth::isLoggedIn() || !Auth::hasRole(2)) { header('Location: /auth/login'); exit; }
        require_once __DIR__ . '/../models/Course.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Category.php';
        $courseModel = new Course(); $userModel = new User(); $catModel = new Category();

        $courses = $courseModel->getByStatuses(['pending','rejected']);
        foreach ($courses as &$c) {
            $instr = $userModel->getUserById($c['instructor_id']);
            $c['instructor_name'] = $instr ? ($instr['fullname'] ?? $instr['username']) : 'N/A';
            $cat = $catModel->getById($c['category_id']);
            $c['category_name'] = $cat ? $cat['name'] : 'N/A';
            $labels = ['pending'=>'Chờ','published'=>'Duyệt','rejected'=>'Từ chối','draft'=>'Nháp'];
            $c['status_label'] = $labels[$c['status']] ?? $c['status'];
        }
        unset($c);
        require __DIR__ . '/../views/admin/courses/manage.php';
    }

    public function courseDetail() {
        if (!class_exists('Auth') || !Auth::isLoggedIn() || !Auth::hasRole(2)) { header('Location: /auth/login'); exit; }
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; if (!$id) { header('Location: /admin/courses'); exit; }
        require_once __DIR__ . '/../models/Course.php'; require_once __DIR__ . '/../models/User.php'; require_once __DIR__ . '/../models/Category.php';
        $courseModel = new Course(); $userModel = new User(); $catModel = new Category();
        $course = $courseModel->getById($id);
        if ($course) {
            $instr = $userModel->getUserById($course['instructor_id']);
            $course['instructor_name'] = $instr ? ($instr['fullname'] ?? $instr['username']) : 'N/A';
            $cat = $catModel->getById($course['category_id']);
            $course['category_name'] = $cat ? $cat['name'] : 'N/A';
            $labels = ['pending'=>'Chờ','published'=>'Duyệt','rejected'=>'Từ chối','draft'=>'Nháp'];
            $course['status_label'] = $labels[$course['status']] ?? $course['status'];
        }
        require __DIR__ . '/../views/admin/courses/detail.php';
    }

    public function approveCourse() {
        if (!class_exists('Auth') || !Auth::isLoggedIn() || !Auth::hasRole(2)) { http_response_code(403); echo 'Unauthorized'; exit; }
        $id = $_POST['course_id'] ?? $_GET['id'] ?? 0; if (!$id) { http_response_code(400); echo 'Bad Request'; return; }
        require_once __DIR__ . '/../models/Course.php'; $course = new Course();
        if ($course->setStatus($id, 'published')) { header('Location: /admin/courses?success=approved'); exit; }
        echo 'Failed to approve';
    }

    public function rejectCourse() {
        if (!class_exists('Auth') || !Auth::isLoggedIn() || !Auth::hasRole(2)) { http_response_code(403); echo 'Unauthorized'; exit; }
        $id = $_POST['course_id'] ?? $_GET['id'] ?? 0; if (!$id) { http_response_code(400); echo 'Bad Request'; return; }
        require_once __DIR__ . '/../models/Course.php'; $course = new Course();
        if ($course->setStatus($id, 'rejected')) { header('Location: /admin/courses?success=rejected'); exit; }
        echo 'Failed to reject';
    }
}

?>
