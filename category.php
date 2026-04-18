<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

$auctionModel = new Auction();
$categoryModel = new Category();

$categorySlug = isset($_GET['slug']) ? $_GET['slug'] : null;
$categoryName = isset($_GET['name']) ? $_GET['name'] : null;
$categoryId = isset($_GET['id']) ? $_GET['id'] : null;
$tab = trim((string)($_GET['tab'] ?? ''));
$favoritesMode = strcasecmp($tab, 'favorites') === 0;
$showCategoryChooser = !$categorySlug && !$categoryName && !$categoryId && !$favoritesMode;

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
} elseif ($showCategoryChooser) {
    $category = null;
    $auctions = [];
    $pageTitle = 'Valitse kategoria - ' . SITE_NAME;
} elseif ($categorySlug || $categoryName || $categoryId) {
    if ($categorySlug) {
        $category = $categoryModel->getCategoryBySlug($categorySlug);
    } elseif ($categoryId) {
        $category = $categoryModel->getCategoryById($categoryId);
    } elseif ($categoryName) {
        $category = $categoryModel->getCategoryByName($categoryName);
    }
    
    if (!$category) {
        header('Location: /category.php');
        exit;
    }
    
    $auctions = $auctionModel->getAuctionsByCategory($category['slug'] ?? $categorySlug);
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
/* Premium Category Page Styles - Modern Marketplace 2025 */
.premium-category-page {
  min-height: calc(100vh - 200px);
  background: var(--bg-main);
  position: relative;
}

/* Premium Category Chooser */
.premium-category-chooser {
  max-width: 1200px;
  margin: 0 auto;
  padding: clamp(24px, 4vw, 48px);
}

.premium-category-header {
  text-align: center;
  margin-bottom: 48px;
  animation: slideUp 0.8s var(--ease-premium);
}

.premium-category-header h1 {
  font-size: clamp(2rem, 4vw, 3.5rem);
  font-weight: 800;
  background: var(--text-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 16px;
  letter-spacing: -0.02em;
}

.premium-category-subtitle {
  font-size: clamp(1rem, 2vw, 1.2rem);
  color: var(--text-500);
  font-weight: 500;
  margin-bottom: 32px;
}

.premium-search-wrap {
  position: relative;
  max-width: 500px;
  margin: 0 auto 48px;
}

.premium-search-input {
  width: 100%;
  padding: 16px 24px 16px 56px;
  border: 2px solid transparent;
  border-radius: 16px;
  background: var(--surface-glass);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  font-size: 1.1rem;
  font-weight: 500;
  color: var(--text-900);
  transition: all 0.4s var(--ease-premium);
  box-shadow: var(--shadow-premium);
}

.premium-search-input:focus {
  border-color: var(--accent-600);
  box-shadow: var(--shadow-premium), var(--glow-accent);
  outline: none;
}

.premium-search-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  fill: var(--text-500);
  pointer-events: none;
}

.premium-category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 48px;
}

.premium-category-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 24px;
  border-radius: 20px;
  background: var(--surface-glass);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  cursor: pointer;
  text-decoration: none;
  color: inherit;
  transition: all 0.6s var(--ease-premium);
  overflow: hidden;
  animation: slideUp 0.6s var(--ease-premium) forwards;
  opacity: 0;
  transform: translateY(30px);
  box-shadow: var(--shadow-soft);
}

.premium-category-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: var(--shadow-float), var(--glow-accent);
  border-color: rgba(99, 102, 241, 0.4);
  background: rgba(99, 102, 241, 0.08);
}

.premium-category-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, transparent 0%, rgba(99, 102, 241, 0.1) 100%);
  opacity: 0;
  transition: opacity 0.4s ease;
  pointer-events: none;
}

.premium-category-card:hover::before {
  opacity: 1;
}

.premium-category-icon {
  font-size: 3rem;
  width: 72px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 16px;
  flex-shrink: 0;
  transition: all 0.4s ease;
}

.premium-category-card:hover .premium-category-icon {
  background: rgba(99, 102, 241, 0.2);
  transform: scale(1.1) rotateY(5deg);
}

.premium-category-info h3 {
  margin: 0 0 8px;
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--text-900);
  transition: color 0.3s ease;
}

.premium-category-info p {
  margin: 0;
  font-size: 0.9rem;
  color: var(--text-500);
  font-weight: 500;
}

.premium-category-card:hover .premium-category-info h3 {
  color: var(--accent-600);
}

/* Animation delays for staggered entrance */
.premium-category-card:nth-child(1) { animation-delay: 0ms; }
.premium-category-card:nth-child(2) { animation-delay: 100ms; }
.premium-category-card:nth-child(3) { animation-delay: 200ms; }
.premium-category-card:nth-child(4) { animation-delay: 300ms; }
.premium-category-card:nth-child(5) { animation-delay: 400ms; }
.premium-category-card:nth-child(6) { animation-delay: 500ms; }
.premium-category-card:nth-child(7) { animation-delay: 600ms; }
.premium-category-card:nth-child(8) { animation-delay: 700ms; }

@keyframes slideUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Premium Auction Listings */
.category-hero {
  text-align: center;
  padding: 48px 0;
  background: var(--surface-glass);
  backdrop-filter: blur(20px);
  margin-bottom: 48px;
  border-radius: 24px;
  box-shadow: var(--shadow-premium);
}

.category-hero h1 {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800;
  background: var(--text-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 16px;
  letter-spacing: -0.02em;
}

.category-hero p {
  font-size: 1.1rem;
  color: var(--text-500);
  font-weight: 500;
  margin: 0;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
}

.category-card {
  background: var(--surface-glass);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: var(--shadow-soft);
  transition: all 0.6s var(--ease-premium);
  text-decoration: none;
  color: inherit;
  position: relative;
}

.category-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: var(--shadow-float), var(--glow-accent);
  border-color: rgba(99, 102, 241, 0.4);
}

.category-media {
  width: 100%;
  height: 240px;
  object-fit: cover;
  display: block;
  transition: transform 0.6s ease;
}

.category-card:hover .category-media {
  transform: scale(1.05);
}

.category-content {
  padding: 24px;
  position: relative;
}

.category-title {
  margin: 0 0 12px;
  font-weight: 700;
  color: var(--text-900);
  font-size: 1.1rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.category-meta {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  color: var(--text-700);
  font-size: 0.85rem;
  margin-bottom: 16px;
  font-weight: 600;
}

.category-row {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  align-items: flex-end;
}

.category-price-label {
  color: var(--text-700);
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 4px;
}

.category-price {
  color: var(--accent-600);
  font-weight: 800;
  font-size: 1.2rem;
  font-variant-numeric: tabular-nums;
}

.category-empty {
  text-align: center;
  background: var(--surface-glass);
  backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  border-radius: 24px;
  padding: 64px 32px;
  margin: 48px auto;
  max-width: 600px;
  box-shadow: var(--shadow-premium);
}

.category-empty .empty-icon {
  font-size: 4rem;
  margin-bottom: 24px;
  opacity: 0.7;
}

.category-empty strong {
  display: block;
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--text-900);
  margin-bottom: 12px;
}

.category-empty p {
  color: var(--text-500);
  font-size: 1rem;
  line-height: 1.6;
  margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .premium-category-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .premium-category-card {
    padding: 20px;
    gap: 16px;
  }

  .premium-category-icon {
    width: 60px;
    height: 60px;
    font-size: 2.5rem;
  }

  .category-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    padding: 0 16px;
  }
}
</style>

<?php if ($showCategoryChooser): ?>
<main class="premium-category-page">
  <div class="premium-category-chooser">
    <div class="premium-category-header">
      <h1>📦 Valitse kategoria</h1>
      <p class="premium-category-subtitle">Selaa huutokauppoja kategorian mukaan ja löydä juuri se mitä etsit</p>
      
      <div class="premium-search-wrap">
        <svg class="premium-search-icon" viewBox="0 0 24 24">
          <path d="M10.5 4a6.5 6.5 0 1 1 0 13 6.5 6.5 0 0 1 0-13Zm0 1.8a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4Zm5.87 10.6 3.2 3.2-1.28 1.28-3.2-3.2 1.28-1.28Z"/>
        </svg>
        <input type="text" class="premium-search-input" id="categorySearch" placeholder="Hae kategoriaa..." autocomplete="off">
      </div>
    </div>

    <div class="premium-category-grid" id="categoryGrid">
      <!-- Categories will be populated by JavaScript -->
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const categoryIcons = {
    'Ajoneuvot': '🚗', 'Työkoneet': '🏗️', 'Asunnot': '🏠', 'Vapaa-aika': '⛵',
    'Piha': '🌿', 'Työkalut': '🔧', 'Rakennus': '🏗️', 'Sisustus': '🛋️',
    'Elektroniikka': '📱', 'Keräily': '🏆', 'Urheilu': '⚽', 'Vaatteet': '👕',
    'Taide': '🎨', 'Antiikki': '🏺', 'Kodin tavarat': '🏡', 'Musiikki': '🎵',
    'Kirjat': '📚', 'Pelit': '🎮', 'Muut': '📦'
  };

  // Get categories from the header dropdown
  const selectEl = document.querySelector('#headerSearchCategory, #searchCategory');
  let categories = [];
  
  if (selectEl) {
    const opts = selectEl.querySelectorAll('option');
    opts.forEach(function(opt) {
      const name = opt.textContent.trim();
      if (name && name !== 'Kaikki kategoriat') {
        categories.push({ name: name, slug: opt.value || name });
      }
    });
  }

  function renderCategories(cats) {
    const grid = document.getElementById('categoryGrid');
    if (!grid) return;
    
    grid.innerHTML = cats.map(function(cat, idx) {
      const icon = categoryIcons[cat.name] || '📦';
      return `
        <a href="/category.php?slug=${encodeURIComponent(cat.slug)}" class="premium-category-card">
          <div class="premium-category-icon">${icon}</div>
          <div class="premium-category-info">
            <h3>${cat.name}</h3>
            <p>Selaa ${cat.name.toLowerCase()}kohteita →</p>
          </div>
        </a>
      `;
    }).join('');
  }

  // Search functionality
  const searchInput = document.getElementById('categorySearch');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      const filtered = categories.filter(function(c) {
        return c.name.toLowerCase().indexOf(q) !== -1;
      });
      renderCategories(filtered);
    });
  }

  // Initial render
  renderCategories(categories);
});
</script>

<?php else: ?>
<main class="premium-category-page">
  <div class="container">
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
            <div class="empty-icon">📦</div>
            <strong><?php echo $favoritesMode ? 'Ei suosikkeja' : 'Ei kohteita'; ?></strong>
            <p>
                <?php if ($favoritesMode): ?>
                    <?php echo htmlspecialchars($favoriteViewMessage !== '' ? $favoriteViewMessage : 'Lisää kohteita suosikkeihin etusivulta tai kohdesivulta.'); ?>
                <?php else: ?>
                    Tässä kategoriassa ei ole tällä hetkellä aktiivisia huutokauppoja.
                <?php endif; ?>
            </p>
            <?php if ($favoriteViewRequiresLogin): ?>
                <p style="margin-top:32px;"><button type="button" class="btn-hero-primary" style="text-decoration:none;border:none;background:var(--accent-gradient);color:white;cursor:pointer;" data-auth-modal="login">Kirjaudu sisään</button></p>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <section class="category-grid">
            <?php foreach ($auctions as $auction): ?>
                <?php
                    $imagePath = trim((string)($categoryListingImagePath((string)($auction['primary_image'] ?? '')) ?? ''));
                    $endTimeIso = date('c', strtotime((string)$auction['end_time']));
                ?>
                <a href="/auctionPage.php?id=<?php echo (int)$auction['id']; ?>" class="category-card" aria-label="<?php echo htmlspecialchars($auction['title']); ?>">
                    <?php if ($imagePath !== ''): ?>
                        <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($auction['title']); ?>" class="category-media" />
                    <?php else: ?>
                        <div class="category-media" style="display:grid; place-items:center; color:var(--text-700); background: var(--surface-soft);">
                            <span style="font-size: 2rem; opacity: 0.5;">📦</span>
                        </div>
                    <?php endif; ?>
                    <div class="category-content">
                        <h2 class="category-title"><?php echo htmlspecialchars($auction['title']); ?></h2>
                        <div class="category-meta">
                            <span><?php echo htmlspecialchars((string)($auction['category_name'] ?? 'Muut')); ?></span>
                            <span><?php echo (int)($auction['bid_count'] ?? 0); ?> tarjous<?php echo (int)($auction['bid_count'] ?? 0) === 1 ? '' : 'ta'; ?></span>
                        </div>
                        <div class="category-row">
                            <div>
                                <div class="category-price-label">Nykyinen hinta</div>
                                <div class="category-price"><?php echo number_format((float)$auction['current_price'], 0, ',', ' '); ?> €</div>
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
  </div>
</main>
<?php endif; ?>

<?php include SRC_PATH . '/views/footer.php'; ?>
