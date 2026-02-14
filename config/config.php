<?php
// Application configuration
define('SITE_NAME', 'Huuto - Suomalainen Huutokauppa');

// AUTO-DETECT BASE_URL for webhotel deployment
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
    define('BASE_URL', getenv('BASE_URL') ?: $baseUrl);
}

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Timezone
date_default_timezone_set('Europe/Helsinki');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
