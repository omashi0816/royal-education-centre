<?php
/**
 * Royal Education Center Management System
 * Security Helper Class
 */

class Security {
    
    // Hash password
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }
    
    // Verify password
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Rehash password if needed
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 10]);
    }
    
    // Generate secure random token
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    // Sanitize input
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    // Escape SQL string (use prepared statements instead)
    public static function escapeString($string) {
        return addslashes($string);
    }
    
    // Validate email
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validate URL
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    // Validate integer
    public static function validateInt($value, $min = null, $max = null) {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            return false;
        }
        if ($min !== null && $value < $min) {
            return false;
        }
        if ($max !== null && $value > $max) {
            return false;
        }
        return true;
    }
    
    // Validate float
    public static function validateFloat($value) {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }
    
    // CSRF Token generation
    public static function generateCsrfToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = self::generateToken(32);
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    // CSRF Token verification
    public static function verifyCsrfToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    // Get CSRF token for form
    public static function csrfField() {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }
    
    // Check login attempts
    public static function checkLoginAttempts($username) {
        $db = Database::getInstance();
        
        $sql = "SELECT login_attempts, locked_until FROM users WHERE username = ?";
        $user = $db->query($sql)->bind(1, $username)->fetch();
        
        if (!$user) {
            return ['allowed' => true];
        }
        
        // Check if account is locked
        if ($user['locked_until'] && new DateTime($user['locked_until']) > new DateTime()) {
            $remaining = (new DateTime($user['locked_until']))->diff(new DateTime())->i;
            return ['allowed' => false, 'locked' => true, 'remaining_minutes' => $remaining];
        }
        
        // Check if max attempts reached
        if ($user['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', strtotime('+' . LOGIN_LOCKOUT_TIME . ' minutes'));
            $sql = "UPDATE users SET locked_until = ? WHERE username = ?";
            $db->query($sql)->bind(1, $lockUntil)->bind(2, $username)->execute();
            
            return ['allowed' => false, 'locked' => true, 'remaining_minutes' => LOGIN_LOCKOUT_TIME];
        }
        
        return ['allowed' => true, 'attempts' => $user['login_attempts']];
    }
    
    // Record failed login attempt
    public static function recordFailedAttempt($username) {
        $db = Database::getInstance();
        $sql = "UPDATE users SET login_attempts = login_attempts + 1 WHERE username = ?";
        $db->query($sql)->bind(1, $username)->execute();
    }
    
    // Reset login attempts on successful login
    public static function resetLoginAttempts($username) {
        $db = Database::getInstance();
        $sql = "UPDATE users SET login_attempts = 0, locked_until = NULL, last_login = CURRENT_TIMESTAMP WHERE username = ?";
        $db->query($sql)->bind(1, $username)->execute();
    }
    
    // Validate file upload
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = null) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }
        
        $maxSize = $maxSize ?? MAX_FILE_SIZE;
        
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'File size exceeds maximum limit'];
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            return ['valid' => false, 'error' => $errors[$file['error']] ?? 'Unknown upload error'];
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }
        
        // Check file type using finfo (more secure)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowedMimes = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'application/pdf' => ['pdf'],
            'application/msword' => ['doc'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
            'text/plain' => ['txt'],
            'video/mp4' => ['mp4'],
            'video/avi' => ['avi'],
            'video/quicktime' => ['mov']
        ];
        
        $validMime = false;
        foreach ($allowedMimes as $allowedMime => $extensions) {
            if ($mime === $allowedMime && in_array($extension, $extensions)) {
                $validMime = true;
                break;
            }
        }
        
        if (!$validMime && !empty($allowedTypes)) {
            return ['valid' => false, 'error' => 'File content does not match extension'];
        }
        
        return ['valid' => true];
    }
    
    // XSS prevention
    public static function xssClean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'xssClean'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    // SQL Injection prevention (use prepared statements)
    public static function preventSqlInjection($data) {
        // This is a fallback - always use prepared statements
        return self::sanitizeInput($data);
    }
    
    // Check for brute force patterns
    public static function checkBruteForce($ip) {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) FROM activity_logs 
                WHERE ip_address = ? AND action = 'login_failed' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $count = $db->query($sql)->bind(1, $ip)->fetchColumn();
        
        return $count > 20; // More than 20 failed attempts in 1 hour
    }
}
