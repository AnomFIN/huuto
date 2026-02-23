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

      <section class="hero container">
        <article class="intro-card">
          <p class="eyebrow">MODERNI SUOMALAINEN MARKKINAPAIKKA</p>
          <h1>Tervetuloa Huutokauppaan!</h1>
          <p id="rotatingSlogan" class="slogan" aria-live="polite"></p>
          <div class="cta-row">
            <a href="add_product.php" class="btn-primary">Ilmoita kohde</a>
            <button class="btn-secondary" data-action="open-benefit">Ensihuutajan etu</button>
          </div>
          <div class="trust-pills">
            <span>5M+ vierailua/kk</span>
            <span>Suomalainen palvelu</span>
            <span>Turvalliset maksut</span>
          </div>
        </article>

        <article class="carousel-card" aria-label="Sulkeutuu pian karuselli">
          <div class="urgent-badge">SULKEUTUU PIAN, TOIMI NYT!</div>
          <div class="carousel-progress"><span id="carouselProgress"></span></div>
          <div class="carousel-wrap">
            <button class="arrow" id="carouselPrev" aria-label="Edellinen">←</button>
            <div class="carousel-track" id="carouselTrack"></div>
            <button class="arrow" id="carouselNext" aria-label="Seuraava">→</button>
          </div>
          <div class="dots" id="carouselDots"></div>
        </article>

        <aside class="category-card" id="heroCategories">
          <div class="category-head">Kaikki kategoriat</div>
          <ul id="categoryList">
            <?php foreach ($categories as $category): ?>
              <li><button data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?> <span>›</span></button></li>
            <?php endforeach; ?>
          </ul>
        </aside>
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

    <script>
      window.__HOME_DATA__ = {
        categories: <?php echo json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        popular: <?php echo json_encode($popularUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        closing: <?php echo json_encode($closingUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        isLoggedIn: <?php echo $isUserLoggedIn ? 'true' : 'false'; ?>,
        favoriteIds: <?php echo json_encode($favoriteIds, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
      };
    </script>
    <script src="app.js" defer></script>
  </body>
</html>
