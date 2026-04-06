<?php
// Premium Huuto247 Auction Detail Page - Beyond algorithms. Into outcomes.
require_once __DIR__ . '/bootstrap.php';

$auctionModel = new Auction();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

$auction = $auctionModel->getAuctionById($id);

if (!$auction) {
    header('Location: index.php');
    exit;
}

// Increment view count
$auctionModel->incrementViews($id);
$auction['views'] = (isset($auction['views']) ? (int)$auction['views'] : 0) + 1;

$images = $auctionModel->getAuctionImages($id);
$bids = $auctionModel->getAuctionBids($id);
$metadata = $auctionModel->getAuctionMetadata($id);
$relatedAuctions = $auctionModel->getRelatedAuctions($auction['category_id'], $id, 4);

if (!is_array($metadata)) {
    $metadata = [];
}

$categoryName = trim((string)($auction['category_name'] ?? 'Muut'));
$categoryKey = mb_strtolower($categoryName, 'UTF-8');

$auctionEndUnix = strtotime((string)$auction['end_time']);
if ($auctionEndUnix === false) {
    $auctionEndUnix = time();
}

function meta_value(array $metadata, string $key, string $fallback = '-'): string
{
    $value = isset($metadata[$key]) ? trim((string)$metadata[$key]) : '';
    return $value !== '' ? $value : $fallback;
}

function meta_bool(array $metadata, string $key, string $yes = 'Kyllä', string $no = 'Ei'): string
{
    $value = mb_strtolower(trim((string)($metadata[$key] ?? '')), 'UTF-8');
    if ($value === '') {
        return '-';
    }

    $truthy = ['1', 'true', 'yes', 'kyllä', 'kylla'];
    $falsy = ['0', 'false', 'no', 'ei'];

    if (in_array($value, $truthy, true)) {
        return $yes;
    }
    if (in_array($value, $falsy, true)) {
        return $no;
    }

    return ucfirst((string)$metadata[$key]);
}

$pageTitle = $auction['title'] . ' - ' . SITE_NAME;
include SRC_PATH . '/views/header.php';
?>

<!-- Premium Auction Detail Page -->
<div class="auction-detail-container">
    <!-- Premium Breadcrumb -->
    <nav class="auction-breadcrumb">
        <a href="index.php" class="breadcrumb-link">
            <svg viewBox="0 0 20 20" fill="currentColor" class="breadcrumb-icon">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Takaisin hakuun
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-category"><?php echo htmlspecialchars($categoryName); ?></span>
    </nav>

    <!-- Premium Header -->
    <header class="auction-header">
        <div class="auction-header-main">
            <div class="auction-badges">
                <span class="category-badge"><?php echo htmlspecialchars($categoryName); ?></span>
                <?php if ($auction['featured'] ?? false): ?>
                    <span class="featured-badge">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.396-.756 1.456-.756 1.852 0l1.286 2.46a1 1 0 00.755.545l2.747.4c.847.123 1.185 1.164.571 1.76l-1.987 1.936a1 1 0 00-.287.884l.469 2.734c.145.845-.74 1.49-1.492 1.088l-2.456-1.29a1 1 0 00-.932 0l-2.456 1.29c-.752.402-1.637-.243-1.492-1.088l.469-2.734a1 1 0 00-.287-.884L2.615 7.892c-.614-.596-.276-1.637.571-1.76l2.747-.4a1 1 0 00.755-.545l1.286-2.46z"/></svg>
                        Suosittu
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="auction-title"><?php echo htmlspecialchars($auction['title']); ?></h1>
            <div class="auction-meta">
                <div class="meta-item">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/></svg>
                    <?php echo htmlspecialchars($auction['location'] ?? 'Ei sijaintia'); ?>
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                    <?php echo number_format($auction['views']); ?> katselua
                </div>
                <div class="meta-item">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    ID: #<?php echo $auction['id']; ?>
                </div>
            </div>
        </div>

        <?php if (function_exists('is_admin') && is_admin()): ?>
            <div class="admin-actions">
                <a href="/edit_auction.php?id=<?php echo (int)$auction['id']; ?>" class="admin-btn">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    Muokkaa
                </a>
            </div>
        <?php endif; ?>
    </header>

    <!-- Main Content Grid -->
    <div class="auction-content-grid">
        <!-- Left Column - Images & Details -->
        <div class="auction-main-content">
            <!-- Premium Image Gallery -->
            <section class="premium-gallery">
                <?php if (!empty($images)): ?>
                    <div class="gallery-container">
                        <div class="main-image-container">
                            <img 
                                src="<?php echo htmlspecialchars($images[0]['image_path']); ?>" 
                                alt="<?php echo htmlspecialchars($auction['title']); ?>"
                                class="main-image" 
                                id="mainImage"
                                onclick="openImageViewer(0)"
                            >
                            <div class="image-overlay">
                                <button class="fullscreen-btn" onclick="openImageViewer(0)">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 11-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <div class="image-counter">1 / <?php echo count($images); ?></div>
                            </div>
                        </div>
                        
                        <?php if (count($images) > 1): ?>
                            <div class="image-thumbnails">
                                <?php foreach ($images as $index => $image): ?>
                                    <img 
                                        src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                        alt="Kuva <?php echo $index + 1; ?>"
                                        class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                        data-index="<?php echo $index; ?>"
                                        onclick="switchMainImage(<?php echo $index; ?>, '<?php echo htmlspecialchars($image['image_path']); ?>')"
                                    >
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($images[0]['caption'])): ?>
                            <p class="image-caption" id="imageCaption">
                                <?php echo htmlspecialchars($images[0]['caption']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="no-images">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                        <h3>Ei kuvia saatavilla</h3>
                        <p>Tälle kohteelle ei ole lisätty kuvia</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Premium Description -->
            <?php if (!empty(trim($auction['description']))): ?>
                <section class="premium-card">
                    <h2 class="card-title">Kuvaus</h2>
                    <div class="description-content">
                        <?php echo nl2br(htmlspecialchars($auction['description'])); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Premium Technical Details -->
            <section class="premium-card">
                <h2 class="card-title">Tekniset tiedot</h2>
                <div class="specs-grid">
                    <?php
                    // Category-specific technical details
                    $specs = [];
                    
                    if ($categoryKey === 'ajoneuvot'):
                        $specs = [
                            'Merkki' => meta_value($metadata, 'vehicle_brand'),
                            'Malli' => meta_value($metadata, 'vehicle_model'),
                            'Vuosimalli' => meta_value($metadata, 'vehicle_year'),
                            'Mittarilukema' => meta_value($metadata, 'mileage') !== '-' ? meta_value($metadata, 'mileage') . ' km' : '-',
                            'Moottori' => meta_value($metadata, 'engine'),
                            'Polttoaine' => meta_value($metadata, 'fuel_type'),
                            'Voimansiirto' => meta_value($metadata, 'transmission'),
                            'Rekisteröity' => meta_bool($metadata, 'registered'),
                            'Katsastettu' => meta_bool($metadata, 'inspected'),
                            'Huoltokirja' => meta_bool($metadata, 'service_book'),
                        ];
                    elseif ($categoryKey === 'kiinteistöt'):
                        $specs = [
                            'Kiinteistötyyppi' => meta_value($metadata, 'property_type'),
                            'Huoneistotyyppi' => meta_value($metadata, 'room_type'),
                            'Asuinpinta-ala' => meta_value($metadata, 'living_area') !== '-' ? meta_value($metadata, 'living_area') . ' m²' : '-',
                            'Tontin pinta-ala' => meta_value($metadata, 'plot_area') !== '-' ? meta_value($metadata, 'plot_area') . ' m²' : '-',
                            'Rakennusvuosi' => meta_value($metadata, 'build_year'),
                            'Energialuokka' => meta_value($metadata, 'energy_class'),
                        ];
                    else:
                        $specs = [
                            'Koko/Mitat' => meta_value($metadata, 'dimensions', meta_value($metadata, 'size')),
                            'Paino' => meta_value($metadata, 'weight') !== '-' ? meta_value($metadata, 'weight') . ' kg' : '-',
                            'Materiaali' => meta_value($metadata, 'material'),
                            'Valmistaja' => meta_value($metadata, 'manufacturer', meta_value($metadata, 'brand')),
                            'Malli' => meta_value($metadata, 'model'),
                            'Kunto' => meta_value($metadata, 'condition_grade', $auction['condition_description'] ?? '-'),
                        ];
                    endif;

                    foreach ($specs as $label => $value):
                        if ($value !== '-'):
                    ?>
                        <div class="spec-item">
                            <dt class="spec-label"><?php echo htmlspecialchars($label); ?></dt>
                            <dd class="spec-value"><?php echo htmlspecialchars($value); ?></dd>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                
                <?php if (!empty($auction['condition_description'])): ?>
                    <div class="condition-section">
                        <h3>Kunnon arviointi</h3>
                        <p class="condition-text"><?php echo nl2br(htmlspecialchars($auction['condition_description'])); ?></p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Bid History -->
            <?php if (!empty($bids)): ?>
                <section class="premium-card">
                    <h2 class="card-title">Tarjoushistoria (<?php echo count($bids); ?>)</h2>
                    <div class="bids-container">
                        <?php foreach (array_slice($bids, 0, 10) as $index => $bid): ?>
                            <div class="bid-item">
                                <div class="bid-rank">#<?php echo $index + 1; ?></div>
                                <div class="bid-details">
                                    <div class="bid-amount"><?php echo number_format($bid['amount'], 0, ',', ' '); ?> €</div>
                                    <div class="bid-meta">
                                        <span class="bid-user"><?php echo htmlspecialchars($bid['username']); ?></span>
                                        <span class="bid-time"><?php echo date('d.m. H:i', strtotime($bid['bid_time'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Terms & Conditions -->
            <section class="premium-card">
                <h2 class="card-title">Huutokaupan ehdot</h2>
                <div class="terms-content">
                    <div class="terms-grid">
                        <div class="term-item">
                            <h4>Maksu</h4>
                            <p>Kohde on maksettava 1 arkipäivän kuluessa huutokaupan päättymisestä.</p>
                        </div>
                        <div class="term-item">
                            <h4>Nouto</h4>
                            <p>Noutettava 5 arkipäivän kuluessa. Varastointikulu 40€/päivä.</p>
                        </div>
                        <div class="term-item">
                            <h4>Takuu</h4>
                            <p>Kohteet myydään sellaisenaan ilman takuuta ellei toisin mainita.</p>
                        </div>
                        <div class="term-item">
                            <h4>Palautus</h4>
                            <p>Huutokauppaostoille ei palautusoikeutta kuluttajansuojalainsäädännön mukaisesti.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Sidebar - Bidding & Info -->
        <div class="auction-sidebar">
            <!-- Premium Bidding Card -->
            <div class="premium-bidding-card">
                <div class="pricing-section">
                    <div class="current-price">
                        <span class="price-label">Nykyinen hinta</span>
                        <span class="price-value"><?php echo number_format($auction['current_price'], 0, ',', ' '); ?> €</span>
                    </div>
                    
                    <?php if (!empty($auction['buy_now_price'])): ?>
                        <div class="buy-now-price">
                            <span class="price-label">Osta heti</span>
                            <span class="price-value green"><?php echo number_format($auction['buy_now_price'], 0, ',', ' '); ?> €</span>
                        </div>
                    <?php endif; ?>
    
                    <div class="starting-price">
                        <span>Aloitushinta: <?php echo number_format($auction['starting_price'], 0, ',', ' '); ?> €</span>
                    </div>
                </div>

                <!-- Premium Countdown -->
                <div class="countdown-section">
                    <div class="countdown-label">Huutokauppa päättyy</div>
                    <div class="countdown-timer" data-end-time="<?php echo $auctionEndUnix; ?>">
                        <div class="countdown-time" id="countdownDisplay">
                            Lataa...
                        </div>
                        <div class="countdown-date">
                            <?php echo date('d.m.Y H:i', $auctionEndUnix); ?>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="bid-btn primary" id="placeBidBtn">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zM8 6V5a2 2 0 114 0v1H8z" clip-rule="evenodd"/>
                        </svg>
                        Tee tarjous
                    </button>
                    
                    <?php if (!empty($auction['buy_now_price'])): ?>
                        <button class="bid-btn success" id="buyNowBtn">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Osta heti
                        </button>
                    <?php endif; ?>

                    <button class="bid-btn secondary" id="watchBtn">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                        Seuraa kohdetta
                    </button>
                </div>

                <!-- Auction Stats -->
                <div class="auction-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $auction['bid_count'] ?? 0; ?></div>
                        <div class="stat-label">Tarjousta</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($auction['views']); ?></div>
                        <div class="stat-label">Katselua</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $auction['watch_count'] ?? 0; ?></div>
                        <div class="stat-label">Seuraajaa</div>
                    </div>
                </div>
            </div>

            <!-- Seller Information -->
            <div class="premium-card seller-card">
                <h3>Myyjätiedot</h3>
                <div class="seller-profile">
                    <div class="seller-avatar">
                        <?php 
                        $sellerName = $auction['seller_username'] ?? 'Huutokaupat.com';
                        echo mb_strtoupper(mb_substr($sellerName, 0, 2, 'UTF-8'), 'UTF-8');
                        ?>
                    </div>
                    <div class="seller-info">
                        <div class="seller-name"><?php echo htmlspecialchars($sellerName); ?></div>
                        <div class="seller-rating">
                            <div class="stars">★★★★☆</div>
                            <span class="rating-value">4.2 (127 arvostelua)</span>
                        </div>
                        <div class="seller-metrics">
                            <div class="metric">
                                <span class="metric-value">245</span>
                                <span class="metric-label">Myyntiä</span>
                            </div>
                            <div class="metric">
                                <span class="metric-value">98%</span>
                                <span class="metric-label">Luotettavuus</span>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="contact-seller-btn">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    Ota yhteyttä myyjään
                </button>
            </div>

            <!-- Trust Indicators -->
            <div class="trust-indicators">
                <div class="trust-item">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <div class="trust-title">Turvallinen maksu</div>
                        <div class="trust-desc">Rahat palautetaan jos kohde ei vastaa kuvausta</div>
                    </div>
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <div class="trust-title">Varmennettu myyjä</div>
                        <div class="trust-desc">Myyjän henkilöllisyys on tarkistettu</div>
                    </div>
                </div>
                <div class="trust-item">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <div class="trust-title">Asiakastuki</div>
                        <div class="trust-desc">Autamme ongelmatilanteissa</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Auctions -->
    <?php if (!empty($relatedAuctions)): ?>
        <section class="related-auctions">
            <h2>Samankaltaisia kohteita</h2>
            <div class="related-grid">
                <?php foreach ($relatedAuctions as $related): ?>
                    <a href="auction.php?id=<?php echo $related['id']; ?>" class="related-card">
                        <div class="related-image">
                            <img 
                                src="<?php echo htmlspecialchars($related['image_url'] ?? '/assets/images/placeholder.svg'); ?>" 
                                alt="<?php echo htmlspecialchars($related['title']); ?>"
                            >
                        </div>
                        <div class="related-content">
                            <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                            <div class="related-price"><?php echo number_format($related['current_price'], 0, ',', ' '); ?> €</div>
                            <div class="related-time">
                                <?php
                                $timeLeft = strtotime($related['end_time']) - time();
                                if ($timeLeft > 0) {
                                    $days = floor($timeLeft / 86400);
                                    $hours = floor($timeLeft % 86400 / 3600);
                                    if ($days > 0) {
                                        echo "{$days}pv {$hours}h";
                                    } else {
                                        $minutes = floor($timeLeft % 3600 / 60);
                                        echo "{$hours}h {$minutes}min";
                                    }
                                } else {
                                    echo "Päättynyt";
                                }
                                ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Image Viewer Modal -->
<div id="imageViewer" class="image-viewer">
    <div class="image-viewer-overlay" onclick="closeImageViewer()"></div>
    <div class="image-viewer-content">
        <button class="image-viewer-close" onclick="closeImageViewer()">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
        <div class="image-viewer-main">
            <button class="image-viewer-prev" onclick="navigateImage(-1)">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
            <img id="viewerImage" src="" alt="" class="viewer-image">
            <button class="image-viewer-next" onclick="navigateImage(1)">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
        <div class="image-viewer-info">
            <div class="viewer-caption" id="viewerCaption"></div>
            <div class="viewer-counter" id="viewerCounter">1 / 1</div>
        </div>
    </div>
</div>

<script>
// Premium Auction Detail JavaScript
(() => {
    'use strict';

    let currentImageIndex = 0;
    const images = <?php echo json_encode($images); ?>;
    const auctionEndTime = <?php echo $auctionEndUnix * 1000; ?>;

    // Image Gallery Functions
    window.switchMainImage = function(index, imageSrc) {
        currentImageIndex = index;
        const mainImage = document.getElementById('mainImage');
        const imageCaption = document.getElementById('imageCaption');
        const counter = document.querySelector('.image-counter');
        
        if (mainImage) {
            mainImage.src = imageSrc;
        }
        
        if (imageCaption && images[index]?.caption) {
            imageCaption.textContent = images[index].caption;
            imageCaption.style.display = 'block';
        } else if (imageCaption) {
            imageCaption.style.display = 'none';
        }
        
        if (counter) {
            counter.textContent = `${index + 1} / ${images.length}`;
        }
        
        // Update thumbnail active state
        document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    };

    // Image Viewer Modal
    window.openImageViewer = function(index) {
        currentImageIndex = index;
        updateViewerImage();
        document.getElementById('imageViewer').classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeImageViewer = function() {
        document.getElementById('imageViewer').classList.remove('open');
        document.body.style.overflow = '';
    };

    window.navigateImage = function(direction) {
        currentImageIndex = (currentImageIndex + direction + images.length) % images.length;
        updateViewerImage();
    };

    function updateViewerImage() {
        const viewerImage = document.getElementById('viewerImage');
        const viewerCaption = document.getElementById('viewerCaption');
        const viewerCounter = document.getElementById('viewerCounter');
        
        if (images[currentImageIndex]) {
            viewerImage.src = images[currentImageIndex].image_path;
            viewerImage.alt = images[currentImageIndex].caption || `Kuva ${currentImageIndex + 1}`;
            
            if (viewerCaption) {
                viewerCaption.textContent = images[currentImageIndex].caption || '';
                viewerCaption.style.display = images[currentImageIndex].caption ? 'block' : 'none';
            }
            
            if (viewerCounter) {
                viewerCounter.textContent = `${currentImageIndex + 1} / ${images.length}`;
            }
        }
    }

    // Countdown Timer
    function updateCountdown() {
        const now = new Date().getTime();
        const timeLeft = auctionEndTime - now;
        const display = document.getElementById('countdownDisplay');
        
        if (!display) return;
        
        if (timeLeft <= 0) {
            display.textContent = 'Huutokauppa päättynyt';
            display.closest('.countdown-timer').classList.add('ended');
            return;
        }
        
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        if (days > 0) {
            display.textContent = `${days} päivää ${hours}h ${minutes}min`;
        } else if (hours > 0) {
            display.textContent = `${hours}h ${minutes}min ${seconds}s`;
        } else if (minutes > 0) {
            display.textContent = `${minutes}min ${seconds}s`;
        } else {
            display.textContent = `${seconds}s`;
            display.closest('.countdown-timer').classList.add('urgent');
        }
    }

    // Initialize
    updateCountdown();
    setInterval(updateCountdown, 1000);
    
    // Keyboard navigation for image viewer
    document.addEventListener('keydown', (e) => {
        if (document.getElementById('imageViewer').classList.contains('open')) {
            if (e.key === 'Escape') closeImageViewer();
            if (e.key === 'ArrowLeft') navigateImage(-1);
            if (e.key === 'ArrowRight') navigateImage(1);
        }
    });

    // Action button handlers
    document.getElementById('placeBidBtn')?.addEventListener('click', () => {
        console.log('Place bid clicked');
        // Implement bidding modal
    });

    document.getElementById('buyNowBtn')?.addEventListener('click', () => {
        console.log('Buy now clicked');
        // Implement buy now functionality
    });

    document.getElementById('watchBtn')?.addEventListener('click', () => {
        console.log('Watch item clicked');
        // Implement watch functionality
    });

    console.log('Premium auction detail page initialized');
})();
</script>

<script src="premium-ux.js" defer></script>

<?php include SRC_PATH . '/views/footer.php'; ?>