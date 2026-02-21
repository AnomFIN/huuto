<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

$auctionModel = new Auction();
$categoryModel = new Category();

$categorySlug = isset($_GET['slug']) ? $_GET['slug'] : null;
$tab = trim((string)($_GET['tab'] ?? ''));
$favoritesMode = strcasecmp($tab, 'favorites') === 0;
$favoriteViewRequiresLogin = false;
$favoriteViewMessage = '';

if ($favoritesMode) {
    $pageTitle = 'Suosikit - ' . SITE_NAME;
    $category = null;
    $auctions = [];

    if (!is_logged_in()) {
        $favoriteViewRequiresLogin = true;
        $favoriteViewMessage = 'Kirjaudu sisään nähdäksesi omat suosikkikohteesi.';
    } else {
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

            $stmt = $db->prepare("SELECT a.*, 
                                         COALESCE(c.name, 'Luokittelematon') as category_name,
                                         (SELECT image_path
                                          FROM auction_images
                                          WHERE auction_id = a.id
                                          ORDER BY is_primary DESC, sort_order ASC, id ASC
                                          LIMIT 1) as primary_image,
                                         (SELECT COUNT(*) FROM bids WHERE auction_id = a.id) as bid_count
                                  FROM user_favourites uf
                                  JOIN auctions a ON a.id = uf.auction_id
                                  LEFT JOIN categories c ON c.id = a.category_id
                                  WHERE uf.user_id = :user_id
                                    AND a.status = 'active'
                                    AND a.end_time > NOW()
                                  ORDER BY uf.created_at DESC");
            $stmt->execute([':user_id' => (int)current_user_id()]);
            $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            if (empty($auctions)) {
                $favoriteViewMessage = 'Et ole lisännyt vielä suosikkikohteita.';
            }
        } catch (Throwable $exception) {
            $auctions = [];
            $favoriteViewMessage = 'Suosikkien lataus epäonnistui. Yritä hetken kuluttua uudelleen.';
        }
    }
} elseif ($categorySlug) {
    $category = $categoryModel->getCategoryBySlug($categorySlug);
    if (!$category) {
        header('Location: /category.php');
        exit;
    }
    $auctions = $auctionModel->getAuctionsByCategory($categorySlug);
    $pageTitle = $category['name'] . ' - ' . SITE_NAME;
} else {
    $category = null;
    $auctions = $auctionModel->getActiveAuctions(50);
    $pageTitle = 'Kaikki kohteet - ' . SITE_NAME;
}

include SRC_PATH . '/views/header.php';

$categoryListingImagePath = static function (?string $path): ?string {
    if ($path === null) {
        return null;
    }

    $trimmed = trim($path);
    if ($trimmed === '') {
        return null;
    }

    if (preg_match('#_wm\.(jpg|jpeg|png|webp|gif)$#i', $trimmed)) {
        return preg_replace('#_wm\.(jpg|jpeg|png|webp|gif)$#i', '_min.$1', $trimmed);
    }

    return $trimmed;
};
?>

<style>
    .category-hero { margin-bottom: 1.2rem; }
    .category-hero h1 { margin: 0 0 .35rem; font-size: clamp(1.4rem, 2.2vw, 2rem); font-weight: 800; color: var(--text-900); }
    .category-hero p { margin: 0; color: var(--text-700); }
    .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
    .category-card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-1); transition: transform .18s ease, box-shadow .18s ease; }
    .category-card:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(16,24,40,.10); }
    .category-media { width: 100%; height: 180px; object-fit: cover; display: block; background: var(--surface-soft); }
    .category-content { padding: .85rem; }
    .category-title { margin: 0 0 .45rem; font-weight: 700; color: var(--text-900); font-size: .95rem; line-height: 1.35; }
    .category-meta { display: flex; justify-content: space-between; gap: .5rem; color: var(--text-700); font-size: .78rem; margin-bottom: .65rem; }
    .category-row { display: flex; justify-content: space-between; gap: .65rem; align-items: flex-end; }
    .category-price-label { color: var(--text-700); font-size: .75rem; }
    .category-price { color: var(--accent-600); font-weight: 800; }
    .category-empty { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 2rem; text-align: center; color: var(--text-700); box-shadow: var(--shadow-1); }
</style>

<section class="category-hero">
    <?php if ($favoritesMode): ?>
        <h1>❤️ Suosikit</h1>
        <p>Kirjautuneen käyttäjän suosikkilistaukset.</p>
    <?php elseif ($category): ?>
        <h1><?php echo htmlspecialchars((string)($category['icon'] ?? '📦')); ?> <?php echo htmlspecialchars($category['name']); ?></h1>
        <p><?php echo htmlspecialchars((string)($category['description'] ?? '')); ?></p>
    <?php else: ?>
        <h1>Kaikki kohteet</h1>
        <p>Selaa kaikkia aktiivisia huutokauppoja.</p>
    <?php endif; ?>
</section>

<?php if (empty($auctions)): ?>
    <section class="category-empty">
        <div style="font-size:2rem; margin-bottom:.4rem;">📦</div>
        <strong><?php echo $favoritesMode ? 'Ei suosikkeja' : 'Ei kohteita'; ?></strong>
        <p style="margin:.5rem 0 0;">
            <?php if ($favoritesMode): ?>
                <?php echo htmlspecialchars($favoriteViewMessage !== '' ? $favoriteViewMessage : 'Lisää kohteita suosikkeihin etusivulta tai kohdesivulta.'); ?>
            <?php else: ?>
                Tässä kategoriassa ei ole tällä hetkellä aktiivisia huutokauppoja.
            <?php endif; ?>
        </p>
        <?php if ($favoriteViewRequiresLogin): ?>
            <p style="margin:.65rem 0 0;"><a href="/auth/login.php" class="btn-primary" style="text-decoration:none;">Kirjaudu sisään</a></p>
        <?php endif; ?>
    </section>
<?php else: ?>
    <section class="category-grid">
        <?php foreach ($auctions as $auction): ?>
            <?php
                $imagePath = trim((string)($categoryListingImagePath((string)($auction['primary_image'] ?? '')) ?? ''));
                $endTimeIso = date('c', strtotime((string)$auction['end_time']));
            ?>
            <a href="/auction.php?id=<?php echo (int)$auction['id']; ?>" class="category-card" aria-label="<?php echo htmlspecialchars($auction['title']); ?>">
                <?php if ($imagePath !== ''): ?>
                    <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" class="category-media" />
                <?php else: ?>
                    <div class="category-media" style="display:grid; place-items:center; color:var(--text-700);">Ei kuvaa</div>
                <?php endif; ?>
                <div class="category-content">
                    <h2 class="category-title"><?php echo htmlspecialchars($auction['title']); ?></h2>
                    <div class="category-meta">
                        <span><?php echo htmlspecialchars((string)($auction['category_name'] ?? 'Muut')); ?></span>
                        <span><?php echo (int)($auction['bid_count'] ?? 0); ?> tarjousta</span>
                    </div>
                    <div class="category-row">
                        <div>
                            <div class="category-price-label">Nykyinen hinta</div>
                            <div class="category-price"><?php echo number_format((float)$auction['current_price'], 2, ',', ' '); ?> €</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="category-price-label">Päättyy</div>
                            <div class="countdown" data-endtime="<?php echo htmlspecialchars($endTimeIso, ENT_QUOTES, 'UTF-8'); ?>"></div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php include SRC_PATH . '/views/footer.php'; ?>
