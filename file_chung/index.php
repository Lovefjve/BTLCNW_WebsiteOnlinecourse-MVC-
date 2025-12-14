<?php
// file_chung/index.php - canonical front controller
// This merged router preserves the project's REQUEST_URI exact routes,
// supports pretty URLs (?url=...) used by Huy, and the query-style
// ?c=controller&a=action used by Duc. Put this file into the project root
// to replace or to use as the canonical router.

// Basic dev-friendly settings
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

// Define BASE_URL for views and controllers if not already defined.
if (!defined('BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    // Normalize root to empty string
    if ($base === '/' || $base === '.') $base = '';
    define('BASE_URL', $base);
}

// Autoloader similar to project to resolve core/controllers/models
spl_autoload_register(function ($class_name) {
    // Autoload from this project's folders. Previous code used ".." and missed the
    // actual controllers/models directory when index.php is in the project root.
    $directories = [__DIR__ . '/core/', __DIR__ . '/controllers/', __DIR__ . '/models/'];
    foreach ($directories as $dir) {
        $file = $dir . $class_name . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});

// --- Part A: exact path switch (project's existing routes) ---
$request = $_SERVER['REQUEST_URI'] ?? '/';
$request = str_replace('/onlinecourse', '', $request); // if deployed under /onlinecourse
$requestPath = strtok($request, '?');

switch ($requestPath) {
    case '/':
    case '':
        if (class_exists('HomeController')) { (new HomeController())->index(); exit; }
        break;
    case '/auth/register': if (class_exists('AuthController')) { (new AuthController())->register(); exit; } break;
    case '/auth/postRegister': if (class_exists('AuthController')) { (new AuthController())->postRegister(); exit; } break;
    case '/auth/login': if (class_exists('AuthController')) { (new AuthController())->login(); exit; } break;
    case '/auth/postLogin': if (class_exists('AuthController')) { (new AuthController())->postLogin(); exit; } break;
    case '/student/dashboard': if (class_exists('StudentController')) { (new StudentController())->dashboard(); exit; } break;
    case '/instructor/dashboard': if (class_exists('InstructorController')) { (new InstructorController())->dashboard(); exit; } break;
    case '/admin/dashboard': if (class_exists('AdminController')) { (new AdminController())->dashboard(); exit; } break;
    case '/admin/users': if (class_exists('AdminController')) { (new AdminController())->manageUsers(); exit; } break;
    case '/admin/categories': if (class_exists('CategoryController')) { (new CategoryController())->manageCategories(); exit; } break;
    case '/admin/courses': if (class_exists('CourseController')) { (new CourseController())->manageCourses(); exit; } break;
    case '/admin/courses/detail': if (class_exists('CourseController')) { (new CourseController())->courseDetail(); exit; } break;
    case '/admin/courses/approve': if (class_exists('CourseController')) { (new CourseController())->approveCourse(); exit; } break;
    case '/admin/courses/reject': if (class_exists('CourseController')) { (new CourseController())->rejectCourse(); exit; } break;
    case '/admin/categories/create': if (class_exists('CategoryController')) { (new CategoryController())->createCategory(); exit; } break;
    case '/admin/categories/edit': if (class_exists('CategoryController')) { (new CategoryController())->editCategory(); exit; } break;
    case '/admin/categories/delete': if (class_exists('CategoryController')) { (new CategoryController())->deleteCategory(); exit; } break;
    case '/admin/createUser': if (class_exists('AdminController')) { (new AdminController())->createUser(); exit; } break;
    case '/admin/editUser': if (class_exists('AdminController')) { (new AdminController())->editUser(); exit; } break;
    case '/admin/deleteUser': if (class_exists('AdminController')) { (new AdminController())->deleteUser(); exit; } break;
    case '/auth/logout': if (class_exists('AuthController')) { (new AuthController())->logout(); exit; } break;
}

// --- Part B: pretty URL router (?url=controller/method/params) ---
if (isset($_GET['url'])) {
    $url = rtrim($_GET['url'], '/');
    $segments = explode('/', $url);

    // common: course routes
    if ( $segments[0] === 'course') {
        if (class_exists('CourseController')) {
            $ctrl = new CourseController();
            $cmd = $segments[1] ?? 'index';
            if ($cmd === 'index' || $cmd === '') { $ctrl->index(); exit; }
            if ($cmd === 'detail' && isset($segments[2])) { $ctrl->detail($segments[2]); exit; }
            if ($cmd === 'enroll' && isset($segments[2])) { $ctrl->enroll($segments[2]); exit; }
            $ctrl->index(); exit;
        }
    }

    // generic /controller/method/params
    $ctrlName = ucfirst($segments[0]) . 'Controller';
    if (class_exists($ctrlName)) {
        $instance = new $ctrlName();
        $method = $segments[1] ?? 'index';
        $params = array_slice($segments, 2);
        if (method_exists($instance, $method)) { call_user_func_array([$instance, $method], $params); exit; }
        if (method_exists($instance, 'index')) { $instance->index(); exit; }
    }
}

// --- Part C: query-style router (?c=controller&a=action) ---
$c = $_GET['c'] ?? null;
$a = $_GET['a'] ?? null;
if ($c) {
    $class = ucfirst($c) . 'Controller';
    if (class_exists($class)) {
        $inst = new $class();
        $method = $a ?? 'index';
        if (method_exists($inst, $method)) { $inst->$method(); exit; }
    }
}

// nothing matched: 404
http_response_code(404);
echo '404 Not Found';
exit;
