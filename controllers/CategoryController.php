<?php
require_once 'core/Auth.php';
require_once 'models/Category.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    // Danh sách danh mục (Admin)
    public function manageCategories() {
        if (!Auth::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        if (!Auth::hasRole(2)) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $user = Auth::getUser();
        $categories = $this->categoryModel->getAll();
        require_once 'views/admin/categories/manage.php';
    }

    // Tạo danh mục (GET/POST)
    public function createCategory() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

    $errors = [];
    $data = ['name' => '', 'description' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['name'] = trim($_POST['name'] ?? '');
            $data['description'] = trim($_POST['description'] ?? '');

            if (empty($data['name'])) {
                $errors['name'] = 'Vui lòng nhập tên danh mục';
            }

            // Check uniqueness by name only
            $existsByName = $this->categoryModel->findByName($data['name']);
            if ($existsByName) {
                $errors['name'] = 'Tên danh mục đã tồn tại';
            }

            if (empty($errors)) {
                if ($this->categoryModel->create($data)) {
                    header('Location: ' . BASE_URL . '/admin/categories?success=created');
                    exit;
                } else {
                    $errors['db'] = 'Lỗi khi lưu vào cơ sở dữ liệu';
                }
            }
        }

        $user = Auth::getUser();
        require_once 'views/admin/categories/create.php';
    }

    // Chỉnh sửa danh mục (GET/POST)
    public function editCategory() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '/admin/categories');
            exit;
        }

        $category = $this->categoryModel->getById($id);
        if (!$category) {
            header('Location: ' . BASE_URL . '/admin/categories');
            exit;
        }

        $errors = [];
        $data = $category;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['name'] = trim($_POST['name'] ?? '');
            $data['description'] = trim($_POST['description'] ?? '');

            if (empty($data['name'])) {
                $errors['name'] = 'Vui lòng nhập tên danh mục';
            }

            $existsByName = $this->categoryModel->findByName($data['name']);
            if ($existsByName && $existsByName['id'] != $id) {
                $errors['name'] = 'Tên danh mục đã tồn tại';
            }

            if (empty($errors)) {
                if ($this->categoryModel->update($id, $data)) {
                    header('Location: ' . BASE_URL . '/admin/categories?success=updated');
                    exit;
                } else {
                    $errors['db'] = 'Lỗi khi cập nhật vào cơ sở dữ liệu';
                }
            }
        }

        $user = Auth::getUser();
        require_once 'views/admin/categories/edit.php';
    }

    // Xóa danh mục (POST)
    public function deleteCategory() {
        if (!Auth::isLoggedIn() || !Auth::hasRole(2)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $this->categoryModel->delete($id);
                header('Location: ' . BASE_URL . '/admin/categories?success=deleted');
                exit;
            }
        }

        http_response_code(400);
        echo 'Bad Request';
        exit;
    }
}
