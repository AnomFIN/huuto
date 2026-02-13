<?php
$pageTitle = 'Omat huudot';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>Omat huudot</h1>
    
    <div style="margin: 1.5rem 0;">
        <a href="/profiili" class="btn btn-secondary">« Takaisin profiiliin</a>
    </div>
    
    <?php if (empty($bids)): ?>
        <p style="text-align: center; padding: 3rem; color: var(--gray-600);">
            Et ole huutanut vielä mihinkään.
        </p>
    <?php else: ?>
        <div style="background: white; padding: 1.5rem; border-radius: 12px;">
            <?php foreach ($bids as $bid): ?>
                <div style="padding: 1rem; border-bottom: 1px solid var(--gray-200); display: flex; gap: 1rem; align-items: center;">
                    <img src="<?= htmlspecialchars($bid['image'] ?? '/assets/img/placeholder.jpg') ?>" 
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                    <div style="flex: 1;">
                        <a href="/kohde/<?= $bid['listing_id'] ?>/<?= $bid['slug'] ?>" style="font-weight: 600; font-size: 1.1rem;">
                            <?= htmlspecialchars($bid['title']) ?>
                        </a>
                        <div style="margin-top: 0.5rem; color: var(--gray-600);">
                            Huutosi: <strong><?= Security::formatPrice($bid['amount']) ?></strong>
                        </div>
                        <div style="margin-top: 0.25rem; color: var(--gray-600); font-size: 0.9rem;">
                            <?= date('d.m.Y H:i', strtotime($bid['created_at'])) ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <?php if ($bid['status'] == 'active'): ?>
                            <span class="badge <?= $bid['highest_bidder_id'] == $_SESSION['user_id'] ? 'badge-success' : 'badge-warning' ?>">
                                <?= $bid['highest_bidder_id'] == $_SESSION['user_id'] ? 'Johdossa' : 'Ei johdossa' ?>
                            </span>
                            <div style="font-size: 0.9rem; color: var(--gray-600); margin-top: 0.5rem;">
                                Nykyinen: <?= Security::formatPrice($bid['current_price']) ?>
                            </div>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= ucfirst($bid['status']) ?></span>
                        <?php endif; ?>
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
