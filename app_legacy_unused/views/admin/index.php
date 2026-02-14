<?php
$pageTitle = 'Admin';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>🔧 Admin Panel</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                <div style="font-size: 2rem; font-weight: bold; color: var(--primary);"><?= $stats['users'] ?></div>
                <div style="color: var(--gray-600);">Käyttäjät</div>
                <a href="/admin/kayttajat" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Hallinnoi</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
                <div style="font-size: 2rem; font-weight: bold; color: var(--primary);"><?= $stats['listings'] ?></div>
                <div style="color: var(--gray-600);">Aktiiviset ilmoitukset</div>
                <a href="/admin/ilmoitukset" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Hallinnoi</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
                <div style="font-size: 2rem; font-weight: bold; color: var(--primary);"><?= $stats['bids'] ?></div>
                <div style="color: var(--gray-600);">Huudot</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📂</div>
                <div style="font-size: 2rem; font-weight: bold; color: var(--primary);"><?= $stats['categories'] ?></div>
                <div style="color: var(--gray-600);">Kategoriat</div>
                <a href="/admin/kategoriat" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Hallinnoi</a>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 2rem;">
        <a href="/" class="btn btn-secondary">« Takaisin etusivulle</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
