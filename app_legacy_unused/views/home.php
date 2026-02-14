<?php
$pageTitle = 'Etusivu';
ob_start();
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <h1>Suomen suosituin huutokauppa-alusta</h1>
        <p>Löydä upeita tuotteita - Tee hyviä kauppoja</p>
        <a href="/kategoriat" class="btn btn-primary btn-lg">Selaa kategorioita</a>
    </section>

    <!-- Ending Soon -->
    <section>
        <div class="section-header">
            <h2>⏰ Päättyvät pian</h2>
            <a href="/paattyvat">Näytä kaikki →</a>
        </div>
        
        <div class="grid">
            <?php foreach (array_slice($endingSoon, 0, 8) as $listing): ?>
                <a href="/kohde/<?= $listing['id'] ?>/<?= $listing['slug'] ?>" class="card" style="text-decoration: none;">
                    <img src="<?= htmlspecialchars($listing['image'] ?? '/assets/img/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($listing['title']) ?>" 
                         class="card-image"
                         loading="lazy">
                    <div class="card-body">
                        <div class="card-title"><?= htmlspecialchars($listing['title']) ?></div>
                        <div class="card-text"><?= htmlspecialchars($listing['category_name']) ?></div>
                        <div class="card-price"><?= Security::formatPrice($listing['current_price']) ?></div>
                        <span class="badge badge-ending" data-countdown="<?= $listing['ends_at'] ?>">
                            <?= Security::timeRemaining($listing['ends_at']) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Newest Listings -->
    <section>
        <div class="section-header">
            <h2>🆕 Uusimmat ilmoitukset</h2>
        </div>
        
        <div class="grid">
            <?php foreach (array_slice($newest, 0, 8) as $listing): ?>
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
    </section>

    <!-- Categories Grid -->
    <section>
        <div class="section-header">
            <h2>📂 Selaa kategorioita</h2>
        </div>
        
        <div class="grid">
            <?php foreach (array_slice($categories, 0, 12) as $category): ?>
                <a href="/kategoria/<?= $category['slug'] ?>" class="card" style="text-decoration: none;">
                    <div class="card-body" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;"><?= $category['icon'] ?></div>
                        <div class="card-title"><?= htmlspecialchars($category['name']) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="d-flex justify-center mt-7">
            <a href="/kategoriat" class="btn btn-secondary">Näytä kaikki kategoriat</a>
        </div>
    </section>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
