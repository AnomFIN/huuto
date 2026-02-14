<?php
$pageTitle = 'Päättyvät pian';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>⏰ Päättyvät 24 tunnin sisällä</h1>
    
    <?php if (empty($listings)): ?>
        <p style="text-align: center; padding: 3rem; color: var(--gray-600);">Ei päättyviä ilmoituksia.</p>
    <?php else: ?>
        <div class="grid" style="margin-top: 2rem;">
            <?php foreach ($listings as $listing): ?>
                <a href="/kohde/<?= $listing['id'] ?>/<?= $listing['slug'] ?>" class="card" style="text-decoration: none;">
                    <img src="<?= htmlspecialchars($listing['image'] ?? '/assets/img/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($listing['title']) ?>" 
                         class="card-image"
                         loading="lazy">
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($listing['title']) ?></div>
                        <div class="card-text"><?= htmlspecialchars($listing['category_name']) ?></div>
                        <div class="card-price"><?= Security::formatPrice($listing['current_price']) ?></div>
                        <span class="badge badge-ending countdown" data-countdown="<?= $listing['ends_at'] ?>">
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
