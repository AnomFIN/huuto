<?php
$pageTitle = 'Blogi';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>📰 Blogi</h1>
    
    <?php if (empty($posts)): ?>
        <p style="text-align: center; padding: 3rem; color: var(--gray-600);">Ei blogikirjoituksia.</p>
    <?php else: ?>
        <div style="display: grid; gap: 2rem; margin-top: 2rem;">
            <?php foreach ($posts as $post): ?>
                <div style="background: white; padding: 2rem; border-radius: 12px;">
                    <h2 style="margin-bottom: 1rem;">
                        <a href="/blogi/<?= $post['slug'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                    </h2>
                    <?php if ($post['excerpt']): ?>
                        <p style="color: var(--gray-600); margin-bottom: 1rem;"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <?php endif; ?>
                    <div style="font-size: 0.875rem; color: var(--gray-500);">
                        <?= date('d.m.Y', strtotime($post['published_at'])) ?>
                    </div>
                    <a href="/blogi/<?= $post['slug'] ?>" class="btn btn-primary" style="margin-top: 1rem;">Lue lisää →</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
