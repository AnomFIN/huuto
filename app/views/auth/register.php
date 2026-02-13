<?php
$pageTitle = 'Rekisteröidy';
ob_start();

$errors = $_SESSION['register_errors'] ?? [];
$data = $_SESSION['register_data'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_data']);
?>

<div class="auth-container">
    <h1>Luo uusi tili</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul style="list-style: none;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/rekisteroidy">
        <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
        
        <div class="form-group">
            <label class="form-label">Nimi</label>
            <input type="text" name="name" class="form-input" 
                   value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Sähköposti</label>
            <input type="email" name="email" class="form-input" 
                   value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Salasana (min. 6 merkkiä)</label>
            <input type="password" name="password" class="form-input" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Rekisteröidy</button>
    </form>
    
    <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-600);">
        Onko sinulla jo tili? <a href="/kirjaudu">Kirjaudu tästä</a>
    </p>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
