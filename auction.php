<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

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

// Parse AI-generated category-specific details
$aiDetails = [];
if (!empty($auction['ai_details'])) {
    $decoded = json_decode($auction['ai_details'], true);
    if ($decoded && !empty($decoded['fields'])) {
        $aiDetails = $decoded['fields'];
    }
}

$pageTitle = $auction['title'] . ' - ' . SITE_NAME;
include SRC_PATH . '/views/header.php';
?>

<div class="mb-4">
    <a href="/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Takaisin etusivulle
    </a>
</div>

<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
        <!-- Images Section -->
        <div class="p-6 lg:border-r border-gray-100">
            <?php if (!empty($images)): ?>
                <div class="mb-3 rounded-xl overflow-hidden bg-gray-50 border border-gray-100" style="aspect-ratio:4/3;">
                    <img src="<?php echo htmlspecialchars($images[0]['image_path']); ?>"
                         alt="<?php echo htmlspecialchars($auction['title']); ?>"
                         class="w-full h-full object-contain"
                         id="mainImage">
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="grid grid-cols-5 gap-2 mt-2">
                        <?php foreach ($images as $image): ?>
                            <button type="button"
                                    onclick="document.getElementById('mainImage').src = this.querySelector('img').src"
                                    class="rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 focus:border-blue-500 focus:outline-none transition-all"
                                    style="aspect-ratio:1/1;">
                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>"
                                     alt="Thumbnail"
                                     class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="w-full rounded-xl bg-gray-100 flex items-center justify-center" style="aspect-ratio:4/3;">
                    <span class="text-gray-300 text-8xl">📦</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Details Section -->
        <div class="p-6 flex flex-col">
            <!-- Category badge -->
            <div class="mb-3">
                <span class="inline-flex items-center bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full border border-blue-100">
                    <?php echo htmlspecialchars($auction['category_name']); ?>
                </span>
                <?php if ($auction['status'] === 'active'): ?>
                    <span class="inline-flex items-center bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-100 ml-2">
                        ● Aktiivinen
                    </span>
                <?php elseif ($auction['status'] === 'ended'): ?>
                    <span class="inline-flex items-center bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full border border-gray-200 ml-2">
                        Päättynyt
                    </span>
                <?php endif; ?>
            </div>

            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-5 leading-tight">
                <?php echo htmlspecialchars($auction['title']); ?>
            </h1>

            <!-- Price and Bidding Section -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 mb-5 border border-blue-100">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Aloitushinta</div>
                        <div class="text-base font-semibold text-gray-700">
                            <?php echo number_format($auction['starting_price'], 2, ',', ' '); ?> €
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Nykyinen hinta</div>
                        <div class="text-3xl font-extrabold text-blue-700">
                            <?php echo number_format($auction['current_price'], 2, ',', ' '); ?> €
                        </div>
                    </div>
                </div>

                <?php if ($auction['buy_now_price']): ?>
                    <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-xs text-green-700 uppercase tracking-wide font-semibold mb-1">Osta heti -hinta</div>
                        <div class="text-xl font-bold text-green-700">
                            <?php echo number_format($auction['buy_now_price'], 2, ',', ' '); ?> €
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Huutokauppa päättyy</div>
                    <div class="countdown text-xl font-bold text-red-600"
                         data-endtime="<?php echo htmlspecialchars(date('c', strtotime($auction['end_time'])), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        <?php echo date('d.m.Y H:i', strtotime($auction['end_time'])); ?>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center border-t border-blue-100 pt-4">
                    <div class="bg-white rounded-lg py-2 px-1 shadow-sm">
                        <div class="text-lg font-bold text-gray-900"><?php echo $auction['bid_count']; ?></div>
                        <div class="text-xs text-gray-500">Tarjousta</div>
                    </div>
                    <div class="bg-white rounded-lg py-2 px-1 shadow-sm">
                        <div class="text-lg font-bold text-gray-900"><?php echo $auction['views']; ?></div>
                        <div class="text-xs text-gray-500">Katselukerrat</div>
                    </div>
                    <div class="bg-white rounded-lg py-2 px-1 shadow-sm">
                        <div class="text-lg font-bold text-gray-900"><?php echo isset($auction['watch_count']) ? $auction['watch_count'] : 0; ?></div>
                        <div class="text-xs text-gray-500">Seuraajaa</div>
                    </div>
                </div>
            </div>

            <!-- Seller Info -->
            <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-100 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-bold text-sm">
                        <?php echo mb_strtoupper(mb_substr($auction['seller_username'], 0, 1)); ?>
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Myyjä</div>
                    <div class="font-semibold text-gray-900 truncate"><?php echo htmlspecialchars($auction['seller_username']); ?></div>
                    <?php if ($auction['location']): ?>
                        <div class="text-sm text-gray-500 flex items-center mt-0.5">
                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <?php echo htmlspecialchars($auction['location']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bid Buttons -->
            <div class="mt-auto">
                <?php if ($auction['buy_now_price']): ?>
                    <button class="w-full bg-green-600 text-white py-3 px-6 rounded-xl font-bold hover:bg-green-700 active:bg-green-800 transition-colors mb-3 shadow-sm">
                        ⚡ Osta heti <?php echo number_format($auction['buy_now_price'], 2, ',', ' '); ?> €
                    </button>
                <?php endif; ?>
                <button class="w-full bg-blue-600 text-white py-3 px-6 rounded-xl font-bold hover:bg-blue-700 active:bg-blue-800 transition-colors shadow-sm">
                    🔨 Tee tarjous
                </button>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="border-t border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h12"/>
            </svg>
            Kuvaus
        </h2>
        <div class="text-gray-700 leading-relaxed whitespace-pre-line">
            <?php echo htmlspecialchars($auction['description'] ?? ''); ?>
        </div>
        <?php if ($auction['condition_description']): ?>
            <div class="mt-4 inline-flex items-center bg-amber-50 text-amber-800 text-sm font-medium px-3 py-1.5 rounded-lg border border-amber-100">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Kunto: <?php echo htmlspecialchars($auction['condition_description']); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- AI-generated Category-Specific Details (below description) -->
    <?php if (!empty($aiDetails)): ?>
        <div class="border-t border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Kohteen tiedot
            </h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8">
                <?php foreach ($aiDetails as $field): ?>
                    <?php if (!empty($field['label']) && !empty($field['value'])): ?>
                        <div class="flex justify-between items-baseline py-2 border-b border-gray-100">
                            <dt class="text-sm font-semibold text-gray-500 capitalize"><?php echo htmlspecialchars($field['label']); ?></dt>
                            <dd class="text-sm font-medium text-gray-900 ml-4 text-right"><?php echo htmlspecialchars($field['value']); ?></dd>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </dl>
        </div>
    <?php endif; ?>

    <!-- Bid History -->
    <?php if (!empty($bids)): ?>
        <div class="border-t border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Tarjoushistoria
            </h2>
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tarjous</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Käyttäjä</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aika</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        <?php foreach ($bids as $index => $bid): ?>
                            <tr class="<?php echo $index === 0 ? 'bg-blue-50' : ''; ?> hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-bold <?php echo $index === 0 ? 'text-blue-700' : 'text-gray-700'; ?>">
                                    <?php if ($index === 0): ?><span class="text-xs mr-1">🏆</span><?php endif; ?>
                                    <?php echo number_format($bid['amount'], 2, ',', ' '); ?> €
                                </td>
                                <td class="px-4 py-3 text-gray-700 text-sm">
                                    <?php echo htmlspecialchars($bid['username']); ?>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs">
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

<?php include SRC_PATH . '/views/footer.php'; ?>
