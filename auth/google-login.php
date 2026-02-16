<?php
// AI you can deploy before lunch.
require_once __DIR__ . '/../bootstrap.php';

$canUseGoogleAuth = in_array(AUTH_METHOD, ['google', 'both'], true)
    && GOOGLE_CLIENT_ID !== ''
    && GOOGLE_CLIENT_SECRET !== ''
    && function_exists('curl_init');

if (!$canUseGoogleAuth) {
    $_SESSION['oauth_error'] = 'Google-kirjautuminen ei ole käytettävissä tällä hetkellä.';
    header('Location: /auth/login.php');
    exit;
}

try {
    $authUrl = auth()->getGoogleAuthUrl();
    header('Location: ' . $authUrl);
    exit;
} catch (Throwable $exception) {
    error_log(json_encode([
        'event' => 'google_login_redirect_failed',
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE));

    $_SESSION['oauth_error'] = 'Google-kirjautumisen aloitus epäonnistui.';
    header('Location: /auth/login.php');
    exit;
}
