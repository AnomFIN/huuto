<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

try {
    // Application bootstrap: load shared configuration, autoloading and common setup.
    require_once __DIR__ . '/bootstrap.php';
} catch (Exception $e) {
    die("Bootstrap error: " . $e->getMessage());
} catch (Error $e) {
    die("Fatal error: " . $e->getMessage());
}

// =============================================================================
// DEBUG SECTION - Vianmääritystä varten (näkyy HTML-kommenteissa)
// =============================================================================
$debugInfo = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'db_driver' => 'unknown',
    'db_host' => defined('DB_HOST') ? DB_HOST : 'NOT_DEFINED',
    'db_name' => defined('DB_NAME') ? DB_NAME : 'NOT_DEFINED', 
    'db_connection' => 'not_tested',
    'auction_counts' => ['popular' => 0, 'closing' => 0, 'featured' => 0],
    'errors' => []
];

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    $debugInfo['db_connection'] = 'SUCCESS';
    $debugInfo['db_driver'] = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $debugInfo['db_version'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
} catch (Exception $e) {
    $debugInfo['db_connection'] = 'FAILED: ' . $e->getMessage();
    $debugInfo['errors'][] = 'Database connection: ' . $e->getMessage();
}

$pageTitle = SITE_NAME . ' - Etusivu';

$popularAuctions = [];
$closingSoonAuctions = [];
$featuredAuctions = [];
$dataLoadError = null;
$favoriteIds = [];
$displayFirstName = '';
$categories = [];
$categoriesFromDb = [];

try {
    // Load categories from database
    $categoryModel = new Category();
    $categoriesFromDb = $categoryModel->getAllCategories();
    $categories = array_map(function($cat) {
        return $cat['name'];
    }, $categoriesFromDb);
} catch (Exception $e) {
    error_log("Category error: " . $e->getMessage());
    $categories = ['Antiikki', 'Ajoneuvot', 'Elektroniikka', 'Kodin tavarat', 'Urheilu', 'Vaatteet', 'Keräily', 'Taide'];
}

try {
    // Create auction model
    $auctionModel = new Auction();
    // Auction model created successfully
    
    // TEMP TEST: Käytä samaa metodia kuin category.php
    // Test getActiveAuctions (same as category.php)
    $testAuctions = $auctionModel->getActiveAuctions(10);
    // TEST RESULT: auction count from getActiveAuctions
    
    // Load popular auctions
    $popularAuctions = $auctionModel->getPopularAuctions(120);
    $debugInfo['auction_counts']['popular'] = count($popularAuctions);
    
    // Load closing soon auctions
    $closingSoonAuctions = $auctionModel->getClosingSoonAuctions(120);
    $debugInfo['auction_counts']['closing'] = count($closingSoonAuctions);
    
    // Load featured auctions
    $featuredAuctions = $auctionModel->getFeaturedAuctions(8);
    $debugInfo['auction_counts']['featured'] = count($featuredAuctions);
    
    // All auction data loaded successfully
    
} catch (Exception $error) {
    $dataLoadError = $error->getMessage();
    $debugInfo['errors'][] = 'Auction loading: ' . $error->getMessage();
    error_log("Homepage auction data load failed: " . $error->getMessage() . "\n" . $error->getTraceAsString());
    echo "<!-- ERROR: " . htmlspecialchars($error->getMessage()) . " -->\n";
    echo "<!-- ERROR TRACE: " . htmlspecialchars($error->getTraceAsString()) . " -->\n";
    $dataLoadError = 'Tietojen lataaminen epäonnistui: ' . $error->getMessage();
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
    // VÄLIAIKAISESTI POISTETTU: Skip auctions with invalid or past end times  
    // if (!$endTimeRaw || $endTimeRaw <= time()) {
        // error_log("Auction ID " . ($auction['id'] ?? 'unknown') . " rejected - end_time: " . ($auction['end_time'] ?? 'missing') . " (parsed: $endTimeRaw, current: " . time() . ")");
    //     return null;
    // }
    
    // Fallback jos end_time on virheellinen
    if (!$endTimeRaw) {
        $endTimeRaw = time() + (7 * 24 * 60 * 60); // +7 päivää
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
        // error_log("Auction ID " . ($auction['id'] ?? 'unknown') . " rejected - no valid price. current_price: " . ($auction['current_price'] ?? 'missing') . ", starting_price: " . ($auction['starting_price'] ?? 'missing'));
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
    $totalCount = count($source);
    $rejectedCount = 0;
    
    foreach ($source as $auction) {
        if (!is_array($auction)) {
            continue;
        }
        $normalized = normalizeAuctionForUi($auction);
        if ($normalized !== null) {
            $items[] = $normalized;
        } else {
            $rejectedCount++;
        }
    }
    
    echo "<!-- DEBUG buildUiData: Käsiteltiin $totalCount, hyväksyttiin " . count($items) . ", hylättiin $rejectedCount -->\n";
    
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

// Finalize debug info 
$debugInfo['total_categories'] = count($categories);
$debugInfo['page_load_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
$debugInfo['memory_usage'] = memory_get_peak_usage(true);
?>
<!--
=============================================================================
HUUTO247.FI DEBUG INFO (vianmääritystä varten)
=============================================================================
Timestamp: <?php echo $debugInfo['timestamp']; ?>

PHP Version: <?php echo $debugInfo['php_version']; ?>

Database:
- Host: <?php echo $debugInfo['db_host']; ?>
- Name: <?php echo $debugInfo['db_name']; ?>
- Driver: <?php echo $debugInfo['db_driver']; ?>
- Connection: <?php echo $debugInfo['db_connection']; ?>
<?php if (isset($debugInfo['db_version'])): ?>
- Version: <?php echo $debugInfo['db_version']; ?>
<?php endif; ?>

Auction Counts:
- Popular: <?php echo $debugInfo['auction_counts']['popular']; ?> pcs
- Closing Soon: <?php echo $debugInfo['auction_counts']['closing']; ?> pcs  
- Featured: <?php echo $debugInfo['auction_counts']['featured']; ?> pcs

Categories: <?php echo $debugInfo['total_categories']; ?> pcs

<?php if (!empty($debugInfo['errors'])): ?>
Errors:
<?php foreach ($debugInfo['errors'] as $error): ?>
- <?php echo htmlspecialchars($error); ?>
<?php endforeach; ?>
<?php else: ?>
Errors: None
<?php endif; ?>

Performance:
- Load Time: <?php echo number_format($debugInfo['page_load_time'] * 1000, 2); ?> ms
- Memory: <?php echo number_format($debugInfo['memory_usage'] / 1024 / 1024, 2); ?> MB

=============================================================================
-->
<!doctype html>
<html lang="fi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="top-bar" role="note" aria-label="Pikatiedotteet">
      <a href="/info.php?page=myyminen">Myy yrityksesi varasto tehokkaasti!</a>
      <span class="top-bar-sep" aria-hidden="true">·</span>
      <a href="/info.php?page=ohjeet">Löydä todellisia löytöjä</a>
    </div>

    <header class="site-header" id="siteHeader">
      <div class="header-row container">
        <div class="header-brand-group">
          <a class="logo" href="index.php" aria-label="Huuto247 etusivu">
            <span class="logo-mark" aria-hidden="true"></span>
            <span>HUUTO247<span class="logo-dot">.fi</span></span>
          </a>
          <div class="header-live-badge" title="Aktiivisia huutoja nyt">
            <span class="header-live-dot" aria-hidden="true"></span>
            <span>LIVE</span>
          </div>
        </div>

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
          <a href="#popularSection">Kategoriat</a>
          <a href="#endingSoonSection">Sulkeutuu pian</a>
          <button class="icon-pill" aria-label="Suosikit">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.2 4.65 12.9a4.6 4.6 0 1 1 6.5-6.5L12 7.25l.85-.85a4.6 4.6 0 1 1 6.5 6.5L12 20.2Z"/></svg>
          </button>
          <button class="icon-pill" aria-label="Seuranta">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Zm10 3.8a3.8 3.8 0 1 0 0-7.6 3.8 3.8 0 0 0 0 7.6Z"/></svg>
          </button>
          <button class="icon-pill" aria-label="Omat huudot">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6.5h16v11H4v-11Zm2 2v7h12v-7H6Zm2.5 10h7v1.8h-7V18.5Z"/></svg>
          </button>
          <?php if ($isUserLoggedIn): ?>
            <span id="loginLink" class="user-greeting">Hei, <?php echo htmlspecialchars($displayFirstName !== '' ? $displayFirstName : 'Käyttäjä', ENT_QUOTES, 'UTF-8'); ?>!</span>
          <?php else: ?>
            <button id="loginLink">Kirjaudu sisään</button>
            <button id="registerLink" class="register-pill">Rekisteröidy</button>
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

      <!-- ============================================================
           HERO SUPER — full-width immersive hero band
           ============================================================ -->
      <section class="hero-super" aria-label="Tervetuloa Huuto247:ään">
        <!-- Ambient floating orbs (pure CSS animation) -->
        <div class="hero-orb hero-orb-1" aria-hidden="true"></div>
        <div class="hero-orb hero-orb-2" aria-hidden="true"></div>
        <div class="hero-orb hero-orb-3" aria-hidden="true"></div>
        <div class="hero-orb hero-orb-4" aria-hidden="true"></div>

        <div class="container hero-super-inner">
          <!-- Live activity strip -->
          <div class="hero-live-strip" data-hero>
            <span class="hero-live-ring" aria-hidden="true"></span>
            <span class="hero-live-dot" aria-hidden="true"></span>
            <strong>LIVE</strong>&ensp;—&ensp;Tuhansia kohteita huutokaupattavana juuri nyt
          </div>

          <!-- Main headline -->
          <h1 class="hero-headline" data-hero>
            Suomen <span class="headline-accent">johtava</span><br>huutokauppa
          </h1>

          <!-- Sub-headline -->
          <p class="hero-sub" data-hero>
            Löydä ainutlaatuiset löydöt tai myy kohteesi tuhansille ostajille.<br>
            Luotettu, nopea ja täysin suomalainen palvelu.
          </p>

          <!-- CTA row -->
          <div class="hero-cta-row" data-hero>
            <button class="btn-hero-primary" data-action="scroll-popular">
              Selaa kohteita
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
            <button class="btn-hero-secondary" data-action="open-benefit">
              Myy oma kohteesi
            </button>
          </div>

          <!-- Trust stats bar -->
          <div class="hero-stats" data-hero>
            <div class="hero-stat">
              <span class="hero-stat-value" data-count data-count-target="14238" data-count-duration="1600">14 238</span>
              <span class="hero-stat-label">Aktiivista kohdetta</span>
            </div>
            <div class="hero-stat-sep" aria-hidden="true"></div>
            <div class="hero-stat">
              <span class="hero-stat-value" data-count data-count-target="89200" data-count-duration="1800">89 200</span>
              <span class="hero-stat-label">Rekisteröitynyttä käyttäjää</span>
            </div>
            <div class="hero-stat-sep" aria-hidden="true"></div>
            <div class="hero-stat">
              <span class="hero-stat-value" data-count data-count-target="5" data-count-suffix="M+" data-count-duration="1200">5M+</span>
              <span class="hero-stat-label">Kuukausivierailua</span>
            </div>
            <div class="hero-stat-sep" aria-hidden="true"></div>
            <div class="hero-stat">
              <span class="hero-stat-value" data-count data-count-target="98" data-count-suffix="%" data-count-duration="1400">98%</span>
              <span class="hero-stat-label">Tyytyväisiä asiakkaita</span>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================
           HERO GRID — carousel + categories (existing app.js bindings)
           ============================================================ -->
      <section class="hero container">
        <article class="intro-card">
          <p class="eyebrow" data-hero>UUSIMMAT ILMOITUKSET</p>
          <h2 class="intro-card-heading" data-hero>Löydä paras tarjous</h2>
          <p id="rotatingSlogan" class="slogan" aria-live="polite" data-hero></p>
          <div class="cta-row" data-hero>
            <button class="btn-primary" data-action="scroll-popular">Ilmoita kohde</button>
            <button class="btn-secondary" data-action="open-benefit">Ensihuutajan etu</button>
          </div>
          <div class="trust-pills" data-hero>
            <span data-count data-count-target="5" data-count-suffix="M+ vierailua/kk" data-count-duration="1200"></span>
            <span>Suomalainen palvelu</span>
            <span>Turvalliset maksut</span>
          </div>
        </article>

        <article class="carousel-card" aria-label="Sulkeutuu pian karuselli" data-hero style="transition-delay:200ms">
          <div class="urgent-badge">SULKEUTUU PIAN, TOIMI NYT!</div>
          <div class="carousel-progress"><span id="carouselProgress"></span></div>
          <div class="carousel-wrap" id="heroCarousel">
            <button class="arrow" id="carouselPrev" aria-label="Edellinen">←</button>
            <div class="carousel-track" id="carouselTrack"></div>
            <button class="arrow" id="carouselNext" aria-label="Seuraava">→</button>
          </div>
          <div class="dots" id="carouselDots"></div>
        </article>

        <aside class="category-card" id="heroCategories" data-hero style="transition-delay:340ms">
          <div class="category-head">Kaikki kategoriat</div>
          <ul id="categoryList"></ul>
          <div id="categoryGrid" style="display: none;"></div>
          <div id="liveAuctionCount" style="display: none;"></div>
        </aside>
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

      <section class="listing-section section-tint" id="popularSection" data-section-reveal data-reveal>
        <div class="container">
          <div class="section-head" data-reveal>
            <div>
              <h2>Suositut</h2>
              <div class="filter-pills" id="popularPills"></div>
            </div>
            <a href="#" class="show-all">Näytä kaikki →</a>
          </div>
          <div class="auction-grid" id="popularGrid" data-card-grid></div>
          <button class="load-more" id="loadMorePopular">Lataa lisää kohteita</button>
          <p class="empty-tip" id="popularTip" hidden>Kokeile hakua tai vaihda kategoriaa.</p>
        </div>
      </section>

      <section class="listing-section" id="endingSoonSection" data-section-reveal data-reveal>
        <div class="container">
          <div class="section-head" data-reveal>
            <div>
              <h2>Sulkeutuu pian</h2>
              <div class="filter-pills" id="endingPills"></div>
            </div>
            <a href="#" class="show-all">Näytä kaikki →</a>
          </div>
          <div class="auction-grid" id="endingGrid" data-card-grid></div>
          <button class="load-more" id="loadMoreEnding">Lataa lisää kohteita</button>
          <p class="empty-tip" id="endingTip" hidden>Kokeile hakua tai vaihda kategoriaa.</p>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-brand">
            <a href="index.php" class="footer-logo">
              <span class="logo-mark" aria-hidden="true"></span>
              <span>HUUTO247<span class="logo-dot">.fi</span></span>
            </a>
            <p>Suomen johtava huutokauppapalvelu</p>
          </div>

          <div class="footer-links">
            <div class="footer-col">
              <h4>Huutokaupat</h4>
              <a href="/category.php">Kaikki kategoriat</a>
              <a href="/category.php?closing_soon=1">Päättyvät pian</a>
              <a href="/add_product.php">Myy kohteesi</a>
            </div>
            
            <div class="footer-col">
              <h4>Tuki</h4>
              <a href="/info.php?page=ohjeet">Ohjeet</a>
              <a href="/info.php?page=tuki">Asiakastuki</a>
              <a href="/info.php?page=maksut">Maksutavat</a>
            </div>
            
            <div class="footer-col">
              <h4>Yritys</h4>
              <a href="/info.php?page=meista">Tietoa meistä</a>
              <a href="/info.php?page=yhteystiedot">Yhteystiedot</a>
              <a href="/info.php?page=rekry">Työpaikat</a>
            </div>
          </div>

          <div class="footer-meta">
            <div class="footer-trust">
              <span>Suomalainen palvelu</span>
              <span>5M+ vierailua/kk</span>
              <span>Yli 89 000 käyttäjää</span>
            </div>
            
            <div class="footer-legal">
              <a href="/info.php?page=kayttoehdot">Käyttöehdot</a>
              <a href="/info.php?page=tietosuoja">Tietosuoja</a>
              <a href="/info.php?page=evasteet">Evästeet</a>
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          <p>&copy; 2026 Huuto247.fi - Kaikki oikeudet pidätetään</p>
          <p>Lahen Huutokaupat Oy</p>
        </div>
      </div>
    </footer>

    <!-- Modals -->
    <dialog id="loginModal" class="modal">
      <form method="dialog" class="modal-content">
        <h2>Kirjaudu sisään</h2>
        <p>Kirjaudu sisään käyttääksesi kaikkia ominaisuuksia</p>
        <div class="modal-actions">
          <button type="button" class="btn-primary">Kirjaudu</button>
          <button type="button" class="btn-secondary" data-close-modal>Peruuta</button>
        </div>
      </form>
    </dialog>

    <dialog id="benefitModal" class="modal">
      <form method="dialog" class="modal-content">
        <h2>Ensihuutajan etu</h2>
        <p>Rekisteröidy ja nauti erikoisalennuksista sekä ensihuutajan eduista!</p>
        <div class="modal-actions">
          <button type="button" class="btn-primary">Ymmärretty</button>
        </div>
      </form>
    </dialog>

    <dialog id="itemModal" class="modal item-modal">
      <form method="dialog" class="modal-content" id="itemModalContent">
        <!-- Populated by JavaScript -->
      </form>
    </dialog>
    <script>
      <!-- DEBUG JSON DATA: Popular count = <?php echo count($popularUiData); ?>, Closing count = <?php echo count($closingUiData); ?>, Featured count = <?php echo count($featuredUiData); ?> -->
      // HOME_DATA loaded
      
      window.__HOME_DATA__ = {
        categories: <?php echo json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        popular: <?php echo json_encode($popularUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        closing: <?php echo json_encode($closingUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        featured: <?php echo json_encode($featuredUiData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
        isLoggedIn: <?php echo $isUserLoggedIn ? 'true' : 'false'; ?>,
        favoriteIds: <?php echo json_encode($favoriteIds, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>,
      };
      
      // console.log('HOME_DATA loaded:', window.__HOME_DATA__);
      // console.log('Popular items:', window.__HOME_DATA__.popular.length);
      // console.log('Closing items:', window.__HOME_DATA__.closing.length);
      // console.log('Featured items:', window.__HOME_DATA__.featured.length);
    </script>
    <script>
      // About to load app.js
      
      // Test if app.js loads
      window.APP_JS_LOADED = false;
      
      // Add error handling for script loading
      window.addEventListener('error', function(e) {
        if (e.target && e.target.src && e.target.src.includes('app.js')) {
          // console.error('app.js failed to load!', e);
        }
      });
    </script>
    <script src="app.js" defer></script>
  </body>
</html>
