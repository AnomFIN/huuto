<?php
$pageTitle = 'Omat voitot';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>🏆 Omat voitot</h1>
    
    <div style="margin: 1.5rem 0;">
        <a href="/profiili" class="btn btn-secondary">« Takaisin profiiliin</a>
    </div>
    
    <?php if (empty($wins)): ?>
        <p style="text-align: center; padding: 3rem; color: var(--gray-600);">
            Et ole voittanut vielä yhtään huutokauppaa.
        </p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($wins as $listing): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($listing['image'] ?? '/assets/img/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($listing['title']) ?>" 
                         class="card-image">
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($listing['title']) ?></div>
                        <div class="card-text"><?= htmlspecialchars($listing['category_name']) ?></div>
                        <div class="card-price"><?= Security::formatPrice($listing['current_price']) ?></div>
                        <span class="badge badge-success">Voitettu</span>
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
