<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/models/Database.php';
require_once __DIR__ . '/src/models/Auction.php';

$auctionModel = new Auction();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: /');
    exit;
}

$auction = $auctionModel->getAuctionById($id);

if (!$auction) {
    header('Location: /');
    exit;
}

// Increment view count
$auctionModel->incrementViews($id);
// Keep in-memory auction data in sync with incremented view count
$auction['views'] = (isset($auction['views']) ? (int)$auction['views'] : 0) + 1;

$images = $auctionModel->getAuctionImages($id);
$bids = $auctionModel->getAuctionBids($id);

$pageTitle = $auction['title'] . ' - ' . SITE_NAME;
include __DIR__ . '/src/views/header.php';
?>

<div class="mb-4">
    <a href="/" class="text-blue-600 hover:underline">← Takaisin etusivulle</a>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
        <!-- Images Section -->
        <div>
            <?php if (!empty($images)): ?>
                <div class="mb-4">
                    <img src="<?php echo htmlspecialchars($images[0]['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($auction['title']); ?>"
                         class="w-full rounded-lg object-cover"
                         id="mainImage">
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach ($images as $image): ?>
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="Thumbnail"
                                 class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 border-2 border-transparent hover:border-blue-500"
                                 onclick="document.getElementById('mainImage').src = this.src">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400 text-6xl">📦</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Details Section -->
        <div>
            <div class="mb-4">
                <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                    <?php echo htmlspecialchars($auction['category_name']); ?>
                </span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                <?php echo htmlspecialchars($auction['title']); ?>
            </h1>

            <!-- Price and Bidding Section -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-sm text-gray-500">Aloitushinta</div>
                        <div class="text-lg font-semibold text-gray-700">
                            <?php echo number_format($auction['starting_price'], 2, ',', ' '); ?> €
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Nykyinen hinta</div>
                        <div class="text-2xl font-bold text-blue-600">
                            <?php echo number_format($auction['current_price'], 2, ',', ' '); ?> €
                        </div>
                    </div>
                </div>

                <?php if ($auction['buy_now_price']): ?>
                    <div class="mb-4">
                        <div class="text-sm text-gray-500">Osta heti -hinta</div>
                        <div class="text-xl font-bold text-green-600">
                            <?php echo number_format($auction['buy_now_price'], 2, ',', ' '); ?> €
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <div class="text-sm text-gray-500">Huutokauppa päättyy</div>
                    <div class="countdown text-xl font-bold text-red-600" 
                         data-endtime="<?php echo htmlspecialchars(date('c', strtotime($auction['end_time'])), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        <?php echo date('d.m.Y H:i', strtotime($auction['end_time'])); ?>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 text-center text-sm border-t pt-4">
                    <div>
                        <div class="font-semibold text-gray-900"><?php echo $auction['bid_count']; ?></div>
                        <div class="text-gray-500">Tarjousta</div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900"><?php echo $auction['views']; ?></div>
                        <div class="text-gray-500">Katselukertaa</div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900"><?php echo $auction['watch_count']; ?></div>
                        <div class="text-gray-500">Seuraajaa</div>
                    </div>
                </div>
            </div>

            <!-- Seller Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-900 mb-2">Myyjä</h3>
                <p class="text-gray-700"><?php echo htmlspecialchars($auction['seller_username']); ?></p>
                <?php if ($auction['location']): ?>
                    <p class="text-sm text-gray-500 mt-1">
                        📍 <?php echo htmlspecialchars($auction['location']); ?>
                    </p>
                <?php endif; ?>
            </div>

            <button class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Tee tarjous
            </button>
        </div>
    </div>

    <!-- Description -->
    <div class="border-t p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Kuvaus</h2>
        <div class="prose max-w-none text-gray-700">
            <?php echo nl2br(htmlspecialchars($auction['description'])); ?>
        </div>

        <?php if ($auction['condition_description']): ?>
            <div class="mt-4">
                <span class="text-sm font-semibold text-gray-700">Kunto:</span>
                <span class="text-sm text-gray-600"><?php echo htmlspecialchars($auction['condition_description']); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bid History -->
    <?php if (!empty($bids)): ?>
        <div class="border-t p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Tarjoushistoria</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tarjous</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Käyttäjä</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Aika</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($bids as $bid): ?>
                            <tr>
                                <td class="px-4 py-2 font-semibold text-blue-600">
                                    <?php echo number_format($bid['amount'], 2, ',', ' '); ?> €
                                </td>
                                <td class="px-4 py-2 text-gray-700">
                                    <?php echo htmlspecialchars($bid['username']); ?>
                                </td>
                                <td class="px-4 py-2 text-gray-500 text-sm">
                                    <?php echo date('d.m.Y H:i', strtotime($bid['bid_time'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/src/views/footer.php'; ?>
