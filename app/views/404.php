<?php
$pageTitle = '404 - Sivua ei löytynyt';
ob_start();
?>

<div class="container" style="text-align: center; padding: 4rem 0;">
    <h1 style="font-size: 4rem; color: var(--gray-400);">404</h1>
    <h2 style="margin-bottom: 1rem;">Sivua ei löytynyt</h2>
    <p style="color: var(--gray-600); margin-bottom: 2rem;">Etsimääsi sivua ei löytynyt. Se on saatettu poistaa tai siirtää.</p>
    <a href="/" class="btn btn-primary">Palaa etusivulle</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
