<?php
// Beyond algorithms. Into outcomes.
require_once __DIR__ . '/../bootstrap.php';

$error = '';
$success = '';
$canUseGoogleAuth = in_array(AUTH_METHOD, ['google', 'both'], true)
    && GOOGLE_CLIENT_ID !== ''
    && GOOGLE_CLIENT_SECRET !== ''
    && function_exists('curl_init');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    try {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');
        $termsAccepted = isset($_POST['terms']);

        if (!$email || !$password || !$fullName) {
            $error = 'Täytä kaikki vaaditut kentät';
        } elseif ($password !== $confirmPassword) {
            $error = 'Salasanat eivät täsmää';
        } elseif (!$termsAccepted) {
            $error = 'Hyväksy käyttöehdot jatkaaksesi';
        } else {
            $result = auth()->register($email, $password, $fullName);

            if (!empty($result['success'])) {
                $success = (string)($result['message'] ?? 'Rekisteröinti onnistui.');
            } else {
                $error = (string)($result['error'] ?? 'Rekisteröinti epäonnistui.');
            }
        }
    } catch (Throwable $exception) {
        error_log(json_encode([
            'event' => 'register_page_form_handler_failed',
            'error' => $exception->getMessage(),
        ], JSON_UNESCAPED_UNICODE));
        $error = 'Rekisteröinti epäonnistui. Yritä hetken kuluttua uudelleen.';
    }
}

if (is_logged_in()) {
    header('Location: /');
    exit;
}

$pageTitle = 'Rekisteröidy - ' . SITE_NAME;
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

    .form-input.valid {
        border-color: #16a34a;
    }

    .form-input.invalid {
        border-color: #dc2626;
    }

    .form-help {
        font-size: 0.8rem;
        color: var(--text-700);
        margin-top: 0.25rem;
    }

    .form-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-700);
        line-height: 1.4;
    }

    .form-checkbox input {
        margin-top: 0.1rem;
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

    .button-google {
        background: white;
        border: 1px solid var(--line);
        color: var(--text-900);
    }

    .button-google:hover {
        background: var(--bg-neutral-50);
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

    .section-divider {
        position: relative;
        margin: 1.5rem 0;
        text-align: center;
    }

    .section-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: var(--line);
    }

    .section-divider span {
        background: var(--surface);
        padding: 0 0.75rem;
        font-size: 0.9rem;
        color: var(--text-700);
    }

    .success-links a {
        color: #16a34a;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .success-links a:hover {
        color: #15803d;
    }

    .required {
        color: var(--accent-600);
    }
</style>

<div class="auth-container">
    <h1 class="auth-title">Luo tili</h1>

    <?php if ($error): ?>
        <div class="message-box message-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message-box message-success">
            <?php echo htmlspecialchars($success); ?>
            <div class="success-links" style="margin-top: 0.5rem;">
                <a href="/auth/login.php">Siirry kirjautumissivulle</a>
            </div>
        </div>
    <?php else: ?>
        <?php if ($canUseGoogleAuth): ?>
            <div class="form-section">
                <a href="/auth/google-login.php" class="button button-google">
                    Jatka Google-tilillä
                </a>
            </div>

            <div class="section-divider">
                <span>tai rekisteröidy sähköpostilla</span>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="register">

            <div class="form-section">
                <label for="full_name" class="form-label">Koko nimi <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" required
                       class="form-input"
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>

            <div class="form-section">
                <label for="email" class="form-label">Sähköposti <span class="required">*</span></label>
                <input type="email" id="email" name="email" required
                       class="form-input"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-section">
                <label for="password" class="form-label">Salasana <span class="required">*</span></label>
                <input type="password" id="password" name="password" required
                       class="form-input">
                <p class="form-help">Vähintään 8 merkkiä, sisällettävä kirjaimia ja numeroita</p>
            </div>

            <div class="form-section">
                <label for="confirm_password" class="form-label">Vahvista salasana <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="form-input">
            </div>

            <div class="form-section">
                <div class="form-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Hyväksyn <a href="/terms" style="color: var(--accent-600); text-decoration: none;">käyttöehdot</a> ja <a href="/privacy" style="color: var(--accent-600); text-decoration: none;">tietosuojaselosteen</a> <span class="required">*</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="button button-primary">
                Luo tili
            </button>
        </form>
    <?php endif; ?>

    <div class="auth-footer">
        <p>Onko sinulla jo tili?</p>
        <a href="/auth/login.php">Kirjaudu sisään</a>
    </div>
</div>

<script>
document.getElementById('password')?.addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = document.getElementById('confirm_password');
    const isValid = password.length >= 8 && /[A-Za-z]/.test(password) && /[0-9]/.test(password);

    if (password.length > 0) {
        this.classList.toggle('valid', isValid);
        this.classList.toggle('invalid', !isValid);
    }

    if (confirmPassword && confirmPassword.value) {
        const passwordsMatch = password === confirmPassword.value;
        confirmPassword.classList.toggle('valid', passwordsMatch);
        confirmPassword.classList.toggle('invalid', !passwordsMatch);
    }
});

document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('password')?.value || '';
    const passwordsMatch = password === this.value;
    this.classList.toggle('valid', this.value && passwordsMatch);
    this.classList.toggle('invalid', this.value && !passwordsMatch);
});
</script>

<?php include __DIR__ . '/../src/views/footer.php'; ?>
