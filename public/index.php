<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/models/Database.php';
require_once __DIR__ . '/../src/models/Auction.php';
require_once __DIR__ . '/../src/models/Category.php';

$auctionModel = new Auction();
$categoryModel = new Category();

// Get featured auctions
$featuredAuctions = $auctionModel->getActiveAuctions(12);
$categories = $categoryModel->getAllCategories();

$pageTitle = SITE_NAME . ' - Etusivu';
include __DIR__ . '/../src/views/header.php';
?>

<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Tervetuloa Huutoon</h1>
    <p class="text-xl text-gray-600">Suomen suurin verkkohuutokauppa-alusta</p>
</div>

<!-- Categories -->
<div class="mb-12">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Selaa kategorioita</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($categories as $category): ?>
            <a href="/category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>" 
               class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow text-center">
                <div class="text-4xl mb-2"><?php echo htmlspecialchars($category['icon']); ?></div>
                <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($category['name']); ?></h3>
                <p class="text-sm text-gray-500"><?php echo $category['active_count']; ?> kohdetta</p>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Auctions -->
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Päättyvät pian</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($featuredAuctions as $auction): ?>
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
                                 data-endtime="<?php echo $auction['end_time']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../src/views/footer.php'; ?>
