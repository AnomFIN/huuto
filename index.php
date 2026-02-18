<?php
// Application bootstrap: load shared configuration, autoloading and common setup.
// Application bootstrap: load shared configuration, autoloading and common setup.
require_once __DIR__ . '/bootstrap.php';

$pageTitle = SITE_NAME . ' - Etusivu';

// Load categories from database
$categoryModel = new Category();
$categoriesFromDb = $categoryModel->getAllCategories();
$categories = array_map(function($cat) {
    return $cat['name'];
}, $categoriesFromDb);

$popularAuctions = [];
$closingSoonAuctions = [];
$dataLoadError = null;

try {
    $auctionModel = new Auction();
    $popularAuctions = $auctionModel->getPopularAuctions(120);
    $closingSoonAuctions = $auctionModel->getClosingSoonAuctions(120);
} catch (Exception $error) {
    error_log(json_encode([
        'event' => 'homepage_data_load_failed',
        'message' => $error->getMessage(),
    ], JSON_UNESCAPED_UNICODE));
    $dataLoadError = 'Tietojen lataaminen epäonnistui. Emme voineet ladata huutokohteita.';
}

// Normalize auction data for UI rendering
function normalizeAuctionForUi(array $auction): ?array
{
    // Skip auctions with invalid IDs
    if (!isset($auction['id']) || !is_numeric($auction['id']) || (int)$auction['id'] <= 0) {
        return null;
    }

    $title = trim((string) ($auction['title'] ?? 'Kohde'));
    $category = trim((string) ($auction['category_name'] ?? 'Muut'));
    $location = trim((string) ($auction['location'] ?? 'Ei sijaintia'));

    $endTimeRaw = isset($auction['end_time']) ? strtotime((string) $auction['end_time']) : false;
    // Skip auctions with invalid or past end times
    if (!$endTimeRaw || $endTimeRaw <= time()) {
        return null;
    }

    $priceNow = isset($auction['current_price']) ? (float) $auction['current_price'] : 0;
    $bidCount = isset($auction['bid_count']) ? (int) $auction['bid_count'] : 0;

    return [
        'id' => (int) $auction['id'],
        'title' => mb_substr($title !== '' ? $title : 'Kohde', 0, 80),
        'location' => mb_substr($location !== '' ? $location : 'Ei sijaintia', 0, 40),
        'category' => mb_substr($category !== '' ? $category : 'Muut', 0, 40),
        'endTime' => gmdate('c', $endTimeRaw),
        'priceNow' => round(max(0, $priceNow), 2),
        'bidsCount' => max(0, $bidCount),
        'minIncrement' => (float) (($priceNow >= 1000) ? 20 : (($priceNow >= 200) ? 10 : 5)),
        'isAd' => false,
        'imageLabel' => mb_substr($title !== '' ? $title : 'Huuto247', 0, 24),
        'seller' => htmlspecialchars($auction['seller_username'] ?? 'Myyjä', ENT_QUOTES, 'UTF-8'),
    ];
}

function buildUiData(array $source): array
{
    $items = [];
    foreach ($source as $auction) {
        if (!is_array($auction)) {
            continue;
        }
        $normalized = normalizeAuctionForUi($auction);
        if ($normalized !== null) {
            $items[] = $normalized;
        }
    }
    return $items;
}

$popularUiData = buildUiData($popularAuctions);
$closingUiData = buildUiData($closingSoonAuctions);
$isUserLoggedIn = is_logged_in();
?>
<!doctype html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/homepage.css">
</head>
<body>
<div class="top-bar">
    <div class="container top-bar-inner">
        <a href="#">Myy yrityksesi varasto tehokkaasti!</a>
        <a href="#">Löydä todellisia löytöjä</a>
    </div>
</div>

<header class="site-header" id="site-header">
    <div class="container header-inner">
        <a class="logo" href="/">HUUTO247 <span>.fi</span></a>

        <div class="search-wrap" role="search">
            <select id="search-category" class="search-category" aria-label="Hakukategoria">
                <option value="ALL">Kaikki kategoriat</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="search-input-wrap">
                <span class="search-icon">⌕</span>
                <input id="search-input" type="search" autocomplete="off" placeholder="Mitä etsit?" aria-label="Hae kohteita">
                <button id="search-clear" class="search-clear" type="button" aria-label="Tyhjennä haku">×</button>
                <span class="search-hint">Enter ↵</span>
            </div>
            <button id="search-submit" class="search-btn" type="button">Hae</button>
        </div>

        <div class="header-actions">
            <button class="icon-btn" aria-label="Suosikit" title="Suosikit"><svg viewBox="0 0 24 24" aria-hidden="true"><title>Suosikit</title><path d="M12 21s-6.7-4.4-9.3-8c-2.2-3 0-8 4.6-8A5 5 0 0 1 12 7.5 5 5 0 0 1 16.7 5c4.6 0 6.8 5 4.6 8-2.6 3.6-9.3 8-9.3 8z"/></svg></button>
            <button class="icon-btn" aria-label="Seuranta" title="Seuranta"><svg viewBox="0 0 24 24" aria-hidden="true"><title>Seuranta</title><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 5h-2v6l5 3 1-1.7-4-2.3z"/></svg></button>
            <button class="icon-btn" aria-label="Omat huudot" title="Omat huudot"><svg viewBox="0 0 24 24" aria-hidden="true"><title>Omat huudot</title><path d="m2 22 6-6 2 2-4 4h16v2H2zm11-20 7 7-1.5 1.5-1.4-1.4-3.6 3.6-4-4L6 12.4 4.6 11 8 7.6l4 4 2.2-2.2-1.4-1.4z"/></svg></button>
            <a id="auth-action" class="link-btn" href="<?php echo $isUserLoggedIn ? '/auth/logout.php' : '/auth/login.php'; ?>"><?php echo $isUserLoggedIn ? 'Kirjaudu ulos' : 'Kirjaudu sisään'; ?></a>
            <a href="/auth/register.php" class="ghost-btn">Rekisteröidy</a>
        </div>
    </div>
</header>

<main>
    <section class="container hero-shell">
        <?php if ($dataLoadError !== null): ?>
            <div class="notice-toast" role="status"><span>ℹ</span><p><?php echo htmlspecialchars($dataLoadError, ENT_QUOTES, 'UTF-8'); ?></p></div>
        <?php endif; ?>

        <section class="hero-grid">
            <article class="panel info-box">
                <h1>Tervetuloa Huutokauppaan!</h1>
                <p id="rotating-slogan">Suomen näyttävin ja tehokkain huutokauppa.</p>
                <div class="trust-pills"><span>5M+ vierailua/kk</span><span>Suomalainen palvelu</span><span>Turvalliset maksut</span></div>
                <div class="cta-row"><button class="primary-btn">Ilmoita kohde</button><button class="secondary-btn">Ensihuutajan etu</button></div>
            </article>

            <section class="panel carousel-panel" aria-label="Sulkeutuu pian">
                <div class="urgency">SULKEUTUU PIAN, TOIMI NYT!</div>
                <div class="carousel-progress"><span id="carousel-progress"></span></div>
                <div id="carousel-track" class="carousel-track"></div>
                <div class="carousel-controls"><button id="carousel-prev" aria-label="Edellinen kohde">←</button><div id="carousel-dots" class="carousel-dots"></div><button id="carousel-next" aria-label="Seuraava kohde">→</button></div>
            </section>

            <aside class="panel categories" id="categories">
                <h2>Kategoriat <span class="cat-all-pill">Kaikki kategoriat</span></h2>
                <ul id="category-list">
                    <li><button class="active" data-category="ALL">Kaikki kategoriat <span>›</span></button></li>
                    <?php foreach ($categories as $category): ?>
                        <li><button data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?> <span>›</span></button></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </section>
    </section>

    <section class="container listings-wrap" id="popular-section">
        <div class="section-head"><div><h2>Suositut</h2><p>Laatukohteet juuri nyt aktiivisimmilta myyjiltä.</p></div><a href="#" class="show-all">Näytä kaikki →</a></div>
        <div class="section-pills"><button class="active" data-section-filter="ALL">Kaikki</button><button data-section-filter="Ajoneuvot">Ajoneuvot</button><button data-section-filter="Työkoneet">Työkoneet</button><button data-section-filter="Elektroniikka">Elektroniikka</button></div>
        <div id="popular-grid" class="listing-grid"></div>
        <button id="popular-load" class="load-more" type="button"><span>Lataa lisää kohteita</span></button>
        <p id="popular-end" class="end-note" hidden>Kokeile hakua tai vaihda kategoriaa.</p>
    </section>

    <section class="container listings-wrap tint" id="closing-section">
        <div class="section-head"><div><h2>Sulkeutuu pian</h2><p>Seuraavat päättyvät kohteet — toimi ajoissa.</p></div><a href="#" class="show-all">Näytä kaikki →</a></div>
        <div class="section-pills"><button class="active" data-section-filter="ALL">Kaikki</button><button data-section-filter="Ajoneuvot">Ajoneuvot</button><button data-section-filter="Työkoneet">Työkoneet</button><button data-section-filter="Elektroniikka">Elektroniikka</button></div>
        <div id="closing-grid" class="listing-grid"></div>
        <button id="closing-load" class="load-more" type="button"><span>Lataa lisää kohteita</span></button>
        <p id="closing-end" class="end-note" hidden>Kokeile hakua tai vaihda kategoriaa.</p>
    </section>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <section><h3><span class="footer-mark"></span>Huuto247.fi</h3><p>Täysin suomalainen palvelu, jonka tuottaa Lahen huutokaupat Oy</p><p>Yli viisi miljoonaa vierailua kuukaudessa.</p><div class="social-row"><a href="#">Youtube</a><a href="#">Instagram</a><a href="#">Facebook</a></div></section>
        <section><h4>Tietoa palvelusta</h4><a href="#">Tietoa huutajalle</a><a href="#">Palvelun käyttöehdot</a><a href="#">Aloita myyminen</a><a href="#">Huutokaupat.com-myyntiehdot</a><a href="#">Hinnasto</a><a href="#">Maksutavat</a></section>
        <section><h4>Olemme apunasi</h4><a href="#">Asiakaspalvelu</a><a href="#">Ohjeet ja vinkit</a><a href="#">Tilaa uutiskirje</a><a href="#">Blogi</a><a href="#">Kampanjat</a></section>
        <section><h4>Yritys</h4><a href="#">Tietoa meistä</a><a href="#">Lahen huutokauppa</a><a href="#">Meille töihin</a><a href="#">Medialle</a><a href="#">Tietosuojaseloste</a><a href="#">Evästeasetukset</a><a href="#">Läpinäkyvyysraportointi</a><a href="#">Saavutettavuusseloste</a></section>
    </div>
    <div class="container footer-bottom"><span>© 2026 Huuto247.fi</span><button class="cookie-pill" type="button">Evästeasetukset</button></div>
</footer>

<div class="modal-backdrop" id="auth-modal" aria-hidden="true"><div class="modal" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title"><h3 id="auth-modal-title">Kirjaudu sisään lisätäksesi kohde suosikkeihin!</h3><p>Kirjautuminen avaa suosikit ja personoidut ilmoitukset.</p><div class="modal-actions"><button id="confirm-login" class="primary-btn" type="button">Kirjaudu</button><button id="cancel-login" class="secondary-btn" type="button">Peruuta</button></div></div></div>

<div class="modal-backdrop" id="item-modal" aria-hidden="true"><div class="modal item-modal" role="dialog" aria-modal="true" aria-labelledby="item-modal-title"><div id="item-modal-image" class="item-modal-image"></div><h3 id="item-modal-title"></h3><p id="item-modal-meta"></p><p id="item-modal-price" class="item-modal-price"></p><p id="item-modal-detail" class="item-modal-detail"></p><div class="modal-actions"><a id="item-view-link" href="#" class="primary-btn">Näytä kokonaan</a><button id="item-modal-close" class="secondary-btn" type="button">Sulje</button></div></div></div>

<script>
window.__HOME_DATA__ = {
    categories: <?php echo json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
    popular: <?php echo json_encode($popularUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
    closing: <?php echo json_encode($closingUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
    isLoggedIn: <?php echo $isUserLoggedIn ? 'true' : 'false'; ?>,
};
</script>
<script src="/assets/js/homepage.js" defer></script>
</body>
</html>
