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

function meta_value(array $metadata, string $key, string $fallback = '-')
{
    $value = isset($metadata[$key]) ? trim((string)$metadata[$key]) : '';
    return $value !== '' ? $value : $fallback;
}

function meta_bool(array $metadata, string $key, string $yes = 'Kyllä', string $no = 'Ei')
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
                        ...existing code...
<?php include SRC_PATH . '/views/footer.php'; ?>
