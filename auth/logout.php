<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/auth.php';

session_start();

// Perform logout
auth()->logout();

// Redirect to homepage with success message
session_start();
$_SESSION['flash_message'] = 'Sinut on kirjattu ulos onnistuneesti.';
$_SESSION['flash_type'] = 'success';

header('Location: /');
exit;
?>