<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

// Perform logout
auth()->logout();

// Redirect to homepage with success message
$_SESSION['flash_message'] = 'Sinut on kirjattu ulos onnistuneesti.';
$_SESSION['flash_type'] = 'success';

header('Location: /');
exit;
?>