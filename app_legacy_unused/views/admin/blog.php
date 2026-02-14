<?php
$pageTitle = 'Hallinnoi blogia';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>📰 Blogin hallinta</h1>
    
    <div style="margin: 1.5rem 0;">
        <a href="/admin" class="btn btn-secondary">« Takaisin admin-paneeliin</a>
    </div>
    
    <div style="background: white; padding: 1.5rem; border-radius: 12px;">
        <p style="text-align: center; padding: 2rem; color: var(--gray-600);">
            Blogin hallinta tulossa...
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
