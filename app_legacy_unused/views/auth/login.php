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
            <input type="email" name="email" class="form-input" required autocomplete="email">
        </div>
        
        <div class="form-group">
            <label class="form-label">Salasana</label>
            <input type="password" name="password" class="form-input" required autocomplete="current-password">
        </div>
        
        <button type="submit" class="btn btn-primary w-full">Kirjaudu</button>
    </form>
    
    <div class="auth-footer">
        Eikö sinulla ole tiliä? <a href="/rekisteroidy">Rekisteröidy tästä</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
