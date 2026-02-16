<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/models/Database.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';

session_start();

$error = '';
$success = '';
$step = isset($_GET['token']) ? 'reset' : 'request';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'request_reset') {
            $email = trim($_POST['email'] ?? '');
            
            if ($email) {
                $result = auth()->requestPasswordReset($email);
                
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['error'];
                }
            } else {
                $error = 'Syötä sähköpostiosoite';
            }
        } elseif ($_POST['action'] === 'reset_password') {
            $token = $_GET['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!$token) {
                $error = 'Virheellinen resetointilinkki';
            } elseif ($password !== $confirmPassword) {
                $error = 'Salasanat eivät täsmää';
            } elseif (strlen($password) < 8) {
                $error = 'Salasanan tulee olla vähintään 8 merkkiä pitkä';
            } else {
                $result = auth()->resetPassword($token, $password);
                
                if ($result['success']) {
                    $success = 'Salasana vaihdettu onnistuneesti! Voit nyt kirjautua sisään.';
                    $step = 'success';
                } else {
                    $error = $result['error'];
                }
            }
        }
    }
}

$pageTitle = 'Salasanan nollaus - ' . SITE_NAME;
include __DIR__ . '/../src/views/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
    <?php if ($step === 'request'): ?>
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-8">Salasanan nollaus</h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600 mb-6">
                Syötä sähköpostiosoitteesi, niin lähetämme sinulle linkin salasanan nollausta varten.
            </p>
            
            <form method="POST">
                <input type="hidden" name="action" value="request_reset">
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Sähköpostiosoite</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Lähetä nollauslinkki
                </button>
            </form>
        <?php endif; ?>
        
    <?php elseif ($step === 'reset'): ?>
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-8">Uusi salasana</h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="action" value="reset_password">
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Uusi salasana</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Vähintään 8 merkkiä</p>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Vahvista salasana</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                Vaihda salasana
            </button>
        </form>
        
    <?php else: ?>
        <div class="text-center">
            <div class="text-green-500 text-6xl mb-4">✓</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Salasana vaihdettu!</h1>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <a href="/auth/login.php" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                Kirjaudu sisään
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Back to login -->
    <div class="text-center mt-6 pt-6 border-t">
        <a href="/auth/login.php" class="text-blue-600 hover:text-blue-800">← Takaisin kirjautumiseen</a>
    </div>
</div>

<?php include __DIR__ . '/../src/views/footer.php'; ?>