<?php
$pageTitle = htmlspecialchars($category['name']);
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <div class="breadcrumb">
        <a href="/">Etusivu</a> / 
        <a href="/kategoriat">Kategoriat</a> / 
        <?= htmlspecialchars($category['name']) ?>
    </div>
    
    <h1><?= $category['icon'] ?> <?= htmlspecialchars($category['name']) ?></h1>
    
    <!-- Filters -->
    <form method="GET" style="background: white; padding: 1.5rem; border-radius: 12px; margin: 1.5rem 0;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Min. hinta</label>
                <input type="number" name="min_price" class="form-input" 
                       value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" placeholder="€">
            </div>
            
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Max. hinta</label>
                <input type="number" name="max_price" class="form-input" 
                       value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" placeholder="€">
            </div>
            
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Maakunta</label>
                <select name="region" class="form-select">
                    <option value="">Kaikki</option>
                    <?php foreach ($regions as $r): ?>
                        <option value="<?= htmlspecialchars($r['region']) ?>" 
                                <?= ($_GET['region'] ?? '') == $r['region'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['region']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin: 0;">
                <label class="form-label">Järjestys</label>
                <select name="sort" class="form-select">
                    <option value="ending" <?= ($_GET['sort'] ?? 'ending') == 'ending' ? 'selected' : '' ?>>Päättyy ensin</option>
                    <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Uusimmat</option>
                    <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Halvin ensin</option>
                    <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Kallein ensin</option>
                </select>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Suodata</button>
    </form>
    
    <!-- Listings -->
    <?php if (empty($listings)): ?>
        <p style="text-align: center; padding: 3rem; color: var(--gray-600);">Ei ilmoituksia tässä kategoriassa.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($listings as $listing): ?>
                <a href="/kohde/<?= $listing['id'] ?>/<?= $listing['slug'] ?>" class="card" style="text-decoration: none;">
                    <img src="<?= htmlspecialchars($listing['image'] ?? '/assets/img/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($listing['title']) ?>" 
                         class="card-image"
                         loading="lazy">
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($listing['title']) ?></div>
                        <div class="card-text"><?= htmlspecialchars($listing['region'] ?? '') ?></div>
                        <div class="card-price"><?= Security::formatPrice($listing['current_price']) ?></div>
                        <span class="badge badge-primary" data-countdown="<?= $listing['ends_at'] ?>">
                            <?= Security::timeRemaining($listing['ends_at']) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
