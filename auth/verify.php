<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/helpers.php';

$error = '';
$success = '';

// Handle verification
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = auth()->verifyEmail($token);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['error'];
    }
} else {
    $error = 'Virheellinen vahvistuslinkki';
}

$pageTitle = 'Sähköpostin vahvistus - ' . SITE_NAME;
include __DIR__ . '/../src/views/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
    <div class="text-center">
        <?php if ($success): ?>
            <div class="text-green-500 text-6xl mb-4">✓</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Sähköposti vahvistettu!</h1>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <p class="text-gray-600 mb-6">Tilisi on nyt aktiivinen ja voit kirjautua sisään.</p>
            <a href="/auth/login.php" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                Siirry kirjautumiseen
            </a>
        <?php else: ?>
            <div class="text-red-500 text-6xl mb-4">✗</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Vahvistus epäonnistui</h1>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <p class="text-gray-600 mb-6">Vahvistuslinkki saattaa olla vanhentunut tai virheellinen.</p>
            <div class="space-y-3">
                <a href="/auth/register.php" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                    Rekisteröidy uudelleen
                </a>
                <br>
                <a href="/auth/login.php" class="text-blue-600 hover:text-blue-800">
                    Tai siirry kirjautumiseen
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../src/views/footer.php'; ?>