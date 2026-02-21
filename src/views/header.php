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
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($metaRobots, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="author" content="Lahen Huutokaupat Oy">
    <meta name="application-name" content="Huuto247">
    <meta name="generator" content="Huuto247.fi">
    <meta name="theme-color" content="#2668ff">

    <meta property="og:type" content="<?php echo htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:locale" content="<?php echo htmlspecialchars($ogLocale, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:site_name" content="Huuto247">
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">

    <meta name="twitter:card" content="<?php echo htmlspecialchars($twitterCard, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">

    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="alternate" hreflang="fi" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">

    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon16x.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon32x.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/assets/favicon32x.png" type="image/png">
    <link rel="apple-touch-icon" href="/assets/favicon32x.png">
        <link rel="stylesheet" href="/styles.css">
</head>
<body>
        <div class="top-bar" role="note" aria-label="Pikatiedotteet">
                <a href="/info.php?page=myyminen">Myy yrityksesi varasto tehokkaasti!</a>
                <a href="/info.php?page=ohjeet">Löydä todellisia löytöjä</a>
        </div>

        <header class="site-header" id="siteHeader">
            <div class="header-row container">
                <a class="logo" href="/index.php" aria-label="Huuto247 etusivu">
                    <span class="logo-mark" aria-hidden="true"></span>
                    <span>HUUTO247<span class="logo-dot">.fi</span></span>
                </a>

                <div class="lang-wrap">
                    <button class="lang-btn" type="button" aria-label="Kieli">🌐 FI</button>
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
                    </div>
                    <button type="submit" class="search-submit">Hae</button>
                </form>

                <nav class="header-links" aria-label="Pikalinkit">
                    <a href="/index.php" class="desktop-only">Etusivu</a>
                    <a href="/category.php?closing_soon=1">Sulkeutuu pian</a>
                    <a href="/info.php?page=kayttajien-valinen-huutokauppa" class="desktop-only">Kauppatavat</a>
                    <a class="icon-pill" href="/category.php?tab=favorites" aria-label="Suosikit">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.2 4.65 12.9a4.6 4.6 0 1 1 6.5-6.5L12 7.25l.85-.85a4.6 4.6 0 1 1 6.5 6.5L12 20.2Z"/></svg>
                    </a>
                    <a class="icon-pill" href="/category.php?tab=watching" aria-label="Seuranta">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Zm10 3.8a3.8 3.8 0 1 0 0-7.6 3.8 3.8 0 0 0 0 7.6Z"/></svg>
                    </a>
                    <a class="icon-pill" href="/category.php?tab=my-bids" aria-label="Omat huudot">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6.5h16v11H4v-11Zm2 2v7h12v-7H6Zm2.5 10h7v1.8h-7V18.5Z"/></svg>
                    </a>

                    <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
                        <span class="user-greeting">Hei, <?php echo htmlspecialchars($headerUserGreeting !== '' ? $headerUserGreeting : 'Käyttäjä', ENT_QUOTES, 'UTF-8'); ?>!</span>
                        <a href="/admin.php" class="desktop-only">Admin</a>
                        <a href="/add_product.php" class="register-pill">Lisää tuote</a>
                        <a href="/auth/logout.php" class="desktop-only">Kirjaudu ulos</a>
                    <?php else: ?>
                        <a href="/auth/login.php">Kirjaudu sisään</a>
                        <a href="/auth/register.php" class="register-pill">Rekisteröidy</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>

        <main class="container" style="padding-top:1.5rem; padding-bottom:2rem;">
