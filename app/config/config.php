<?php
/**
 * Royal Education Center Management System
 * Configuration File
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
define('ENVIRONMENT', 'development'); // 'production' or 'development'

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../../logs/error.log');
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'royal_edu_center');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Constants
define('APP_NAME', 'Royal Education Center');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/royal-edu-center/public');
define('ASSETS_URL', BASE_URL . '/assets');

// File Paths
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Upload Directories
define('UPLOAD_ASSIGNMENTS', UPLOAD_PATH . '/assignments');
define('UPLOAD_NOTES', UPLOAD_PATH . '/notes');
define('UPLOAD_VIDEOS', UPLOAD_PATH . '/videos');
define('UPLOAD_RECEIPTS', UPLOAD_PATH . '/receipts');
define('UPLOAD_BACKUPS', UPLOAD_PATH . '/backups');
define('UPLOAD_PROFILES', UPLOAD_PATH . '/profiles');

// Security Settings
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 15); // minutes
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 6);

// Twilio SMS settings. Set these values before enabling production OTP SMS.
define('TWILIO_ACCOUNT_SID', '');
define('TWILIO_AUTH_TOKEN', '');
define('TWILIO_FROM_NUMBER', '');
// Keep demo mode enabled only for local development without Twilio credentials.
define('OTP_DEMO_MODE', ENVIRONMENT === 'development');
define('OTP_EXPIRY_SECONDS', 300);

// Pagination
define('ITEMS_PER_PAGE', 20);
define('ITEMS_PER_PAGE_SMALL', 10);
define('ITEMS_PER_PAGE_LARGE', 50);

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'F j, Y');
define('DISPLAY_DATETIME_FORMAT', 'F j, Y g:i A');

// File Upload Limits
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'txt']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'avi', 'mov', 'wmv']);

// Role IDs
define('ROLE_ADMIN', 1);
define('ROLE_MANAGER', 2);
define('ROLE_TEACHER', 3);
define('ROLE_STUDENT', 4);
define('ROLE_RECEPTIONIST', 5);
define('ROLE_CASHIER', 6);

// Timezone
date_default_timezone_set('Asia/Colombo');

// Load helper functions
require_once APP_PATH . '/helpers/helpers.php';

// Initialize session security
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
