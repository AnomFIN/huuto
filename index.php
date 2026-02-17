<?php
// Application bootstrap: load shared configuration, autoloading and common setup.
// Application bootstrap: load shared configuration, autoloading and common setup.
require_once __DIR__ . '/bootstrap.php';

$pageTitle = SITE_NAME . ' - Etusivu';
$categories = [
    'Ajoneuvot',
    'Työkoneet',
    'Asunnot',
    'Vapaa-aika',
    'Piha',
    'Työkalut',
    'Rakennus',
    'Sisustus',
    'Elektroniikka',
    'Keräily',
    'Muut',
];

$popularAuctions = [];
$closingSoonAuctions = [];
$dataLoadError = null;

try {
    $auctionModel = new Auction();
    $popularAuctions = $auctionModel->getPopularAuctions(120);
    $closingSoonAuctions = $auctionModel->getClosingSoonAuctions(120);
} catch (Throwable $error) {
    error_log(json_encode([
        'event' => 'homepage_data_load_failed',
        'message' => $error->getMessage(),
    ], JSON_UNESCAPED_UNICODE));
    $dataLoadError = 'Tietojen lataaminen epäonnistui. Näytämme väliaikaiset demo-kohteet.';
}

// Beyond algorithms. Into outcomes.
function normalizeAuctionForUi(array $auction, array $fallbackCategories): array
{
    // Sanitize user-generated content to prevent XSS attacks
    // Extract and sanitize title (only if from database)
    if (isset($auction['title'])) {
        $title = htmlspecialchars(trim((string) $auction['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    } else {
        $title = 'Kohde'; // Trusted default
    }
    
    // Extract and sanitize location (only if from database)
    if (isset($auction['location'])) {
        $location = htmlspecialchars(trim((string) $auction['location']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    } else {
        $location = 'Helsinki'; // Trusted default
    }
    
    // Extract and sanitize category (only if from database)
    if (isset($auction['category_name'])) {
        $category = htmlspecialchars(trim((string) $auction['category_name']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    } else {
        // $fallbackCategories contains only trusted hardcoded values (see $categories array at top of file)
        $category = $fallbackCategories[array_rand($fallbackCategories)]; // Trusted fallback
    }

    $endTimeRaw = isset($auction['end_time']) ? strtotime((string) $auction['end_time']) : false;
    $endTimestamp = ($endTimeRaw && $endTimeRaw > time()) ? $endTimeRaw : time() + random_int(3600, 60 * 60 * 24 * 6);

    $priceNow = isset($auction['current_price']) ? (float) $auction['current_price'] : (float) random_int(35, 3200);
    $bidCount = isset($auction['bid_count']) ? (int) $auction['bid_count'] : random_int(1, 31);

    return [
        'id' => (int) ($auction['id'] ?? random_int(10000, 99999)),
        'title' => mb_substr($title !== '' ? $title : 'Kohde', 0, 80),
        'location' => mb_substr($location !== '' ? $location : 'Helsinki', 0, 40),
        'category' => mb_substr($category !== '' ? $category : 'Muut', 0, 40),
        'endTime' => gmdate('c', $endTimestamp),
        'priceNow' => round(max(1, $priceNow), 2),
        'bidsCount' => max(0, $bidCount),
        'minIncrement' => (float) (($priceNow >= 1000) ? 20 : (($priceNow >= 200) ? 10 : 5)),
        'isAd' => false,
        'imageLabel' => mb_substr($title !== '' ? $title : 'Huuto247', 0, 24),
        'seller' => 'Verified-myyjä',
    ];
}

function buildUiData(array $source, array $categories, int $targetCount): array
{
    $items = [];
    foreach ($source as $auction) {
        if (count($items) >= $targetCount) {
            break;
        }
        if (!is_array($auction)) {
            continue;
        }
        $items[] = normalizeAuctionForUi($auction, $categories);
    }

    while (count($items) < $targetCount) {
        $seed = count($items) + 1;
        $price = random_int(40, 3600);
        $cities = ['Helsinki', 'Lahti', 'Tampere', 'Turku', 'Oulu', 'Jyväskylä'];
        $items[] = [
            'id' => 500000 + $seed,
            'title' => sprintf('Premium-kohde %02d', $seed),
            'location' => $cities[array_rand($cities)],
            'category' => $categories[array_rand($categories)],
            'endTime' => gmdate('c', time() + random_int(1200, 60 * 60 * 24 * 8)),
            'priceNow' => (float) $price,
            'bidsCount' => random_int(1, 41),
            'minIncrement' => (float) (($price >= 1000) ? 20 : (($price >= 200) ? 10 : 5)),
            'isAd' => false,
            'imageLabel' => 'Huuto247',
            'seller' => 'Premium Seller',
        ];
    }

    return $items;
}

$popularUiData = buildUiData($popularAuctions, $categories, 180);
$closingUiData = buildUiData($closingSoonAuctions, $categories, 180);
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
            <div class="language-switch" data-dropdown>
                <button class="language-btn" aria-expanded="false" aria-haspopup="menu" id="language-toggle">🌐 FI ▾</button>
                <div class="dropdown-menu" role="menu" aria-labelledby="language-toggle">
                    <button role="menuitem">FI</button>
                    <button role="menuitem">EN</button>
                    <button role="menuitem">SV</button>
                </div>
            </div>
            <button class="icon-btn" aria-label="Suosikit" title="Suosikit"><svg viewBox="0 0 24 24" aria-hidden="true"><title>Suosikit</title><path d="M12 21s-6.7-4.4-9.3-8c-2.2-3 0-8 4.6-8A5 5 0 0 1 12 7.5 5 5 0 0 1 16.7 5c4.6 0 6.8 5 4.6 8-2.6 3.6-9.3 8-9.3 8z"/></svg></button>
            <button class="icon-btn" aria-label="Seuranta" title="Seuranta"><svg viewBox="0 0 24 24" aria-hidden="true"><title>Seuranta</title><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 5h-2v6l5 3 1-1.7-4-2.3z"/></svg></button>
            <button class="icon-btn" aria-label="Omat huudot" title="Omat huudot"><svg viewBox="0 0 24 24" aria-hidden="true"><title>Omat huudot</title><path d="m2 22 6-6 2 2-4 4h16v2H2zm11-20 7 7-1.5 1.5-1.4-1.4-3.6 3.6-4-4L6 12.4 4.6 11 8 7.6l4 4 2.2-2.2-1.4-1.4z"/></svg></button>
            <a id="auth-action" class="link-btn" href="<?php echo $isUserLoggedIn ? '/auth/logout.php' : '/auth/login.php'; ?>"><?php echo $isUserLoggedIn ? 'Kirjaudu ulos' : 'Kirjaudu sisään'; ?></a>
            <button class="ghost-btn" type="button">Rekisteröidy</button>
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

<div class="modal-backdrop" id="item-modal" aria-hidden="true"><div class="modal item-modal" role="dialog" aria-modal="true" aria-labelledby="item-modal-title"><div id="item-modal-image" class="item-modal-image"></div><h3 id="item-modal-title"></h3><p id="item-modal-meta"></p><p id="item-modal-price" class="item-modal-price"></p><p id="item-modal-detail" class="item-modal-detail"></p><div class="modal-actions"><button id="item-bid-btn" class="primary-btn" type="button">Huutaa nyt</button><button id="item-modal-close" class="secondary-btn" type="button">Sulje</button></div></div></div>

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
