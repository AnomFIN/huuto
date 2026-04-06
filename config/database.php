<?php
/**
 * Database configuration - loads from .env file
 * This file is used by migration scripts and installation wizard
 */

// Load .env file if not already loaded
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$envFile = BASE_PATH . '/.env';
if (file_exists($envFile) && !function_exists('env')) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '"\' ');
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

function env($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

return [
    'host' => env('DB_HOST', 'localhost'),
    'dbname' => env('DB_NAME', 'huuto_db'),
    'username' => env('DB_USER', 'root'),
    'password' => env('DB_PASS', ''),
    'charset' => env('DB_CHARSET', 'utf8mb4')
];
