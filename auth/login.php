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

<style>
    .auth-container {
        max-width: 400px;
        margin: 3rem auto;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 2.5rem;
        box-shadow: var(--shadow-1);
    }

    .auth-title {
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-900);
        margin: 0 0 2rem;
    }

    .message-box {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .message-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }

    .message-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
    }

    .form-section {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-900);
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--surface);
        font-size: 0.95rem;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent-600);
        box-shadow: 0 0 0 3px rgba(38,104,255,.16);
    }

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-700);
    }

    .button {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .button-primary {
        background: var(--accent-600);
        color: white;
    }

    .button-primary:hover {
        background: var(--accent-700);
    }

    .button-secondary {
        background: var(--surface);
        border: 1px solid var(--line);
        color: var(--text-900);
    }

    .button-secondary:hover {
        background: var(--bg-neutral-50);
    }

    .button-google {
        background: white;
        border: 1px solid var(--line);
        color: var(--text-900);
    }

    .button-google:hover {
        background: var(--bg-neutral-50);
    }

    .section-divider {
        border-top: 1px solid var(--line);
        padding-top: 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .section-divider h3 {
        text-align: center;
        color: var(--text-700);
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .code-input {
        text-align: center;
        font-size: 1.25rem;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.25rem;
    }

    .magic-code-info {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .magic-code-info p {
        margin-bottom: 0.5rem;
        color: var(--text-700);
    }

    .magic-code-info .email {
        font-weight: 600;
        color: var(--text-900);
    }

    .auth-footer {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--line);
        color: var(--text-700);
    }

    .auth-footer a {
        color: var(--accent-600);
        text-decoration: none;
        font-weight: 600;
    }

    .auth-footer a:hover {
        color: var(--accent-700);
    }

    .back-link {
        text-align: center;
        margin-top: 1rem;
    }

    .back-link a {
        color: var(--text-700);
        text-decoration: none;
        font-size: 0.9rem;
    }

    .back-link a:hover {
        color: var(--text-900);
    }

    .forgot-password-link {
        color: var(--accent-600);
        text-decoration: none;
        font-size: 0.9rem;
    }

    .forgot-password-link:hover {
        color: var(--accent-700);
    }

    .form-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
</style>

<div class="auth-container">
    <h1 class="auth-title">Kirjaudu sisään</h1>

    <?php if ($error): ?>
        <div class="message-box message-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message-box message-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!$showMagicCodeForm): ?>
        <form method="POST" class="form-section">
            <input type="hidden" name="action" value="login">

            <div class="form-section">
                <label for="email" class="form-label">Sähköposti</label>
                <input type="email" id="email" name="email" required
                       class="form-input"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-section">
                <label for="password" class="form-label">Salasana</label>
                <input type="password" id="password" name="password" required
                       class="form-input">
            </div>

            <div class="form-row">
                <div class="form-checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Muista minut</label>
                </div>
                <a href="/auth/reset-password.php" class="forgot-password-link">Unohtuiko salasana?</a>
            </div>

            <button type="submit" class="button button-primary">
                Kirjaudu sisään
            </button>
        </form>

        <?php if ($canUseGoogleAuth): ?>
            <div class="form-section">
                <a href="<?php echo htmlspecialchars($googleLoginUrl); ?>" class="button button-google">
                    Kirjaudu Google-tilillä
                </a>
            </div>
        <?php endif; ?>

        <div class="section-divider">
            <h3>Tai kirjaudu koodilla</h3>
            <form method="POST">
                <input type="hidden" name="action" value="send_magic_code">

                <div class="form-section">
                    <label for="magic_email" class="form-label">Sähköposti</label>
                    <input type="email" id="magic_email" name="magic_email" required
                           class="form-input">
                </div>

                <button type="submit" class="button button-secondary">
                    Lähetä koodi sähköpostiin
                </button>
            </form>
        </div>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="action" value="verify_magic_code">

            <div class="magic-code-info">
                <p>Lähetimme 6-numeroisen koodin osoitteeseen:</p>
                <p class="email"><?php echo htmlspecialchars($_SESSION['magic_email'] ?? ''); ?></p>
            </div>

            <div class="form-section">
                <label for="code" class="form-label">Syötä koodi</label>
                <input type="text" id="code" name="code" required maxlength="6" pattern="[0-9]{6}"
                       class="form-input code-input"
                       placeholder="123456">
            </div>

            <button type="submit" class="button button-primary">
                Vahvista ja kirjaudu
            </button>
        </form>

        <div class="back-link">
            <a href="/auth/login.php">← Takaisin kirjautumiseen</a>
        </div>
    <?php endif; ?>

    <div class="auth-footer">
        <p>Eikö sinulla ole tiliä?</p>
        <a href="/auth/register.php">Rekisteröidy tästä</a>
    </div>
</div>

<?php include __DIR__ . '/../src/views/footer.php'; ?>
