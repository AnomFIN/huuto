<?php
/*
============================================================================
PREMIUM TODO APP - CONFIGURATION
Generated: <?php echo date('Y-m-d H:i:s'); ?>

============================================================================
*/

// Installation status
define('TODO_INSTALLED', true);
define('TODO_VERSION', '1.0.0');

// Database configuration (SQLite for development)
define('DB_TYPE', 'sqlite');
define('DB_PATH', __DIR__ . '/todo_app.db');
define('DB_HOST', 'localhost');
define('DB_NAME', 'huuto_todo');
define('DB_USER', 'root');
define('DB_PASS', '');

// Security settings
define('LOGIN_PASSWORD', 'admin123');
define('SESSION_LIFETIME', 86400); // 24 hours

// File upload settings
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// App settings
define('APP_NAME', 'Premium Todo');
define('APP_URL', 'http://localhost:8080');

// Timezone
date_default_timezone_set('Europe/Helsinki');

?>