<?php
/**
 * Royal Education Center Management System
 * Front Controller / Router
 */

// Load configuration
require_once __DIR__ . '/../app/config/config.php';

// Load core classes
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Session.php';
require_once APP_PATH . '/core/Security.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/View.php';
require_once APP_PATH . '/core/Controller.php';

// Set error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (ENVIRONMENT === 'development') {
        echo "<div style='background:#f8d7da;color:#721c24;padding:15px;margin:10px;border:1px solid #f5c6cb;border-radius:4px;'>";
        echo "<strong>Error:</strong> [$errno] $errstr<br>";
        echo "File: $errfile on line $errline";
        echo "</div>";
    } else {
        error_log("Error: [$errno] $errstr in $errfile on line $errline");
    }
});

// Set exception handler
set_exception_handler(function($exception) {
    if (ENVIRONMENT === 'development') {
        echo "<div style='background:#f8d7da;color:#721c24;padding:15px;margin:10px;border:1px solid #f5c6cb;border-radius:4px;'>";
        echo "<strong>Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "File: " . $exception->getFile() . " on line " . $exception->getLine();
        echo "</div>";
    } else {
        error_log("Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    }
});

// Check session timeout
if (isLoggedIn()) {
    if (!Session::checkTimeout()) {
        Session::destroy();
        redirect(BASE_URL . '/login');
    }
}

// Dynamically compute base path from SCRIPT_NAME
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

// Parse request URI (strip query string and base path)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($scriptDir && strpos($requestUri, $scriptDir) === 0) {
    $requestUri = substr($requestUri, strlen($scriptDir));
}
$requestUri = rtrim($requestUri, '/');
if ($requestUri === '' || $requestUri === '/index.php') {
    $requestUri = '/';
}

// Parse URL segments
$segments = explode('/', trim($requestUri, '/'));
$segments = array_filter($segments, fn($s) => $s !== '');
$segments = array_values($segments);

$controller = $segments[0] ?? '';
$action = $segments[1] ?? 'index';
$params = array_slice($segments, 2);

// Default routes
$routes = [
    '' => ['controller' => 'Auth', 'action' => 'login'],
    'login' => ['controller' => 'Auth', 'action' => 'login'],
    'register' => ['controller' => 'Auth', 'action' => 'register'],
    'logout' => ['controller' => 'Auth', 'action' => 'logout'],
    'dashboard' => ['controller' => 'Dashboard', 'action' => 'index'],
    'forgot-password' => ['controller' => 'Auth', 'action' => 'forgotPassword'],
    'reset-password' => ['controller' => 'Auth', 'action' => 'resetPassword'],
];

// Check for predefined routes (match controller segment)
$routeKey = $controller;
if (isset($routes[$routeKey]) && count($segments) <= 1) {
    $controller = $routes[$routeKey]['controller'];
    $action = $routes[$routeKey]['action'];
    $params = [];
} elseif (isset($routes[$routeKey])) {
    $controller = $routes[$routeKey]['controller'];
    // keep action from URL
}

// Map controller names to files
$controllerMap = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'admin' => 'AdminController',
    'manager' => 'ManagerController',
    'teacher' => 'TeacherController',
    'student' => 'StudentController',
    'receptionist' => 'ReceptionistController',
    'cashier' => 'CashierController',
];

$controllerKey = strtolower($controller);
$controllerClass = $controllerMap[$controllerKey] ?? ucfirst($controller) . 'Controller';

// Convert hyphenated action to camelCase (e.g., edit-course -> editCourse)
if (strpos($action, '-') !== false) {
    $action = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $action))));
}

// Load controller file
$controllerFile = APP_PATH . '/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    if (ENVIRONMENT === 'development') {
        die("Controller not found: $controllerClass (file: $controllerFile)");
    }
    die('404 - Page not found');
}

require_once $controllerFile;

// Check if controller class exists
if (!class_exists($controllerClass)) {
    http_response_code(404);
    die("Controller class not found: $controllerClass");
}

// Instantiate controller
$controllerInstance = new $controllerClass();

// Check if action exists
if (!method_exists($controllerInstance, $action)) {
    http_response_code(404);
    if (ENVIRONMENT === 'development') {
        die("Action not found: $action in $controllerClass");
    }
    die('404 - Page not found');
}

// Call controller action
try {
    call_user_func_array([$controllerInstance, $action], $params);
} catch (Exception $e) {
    if (ENVIRONMENT === 'development') {
        die("Error: " . $e->getMessage());
    }
    error_log("Error: " . $e->getMessage());
    die('An error occurred. Please try again later.');
}
