<?php
$pageTitle = 'Profiili';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px;">
        <h1>👤 Profiili</h1>
        
        <div style="margin-top: 2rem; padding: 1.5rem; background: var(--gray-50); border-radius: 8px;">
            <p><strong>Nimi:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
            <p><strong>Sähköposti:</strong> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
            <p><strong>Rooli:</strong> <?= htmlspecialchars($_SESSION['user_role']) ?></p>
        </div>
        
        <div style="margin-top: 2rem; display: grid; gap: 1rem;">
            <a href="/omat-ilmoitukset" class="btn btn-primary">📝 Omat ilmoitukset</a>
            <a href="/omat-huudot" class="btn btn-primary">💰 Omat huudot</a>
            <a href="/omat-voitot" class="btn btn-success">🏆 Omat voitot</a>
            <a href="/luo-ilmoitus" class="btn btn-secondary">➕ Luo uusi ilmoitus</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
