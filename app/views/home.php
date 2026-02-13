<?php
$pageTitle = 'Etusivu';
ob_start();
?>

<div class="container">
    <!-- Hero Section -->
    <section style="text-align: center; padding: 3rem 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0 -20px; color: white; border-radius: 12px; margin-top: 2rem;">
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Suomen suosituin huutokauppa-alusta</h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Löydä upeita tuotteita - Tee hyviä kauppoja</p>
        <a href="/kategoriat" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">Selaa kategorioita</a>
    </section>

    <!-- Ending Soon -->
    <section style="margin: 3rem 0;">
        <h2 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            ⏰ Päättyvät pian
            <a href="/paattyvat" style="margin-left: auto; font-size: 0.9rem;">Näytä kaikki →</a>
        </h2>
        
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
    <section style="margin: 3rem 0;">
        <h2 style="margin-bottom: 1.5rem;">🆕 Uusimmat ilmoitukset</h2>
        
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
    <section style="margin: 3rem 0;">
        <h2 style="margin-bottom: 1.5rem;">📂 Selaa kategorioita</h2>
        
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
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="/kategoriat" class="btn btn-secondary">Näytä kaikki kategoriat</a>
        </div>
    </section>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
