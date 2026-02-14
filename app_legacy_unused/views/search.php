<?php
$pageTitle = 'Haku';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>🔍 Haku</h1>
    
    <form method="GET" action="/haku" style="background: white; padding: 2rem; border-radius: 12px; margin: 1.5rem 0;">
        <div class="form-group">
            <label class="form-label">Hakusana</label>
            <input type="search" name="q" class="form-input" 
                   value="<?= htmlspecialchars($query ?? '') ?>" placeholder="Etsi ilmoituksia...">
        </div>
        
        <div class="form-group">
            <label class="form-label">Kategoria</label>
            <select name="category" class="form-select">
                <option value="">Kaikki kategoriat</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="ending_soon" <?= $endingSoon ? 'checked' : '' ?>>
                Vain päättyvät pian (24h)
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary">Hae</button>
    </form>
    
    <?php if (isset($query) && $query !== ''): ?>
        <p style="color: var(--gray-600); margin-bottom: 1rem;">
            Löytyi <?= count($results) ?> ilmoitusta hakusanalla "<?= htmlspecialchars($query) ?>"
        </p>
    <?php endif; ?>
    
    <?php if (!empty($results)): ?>
        <div class="grid">
            <?php foreach ($results as $listing): ?>
                <a href="/kohde/<?= $listing['id'] ?>/<?= $listing['slug'] ?>" class="card" style="text-decoration: none;">
                    <img src="<?= htmlspecialchars($listing['image'] ?? '/assets/img/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($listing['title']) ?>" 
                         class="card-image"
                         loading="lazy">
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($listing['title']) ?></div>
                        <div class="card-text"><?= htmlspecialchars($listing['category_name']) ?></div>
                        <div class="card-price"><?= Security::formatPrice($listing['current_price']) ?></div>
                        <span class="badge badge-primary" data-countdown="<?= $listing['ends_at'] ?>">
                            <?= Security::timeRemaining($listing['ends_at']) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($query)): ?>
        <p style="text-align: center; padding: 3rem; color: var(--gray-600);">
            Ei hakutuloksia.
        </p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
