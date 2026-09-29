<?php
/**
 * Royal Education Center Management System
 * Auth Controller
 */

require_once APP_PATH . '/models/Auth.php';
require_once APP_PATH . '/models/Role.php';
require_once APP_PATH . '/models/Student.php';
require_once APP_PATH . '/models/Teacher.php';

class AuthController extends Controller {
    private $authModel;
    private $roleModel;
    
    public function __construct() {
        parent::__construct();
        $this->authModel = new Auth();
        $this->roleModel = new Role();
    }
    
    // Login page
    public function login() {
        // Redirect if already logged in
        if (isLoggedIn()) {
            $this->redirect(BASE_URL . '/dashboard');
        }
        
        if (isPost()) {
            $this->handleLogin();
        } else {
            $this->view('auth.login');
        }
    }
    
    // Handle login form submission
    private function handleLogin() {
        $username = $this->post('username');
        $password = $this->post('password');
        $csrfToken = $this->post('csrf_token');
        
        // Validate CSRF token
        if (!verifyCsrfToken($csrfToken)) {
            setFlash('error', 'Invalid request. Please try again.');
            $this->redirect(BASE_URL . '/login');
        }
        
        // Validate inputs
        if (empty($username) || empty($password)) {
            setFlash('error', 'Please enter both username and password.');
            $this->redirect(BASE_URL . '/login');
        }
        
        // Check for brute force
        if (Security::checkBruteForce(getClientIp())) {
            setFlash('error', 'Too many failed attempts. Please try again later.');
            $this->redirect(BASE_URL . '/login');
        }
        
        // Verify credentials
        $result = $this->authModel->verifyCredentials($username, $password);
        
        if ($result['success']) {
            // Set session
            $user = $result['user'];
            Session::setUser($user);
            
            // Log activity
            logActivity($user['id'], 'login', 'auth', 'User logged in');
            
            // Redirect based on normalized role
            $roleName = strtolower(trim((string) ($user['role_name'] ?? '')));
            $redirectMap = [
                'admin' => '/admin/dashboard',
                'manager' => '/manager/dashboard',
                'teacher' => '/teacher/dashboard',
                'student' => '/student/dashboard',
                'receptionist' => '/receptionist/dashboard',
                'cashier' => '/cashier/dashboard'
            ];

            $redirectUrl = $redirectMap[$roleName] ?? '/dashboard';

            setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
            $this->redirect(BASE_URL . $redirectUrl);
        } else {
            // Log failed attempt
            logActivity(null, 'login_failed', 'auth', "Failed login attempt for: $username", getClientIp());
            setFlash('error', $result['message']);
            $this->redirect(BASE_URL . '/login');
        }
    }
    
    // Register page
    public function register() {
        if (isLoggedIn()) {
            $this->redirect(BASE_URL . '/dashboard');
        }

        if (isPost()) {
            $this->handleRegistration();
        } else {
            $this->view('auth.register');
        }
    }

    private function handleRegistration() {
        $fullName = trim($this->post('full_name', ''));
        $email = trim($this->post('email', ''));
        $phone = trim($this->post('phone', ''));
        $password = $this->post('password', '');
        $confirmPassword = $this->post('confirm_password', '');
        $csrfToken = $this->post('csrf_token');

        if (!verifyCsrfToken($csrfToken)) {
            setFlash('error', 'Invalid request. Please try again.');
            $this->redirect(BASE_URL . '/register');
        }

        if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
            setFlash('error', 'Please fill in all required fields.');
            $this->redirect(BASE_URL . '/register');
        }

        if (!Security::validateEmail($email)) {
            setFlash('error', 'Please enter a valid email address.');
            $this->redirect(BASE_URL . '/register');
        }

        if ($password !== $confirmPassword) {
            setFlash('error', 'Passwords do not match.');
            $this->redirect(BASE_URL . '/register');
        }

        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            setFlash('error', 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.');
            $this->redirect(BASE_URL . '/register');
        }

        $role = $this->roleModel->firstWhere('name', 'student');
        if (!$role) {
            setFlash('error', 'Student registration is currently unavailable. Please contact the administrator.');
            $this->redirect(BASE_URL . '/register');
        }

        if ($this->authModel->emailExists($email)) {
            setFlash('error', 'An account with this email already exists.');
            $this->redirect(BASE_URL . '/register');
        }

        $baseUsername = strtolower(preg_replace('/[^a-z0-9]+/', '', explode('@', $email)[0]));
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }

        $username = $baseUsername;
        $counter = 1;
        while ($this->authModel->usernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $userId = $this->authModel->createUserWithProfile([
            'role_id' => $role['id'],
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $fullName,
            'phone' => $phone,
            'status' => 'active'
        ], 'student');

        if (!$userId) {
            setFlash('error', 'Unable to create your account right now. Please try again.');
            $this->redirect(BASE_URL . '/register');
        }

        logActivity($userId, 'create', 'users', 'Registered new user: ' . $fullName);
        setFlash('success', 'Account created successfully. You can now log in with your email/username and password.');
        $this->redirect(BASE_URL . '/login');
    }

    // Logout
    public function logout() {
        if (isLoggedIn()) {
            $userId = currentUserId();
            logActivity($userId, 'logout', 'auth', 'User logged out');
            Session::destroy();
        }
        
        setFlash('info', 'You have been logged out successfully.');
        $this->redirect(BASE_URL . '/login');
    }
    
    // Forgot password (placeholder for future implementation)
    public function forgotPassword() {
        if (isPost()) {
            $email = $this->post('email');
            // TODO: Implement password reset functionality
            setFlash('info', 'Password reset functionality will be implemented soon.');
            $this->redirect(BASE_URL . '/login');
        }
        $this->view('auth.forgot-password');
    }
    
    // Reset password (placeholder for future implementation)
    public function resetPassword() {
        if (isPost()) {
            // TODO: Implement password reset functionality
            setFlash('info', 'Password reset functionality will be implemented soon.');
            $this->redirect(BASE_URL . '/login');
        }
        $this->view('auth.reset-password');
    }
}
