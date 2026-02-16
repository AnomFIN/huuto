<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

$auctionModel = new Auction();
$categoryModel = new Category();

$categorySlug = isset($_GET['slug']) ? $_GET['slug'] : null;

if ($categorySlug) {
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
?>

<div class="mb-8">
    <?php if ($category): ?>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
            <?php echo htmlspecialchars($category['icon']); ?> <?php echo htmlspecialchars($category['name']); ?>
        </h1>
        <p class="text-xl text-gray-600"><?php echo htmlspecialchars($category['description']); ?></p>
    <?php else: ?>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Kaikki kohteet</h1>
        <p class="text-xl text-gray-600">Selaa kaikkia aktiivisia huutokauppoja</p>
    <?php endif; ?>
</div>

<?php if (empty($auctions)): ?>
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <div class="text-6xl mb-4">📦</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Ei kohteita</h2>
        <p class="text-gray-600">Tässä kategoriassa ei ole tällä hetkellä aktiivisia huutokauppoja.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($auctions as $auction): ?>
            <a href="/auction.php?id=<?php echo $auction['id']; ?>" 
               class="bg-white rounded-lg shadow hover:shadow-xl transition-shadow overflow-hidden">
                <?php if ($auction['primary_image']): ?>
                    <img src="<?php echo htmlspecialchars($auction['primary_image']); ?>" 
                         alt="<?php echo htmlspecialchars($auction['title']); ?>"
                         class="w-full h-48 object-cover">
                <?php else: ?>
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400 text-4xl">📦</span>
                    </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 truncate">
                        <?php echo htmlspecialchars($auction['title']); ?>
                    </h3>
                    
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-500">
                            <?php echo htmlspecialchars($auction['category_name']); ?>
                        </span>
                        <span class="text-sm text-gray-500">
                            <?php echo $auction['bid_count']; ?> tarjousta
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500">Nykyinen hinta</div>
                            <div class="text-lg font-bold text-blue-600">
                                <?php echo number_format($auction['current_price'], 2, ',', ' '); ?> €
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">Päättyy</div>
                            <div class="countdown text-sm font-semibold text-red-600" 
                                 data-endtime="<?php echo htmlspecialchars(date('c', strtotime($auction['end_time'])), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include SRC_PATH . '/views/footer.php'; ?>
