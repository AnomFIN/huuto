<?php
/**
 * Security & Helper Functions
 */

class Security {
    
    /**
     * Generate CSRF token
     */
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Check rate limit for login attempts
     */
    public static function checkRateLimit($ip, $email, $maxAttempts = 5, $timeWindow = 900) {
        $db = Database::getInstance();
        
        // Clean old attempts
        $db->execute(
            "DELETE FROM login_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)",
            [$timeWindow]
        );
        
        // Count recent attempts
        $count = $db->fetch(
            "SELECT COUNT(*) as count FROM login_attempts 
             WHERE ip = ? AND email = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)",
            [$ip, $email, $timeWindow]
        )['count'] ?? 0;
        
        return $count < $maxAttempts;
    }
    
    /**
     * Log login attempt
     */
    public static function logLoginAttempt($ip, $email) {
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO login_attempts (ip, email) VALUES (?, ?)",
            [$ip, $email]
        );
    }
    
    /**
     * Get client IP
     */
    public static function getClientIp() {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Generate slug from string
     */
    public static function generateSlug($string) {
        $string = mb_strtolower($string, 'UTF-8');
        $string = preg_replace('/[^a-z0-9\sÄäÖöÅå-]/u', '', $string);
        $string = preg_replace('/[\s-]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }
    
    /**
     * Format price in euros
     */
    public static function formatPrice($price) {
        return number_format($price, 2, ',', ' ') . ' €';
    }
    
    /**
     * Time remaining until datetime
     */
    public static function timeRemaining($datetime) {
        $now = new DateTime();
        $end = new DateTime($datetime);
        
        if ($end <= $now) {
            return 'Päättynyt';
        }
        
        $diff = $now->diff($end);
        
        if ($diff->days > 0) {
            return $diff->days . ' pv ' . $diff->h . ' t';
        } elseif ($diff->h > 0) {
            return $diff->h . ' t ' . $diff->i . ' min';
        } else {
            return $diff->i . ' min';
        }
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Require login
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /kirjaudu');
            exit;
        }
    }
    
    /**
     * Require admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            die('Access denied');
        }
    }
}
