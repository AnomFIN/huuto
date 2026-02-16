<?php
/**
 * Application Configuration
 * Supports .env file for environment-specific settings
 */

// Ensure BASE_PATH is defined (should be set by bootstrap.php)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Load environment variables from .env file if it exists
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Remove quotes if present
        $value = trim($value, '"\'');
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Helper function to get environment variable with fallback
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    // Convert boolean strings
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
    }
    return $value;
}

// ============================================================
// Core Settings
// ============================================================
define('SITE_NAME', env('SITE_NAME', 'Huuto - Suomalainen Huutokauppa'));
define('BASE_URL', env('BASE_URL', 'http://localhost:8000'));
define('TIMEZONE', env('TIMEZONE', 'Europe/Helsinki'));

// ============================================================
// Database Configuration
// ============================================================
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'huuto_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// ============================================================
// Security Settings
// ============================================================
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 7200)); // 2 hours
define('CSRF_TOKEN_LIFETIME', env('CSRF_TOKEN_LIFETIME', 3600)); // 1 hour
define('PASSWORD_MIN_LENGTH', env('PASSWORD_MIN_LENGTH', 8));

// Rate limiting
define('MAX_LOGIN_ATTEMPTS', env('MAX_LOGIN_ATTEMPTS', 5));
define('LOGIN_LOCKOUT_TIME', env('LOGIN_LOCKOUT_TIME', 900)); // 15 minutes
define('MAX_BID_ATTEMPTS', env('MAX_BID_ATTEMPTS', 10));
define('BID_RATE_WINDOW', env('BID_RATE_WINDOW', 60)); // 1 minute

// ============================================================
// File Upload Settings
// ============================================================
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
define('UPLOAD_MAX_SIZE', env('UPLOAD_MAX_SIZE', 5 * 1024 * 1024)); // 5MB
define('UPLOAD_MAX_FILES', env('UPLOAD_MAX_FILES', 8));
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// ============================================================
// Email Configuration
// ============================================================
define('MAIL_ENABLED', env('MAIL_ENABLED', false));
define('MAIL_DEV_MODE', env('MAIL_DEV_MODE', true)); // Log emails instead of sending
define('MAIL_LOG_FILE', BASE_PATH . '/logs/auth.log');
define('MAIL_FROM', env('MAIL_FROM', 'noreply@huuto.fi'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'Huuto'));

// SMTP settings (optional - leave empty to use PHP mail())
define('SMTP_HOST', env('SMTP_HOST', ''));
define('SMTP_PORT', env('SMTP_PORT', 587));
define('SMTP_USERNAME', env('SMTP_USERNAME', ''));
define('SMTP_PASSWORD', env('SMTP_PASSWORD', ''));
define('SMTP_ENCRYPTION', env('SMTP_ENCRYPTION', 'tls')); // 'tls' or 'ssl'

// ============================================================
// Authentication Settings
// ============================================================
define('AUTH_METHOD', env('AUTH_METHOD', 'email')); // 'email', 'google', or 'both'

// Email verification settings
define('EMAIL_CODE_LENGTH', 6);
define('EMAIL_CODE_EXPIRY', 600); // 10 minutes
define('EMAIL_CODE_MAX_ATTEMPTS', 5);

// Google OAuth settings
define('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/google-callback.php');

// ============================================================
// Auction Settings
// ============================================================
define('DEFAULT_BID_INCREMENT', env('DEFAULT_BID_INCREMENT', 1.00));
define('ANTI_SNIPE_ENABLED', env('ANTI_SNIPE_ENABLED', true));
define('ANTI_SNIPE_WINDOW', env('ANTI_SNIPE_WINDOW', 60)); // seconds
define('ANTI_SNIPE_EXTENSION', env('ANTI_SNIPE_EXTENSION', 120)); // seconds
define('ANTI_SNIPE_MAX_EXTENSIONS', env('ANTI_SNIPE_MAX_EXTENSIONS', 5));

// ============================================================
// Application Settings
// ============================================================
date_default_timezone_set(TIMEZONE);

// Error reporting
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Check if HTTPS is actually enabled (handle proxy headers and HTTPS=off)
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
);
if (env('APP_ENV', 'production') === 'production' && $isHttps) {
    ini_set('session.cookie_secure', 1);
}

// Start session after ini settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create necessary directories
$dirs = [
    BASE_PATH . '/uploads/',
    BASE_PATH . '/uploads/auctions/',
    BASE_PATH . '/uploads/avatars/',
    BASE_PATH . '/logs/',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Ensure logs directory is protected
$htaccess = BASE_PATH . '/logs/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Require all denied\n");
}
