<?php
// Enable output buffering to avoid "headers already sent" issues from accidental whitespace
ob_start();
// Khởi tạo các hằng số
define('BASE_URL', 'http://localhost/onlinecourse');

// Autoload các class
spl_autoload_register(function ($class_name) {
    $directories = ['core/', 'controllers/', 'models/'];
    
    foreach ($directories as $dir) {
        $file = $dir . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Xử lý routing đơn giản
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Loại bỏ base url nếu có
$request = str_replace('/onlinecourse', '', $request);

// Loại bỏ query string từ request path
$request = strtok($request, '?');

// Routing
switch ($request) {
    case '/':
    case '':
        $controller = new HomeController();
        $controller->index();
        break;
    case '/auth/register':
        $controller = new AuthController();
        $controller->register();
        break;
    case '/auth/postRegister':
        $controller = new AuthController();
        $controller->postRegister();
        break;
    case '/auth/login':
        $controller = new AuthController();
        $controller->login();
        break;
    case '/auth/postLogin':
        $controller = new AuthController();
        $controller->postLogin();
        break;
    case '/student/dashboard':
        $controller = new StudentController();
        $controller->dashboard();
        break;
    case '/instructor/dashboard':
        $controller = new InstructorController();
        $controller->dashboard();
        break;
    case '/admin/dashboard':
        $controller = new AdminController();
        $controller->dashboard();
        break;
    case '/admin/users':
        $controller = new AdminController();
        $controller->manageUsers();
        break;
    case '/admin/createUser':
        $controller = new AdminController();
        $controller->createUser();
        break;
    case '/admin/editUser':
        $controller = new AdminController();
        $controller->editUser();
        break;
    case '/admin/deleteUser':
        $controller = new AdminController();
        $controller->deleteUser();
        break;
    case '/auth/logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
?>