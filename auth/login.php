<?php
// Engineered for autonomy, designed for humans.
require_once __DIR__ . '/../bootstrap.php';

$error = '';
$success = '';
$showMagicCodeForm = false;

if (!empty($_SESSION['oauth_error'])) {
    $error = (string)$_SESSION['oauth_error'];
    unset($_SESSION['oauth_error']);
}

$canUseGoogleAuth = in_array(AUTH_METHOD, ['google', 'both'], true)
    && GOOGLE_CLIENT_ID !== ''
    && GOOGLE_CLIENT_SECRET !== ''
    && function_exists('curl_init');
$googleLoginUrl = $canUseGoogleAuth ? '/auth/google-login.php' : null;

function sanitize_internal_redirect(?string $redirect): string {
    if (!$redirect || !is_string($redirect)) {
        return '/';
    }

    if (!str_starts_with($redirect, '/')) {
        return '/';
    }

    if (str_starts_with($redirect, '//')) {
        return '/';
    }

    return $redirect;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'login') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $rememberMe = isset($_POST['remember']);

            if ($email && $password) {
                $result = auth()->login($email, $password, $rememberMe);

                if (!empty($result['success'])) {
                    $redirect = sanitize_internal_redirect($_SESSION['redirect_after_login'] ?? '/');
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect);
                    exit;
                }

                $error = (string)($result['error'] ?? 'Kirjautuminen epäonnistui.');
            } else {
                $error = 'Syötä sähköposti ja salasana';
            }
        } elseif ($_POST['action'] === 'send_magic_code') {
            $email = trim($_POST['magic_email'] ?? '');

            if ($email) {
                $result = auth()->sendMagicCode($email);

                if (!empty($result['success'])) {
                    $success = (string)($result['message'] ?? 'Koodi lähetetty.');
                    $showMagicCodeForm = true;
                    $_SESSION['magic_email'] = $email;
                } else {
                    $error = (string)($result['error'] ?? 'Koodin lähetys epäonnistui.');
                }
            } else {
                $error = 'Syötä sähköpostiosoite';
            }
        } elseif ($_POST['action'] === 'verify_magic_code') {
            $email = (string)($_SESSION['magic_email'] ?? '');
            $code = trim($_POST['code'] ?? '');

            if ($email && $code) {
                $result = auth()->verifyMagicCode($email, $code);

                if (!empty($result['success'])) {
                    unset($_SESSION['magic_email']);
                    $redirect = sanitize_internal_redirect($_SESSION['redirect_after_login'] ?? '/');
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect);
                    exit;
                }

                $error = (string)($result['error'] ?? 'Koodi ei kelpaa.');
                $showMagicCodeForm = true;
            } else {
                $error = 'Syötä koodi';
                $showMagicCodeForm = true;
            }
        }
    } catch (Throwable $exception) {
        error_log(json_encode([
            'event' => 'login_page_form_handler_failed',
            'action' => $_POST['action'] ?? null,
            'error' => $exception->getMessage(),
        ], JSON_UNESCAPED_UNICODE));
        $error = 'Kirjautuminen epäonnistui. Yritä hetken kuluttua uudelleen.';
    }
}

// Check if already logged in
if (is_logged_in()) {
    header('Location: /');
    exit;
}

$pageTitle = 'Kirjaudu sisään - ' . SITE_NAME;
include __DIR__ . '/../src/views/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
    <h1 class="text-2xl font-bold text-center text-gray-900 mb-8">Kirjaudu sisään</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!$showMagicCodeForm): ?>
        <form method="POST" class="mb-6">
            <input type="hidden" name="action" value="login">

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Sähköposti</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Salasana</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="mr-2">
                    <label for="remember" class="text-sm text-gray-600">Muista minut</label>
                </div>
                <a href="/auth/reset-password.php" class="text-sm text-blue-600 hover:text-blue-800">Unohtuiko salasana?</a>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                Kirjaudu sisään
            </button>
        </form>

        <?php if ($canUseGoogleAuth): ?>
            <div class="mb-6">
                <a href="<?php echo htmlspecialchars($googleLoginUrl); ?>"
                   class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                    <span>Kirjaudu Google-tilillä</span>
                </a>
            </div>
        <?php endif; ?>

        <div class="border-t pt-6 mb-6">
            <h3 class="text-center text-gray-600 mb-4">Tai kirjaudu koodilla</h3>
            <form method="POST">
                <input type="hidden" name="action" value="send_magic_code">

                <div class="mb-4">
                    <label for="magic_email" class="block text-sm font-medium text-gray-700 mb-2">Sähköposti</label>
                    <input type="email" id="magic_email" name="magic_email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                    Lähetä koodi sähköpostiin
                </button>
            </form>
        </div>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="action" value="verify_magic_code">

            <div class="text-center mb-6">
                <p class="text-gray-600">Lähetimme 6-numeroisen koodin osoitteeseen:</p>
                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['magic_email'] ?? ''); ?></p>
            </div>

            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Syötä koodi</label>
                <input type="text" id="code" name="code" required maxlength="6" pattern="[0-9]{6}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-2xl font-mono"
                       placeholder="123456">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                Vahvista ja kirjaudu
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="/auth/login.php" class="text-sm text-gray-600 hover:text-gray-800">← Takaisin kirjautumiseen</a>
        </div>
    <?php endif; ?>

    <div class="text-center mt-6 pt-6 border-t">
        <p class="text-gray-600">Eikö sinulla ole tiliä?</p>
        <a href="/auth/register.php" class="text-blue-600 hover:text-blue-800 font-medium">Rekisteröidy tästä</a>
    </div>
</div>

<?php include __DIR__ . '/../src/views/footer.php'; ?>
