<?php
class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        if (!empty($_GET['r'])) {
            $route = explode('/', $_GET['r']);
            
            // Format: CourseController
            $this->controller = ucfirst($route[0]) . 'Controller';
            
            if (isset($route[1])) {
                $this->method = $route[1];
            }
            
            if (isset($route[2])) {
                $this->params = array_slice($route, 2);
            }
        }
        
        $this->render();
    }

    private function render() {
        $controllerPath = __DIR__ . '/../controllers/' . $this->controller . '.php';
        
        if (!file_exists($controllerPath)) {
            die("Controller không tồn tại: " . htmlspecialchars($this->controller));
        }
        
        require_once $controllerPath;
        
        $controller = new $this->controller();
        
        if (!method_exists($controller, $this->method)) {
            die("Method không tồn tại: " . htmlspecialchars($this->method));
        }
        
        call_user_func_array([$controller, $this->method], $this->params);
    }
}
?>