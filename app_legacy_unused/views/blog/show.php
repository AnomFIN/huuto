<?php
$pageTitle = htmlspecialchars($post['title']);
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px;">
        <div class="breadcrumb">
            <a href="/">Etusivu</a> / 
            <a href="/blogi">Blogi</a> / 
            <?= htmlspecialchars($post['title']) ?>
        </div>
        
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        
        <div style="font-size: 0.875rem; color: var(--gray-500); margin: 1rem 0;">
            Julkaistu: <?= date('d.m.Y', strtotime($post['published_at'])) ?>
        </div>
        
        <div style="margin-top: 2rem; line-height: 1.8;">
            <?php
            // Note: content_html should only be editable by trusted admins
            // Consider adding HTML purification if user-generated content is allowed
            echo $post['content_html'];
            ?>
        </div>
        
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--gray-200);">
            <a href="/blogi" class="btn btn-secondary">« Takaisin blogiin</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
