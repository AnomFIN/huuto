<?php
$pageTitle = 'Kategoriat';
ob_start();
?>

<div class="container mt-7">
    <div class="section-header">
        <h1>Kaikki kategoriat</h1>
    </div>
    
    <div class="grid grid-4">
        <?php foreach ($categories as $category): ?>
            <a href="/kategoria/<?= $category['slug'] ?>" class="card">
                <div class="card-body text-center p-8">
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
