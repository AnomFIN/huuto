<?php
/**
 * Bootstrap File
 * Centralized initialization for all entry points
 * This ensures consistent configuration, paths, and error handling across the application
 */

// Define base paths - these are the ONLY path definitions in the entire application
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}
if (!defined('SRC_PATH')) {
    define('SRC_PATH', BASE_PATH . '/src');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', APP_PATH);
}

// Set up error handling before loading anything else
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php_errors.log');

// Custom error handler that logs and shows friendly message
function handleFatalError() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log(sprintf(
            "FATAL ERROR: %s in %s on line %d",
            $error['message'],
            $error['file'],
            $error['line']
        ));
        
        // Show friendly message to user in debug mode
        $showErrors = (defined('APP_DEBUG') && APP_DEBUG) || ini_get('display_errors');
        if ($showErrors) {
            echo "<!DOCTYPE html><html><head><title>Virhe</title></head><body>";
            echo "<h1>Tekninen virhe</h1>";
            echo "<p>Sivuston lataamisessa tapahtui virhe. Yritä myöhemmin uudelleen.</p>";
            echo "<p><small>Virhe on lokitettu ja korjataan pian.</small></p>";
            echo "</body></html>";
        }
    }
}
register_shutdown_function('handleFatalError');

// Custom exception handler
set_exception_handler(function($exception) {
    error_log(sprintf(
        "UNCAUGHT EXCEPTION: %s in %s on line %d\nStack trace:\n%s",
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));
    
    // Show friendly message to user in debug mode
    $showErrors = (defined('APP_DEBUG') && APP_DEBUG) || ini_get('display_errors');
    if ($showErrors) {
        echo "<!DOCTYPE html><html><head><title>Virhe</title></head><body>";
        echo "<h1>Tekninen virhe</h1>";
        echo "<p>Tapahtui odottamaton virhe: " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><small>Virhe on lokitettu ja korjataan pian.</small></p>";
        echo "</body></html>";
    }
});

try {
    // Create logs directory if it doesn't exist
    if (!is_dir(BASE_PATH . '/logs')) {
        mkdir(BASE_PATH . '/logs', 0755, true);
    }
    
    // Load the main configuration file
    require_once APP_PATH . '/config.php';
    
    // Load core application files in correct order
    require_once APP_PATH . '/db.php';
    require_once APP_PATH . '/helpers.php';
    require_once APP_PATH . '/email.php';
    require_once APP_PATH . '/auth.php';
    
    // Load model classes
    require_once SRC_PATH . '/models/Database.php';
    require_once SRC_PATH . '/models/Auction.php';
    require_once SRC_PATH . '/models/Category.php';
    
} catch (Exception $e) {
    error_log("Bootstrap error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    // Show user-friendly error
    echo "<!DOCTYPE html><html><head><title>Virhe</title></head><body>";
    echo "<h1>Sivuston käynnistys epäonnistui</h1>";
    echo "<p>Sivuston alustuksessa tapahtui virhe. Yritä myöhemmin uudelleen.</p>";
    // Only show error details in debug mode
    $showErrors = (defined('APP_DEBUG') && APP_DEBUG) || ini_get('display_errors');
    if ($showErrors) {
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    }
    echo "</body></html>";
    exit(1);
}

// Note: The site is already installed and live at https://huuto247.fi
// No installation checks are performed in production
