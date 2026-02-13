<?php
$pageTitle = htmlspecialchars($page['title']);
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 12px;">
        <h1><?= htmlspecialchars($page['title']) ?></h1>
        <div style="margin-top: 2rem; line-height: 1.8;">
            <?= $page['content_html'] ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
