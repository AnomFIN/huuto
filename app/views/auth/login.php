<?php
$pageTitle = 'Kirjaudu';
ob_start();
?>

<div class="auth-container">
    <h1>Kirjaudu sisään</h1>
    
    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['login_error']) ?>
        </div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
    
    <form method="POST" action="/kirjaudu">
        <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
        
        <div class="form-group">
            <label class="form-label">Sähköposti</label>
            <input type="email" name="email" class="form-input" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Salasana</label>
            <input type="password" name="password" class="form-input" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Kirjaudu</button>
    </form>
    
    <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-600);">
        Eikö sinulla ole tiliä? <a href="/rekisteroidy">Rekisteröidy tästä</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
