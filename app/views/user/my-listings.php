<?php
$pageTitle = 'Omat ilmoitukset';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>Omat ilmoitukset</h1>
    
    <div style="margin: 1.5rem 0;">
        <a href="/profiili" class="btn btn-secondary">« Takaisin profiiliin</a>
        <a href="/luo-ilmoitus" class="btn btn-success">+ Luo uusi ilmoitus</a>
    </div>
    
    <?php if (empty($listings)): ?>
        <p style="text-align: center; padding: 3rem; color: var(--gray-600);">
            Et ole luonut vielä yhtään ilmoitusta.
        </p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($listings as $listing): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($listing['image'] ?? '/assets/img/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($listing['title']) ?>" 
                         class="card-image">
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($listing['title']) ?></div>
                        <div class="card-text"><?= htmlspecialchars($listing['category_name']) ?></div>
                        <div class="card-price"><?= Security::formatPrice($listing['current_price']) ?></div>
                        <div style="margin-top: 0.5rem;">
                            <span class="badge <?= $listing['status'] == 'active' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= $listing['status'] == 'active' ? 'Aktiivinen' : ucfirst($listing['status']) ?>
                            </span>
                            <span class="badge badge-primary"><?= $listing['bid_count'] ?> huutoa</span>
                        </div>
                        <a href="/kohde/<?= $listing['id'] ?>/<?= $listing['slug'] ?>" class="btn btn-primary" style="margin-top: 1rem; width: 100%; text-align: center;">
                            Näytä
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
