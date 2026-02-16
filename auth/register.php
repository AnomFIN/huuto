<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/models/Database.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';

session_start();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $termsAccepted = isset($_POST['terms']);
    
    // Validation
    if (!$email || !$password || !$fullName) {
        $error = 'Täytä kaikki vaaditut kentät';
    } elseif ($password !== $confirmPassword) {
        $error = 'Salasanat eivät täsmää';
    } elseif (!$termsAccepted) {
        $error = 'Hyväksy käyttöehdot jatkaaksesi';
    } else {
        $result = auth()->register($email, $password, $fullName);
        
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['error'];
        }
    }
}

// Check if already logged in
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
    
    <!-- Login Link -->
    <div class="text-center mt-6 pt-6 border-t">
        <p class="text-gray-600">Onko sinulla jo tili?</p>
        <a href="/auth/login.php" class="text-blue-600 hover:text-blue-800 font-medium">Kirjaudu sisään</a>
    </div>
</div>

<script>
// Real-time password validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = document.getElementById('confirm_password');
    
    // Simple validation feedback
    const isValid = password.length >= 8 && /[A-Za-z]/.test(password) && /[0-9]/.test(password);
    
    if (password.length > 0) {
        if (isValid) {
            this.style.borderColor = '#10B981';
        } else {
            this.style.borderColor = '#EF4444';
        }
    }
    
    // Check password match
    if (confirmPassword.value && password !== confirmPassword.value) {
        confirmPassword.style.borderColor = '#EF4444';
    } else if (confirmPassword.value) {
        confirmPassword.style.borderColor = '#10B981';
    }
});

document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    
    if (this.value && password !== this.value) {
        this.style.borderColor = '#EF4444';
    } else if (this.value) {
        this.style.borderColor = '#10B981';
    }
});
</script>

<?php include __DIR__ . '/../src/views/footer.php'; ?>