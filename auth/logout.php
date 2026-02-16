<?php
require_once __DIR__ . '/../bootstrap.php';

// Set flash message before logout
$_SESSION['flash_message'] = 'Sinut on kirjattu ulos onnistuneesti.';
$_SESSION['flash_type'] = 'success';

// Perform logout
auth()->logout();

header('Location: /');
exit;
?>