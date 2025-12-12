<?php
class CourseController extends Controller {

    private $courseModel;
    private $categoryModel;
    private $enrollmentModel;

    public function __construct() {
        $this->courseModel = new Course();
        $this->categoryModel = new Category();
        $this->enrollmentModel = new Enrollment();
    }

    // 1. Danh sách khóa học
    public function index() {
        $keyword = $_GET['keyword'] ?? '';
        $category = $_GET['category'] ?? '';

        $courses = $this->courseModel->getAll($keyword, $category);
        $categories = $this->categoryModel->getAll();

        $this->view('courses/index', [
            'courses' => $courses,
            'categories' => $categories,
            'keyword' => $keyword,
            'category' => $category
        ]);
    }

    // 2. Chi tiết khóa học - ĐÃ THÊM VÀO ĐÂY!!!
    public function detail($id = null) {
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = "Khóa học không tồn tại!";
            $this->redirect('?url=course');
        }

        $course = $this->courseModel->getById($id);
        if (!$course) {
            $_SESSION['error'] = "Không tìm thấy khóa học!";
            $this->redirect('?url=course');
        }

        $totalStudents = $this->courseModel->countEnrollments($id);
        $isEnrolled = false;

        if (isset($_SESSION['user_id']) && $_SESSION['role'] == 0) {
            $isEnrolled = $this->enrollmentModel->isEnrolled($id, $_SESSION['user_id']);
        }

        $this->view('courses/detail', [
            'course' => $course,
            'totalStudents' => $totalStudents,
            'isEnrolled' => $isEnrolled
        ]);
    }

    // 3. Đăng ký khóa học - ĐÃ THÊM VÀO ĐÂY!!!
    public function enroll($id = null) {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
            $_SESSION['error'] = "Bạn cần đăng nhập tài khoản học viên!";
            $this->redirect('?url=course');
        }

        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = "Khóa học không hợp lệ!";
            $this->redirect('?url=course');
        }

        if ($this->enrollmentModel->enroll($id, $_SESSION['user_id'])) {
            $_SESSION['success'] = "Đăng ký khóa học thành công!";
        } else {
            $_SESSION['error'] = "Bạn đã đăng ký khóa học này rồi!";
        }

        $this->redirect("?url=course/detail/$id");
    }
}
?>