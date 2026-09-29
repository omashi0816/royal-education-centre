<?php
/**
 * Royal Education Center Management System
 * Base Controller Class
 */

abstract class Controller {
    protected $view;
    
    public function __construct() {
        $this->view = new View();
    }
    
    // Load view with data
    protected function view($viewName, $data = []) {
        return $this->view->render($viewName, $data);
    }
    
    // Load layout view
    protected function layout($layoutName, $viewName, $data = []) {
        return $this->view->renderLayout($layoutName, $viewName, $data);
    }
    
    // Redirect to URL
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    // Redirect back
    protected function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        header("Location: $referer");
        exit;
    }
    
    // Send JSON response
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    // Send success JSON
    protected function success($message = 'Success', $data = null) {
        $response = ['success' => true, 'message' => $message];
        if ($data !== null) {
            $response['data'] = $data;
        }
        return $this->json($response);
    }
    
    // Send error JSON
    protected function error($message = 'Error', $statusCode = 400, $errors = null) {
        $response = ['success' => false, 'message' => $message];
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        return $this->json($response, $statusCode);
    }
    
    // Check if user is logged in
    protected function requireAuth() {
        if (!isLoggedIn()) {
            if (isAjax()) {
                return $this->error('Authentication required', 401);
            }
            setFlash('error', 'Please login to continue');
            $this->redirect(BASE_URL . '/login');
        }
    }
    
    // Check if user has specific role
    protected function requireRole($roles) {
        $this->requireAuth();
        
        $roles = is_array($roles) ? $roles : [$roles];
        $currentRole = currentUserRole();
        
        if (!in_array($currentRole, $roles)) {
            if (isAjax()) {
                return $this->error('Access denied', 403);
            }
            setFlash('error', 'Access denied');
            $this->redirect(BASE_URL . '/dashboard');
        }
    }
    
    // Check if user has specific permission
    protected function requirePermission($permission) {
        $this->requireAuth();
        
        if (!hasPermission($permission)) {
            if (isAjax()) {
                return $this->error('Permission denied', 403);
            }
            setFlash('error', 'Permission denied');
            $this->redirect(BASE_URL . '/dashboard');
        }
    }
    
    // Check if user has any of the specified roles
    protected function hasAnyRole($roles) {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array(currentUserRole(), $roles);
    }
    
    // Get POST data
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    // Get GET data
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    // Get request data (POST or GET)
    protected function input($key = null, $default = null) {
        if (isPost()) {
            return $this->post($key, $default);
        }
        return $this->get($key, $default);
    }
    
    // Validate request data
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field][] = "The $field field is required";
                }
                
                if (strpos($r, 'min:') === 0 && isset($data[$field])) {
                    $min = substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = "The $field must be at least $min characters";
                    }
                }
                
                if (strpos($r, 'max:') === 0 && isset($data[$field])) {
                    $max = substr($r, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field][] = "The $field must not exceed $max characters";
                    }
                }
                
                if ($r === 'email' && isset($data[$field]) && !validateEmail($data[$field])) {
                    $errors[$field][] = "The $field must be a valid email";
                }
                
                if ($r === 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field][] = "The $field must be numeric";
                }
            }
        }
        
        return $errors;
    }
    
    // Set flash message
    protected function flash($type, $message) {
        setFlash($type, $message);
    }
    
    // Get file from request
    protected function file($key) {
        return $_FILES[$key] ?? null;
    }
}
