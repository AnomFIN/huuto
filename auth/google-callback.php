<?php
// Commit to intelligence. Push innovation. Pull results.
require_once __DIR__ . '/../bootstrap.php';

$canUseGoogleAuth = in_array(AUTH_METHOD, ['google', 'both'], true)
    && GOOGLE_CLIENT_ID !== ''
    && GOOGLE_CLIENT_SECRET !== ''
    && function_exists('curl_init');

if (!$canUseGoogleAuth) {
    $_SESSION['oauth_error'] = 'Google-kirjautuminen ei ole käytettävissä.';
    header('Location: /auth/login.php');
    exit;
}

$code = trim($_GET['code'] ?? '');
$state = trim($_GET['state'] ?? '');
$error = trim($_GET['error'] ?? '');

if ($error !== '') {
    $_SESSION['oauth_error'] = 'Google-kirjautuminen peruttiin tai epäonnistui.';
    header('Location: /auth/login.php');
    exit;
}

if ($code === '' || $state === '') {
    $_SESSION['oauth_error'] = 'Google-kirjautumisen vastaus oli puutteellinen.';
    header('Location: /auth/login.php');
    exit;
}

$result = auth()->handleGoogleCallback($code, $state);

if (!empty($result['success'])) {
    $redirect = $_SESSION['redirect_after_login'] ?? '/';
    unset($_SESSION['redirect_after_login']);

    if (!is_string($redirect) || !str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
        $redirect = '/';
    }

    header('Location: ' . $redirect);
    exit;
}

$_SESSION['oauth_error'] = $result['error'] ?? 'Google-kirjautuminen epäonnistui.';
header('Location: /auth/login.php');
exit;
