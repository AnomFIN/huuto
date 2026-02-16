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

<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
    <h1 class="text-2xl font-bold text-center text-gray-900 mb-8">Luo tili</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($success); ?>
            <p class="mt-2">
                <a href="/auth/login.php" class="text-green-700 underline">Siirry kirjautumissivulle</a>
            </p>
        </div>
    <?php else: ?>
        <?php if ($canUseGoogleAuth): ?>
            <div class="mb-6">
                <a href="/auth/google-login.php"
                   class="w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                    <span>Jatka Google-tilillä</span>
                </a>
            </div>

            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-2 bg-white text-gray-500">tai rekisteröidy sähköpostilla</span></div>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="register">

            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Koko nimi *</label>
                <input type="text" id="full_name" name="full_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Sähköposti *</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Salasana *</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Vähintään 8 merkkiä, sisällettävä kirjaimia ja numeroita</p>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Vahvista salasana *</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" id="terms" name="terms" required class="mr-2">
                    <label for="terms" class="text-sm text-gray-700">
                        Hyväksyn <a href="/terms" class="text-blue-600 hover:text-blue-800">käyttöehdot</a> ja <a href="/privacy" class="text-blue-600 hover:text-blue-800">tietosuojaselosteen</a> *
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                Luo tili
            </button>
        </form>
    <?php endif; ?>

    <div class="text-center mt-6 pt-6 border-t">
        <p class="text-gray-600">Onko sinulla jo tili?</p>
        <a href="/auth/login.php" class="text-blue-600 hover:text-blue-800 font-medium">Kirjaudu sisään</a>
    </div>
</div>

<script>
document.getElementById('password')?.addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = document.getElementById('confirm_password');
    const isValid = password.length >= 8 && /[A-Za-z]/.test(password) && /[0-9]/.test(password);

    if (password.length > 0) {
        this.style.borderColor = isValid ? '#10B981' : '#EF4444';
    }

    if (confirmPassword && confirmPassword.value) {
        confirmPassword.style.borderColor = password !== confirmPassword.value ? '#EF4444' : '#10B981';
    }
});

document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('password')?.value || '';
    this.style.borderColor = this.value && password !== this.value ? '#EF4444' : '#10B981';
});
</script>

<?php include __DIR__ . '/../src/views/footer.php'; ?>
