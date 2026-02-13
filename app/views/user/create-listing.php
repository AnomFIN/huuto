<?php
$pageTitle = 'Luo ilmoitus';
ob_start();

$errors = $_SESSION['listing_errors'] ?? [];
unset($_SESSION['listing_errors']);
?>

<div class="container" style="margin-top: 2rem;">
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px;">
        <h1>Luo uusi ilmoitus</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul style="list-style: none;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/luo-ilmoitus" enctype="multipart/form-data" style="margin-top: 2rem;">
            <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Otsikko *</label>
                <input type="text" name="title" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Kuvaus *</label>
                <textarea name="description" class="form-textarea" required></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Kategoria *</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Valitse kategoria</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Kunto</label>
                <select name="condition" class="form-select">
                    <option value="">Valitse</option>
                    <option value="Uusi">Uusi</option>
                    <option value="Erinomainen">Erinomainen</option>
                    <option value="Hyvä">Hyvä</option>
                    <option value="Tyydyttävä">Tyydyttävä</option>
                    <option value="Korjattava">Korjattava</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Maakunta</label>
                <select name="region" class="form-select">
                    <option value="">Valitse</option>
                    <option value="Uusimaa">Uusimaa</option>
                    <option value="Pirkanmaa">Pirkanmaa</option>
                    <option value="Varsinais-Suomi">Varsinais-Suomi</option>
                    <option value="Pohjois-Pohjanmaa">Pohjois-Pohjanmaa</option>
                    <option value="Kanta-Häme">Kanta-Häme</option>
                    <option value="Satakunta">Satakunta</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Aloitushinta (€) *</label>
                <input type="number" name="start_price" class="form-input" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Osta heti -hinta (€) (valinnainen)</label>
                <input type="number" name="buy_now_price" class="form-input" step="0.01" min="0">
            </div>
            
            <div class="form-group">
                <label class="form-label">Minimikorotus (€)</label>
                <input type="number" name="min_increment" class="form-input" step="0.01" value="5.00" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Kesto (päivää)</label>
                <select name="days" class="form-select">
                    <option value="1">1 päivä</option>
                    <option value="3">3 päivää</option>
                    <option value="7" selected>7 päivää</option>
                    <option value="14">14 päivää</option>
                    <option value="30">30 päivää</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-success" style="width: 100%;">Luo ilmoitus</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
