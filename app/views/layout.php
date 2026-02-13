<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $metaDescription ?? 'Huuto - Suomalainen huutokauppa-alusta' ?>">
    <title><?= $pageTitle ?? 'Huuto' ?> - Huutokaupat</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Cookie Banner -->
    <div id="cookie-banner" class="cookie-banner">
        <div class="cookie-content">
            <div class="cookie-text">
                <strong>Evästeet</strong><br>
                Käytämme evästeitä sivuston toiminnan varmistamiseen ja käyttökokemuksen parantamiseen.
            </div>
            <div class="cookie-buttons">
                <button id="accept-cookies" class="btn btn-primary">Hyväksy</button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container header-content">
            <a href="/" class="logo">🎯 Huuto</a>
            
            <form action="/haku" method="GET" class="search-bar">
                <input type="search" name="q" placeholder="Etsi ilmoituksia..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit">Hae</button>
            </form>
            
            <nav class="header-nav">
                <?php if (Security::isLoggedIn()): ?>
                    <a href="/profiili">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
                    <a href="/luo-ilmoitus" class="btn btn-success">+ Luo ilmoitus</a>
                    <?php if (Security::isAdmin()): ?>
                        <a href="/admin">Admin</a>
                    <?php endif; ?>
                    <a href="/kirjaudu-ulos">Kirjaudu ulos</a>
                <?php else: ?>
                    <a href="/kirjaudu">Kirjaudu</a>
                    <a href="/rekisteroidy" class="btn btn-primary">Rekisteröidy</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Category Navigation -->
    <nav class="category-nav">
        <div class="container">
            <ul>
                <li><a href="/kategoriat">📂 Kaikki kategoriat</a></li>
                <li><a href="/paattyvat">⏰ Päättyvät pian</a></li>
                <li><a href="/haku">🔍 Haku</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="container" style="margin-top: 1rem;">
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="container" style="margin-top: 1rem;">
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                </div>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h4>Tietoa</h4>
                    <ul>
                        <li><a href="/sivu/tietoa-palvelusta">Tietoa palvelusta</a></li>
                        <li><a href="/sivu/yritys">Yritys</a></li>
                        <li><a href="/sivu/tyopaikat">Meille töihin</a></li>
                        <li><a href="/sivu/media">Medialle</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Ostajalle</h4>
                    <ul>
                        <li><a href="/sivu/tietoa-huutajalle">Tietoa huutajalle</a></li>
                        <li><a href="/sivu/ohjeet">Ohjeet ja vinkit</a></li>
                        <li><a href="/sivu/maksutavat">Maksutavat</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Myyjälle</h4>
                    <ul>
                        <li><a href="/sivu/aloita-myyminen">Aloita myyminen</a></li>
                        <li><a href="/sivu/myyntiehdot">Myyntiehdot</a></li>
                        <li><a href="/sivu/hinnasto">Hinnasto</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Asiakaspalvelu</h4>
                    <ul>
                        <li><a href="/sivu/asiakaspalvelu">Ota yhteyttä</a></li>
                        <li><a href="/sivu/kayttoehdot">Käyttöehdot</a></li>
                        <li><a href="/sivu/tietosuoja">Tietosuojaseloste</a></li>
                        <li><a href="/sivu/saavutettavuus">Saavutettavuus</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Huuto. Kaikki oikeudet pidätetään.</p>
            </div>
        </div>
    </footer>

    <script src="/assets/js/main.js"></script>
</body>
</html>
