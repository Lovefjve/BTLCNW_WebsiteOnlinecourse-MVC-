<?php
// index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Get controller and action
$controller = $_GET['c'] ?? 'instructor';
$action = $_GET['a'] ?? 'course';

// Simple routing
if ($controller === 'instructor') {
    // Include controller
    require_once 'controllers/InstructorController.php';
    
    // Create instance
    $instructorController = new InstructorController();
    
    // Map actions to methods
    $actionMap = [
        'course' => 'course',
        'createCourse' => 'createCourse',
        'storeCourse' => 'storeCourse',
        'edit' => 'editCourse',
        'update' => 'updateCourse',
        'delete' => 'deleteCourse'
    ];
    
    // Get method name
    $method = $actionMap[$action] ?? 'course';
    
    // Check if method exists
    if (method_exists($instructorController, $method)) {
        $instructorController->$method();
    } else {
        // Default to courses
        $instructorController->courses();
    }
} else {
    // Default to instructor courses
    header('Location: ?c=instructor&a=course');
    exit;
}
?>