<?php
/**
 * Helper Functions
 * Security, validation, and utility functions
 */

/**
 * Output escaping - prevent XSS
 */
function e($value, $encoding = 'UTF-8') {
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, $encoding);
}

/**
 * JSON output escaping
 */
function e_json($value) {
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

/**
 * URL escaping
 */
function e_url($value) {
    return rawurlencode($value);
}

/**
 * JavaScript escaping
 */
function e_js($value) {
    return json_encode($value);
}

// ============================================================
// CSRF Protection
// ============================================================

/**
 * Generate CSRF token
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_LIFETIME) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF input field
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token
 */
function csrf_verify($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    }
    
    if (empty($token) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require CSRF token or die
 */
function csrf_protect() {
    if (!csrf_verify()) {
        http_response_code(403);
        die('CSRF token verification failed');
    }
}

// ============================================================
// Rate Limiting
// ============================================================

/**
 * Check rate limit
 * @param string $identifier Unique identifier (IP, user_id, email)
 * @param string $action Action type (login, bid, api)
 * @param int $maxAttempts Maximum attempts allowed
 * @param int $window Time window in seconds
 * @return bool True if allowed, false if rate limited
 */
function rate_limit_check($identifier, $action, $maxAttempts, $window) {
    $db = db();
    
    // Clean up old entries
    $db->execute(
        'DELETE FROM rate_limits WHERE window_start < ?',
        [date('Y-m-d H:i:s', time() - $window)]
    );
    
    // Check current attempts
    $record = $db->queryOne(
        'SELECT * FROM rate_limits WHERE identifier = ? AND action_type = ? AND window_start > ?',
        [$identifier, $action, date('Y-m-d H:i:s', time() - $window)]
    );
    
    if (!$record) {
        // First attempt in this window
        $db->insert('rate_limits', [
            'identifier' => $identifier,
            'action_type' => $action,
            'attempts' => 1,
            'window_start' => date('Y-m-d H:i:s')
        ]);
        return true;
    }
    
    if ($record['attempts'] >= $maxAttempts) {
        return false; // Rate limited
    }
    
    // Increment attempts
    $db->execute(
        'UPDATE rate_limits SET attempts = attempts + 1 WHERE id = ?',
        [$record['id']]
    );
    
    return true;
}

/**
 * Check login rate limit
 */
function rate_limit_login($identifier) {
    return rate_limit_check($identifier, 'login', MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME);
}

/**
 * Check bid rate limit
 */
function rate_limit_bid($identifier) {
    return rate_limit_check($identifier, 'bid', MAX_BID_ATTEMPTS, BID_RATE_WINDOW);
}

// ============================================================
// Validation
// ============================================================

/**
 * Validate email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function validate_password($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ['valid' => false, 'error' => 'Salasanan on oltava vähintään ' . PASSWORD_MIN_LENGTH . ' merkkiä'];
    }
    
    // Require at least one letter and one number
    if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'error' => 'Salasanassa on oltava sekä kirjaimia että numeroita'];
    }
    
    return ['valid' => true];
}

/**
 * Validate Finnish phone number (basic)
 */
function validate_phone($phone) {
    // Remove spaces and dashes
    $cleaned = preg_replace('/[\s\-]/', '', $phone);
    
    // Check if it's a valid Finnish phone number format
    if (preg_match('/^(\+358|0)[0-9]{6,12}$/', $cleaned)) {
        return true;
    }
    
    return false;
}

/**
 * Sanitize filename
 */
function sanitize_filename($filename) {
    // Remove any path components
    $filename = basename($filename);
    
    // Remove special characters, keep only alphanumeric, dash, underscore, and dot
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    
    return $filename;
}

// ============================================================
// Authentication Helpers
// ============================================================

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

/**
 * Get current user ID
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    static $user = null;
    if ($user === null) {
        $user = db()->queryOne('SELECT * FROM users WHERE id = ?', [current_user_id()]);
    }
    
    return $user;
}

/**
 * Check if simple admin panel session is active
 */
function is_panel_admin_authenticated() {
    return !empty($_SESSION['panel_admin_authenticated']) && $_SESSION['panel_admin_authenticated'] === true;
}

/**
 * Require simple admin panel session or redirect to admin login page
 */
function require_panel_admin() {
    if (!is_panel_admin_authenticated()) {
        $next = urlencode((string)($_SERVER['REQUEST_URI'] ?? '/admin.php'));
        header('Location: /admin.php?next=' . $next);
        exit;
    }
}

/**
 * Check if user is admin
 */
function is_admin() {
    if (is_panel_admin_authenticated()) {
        return true;
    }

    $user = current_user();
    return $user && ($user['role'] === 'admin' || $user['role'] === 'moderator');
}

/**
 * Require login or redirect
 */
function require_login($redirect = '/login.php') {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Require admin or die
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Access denied. Admin privileges required.');
    }
}

// ============================================================
// Audit Logging
// ============================================================

/**
 * Log audit event
 */
function audit_log($action, $entity_type = null, $entity_id = null, $details = null) {
    $db = db();
    
    $data = [
        'user_id' => current_user_id(),
        'action' => $action,
        'entity_type' => $entity_type,
        'entity_id' => $entity_id,
        'details' => is_array($details) ? json_encode($details) : $details,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ];
    
    try {
        $db->insert('audit_log', $data);
    } catch (Exception $e) {
        error_log('Failed to log audit event: ' . $e->getMessage());
    }
}

// ============================================================
// Formatting Helpers
// ============================================================

/**
 * Format price
 */
function format_price($amount, $decimals = 2) {
    return number_format($amount, $decimals, ',', ' ') . ' €';
}

/**
 * Format date in Finnish
 */
function format_date($date, $format = 'd.m.Y H:i') {
    if (is_string($date)) {
        $date = strtotime($date);
    }
    return date($format, $date);
}

/**
 * Time ago in Finnish
 */
function time_ago($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'juuri nyt';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' min sitten';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' ' . ($hours == 1 ? 'tunti' : 'tuntia') . ' sitten';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' ' . ($days == 1 ? 'päivä' : 'päivää') . ' sitten';
    } else {
        return format_date($timestamp, 'd.m.Y');
    }
}

/**
 * Countdown to datetime
 */
function countdown_to($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $diff = $timestamp - time();
    
    if ($diff <= 0) {
        return 'Päättynyt';
    }
    
    $days = floor($diff / 86400);
    $hours = floor(($diff % 86400) / 3600);
    $minutes = floor(($diff % 3600) / 60);
    $seconds = $diff % 60;
    
    if ($days > 0) {
        return sprintf('%d pv %d h', $days, $hours);
    } elseif ($hours > 0) {
        return sprintf('%d h %d min', $hours, $minutes);
    } elseif ($minutes > 0) {
        return sprintf('%d min %d s', $minutes, $seconds);
    } else {
        return sprintf('%d s', $seconds);
    }
}

// ============================================================
// Redirect Helpers
// ============================================================

/**
 * Redirect to URL
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Redirect back to referrer or fallback
 */
function redirect_back($fallback = '/') {
    $referer = $_SERVER['HTTP_REFERER'] ?? $fallback;
    redirect($referer);
}

/**
 * Set flash message
 */
function flash($key, $message = null) {
    if ($message === null) {
        // Get and clear flash message
        $msg = $_SESSION['flash_' . $key] ?? null;
        unset($_SESSION['flash_' . $key]);
        return $msg;
    }
    
    // Set flash message
    $_SESSION['flash_' . $key] = $message;
}

/**
 * Get and clear flash message
 */
function get_flash($key) {
    return flash($key);
}

// ============================================================
// File Upload Helpers
// ============================================================

/**
 * Handle file upload
 */
function handle_upload($file, $destination_dir, $allowed_types = null) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Virheellinen tiedosto'];
    }
    
    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'Tiedosto on liian suuri'];
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'Tiedostoa ei valittu'];
        default:
            return ['success' => false, 'error' => 'Tuntematon virhe'];
    }
    
    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'error' => 'Tiedosto on liian suuri (max ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . ' MB)'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed = $allowed_types ?? ALLOWED_IMAGE_TYPES;
    if (!in_array($mime, $allowed)) {
        return ['success' => false, 'error' => 'Tiedostotyyppi ei ole sallittu'];
    }
    
    // Generate safe filename based on detected MIME type, not user-supplied extension
    $extension_map = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    
    $ext = $extension_map[$mime] ?? null;
    if ($ext === null) {
        // Should not occur if $allowed and $extension_map are kept in sync,
        // but fail safely if MIME type is not recognized.
        return ['success' => false, 'error' => 'Tuntematon tiedostotyyppi'];
    }
    
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $filepath = rtrim($destination_dir, '/') . '/' . $filename;
    
    // Ensure directory exists
    if (!is_dir($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    // Move file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'error' => 'Tiedoston tallennus epäonnistui'];
    }
    
    return [
        'success' => true,
        'filename' => $filename,
        'filepath' => $filepath,
        'mime' => $mime,
        'size' => $file['size']
    ];
}

/**
 * Create thumbnail
 */
function create_thumbnail($source, $destination, $maxWidth = 300, $maxHeight = 300) {
    $imageInfo = getimagesize($source);
    
    // Validate that getimagesize succeeded
    if ($imageInfo === false) {
        error_log("create_thumbnail: Failed to read image info from $source");
        return false;
    }
    
    list($width, $height, $type) = $imageInfo;
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = floor($width * $ratio);
    $newHeight = floor($height * $ratio);
    
    // Create image resources
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $src = imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $src = imagecreatefromwebp($source);
            break;
        default:
            error_log("create_thumbnail: Unsupported image type for $source");
            return false;
    }
    
    if ($src === false) {
        error_log("create_thumbnail: Failed to create image resource from $source");
        return false;
    }
    
    $dst = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG, GIF, and WebP
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF || $type === IMAGETYPE_WEBP) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    
    // Resize
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save
    $result = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($dst, $destination, 85);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($dst, $destination, 8);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($dst, $destination);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($dst, $destination, 85);
            break;
    }
    
    imagedestroy($src);
    imagedestroy($dst);
    
    return $result;
}

function create_watermarked_variant($source, $destination, $maxWidth = 2200, $maxHeight = 2200) {
    $imageInfo = getimagesize($source);
    if ($imageInfo === false) {
        return false;
    }

    $width = (int)$imageInfo[0];
    $height = (int)$imageInfo[1];
    $type = (int)$imageInfo[2];

    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $src = imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $src = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }

    if ($src === false) {
        return false;
    }

    $ratio = min($maxWidth / max(1, $width), $maxHeight / max(1, $height), 1);
    $newWidth = max(1, (int)floor($width * $ratio));
    $newHeight = max(1, (int)floor($height * $ratio));

    $dst = imagecreatetruecolor($newWidth, $newHeight);
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF || $type === IMAGETYPE_WEBP) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $watermarkText = 'HUUTO247.FI  •  LAHEN HUUTOKAUPPAT OY';
    $fontPathCandidates = [
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        '/usr/share/fonts/TTF/DejaVuSans-Bold.ttf',
    ];
    $fontPath = null;
    foreach ($fontPathCandidates as $candidate) {
        if (is_file($candidate)) {
            $fontPath = $candidate;
            break;
        }
    }

    if ($fontPath !== null && function_exists('imagettftext')) {
        $base = min($newWidth, $newHeight);
        $fontSize = max(28, (int)round($base / 10));
        $angle = -28;

        $textColor = imagecolorallocatealpha($dst, 235, 235, 235, 82);
        $shadowColor = imagecolorallocatealpha($dst, 0, 0, 0, 95);

        $bbox = imagettfbbox($fontSize, $angle, $fontPath, $watermarkText);
        $bboxWidth = (int)(max($bbox[2], $bbox[4]) - min($bbox[0], $bbox[6]));
        $bboxHeight = (int)(max($bbox[1], $bbox[3]) - min($bbox[5], $bbox[7]));

        $x = (int)(($newWidth - $bboxWidth) / 2);
        $y = (int)(($newHeight + $bboxHeight) / 2);

        imagettftext($dst, $fontSize, $angle, $x + 3, $y + 3, $shadowColor, $fontPath, $watermarkText);
        imagettftext($dst, $fontSize, $angle, $x, $y, $textColor, $fontPath, $watermarkText);

        $offset = (int)max(36, $fontSize * 1.4);
        imagettftext($dst, $fontSize, $angle, $x - $offset, $y - $offset, $shadowColor, $fontPath, $watermarkText);
        imagettftext($dst, $fontSize, $angle, $x - $offset - 2, $y - $offset - 2, $textColor, $fontPath, $watermarkText);
        imagettftext($dst, $fontSize, $angle, $x + $offset, $y + $offset, $shadowColor, $fontPath, $watermarkText);
        imagettftext($dst, $fontSize, $angle, $x + $offset - 2, $y + $offset - 2, $textColor, $fontPath, $watermarkText);
    } else {
        $fallbackColor = imagecolorallocatealpha($dst, 220, 220, 220, 80);
        $shadow = imagecolorallocatealpha($dst, 0, 0, 0, 95);
        $line = 'HUUTO247.FI / LAHEN HUUTOKAUPPAT OY';
        $font = 5;
        $stepY = max(54, (int)($newHeight / 6));
        for ($y = (int)($newHeight * 0.15); $y < $newHeight; $y += $stepY) {
            $x = (int)(($y / max(1, $newHeight)) * $newWidth * 0.45);
            imagestring($dst, $font, $x + 1, $y + 1, $line, $shadow);
            imagestring($dst, $font, $x, $y, $line, $fallbackColor);
        }
    }

    $result = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($dst, $destination, 88);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($dst, $destination, 7);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($dst, $destination);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($dst, $destination, 88);
            break;
    }

    imagedestroy($src);
    imagedestroy($dst);

    return $result;
}

function create_listing_thumbnail($source, $destination, $maxWidth = 680, $maxHeight = 520) {
    return create_thumbnail($source, $destination, $maxWidth, $maxHeight);
}

// ============================================================
// Pagination Helper
// ============================================================

/**
 * Calculate pagination
 */
function paginate($total, $page = 1, $perPage = 20) {
    $page = max(1, (int)$page);
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $page > 1,
        'has_next' => $page < $totalPages
    ];
}
