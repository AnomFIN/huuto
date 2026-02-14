<?php
$pageTitle = 'Hallinnoi ilmoituksia';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>📝 Ilmoitusten hallinta</h1>
    
    <div style="margin: 1.5rem 0;">
        <a href="/admin" class="btn btn-secondary">« Takaisin admin-paneeliin</a>
    </div>
    
    <div style="background: white; padding: 1.5rem; border-radius: 12px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--gray-300);">
                    <th style="padding: 0.75rem; text-align: left;">ID</th>
                    <th style="padding: 0.75rem; text-align: left;">Otsikko</th>
                    <th style="padding: 0.75rem; text-align: left;">Myyjä</th>
                    <th style="padding: 0.75rem; text-align: left;">Kategoria</th>
                    <th style="padding: 0.75rem; text-align: left;">Hinta</th>
                    <th style="padding: 0.75rem; text-align: left;">Status</th>
                    <th style="padding: 0.75rem; text-align: left;">Toiminnot</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $listing): ?>
                    <tr style="border-bottom: 1px solid var(--gray-200);">
                        <td style="padding: 0.75rem;"><?= $listing['id'] ?></td>
                        <td style="padding: 0.75rem;">
                            <a href="/kohde/<?= $listing['id'] ?>/<?= $listing['slug'] ?>">
                                <?= htmlspecialchars($listing['title']) ?>
                            </a>
                        </td>
                        <td style="padding: 0.75rem;"><?= htmlspecialchars($listing['user_name']) ?></td>
                        <td style="padding: 0.75rem;"><?= htmlspecialchars($listing['category_name']) ?></td>
                        <td style="padding: 0.75rem;"><?= Security::formatPrice($listing['current_price']) ?></td>
                        <td style="padding: 0.75rem;">
                            <span class="badge <?= $listing['status'] == 'active' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= htmlspecialchars($listing['status']) ?>
                            </span>
                        </td>
                        <td style="padding: 0.75rem;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
                                <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                                <button type="submit" name="action" value="archive" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                    Arkistoi
                                </button>
                                <button type="submit" name="action" value="delete" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;" data-confirm="Haluatko varmasti poistaa tämän ilmoituksen?">
                                    Poista
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
