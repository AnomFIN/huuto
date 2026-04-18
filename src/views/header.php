<?php
$headerCategories = [];
$headerSelectedCategory = trim((string)($_GET['category'] ?? ''));
$headerSearchQuery = trim((string)($_GET['q'] ?? ''));
$headerUserGreeting = '';

if (class_exists('Category')) {
    try {
        $headerCategoryModel = new Category();
        $headerCategories = $headerCategoryModel->getAllCategories();
    } catch (Throwable $exception) {
        $headerCategories = [];
    }
}

if (function_exists('is_logged_in') && is_logged_in() && function_exists('current_user')) {
    $headerCurrentUser = current_user();
    $headerRawName = trim((string)($headerCurrentUser['full_name'] ?? $headerCurrentUser['username'] ?? 'Käyttäjä'));
    if ($headerRawName !== '') {
        $headerUserGreeting = explode(' ', $headerRawName)[0];
    }
}

$defaultMetaDescription = 'Huuto247 (huuto247.fi) on suomalainen huutokauppapalvelu yrityksille ja kuluttajille. Löydä kohteet, seuraa huutoja ja myy tehokkaasti.';
$metaTitle = trim((string)($pageTitle ?? SITE_NAME));
$metaDescription = trim((string)($pageDescription ?? $defaultMetaDescription));
$metaKeywords = trim((string)($pageKeywords ?? 'huutokauppa, verkkohuutokauppa, Huuto247, huuto247.fi, Lahen Huutokaupat Oy'));
$metaRobots = trim((string)($pageRobots ?? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1'));

$requestUri = (string)($_SERVER['REQUEST_URI'] ?? '/');
$requestPath = (string)(parse_url($requestUri, PHP_URL_PATH) ?? '/');
$canonicalTarget = trim((string)($canonicalPath ?? $requestPath));
if ($canonicalTarget === '') {
    $canonicalTarget = '/';
}
$canonicalUrl = rtrim((string)BASE_URL, '/') . '/' . ltrim($canonicalTarget, '/');

$ogType = trim((string)($openGraphType ?? 'website'));
$ogLocale = trim((string)($openGraphLocale ?? 'fi_FI'));
$ogImage = trim((string)($openGraphImage ?? (rtrim((string)BASE_URL, '/') . '/assets/logo.png')));
$twitterCard = trim((string)($twitterCardType ?? 'summary_large_image'));
?>
<!DOCTYPE html>
<html lang="fi">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="/styles.css">
</head>
<body>
        <div class="top-bar" role="note" aria-label="Pikatiedotteet">
		<a href="/info.php?page=myyminen" class="top-bar-link">🔥 Myy yrityksesi varasto tehokkaasti!</a>
        <span class="top-bar-sep" aria-hidden="true">·</span>
		<a href="/info.php?page=ohjeet" class="top-bar-link">💎 Löydä todellisia löytöjä</a>

        <header class="site-header" id="siteHeader">
            <div class="header-row container">
        <div class="header-brand-group">
                <a class="logo" href="/index.php" aria-label="Huuto247 etusivu">
                    <span class="logo-icon" aria-hidden="true">H</span>
                    <span class="logo-text">uuto<span class="logo-247">24<span class="logo-7">/7</span></span><span class="logo-dot">.fi</span></span>
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

                <form class="search-shell" role="search" action="/category.php" method="GET" aria-label="Kohteiden haku">
                    <label class="sr-only" for="headerSearchCategory">Kategoria</label>
                    <select id="headerSearchCategory" name="category">
                        <option value="">Kaikki kategoriat</option>
                        <?php foreach ($headerCategories as $headerCategory): ?>
                            <?php
                                $categoryName = trim((string)($headerCategory['name'] ?? ''));
                                if ($categoryName === '') {
                                    continue;
                                }
                                $isSelected = strcasecmp($headerSelectedCategory, $categoryName) === 0;
                            ?>
                            <option value="<?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="search-field-wrap">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.5 4a6.5 6.5 0 1 1 0 13 6.5 6.5 0 0 1 0-13Zm0 1.8a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4Zm5.87 10.6 3.2 3.2-1.28 1.28-3.2-3.2 1.28-1.28Z"/></svg>
                        <input id="headerSearchInput" type="search" name="q" placeholder="Mitä etsit?" autocomplete="off" value="<?php echo htmlspecialchars($headerSearchQuery, ENT_QUOTES, 'UTF-8'); ?>" />
            <button type="button" id="clearSearch" class="clear-btn" aria-label="Tyhjennä haku">✕</button>
            <span id="searchHint" class="search-hint">Paina Enter hakeaksesi</span>
                    </div>
                    <button type="submit" class="search-submit">Hae</button>
                </form>

                <!-- Desktop nav links -->
                <nav class="header-links header-links--desktop" aria-label="Pikalinkit">
          <button class="header-nav-link" data-action="open-category-menu" aria-label="Avaa kategoriat">
            Kategoriat
          </button>
          <button class="favorites-pill" id="headerFavoritesBtn" aria-label="Suosikit">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.2 4.65 12.9a4.6 4.6 0 1 1 6.5-6.5L12 7.25l.85-.85a4.6 4.6 0 1 1 6.5 6.5L12 20.2Z"/></svg>
            <span class="favorites-label">Suosikit</span>
            <span class="favorites-count" id="headerFavCount">0</span>
          </button>

                    <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
                        <span class="user-greeting">Hei, <?php echo htmlspecialchars($headerUserGreeting !== '' ? $headerUserGreeting : 'Käyttäjä', ENT_QUOTES, 'UTF-8'); ?>!</span>
                    <?php else: ?>
            <button type="button" id="loginTrigger" class="btn-login-header" data-auth-modal="login">
              <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
              Kirjaudu
            </button>
            <button type="button" id="registerTrigger" class="btn-register-header" data-auth-modal="register">
              Rekisteröidy
              <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"/></svg>
            </button>
                    <?php endif; ?>
                </nav>

                <!-- Mobile hamburger -->
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Avaa valikko" aria-expanded="false" aria-controls="mobileMenuDrawer">
                  <span class="hamburger-line"></span>
                  <span class="hamburger-line"></span>
                  <span class="hamburger-line"></span>
                </button>
            </div>
        </header>

        <!-- Mobile menu drawer -->
        <div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>
        <nav class="mobile-menu-drawer" id="mobileMenuDrawer" aria-label="Mobiilivalikko" aria-hidden="true">
          <div class="mobile-menu-header">
            <a class="logo" href="/index.php" aria-label="Huuto247 etusivu">
              <span class="logo-icon" aria-hidden="true">H</span>
              <span class="logo-text">uuto<span class="logo-247">24<span class="logo-7">/7</span></span></span>
            </a>
            <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Sulje valikko">
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
          </div>

          <form class="mobile-search-form" action="/category.php" method="GET">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" name="q" placeholder="Mitä etsit?" autocomplete="off">
          </form>

          <ul class="mobile-menu-links">
            <li><button data-action="open-category-menu">Kategoriat</button></li>
            <li><a href="/category.php?closing_soon=1">Päättyvät pian</a></li>
            <li><a href="/add_product.php">Myy kohteesi</a></li>
            <li><a href="/category.php?tab=favorites">Suosikit</a></li>
          </ul>

          <div class="mobile-menu-auth">
            <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
              <span class="user-greeting">Hei, <?php echo htmlspecialchars($headerUserGreeting !== '' ? $headerUserGreeting : 'Käyttäjä', ENT_QUOTES, 'UTF-8'); ?>!</span>
              <a href="/auth/logout.php" class="mobile-menu-logout">Kirjaudu ulos</a>
            <?php else: ?>
              <button type="button" class="mobile-auth-btn mobile-auth-btn--login" data-auth-modal="login">Kirjaudu sisään</button>
              <button type="button" class="mobile-auth-btn mobile-auth-btn--register" data-auth-modal="register">Luo tili</button>
            <?php endif; ?>
          </div>
        </nav>

        <script>
        (function(){
          var btn = document.getElementById('mobileMenuBtn');
          var drawer = document.getElementById('mobileMenuDrawer');
          var backdrop = document.getElementById('mobileMenuBackdrop');
          var closeBtn = document.getElementById('mobileMenuClose');
          if (!btn || !drawer) return;

          function open() {
            drawer.classList.add('open');
            backdrop.classList.add('open');
            drawer.setAttribute('aria-hidden', 'false');
            btn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
          }
          function close() {
            drawer.classList.remove('open');
            backdrop.classList.remove('open');
            drawer.setAttribute('aria-hidden', 'true');
            btn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
          }

          btn.addEventListener('click', open);
          if (closeBtn) closeBtn.addEventListener('click', close);
          if (backdrop) backdrop.addEventListener('click', close);
          document.addEventListener('keydown', function(e) { if (e.key === 'Escape') close(); });

          // Close drawer when auth modal opens
          drawer.addEventListener('click', function(e) {
            if (e.target.closest('[data-auth-modal]') || e.target.closest('[data-action="open-category-menu"]')) {
              close();
            }
          });
        })();
        </script>

        <main class="container" style="padding-top:1.5rem; padding-bottom:2rem;">
