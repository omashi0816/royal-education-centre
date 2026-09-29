<?php
/**
 * Royal Education Center Management System
 * Session Management
 */

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function destroy() {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }
    
    public static function regenerate() {
        session_regenerate_id(true);
    }
    
    public static function setFlash($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }
    
    public static function getFlash($type) {
        if (self::has('flash') && isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
    
    public static function hasFlash($type) {
        return self::has('flash') && isset($_SESSION['flash'][$type]);
    }
    
    // Check session timeout
    public static function checkTimeout() {
        if (self::has('last_activity')) {
            $inactive = time() - self::get('last_activity');
            if ($inactive > SESSION_TIMEOUT) {
                self::destroy();
                return false;
            }
        }
        self::set('last_activity', time());
        return true;
    }
    
    // Set user session data after login
    public static function setUser($user) {
        $roleName = strtolower(trim((string) ($user['role_name'] ?? '')));

        self::set('user_id', $user['id']);
        self::set('role_id', $user['role_id']);
        self::set('role', $roleName);
        self::set('role_name', $roleName);
        self::set('user_name', $user['full_name']);
        self::set('email', $user['email']);
        self::set('logged_in', true);
        self::set('last_activity', time());
    }
    
    // Clear user session (logout)
    public static function clearUser() {
        self::remove('user_id');
        self::remove('role_id');
        self::remove('role');
        self::remove('user_name');
        self::remove('email');
        self::remove('logged_in');
    }
}
