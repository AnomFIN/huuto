<?php
require_once __DIR__ . '/../bootstrap.php';

// Perform logout
auth()->logout();

// Start a new session and set flash message after logout
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['flash_message'] = 'Sinut on kirjattu ulos onnistuneesti.';
$_SESSION['flash_type'] = 'success';
header('Location: /');
exit;
?>