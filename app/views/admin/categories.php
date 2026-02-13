<?php
$pageTitle = 'Hallinnoi kategorioita';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>📂 Kategorioiden hallinta</h1>
    
    <div style="margin: 1.5rem 0;">
        <a href="/admin" class="btn btn-secondary">« Takaisin admin-paneeliin</a>
    </div>
    
    <div style="background: white; padding: 1.5rem; border-radius: 12px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--gray-300);">
                    <th style="padding: 0.75rem; text-align: left;">ID</th>
                    <th style="padding: 0.75rem; text-align: left;">Ikoni</th>
                    <th style="padding: 0.75rem; text-align: left;">Nimi</th>
                    <th style="padding: 0.75rem; text-align: left;">Slug</th>
                    <th style="padding: 0.75rem; text-align: left;">Järjestys</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr style="border-bottom: 1px solid var(--gray-200);">
                        <td style="padding: 0.75rem;"><?= $category['id'] ?></td>
                        <td style="padding: 0.75rem; font-size: 1.5rem;"><?= $category['icon'] ?></td>
                        <td style="padding: 0.75rem;"><?= htmlspecialchars($category['name']) ?></td>
                        <td style="padding: 0.75rem; font-family: monospace; color: var(--gray-600);"><?= htmlspecialchars($category['slug']) ?></td>
                        <td style="padding: 0.75rem;"><?= $category['sort_order'] ?></td>
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
