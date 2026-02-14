<?php
// Application configuration
define('SITE_NAME', 'Huuto - Suomalainen Huutokauppa');
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost:8000');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Timezone
date_default_timezone_set('Europe/Helsinki');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
