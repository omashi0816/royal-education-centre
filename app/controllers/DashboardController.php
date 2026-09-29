<?php
/**
 * Royal Education Center Management System
 * Dashboard Controller
 */

class DashboardController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }
    
    // Main dashboard - redirects based on role
    public function index() {
        $role = currentUserRole();

        if (empty($role) && currentUserId()) {
            $db = Database::getInstance();
            $sql = "SELECT r.name AS role_name
                    FROM users u
                    INNER JOIN roles r ON u.role_id = r.id
                    WHERE u.id = ?";
            $role = $db->query($sql)->bind(1, currentUserId())->fetchColumn();
            $role = strtolower(trim((string) $role));
        }

        $redirectMap = [
            'admin' => '/admin/dashboard',
            'manager' => '/manager/dashboard',
            'teacher' => '/teacher/dashboard',
            'student' => '/student/dashboard',
            'receptionist' => '/receptionist/dashboard',
            'cashier' => '/cashier/dashboard'
        ];

        $redirectUrl = $redirectMap[$role] ?? '/dashboard';
        $this->redirect(BASE_URL . $redirectUrl);
    }
    
    // User profile
    public function profile() {
        $this->requireAuth();
        
        $db = Database::getInstance();
        $userId = currentUserId();
        
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.id = ?";
        $user = $db->query($sql)->bind(1, $userId)->fetch();
        
        if (isPost()) {
            $this->updateProfile($userId);
        }
        
        $this->layout('main', 'dashboard.profile', [
            'pageTitle' => 'My Profile',
            'user' => $user
        ]);
    }
    
    // Update user profile
    private function updateProfile($userId) {
        $fullName = $this->post('full_name');
        $phone = $this->post('phone');
        $email = $this->post('email');
        $currentPassword = $this->post('current_password');
        $newPassword = $this->post('new_password');
        $csrfToken = $this->post('csrf_token');
        
        // Validate CSRF
        if (!verifyCsrfToken($csrfToken)) {
            setFlash('error', 'Invalid request');
            $this->back();
        }
        
        // Validate required fields
        if (empty($fullName) || empty($email)) {
            setFlash('error', 'Name and email are required');
            $this->back();
        }
        
        $db = Database::getInstance();
        
        // Update basic info
        $sql = "UPDATE users SET full_name = ?, phone = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $db->query($sql)->bind(1, $fullName)->bind(2, $phone)->bind(3, $email)->bind(4, $userId)->execute();
        
        // Update password if provided
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                setFlash('error', 'Current password is required to change password');
                $this->back();
            }
            
            // Verify current password
            $sql = "SELECT password_hash FROM users WHERE id = ?";
            $currentHash = $db->query($sql)->bind(1, $userId)->fetchColumn();
            
            if (!Security::verifyPassword($currentPassword, $currentHash)) {
                setFlash('error', 'Current password is incorrect');
                $this->back();
            }
            
            if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
                setFlash('error', 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters');
                $this->back();
            }
            
            $newHash = Security::hashPassword($newPassword);
            $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
            $db->query($sql)->bind(1, $newHash)->bind(2, $userId)->execute();
        }
        
        logActivity($userId, 'update', 'profile', 'User updated profile');
        setFlash('success', 'Profile updated successfully');
        $this->back();
    }
}
