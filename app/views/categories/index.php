<?php
$pageTitle = 'Kategoriat';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>Kaikki kategoriat</h1>
    
    <div class="grid" style="margin-top: 2rem;">
        <?php foreach ($categories as $category): ?>
            <a href="/kategoria/<?= $category['slug'] ?>" class="card" style="text-decoration: none;">
                <div class="card-body" style="text-align: center; padding: 2rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;"><?= $category['icon'] ?></div>
                    <div class="card-title"><?= htmlspecialchars($category['name']) ?></div>
                    <div class="card-text"><?= $category['listing_count'] ?> ilmoitusta</div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
