<?php
require_once __DIR__ . '/../bootstrap.php';

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

<style>
    .verify-wrap { max-width: 520px; margin: 3rem auto; background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow-1); padding: 2rem; text-align: center; }
    .verify-icon { font-size: 2.2rem; margin-bottom: .7rem; }
    .verify-title { margin: 0 0 .75rem; color: var(--text-900); font-size: 1.45rem; font-weight: 800; }
    .verify-note { margin: 0 0 1rem; color: var(--text-700); }
    .verify-message { border-radius: 10px; padding: .8rem 1rem; margin-bottom: 1rem; font-size: .92rem; }
    .verify-message.ok { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .verify-message.err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .verify-actions { display: inline-flex; gap: .55rem; flex-wrap: wrap; justify-content: center; }
    .verify-btn { padding: .65rem 1rem; border-radius: 8px; font-weight: 700; text-decoration: none; }
    .verify-btn.primary { background: var(--accent-600); color: #fff; }
    .verify-btn.secondary { border: 1px solid var(--line); color: var(--text-900); background: var(--surface); }
</style>

<section class="verify-wrap">
    <?php if ($success): ?>
        <div class="verify-icon">✅</div>
        <h1 class="verify-title">Sähköposti vahvistettu!</h1>
        <p class="verify-message ok"><?php echo htmlspecialchars($success); ?></p>
        <p class="verify-note">Tilisi on nyt aktiivinen ja voit kirjautua sisään.</p>
        <div class="verify-actions">
            <a href="/auth/login.php" class="verify-btn primary">Siirry kirjautumiseen</a>
        </div>
    <?php else: ?>
        <div class="verify-icon">⚠️</div>
        <h1 class="verify-title">Vahvistus epäonnistui</h1>
        <p class="verify-message err"><?php echo htmlspecialchars($error); ?></p>
        <p class="verify-note">Vahvistuslinkki saattaa olla vanhentunut tai virheellinen.</p>
        <div class="verify-actions">
            <a href="/auth/register.php" class="verify-btn primary">Rekisteröidy uudelleen</a>
            <a href="/auth/login.php" class="verify-btn secondary">Kirjaudu sisään</a>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../src/views/footer.php'; ?>