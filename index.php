<?php
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
$favoriteIds = [];
$displayFirstName = '';

try {
    $auctionModel = new Auction();
    $popularAuctions = $auctionModel->getPopularAuctions(120);
    $closingSoonAuctions = $auctionModel->getClosingSoonAuctions(120);
    $featuredAuctions = $auctionModel->getFeaturedAuctions(8);
} catch (Exception $error) {
    error_log(json_encode([
        'event' => 'homepage_data_load_failed',
        'message' => $error->getMessage(),
    ], JSON_UNESCAPED_UNICODE));
    $dataLoadError = 'Tietojen lataaminen epäonnistui. Emme voineet ladata huutokohteita.';
}

// Sanitize user-generated content to prevent XSS attacks
function sanitizeUserContent(string $content): string
{
  $normalized = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $normalized = strip_tags($normalized);
  $normalized = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $normalized) ?? $normalized;
  $normalized = preg_replace('/(?:\"|\')?\s*\/?\s*>+/u', ' ', $normalized) ?? $normalized;
  $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? $normalized;

  return trim($normalized);
}

function normalizeImagePath(?string $imagePath): ?string
{
  if ($imagePath === null) {
    return null;
  }

  $trimmed = trim($imagePath);
  if ($trimmed === '') {
    return null;
  }

  if (preg_match('#^https?://#i', $trimmed)) {
    return $trimmed;
  }

  if ($trimmed[0] !== '/') {
    $trimmed = '/' . $trimmed;
  }

  $parts = explode('/', $trimmed);
  $encodedParts = array_map(static function ($segment) {
    return rawurlencode(rawurldecode($segment));
  }, $parts);

  return implode('/', $encodedParts);
}

function toListingImagePath(?string $imagePath): ?string
{
  if ($imagePath === null) {
    return null;
  }

  $trimmed = trim($imagePath);
  if ($trimmed === '') {
    return null;
  }

  if (preg_match('#_wm\.(jpg|jpeg|png|webp|gif)$#i', $trimmed)) {
    return preg_replace('#_wm\.(jpg|jpeg|png|webp|gif)$#i', '_min.$1', $trimmed);
  }

  return $trimmed;
}

// Beyond algorithms. Into outcomes.
function normalizeAuctionForUi(array $auction): ?array
{
  global $categories;

  // Keep values as plain text here; escaping is handled in app.js rendering.
  $title = isset($auction['title']) ? trim((string) $auction['title']) : 'Kohde';
  $location = isset($auction['location']) ? trim((string) $auction['location']) : 'Helsinki';
  if (isset($auction['category_name'])) {
    $category = trim((string) $auction['category_name']);
  } else {
    $category = !empty($categories) ? (string)$categories[array_rand($categories)] : 'Muut';
  }

    $endTimeRaw = isset($auction['end_time']) ? strtotime((string) $auction['end_time']) : false;
    // Skip auctions with invalid or past end times
    if (!$endTimeRaw || $endTimeRaw <= time()) {
        return null;
    }

    // Determine current price, falling back to starting_price if needed.
    $priceNow = null;
    if (isset($auction['current_price']) && is_numeric($auction['current_price'])) {
        $priceNow = (float) $auction['current_price'];
    } elseif (isset($auction['starting_price']) && is_numeric($auction['starting_price'])) {
        $priceNow = (float) $auction['starting_price'];
    }

    // Skip auctions without any valid price information
    if ($priceNow === null) {
        return null;
    }
    $bidCount = isset($auction['bid_count']) ? (int) $auction['bid_count'] : 0;
    
    // Handle auction image - use primary image from database
    $imageUrl = normalizeImagePath(toListingImagePath($auction['primary_image'] ?? null));

    return [
        'id' => (int) $auction['id'],
        'title' => mb_substr(sanitizeUserContent($title !== '' ? $title : 'Kohde'), 0, 120),
        'location' => mb_substr(sanitizeUserContent($location !== '' ? $location : 'Ei sijaintia'), 0, 80),
        'category' => mb_substr(sanitizeUserContent($category !== '' ? $category : 'Muut'), 0, 40),
        'endTime' => date('c', $endTimeRaw),
        'priceNow' => round(max(0, $priceNow), 2),
        'startingPrice' => isset($auction['starting_price']) ? round((float)$auction['starting_price'], 2) : round(max(0, $priceNow), 2),
        'buyNowPrice' => isset($auction['buy_now_price']) && $auction['buy_now_price'] !== null ? round((float)$auction['buy_now_price'], 2) : null,
        'bidsCount' => max(0, $bidCount),
        'minIncrement' => (float) (($priceNow >= 1000) ? 20 : (($priceNow >= 200) ? 10 : 5)),
        'isAd' => false,
        'imageLabel' => mb_substr($title !== '' ? $title : 'Huuto247', 0, 24),
        'seller' => 'Verified-myyjä', // Hardcoded trusted value, not from database
        'delivery' => 'Nouto / Toimitus',
        'imageUrl' => $imageUrl, // Real image from database
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
$featuredUiData = buildUiData($featuredAuctions);
$isUserLoggedIn = is_logged_in();

if ($isUserLoggedIn && function_exists('current_user')) {
  $currentUserData = current_user();
  $rawUserName = trim((string)($currentUserData['full_name'] ?? $currentUserData['username'] ?? ''));
  if ($rawUserName !== '') {
    $displayFirstName = explode(' ', $rawUserName)[0];
  }

  try {
    $db = Database::getInstance()->getConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS user_favourites (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      auction_id INT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY uq_user_auction (user_id, auction_id),
      INDEX idx_user (user_id),
      INDEX idx_auction (auction_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $favoriteStmt = $db->prepare('SELECT auction_id FROM user_favourites WHERE user_id = ?');
    $favoriteStmt->execute([(int)current_user_id()]);
    $favoriteIds = array_map('intval', $favoriteStmt->fetchAll(PDO::FETCH_COLUMN));
  } catch (Throwable $exception) {
    $favoriteIds = [];
  }
}

$metaTitle = $pageTitle;
$metaDescription = 'Huuto247 (huuto247.fi) etusivu: selaa suosittuja huutokauppoja, sulkeutuvia kohteita ja löydä parhaat huudot nopeasti.';
$metaKeywords = 'Huuto247, huuto247.fi, huutokauppa, verkkohuutokauppa, myy huutokaupassa, osta huutokaupasta';
$metaRobots = 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';
$canonicalUrl = rtrim((string)BASE_URL, '/') . '/index.php';
$ogImage = rtrim((string)BASE_URL, '/') . '/assets/logo.png';
?>
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="robots" content="<?php echo htmlspecialchars($metaRobots, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="author" content="Lahen Huutokaupat Oy" />
    <meta name="application-name" content="Huuto247" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="fi_FI" />
    <meta property="og:site_name" content="Huuto247" />
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>" />
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>" />
    <link rel="alternate" hreflang="fi" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon16x.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon32x.png" />
    <link rel="shortcut icon" href="/assets/favicon32x.png" type="image/png" />
    <link rel="apple-touch-icon" href="/assets/favicon32x.png" />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="top-bar" role="note" aria-label="Pikatiedotteet">
      <a href="/info.php?page=myyminen">Myy yrityksesi varasto tehokkaasti!</a>
      <a href="/info.php?page=ohjeet">Löydä todellisia löytöjä</a>
    </div>

    <header class="site-header" id="siteHeader">
      <div class="header-row container">
        <a class="logo" href="index.php" aria-label="Huuto247 etusivu">
          <span class="logo-mark" aria-hidden="true"></span>
          <span>HUUTO247<span class="logo-dot">.fi</span></span>
        </a>

        <div class="lang-wrap">
          <button id="langToggle" class="lang-btn" aria-label="Vaihda kieli" aria-expanded="false" aria-controls="langMenu">🌐 FI ▾</button>
          <ul id="langMenu" class="lang-menu" role="menu" aria-label="Kielivalinta">
            <li><button role="menuitem">FI</button></li>
            <li><button role="menuitem">EN</button></li>
            <li><button role="menuitem">SV</button></li>
          </ul>
        </div>

        <form id="searchForm" class="search-shell" role="search" aria-label="Kohteiden haku">
          <label class="sr-only" for="searchCategory">Kategoria</label>
          <select id="searchCategory">
            <option value="">Kaikki kategoriat</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
          </select>
          <div class="search-field-wrap">
            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.5 4a6.5 6.5 0 1 1 0 13 6.5 6.5 0 0 1 0-13Zm0 1.8a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4Zm5.87 10.6 3.2 3.2-1.28 1.28-3.2-3.2 1.28-1.28Z"/></svg>
            <input id="searchInput" type="search" placeholder="Mitä etsit?" autocomplete="off" />
            <button type="button" id="clearSearch" class="clear-btn" aria-label="Tyhjennä haku">✕</button>
            <span id="searchHint" class="search-hint">Paina Enter hakeaksesi</span>
          </div>
          <button type="submit" class="search-submit">Hae</button>
        </form>

        <nav class="header-links" aria-label="Pikalinkit">
          <a href="/category.php">Kategoriat</a>
          <a href="/category.php?closing_soon=1">Sulkeutuu pian</a>
          <a class="icon-pill" href="/category.php?tab=favorites" aria-label="Suosikit">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.2 4.65 12.9a4.6 4.6 0 1 1 6.5-6.5L12 7.25l.85-.85a4.6 4.6 0 1 1 6.5 6.5L12 20.2Z"/></svg>
          </a>
          <a class="icon-pill" href="/category.php?tab=watching" aria-label="Seuranta">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Zm10 3.8a3.8 3.8 0 1 0 0-7.6 3.8 3.8 0 0 0 0 7.6Z"/></svg>
          </a>
          <a class="icon-pill" href="/category.php?tab=my-bids" aria-label="Omat huudot">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6.5h16v11H4v-11Zm2 2v7h12v-7H6Zm2.5 10h7v1.8h-7V18.5Z"/></svg>
          </a>
          <?php if ($isUserLoggedIn): ?>
            <span id="loginLink" class="user-greeting">Hei, <?php echo htmlspecialchars($displayFirstName !== '' ? $displayFirstName : 'Käyttäjä', ENT_QUOTES, 'UTF-8'); ?>!</span>
          <?php else: ?>
            <a href="/auth/login.php" id="loginLink">Kirjaudu sisään</a>
            <a href="/auth/register.php" id="registerLink" class="register-pill">Rekisteröidy</a>
          <?php endif; ?>
        </nav>
      </div>
    </header>

    <main>
      <?php if ($dataLoadError !== null): ?>
        <div class="notice-toast container" role="status">
          <span>ℹ</span>
          <p><?php echo htmlspecialchars($dataLoadError, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
      <?php endif; ?>

      <section class="hero-premium container">
        <div class="hero-main">
          <div class="hero-content">
            <div class="hero-eyebrow">SUOMEN JOHTAVA HUUTOKAUPPAPALVELU</div>
            <h1 class="hero-title">Löydä ainutlaatuisia löytöjä<br><span class="hero-title-accent">Suomen suurimmasta huutokaupasta</span></h1>
            <p class="hero-subtitle">Yli 50 000 aktiivista kohdetta, miljoona kätevää tarjoajaa ja Suomen luotettavin huutokauppayhteisö. Aloita huutaminen jo tänään.</p>
            
            <div class="hero-stats">
              <div class="hero-stat">
                <div class="hero-stat-number">5M+</div>
                <div class="hero-stat-label">Vierailua kuukaudessa</div>
              </div>
              <div class="hero-stat">
                <div class="hero-stat-number">50K+</div>
                <div class="hero-stat-label">Aktiivista kohdetta</div>
              </div>
              <div class="hero-stat">
                <div class="hero-stat-number">98.8%</div>
                <div class="hero-stat-label">Onnistuneet kaupat</div>
              </div>
            </div>

            <div class="hero-actions">
              <a href="add_product.php" class="btn-hero-primary">
                <svg class="btn-icon" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>
                Myy kohteesi
              </a>
              <button class="btn-hero-secondary" data-action="browse-categories">
                <svg class="btn-icon" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Selaa kohteita
              </button>
            </div>

            <div class="hero-trust">
              <div class="hero-trust-item">
                <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Turvallinen maksaminen</span>
              </div>
              <div class="hero-trust-item">
                <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Varmennetut myyjät</span>
              </div>
              <div class="hero-trust-item">
                <svg viewBox="0 0 24 24"><path d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l-3-9m3 9l3-9"/></svg>
                <span>Suomalainen palvelu</span>
              </div>
            </div>
          </div>
        </div>

        <div class="hero-visual">
          <div class="hero-carousel-container">
            <div class="hero-carousel-header">
              <div class="hero-carousel-title">
                <span class="pulse-dot"></span>
                Sulkeutuu pian - Toimi nopeasti!
              </div>
              <div class="hero-carousel-stats">
                <span id="liveAuctionCount"><?php echo count($closingUiData); ?></span> aktiiivista huutokauppaa
              </div>
            </div>
            
            <div class="hero-carousel" id="heroCarousel">
              <div class="carousel-track" id="carouselTrack"></div>
              <div class="carousel-nav">
                <button class="carousel-arrow carousel-prev" id="carouselPrev" aria-label="Edellinen">
                  <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button class="carousel-arrow carousel-next" id="carouselNext" aria-label="Seuraava">
                  <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </button>
              </div>
              <div class="carousel-dots" id="carouselDots"></div>
            </div>
          </div>
        </div>
      </section>

      <?php if (!empty($featuredUiData)): ?>
      <section class="featured-section">
        <div class="container">
          <div class="section-header-premium">
            <div class="section-badge">Toimituksen valinta</div>
            <h2 class="section-title">Viikonlopun parhaat löydöt</h2>
            <p class="section-subtitle">Asiantuntijamme ovat valikoineet nämä ainutlaatuiset kohteet</p>
          </div>
          
          <div class="featured-grid" id="featuredGrid">
            <!-- Populated by JavaScript -->
          </div>
          
          <div class="featured-footer">
            <a href="/category.php?featured=1" class="btn-outline">Näytä kaikki valinnat</a>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <section class="trust-section">
        <div class="container">
          <div class="trust-content">
            <div class="trust-main">
              <h2>Miksi huutokaupat toimivat?</h2>
              <p>Huutokauppamalli varmistaa, että saat parhaan mahdollisen hinnan sekä myyjänä että ostajana. Kilpailutus nostaa arvot oikealle tasolle ja avoin tarjoushistoria pitää kaupankäynnin reiluna.</p>
            </div>
            
            <div class="trust-features">
              <div class="trust-feature">
                <div class="trust-feature-icon">
                  <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3>Turvalliset maksut</h3>
                <p>Kaikki maksut kulkevat varmennettujen palveluntarjoajien kautta. Rahat siirtyvät vain onnistuneiden kauppojen yhteydessä.</p>
              </div>
              
              <div class="trust-feature">
                <div class="trust-feature-icon">
                  <svg viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3>Läpinäkyvä tarjouskilpailu</h3>
                <p>Näet reaaliaikaisesti kaikki tarjoukset ja niiden historian. Ei piilotettuja maksuja tai yllätyskustannuksia.</p>
              </div>
              
              <div class="trust-feature">
                <div class="trust-feature-icon">
                  <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Varmennettu myyjäverkosto</h3>
                <p>Kaikki myyjät on tarkistettu ja arvioitu. Katso myyjäprofiilista historia, arvostelut ja luotettavuustiedot.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="cta-blocks">
        <div class="container">
          <div class="cta-blocks-grid">
            <div class="cta-block cta-buyer">
              <div class="cta-block-icon">🏆</div>
              <h3>Ostajalle</h3>
              <p>Löydä ainutlaatuisia löytöjä kilpailluilla hinnoilla. Yli 50 000 kohdetta joka päivä.</p>
              <ul class="cta-features">
                <li>Kilpailutetut hinnat</li>
                <li>Etsi merkin tai kategorian mukaan</li>
                <li>Huutohälytykset ja seuranta</li>
                <li>Turvallinen maksaminen</li>
              </ul>
              <a href="/category.php" class="btn-cta">Aloita ostaminen</a>
            </div>
            
            <div class="cta-block cta-seller">
              <div class="cta-block-icon">💰</div>
              <h3>Myyjälle</h3>
              <p>Saa kohteistasi paras mahdollinen hinta miljoonan huutajan markkinapaikassa.</p>
              <ul class="cta-features">
                <li>Kilpailutus nostaa hintoja</li>
                <li>Maksimoi tuloksesi AI-työkauilla</li>
                <li>Nopea ja helppo listaaminen</li>
                <li>Pro-myyjätyökalut yrityksille</li>
              </ul>
              <a href="/add_product.php" class="btn-cta">Aloita myyminen</a>
            </div>
          </div>
        </div>
      </section>

      <section class="hero container">
        <!-- Premium Category Navigation -->
        <section class="premium-categories" id="heroCategories">
          <div class="categories-header">
            <h2>Selaa kategorioita</h2>
            <p>Löydä juuri sinun tarpeisiisi sopivat kohteet</p>
          </div>
          <div class="categories-grid" id="categoryGrid">
            <?php 
            $categoryIcons = [
              'Ajoneuvot' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>',
              'Työkoneet' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>',
              'Asunnot' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>',
              'Vapaa-aika' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
              'Piha' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
              'Työkalut' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>',
              'Rakennus' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 2l2 6h8l2-6H6zm3 7L7 19h2l.67-2h4.66L15 19h2L15 9H9zm.67 2h2.66l.67 2H9.67l.67-2z"/></svg>',
              'Sisustus' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V6H1v15h2v-3h18v3h2V10c0-2.21-1.79-4-4-4z"/></svg>',
              'Elektroniikka' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 12H3V4h18v10z"/></svg>',
              'Keräily' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.17 6l2 2H20v10H4V6h5.17M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>',
              'Muut' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6,2A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2H6Z" /></svg>'
            ];
            
            foreach ($categories as $index => $category): 
              $icon = $categoryIcons[$category] ?? $categoryIcons['Muut'];
              $count = rand(15, 250); // Mockup count, replace with real count from database
            ?>
              <div class="category-card-premium" data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="category-icon">
                  <?php echo $icon; ?>
                </div>
                <div class="category-info">
                  <h3 class="category-name"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h3>
                  <div class="category-count"><?php echo $count; ?> kohdetta</div>
                </div>
                <div class="category-arrow">
                  <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <!-- Quick Access Links -->
          <div class="category-quick-access">
            <a href="/category.php?featured=1" class="quick-link featured">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.396-.756 1.456-.756 1.852 0l1.286 2.46a1 1 0 00.755.545l2.747.4c.847.123 1.185 1.164.571 1.76l-1.987 1.936a1 1 0 00-.287.884l.469 2.734c.145.845-.74 1.49-1.492 1.088l-2.456-1.29a1 1 0 00-.932 0l-2.456 1.29c-.752.402-1.637-.243-1.492-1.088l.469-2.734a1 1 0 00-.287-.884L2.615 7.892c-.614-.596-.276-1.637.571-1.76l2.747-.4a1 1 0 00.755-.545l1.286-2.46z"/>
              </svg>
              Suositut kohteet
            </a>
            <a href="/category.php?closing_soon=1" class="quick-link ending">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
              </svg>
              Päättyy pian
            </a>
            <a href="/category.php?new_listings=1" class="quick-link new">
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
              Uudet ilmoitukset
            </a>
          </div>
        </section>
      </section>

      <section class="listing-section section-tint" id="popularSection">
        <div class="container">
          <div class="section-head">
            <div>
              <h2>Suositut</h2>
              <div class="filter-pills" id="popularPills"></div>
            </div>
            <a href="/category.php" class="show-all">Näytä kaikki →</a>
          </div>
          <div class="auction-grid" id="popularGrid"></div>
          <button class="load-more" id="loadMorePopular">Lataa lisää kohteita</button>
          <p class="empty-tip" id="popularTip" hidden>Kokeile hakua tai vaihda kategoriaa.</p>
        </div>
      </section>

      <section class="listing-section" id="endingSoonSection">
        <div class="container">
          <div class="section-head">
            <div>
              <h2>Sulkeutuu pian</h2>
              <div class="filter-pills" id="endingPills"></div>
            </div>
            <a href="/category.php?closing_soon=1" class="show-all">Näytä kaikki →</a>
          </div>
          <div class="auction-grid" id="endingGrid"></div>
          <button class="load-more" id="loadMoreEnding">Lataa lisää kohteita</button>
          <p class="empty-tip" id="endingTip" hidden>Kokeile hakua tai vaihda kategoriaa.</p>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container footer-grid">
        <div>
          <div class="footer-brand"><span class="logo-mark" aria-hidden="true"></span>Huuto247.fi</div>
          <p>Täysin suomalainen palvelu, jonka tuottaa Lahen Huutokaupat Oy.</p>
          <p>Yli viisi miljoonaa vierailua kuukaudessa.</p>
          <div class="socials"><a href="/info.php?page=some">Youtube</a><a href="/info.php?page=some">Instagram</a><a href="/info.php?page=some">Facebook</a></div>
        </div>
        <div class="links-col" id="footerLinks"></div>
      </div>
      <div class="container footer-bottom">
        <small>© 2026 Huuto247.fi</small>
        <a class="cookie-pill" href="/info.php?page=evasteet">Evästeasetukset</a>
      </div>
    </footer>

    <dialog id="loginModal" class="modal">
      <form method="dialog" class="modal-card">
        <h3>Kirjaudu sisään</h3>
        <p>Kirjaudu sisään lisätäksesi kohde suosikkeihin!</p>
        <div class="modal-actions">
          <button id="simulateLogin" value="confirm" class="btn-primary">Simuloi kirjautuminen</button>
          <button value="cancel" class="btn-secondary">Peruuta</button>
        </div>
      </form>
    </dialog>

    <dialog id="benefitModal" class="modal">
      <form method="dialog" class="modal-card">
        <h3>Ensihuutajan etu</h3>
        <p>Huutohälytykset, suosikit ja pikahuuto yhdellä kirjautumisella.</p>
        <div class="modal-actions"><button value="confirm" class="btn-primary">Ymmärretty</button></div>
      </form>
    </dialog>

    <dialog id="itemModal" class="modal item-modal">
      <form method="dialog" class="modal-card" id="itemModalContent"></form>
    </dialog>

    <!-- Cookie Consent Popup -->
    <div id="cookieConsent" class="cookie-consent" style="display: none;">
      <div class="cookie-content">
        <div class="cookie-icon">🍪</div>
        <div class="cookie-text">
          <h4>Evästeet ja yksityisyys</h4>
          <p>Käytämme evästeitä parantaaksemme käyttökokemustasi ja analysoidaksemme sivuston käyttöä. Hyväksymällä annat meille luvan käyttää tarvittavia sekä vapaaehtoisia evästeitä.</p>
        </div>
        <div class="cookie-actions">
          <button id="acceptAllCookies" class="btn-primary">Hyväksy kaikki</button>
          <button id="acceptNecessary" class="btn-secondary">Vain välttämättömät</button>
          <button id="cookieSettings" class="btn-text">Muokkaa asetuksia</button>
        </div>
      </div>
    </div>

    <!-- Cookie Settings Modal -->
    <dialog id="cookieSettingsModal" class="modal">
      <div class="modal-card cookie-settings-modal">
        <h3>Evästeasetukset</h3>
        <div class="cookie-categories">
          <div class="cookie-category">
            <div class="cookie-category-header">
              <h4>Välttämättömät evästeet</h4>
              <span class="cookie-always-on">Aina päällä</span>
            </div>
            <p>Nämä evästeet ovat välttämättömiä sivuston perustoiminnoille, kuten kirjautumiselle ja ostoskorille.</p>
          </div>
          
          <div class="cookie-category">
            <div class="cookie-category-header">
              <h4>Analytiikka</h4>
              <label class="cookie-toggle">
                <input type="checkbox" id="analyticsToggle">
                <span class="toggle-slider"></span>
              </label>
            </div>
            <p>Auttaa meitä ymmärtämään, miten käytät sivustoa, jotta voimme parantaa käyttökokemusta.</p>
          </div>
          
          <div class="cookie-category">
            <div class="cookie-category-header">
              <h4>Markkinointi</h4>
              <label class="cookie-toggle">
                <input type="checkbox" id="marketingToggle">
                <span class="toggle-slider"></span>
              </label>
            </div>
            <p>Mahdollistaa kohdistetun mainonnan ja personoidun sisällön näyttämisen.</p>
          </div>
        </div>
        
        <div class="modal-actions">
          <button id="saveCookieSettings" class="btn-primary">Tallenna asetukset</button>
          <button id="closeCookieSettings" value="cancel" class="btn-secondary">Peruuta</button>
        </div>
      </div>
    </dialog>

    <script>
      window.__HOME_DATA__ = {
        categories: <?php echo json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        popular: <?php echo json_encode($popularUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        closing: <?php echo json_encode($closingUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        featured: <?php echo json_encode($featuredUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        isLoggedIn: <?php echo $isUserLoggedIn ? 'true' : 'false'; ?>,
        favoriteIds: <?php echo json_encode($favoriteIds, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
      };
    </script>
    <script src="app.js" defer></script>
    <script src="premium-ux.js" defer></script>
  </body>
</html>
