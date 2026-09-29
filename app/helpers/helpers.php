<?php
/**
 * Royal Education Center Management System
 * Helper Functions
 */

// Sanitize input data
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate phone number (Sri Lanka format)
function validatePhone($phone) {
    return preg_match('/^(\+94|0)?[0-9]{9,10}$/', $phone);
}

// Generate random string
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Generate unique code with prefix
function generateCode($prefix, $table, $column, $length = 4) {
    $db = Database::getInstance();
    $code = $prefix . str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    
    // Check if code exists, regenerate if needed
    $count = $db->query("SELECT COUNT(*) FROM $table WHERE $column = ?")->bind(1, $code)->fetchColumn();
    if ($count > 0) {
        return generateCode($prefix, $table, $column, $length);
    }
    
    return $code;
}

// Format currency
function formatCurrency($amount) {
    return 'LKR ' . number_format($amount, 2);
}

// Format date
function formatDate($date, $format = null) {
    $format = $format ?? DISPLAY_DATE_FORMAT;
    return date($format, strtotime($date));
}

// Format datetime
function formatDateTime($datetime, $format = null) {
    $format = $format ?? DISPLAY_DATETIME_FORMAT;
    return date($format, strtotime($datetime));
}

// Calculate age from date of birth
function calculateAge($dob) {
    return floor((time() - strtotime($dob)) / 31556926);
}

// Check if date is valid
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Redirect to URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Get current URL
function currentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// Get client IP address
function getClientIp() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Get user agent
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

// Set flash message
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

// Get flash message
function getFlash($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

// Check if flash message exists
function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

// Get all flash messages
function getAllFlash() {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

// CSRF Token
function getCsrfToken() {
    return $_SESSION[CSRF_TOKEN_NAME] ?? '';
}

function verifyCsrfToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Output CSRF hidden input field
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . getCsrfToken() . '">';
}

// Check if request is POST
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Check if request is GET
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

// Check if request is AJAX
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Send JSON response
function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Send error JSON response
function sendError($message, $statusCode = 400) {
    sendJson(['success' => false, 'error' => $message], $statusCode);
}

// Send success JSON response
function sendSuccess($data = null, $message = 'Success') {
    sendJson(['success' => true, 'message' => $message, 'data' => $data]);
}

// Upload file
function uploadFile($file, $destination, $allowedTypes = []) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File size exceeds maximum limit'];
    }
    
    // Check file type
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $destination . '/' . $filename;
    
    // Create directory if not exists
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    }
    
    return ['success' => false, 'error' => 'Failed to upload file'];
}

// Delete file
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// Paginate query results
function paginate($query, $page = 1, $perPage = ITEMS_PER_PAGE) {
    $page = max(1, (int)$page);
    $offset = ($page - 1) * $perPage;
    
    $db = Database::getInstance();
    
    // Get total count
    $countQuery = "SELECT COUNT(*) FROM ($query) as temp";
    $total = $db->query($countQuery)->fetchColumn();
    
    // Get paginated results
    $limitQuery = "$query LIMIT $perPage OFFSET $offset";
    $results = $db->query($limitQuery)->fetchAll();
    
    return [
        'data' => $results,
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'last_page' => ceil($total / $perPage)
    ];
}

// Convert array to HTML attributes
function htmlAttributes($attributes) {
    $html = '';
    foreach ($attributes as $key => $value) {
        if (is_bool($value)) {
            if ($value) {
                $html .= " $key";
            }
        } else {
            $html .= " $key=\"$value\"";
        }
    }
    return $html;
}

// Truncate text
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Debug variable
function debug($var, $die = false) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die) {
        die();
    }
}

// Log activity
function logActivity($userId, $action, $module, $description = null) {
    $db = Database::getInstance();
    $sql = "INSERT INTO activity_logs (user_id, action, module, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $db->query($sql)
       ->bind(1, $userId)
       ->bind(2, $action)
       ->bind(3, $module)
       ->bind(4, $description)
       ->bind(5, getClientIp())
       ->bind(6, getUserAgent())
       ->execute();
}

// Check if user has permission
function hasPermission($permission) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $db = Database::getInstance();
    $sql = "SELECT COUNT(*) FROM role_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            INNER JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = ? AND p.name = ?";
    
    $count = $db->query($sql)
                ->bind(1, $_SESSION['user_id'])
                ->bind(2, $permission)
                ->fetchColumn();
    
    return $count > 0;
}

// Check if user has role
function hasRole($role) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    return $_SESSION['role'] === $role;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Get current user ID
function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user role
function currentUserRole() {
    $role = $_SESSION['role'] ?? $_SESSION['role_name'] ?? null;
    return $role !== null ? strtolower(trim((string) $role)) : null;
}

// Get current user name
function currentUserName() {
    return $_SESSION['user_name'] ?? null;
}

// Get setting value
function getSetting($key, $default = null) {
    $db = Database::getInstance();
    $sql = "SELECT setting_value FROM settings WHERE setting_key = ?";
    $value = $db->query($sql)->bind(1, $key)->fetchColumn();
    return $value !== false ? $value : $default;
}

// Update setting value
function setSetting($key, $value, $group = 'general') {
    $db = Database::getInstance();
    $sql = "INSERT INTO settings (setting_key, setting_value, setting_group) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP";
    
    return $db->query($sql)
               ->bind(1, $key)
               ->bind(2, $value)
               ->bind(3, $group)
               ->bind(4, $value)
               ->execute();
}
