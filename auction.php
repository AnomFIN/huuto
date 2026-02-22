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
$auction['views'] = (isset($auction['views']) ? (int)$auction['views'] : 0) + 1;

$images = $auctionModel->getAuctionImages($id);
$bids   = $auctionModel->getAuctionBids($id);

// Build anonymous bidder map (Tarjoaja 1, 2, 3 … based on first-bid order)
$bidderMap     = [];
$bidderCounter = 0;
$bidsAsc = array_reverse($bids); // bids are DESC by time; reverse for ASC chronology
foreach ($bidsAsc as $b) {
    $uid = $b['user_id'];
    if (!isset($bidderMap[$uid])) {
        $bidderCounter++;
        $bidderMap[$uid] = $bidderCounter;
    }
}
$uniqueBidderCount = count($bidderMap);
$totalBidCount     = count($bids);

// Parse AI-generated category-specific details
$aiDetails = [];
if (!empty($auction['ai_details'])) {
    $decoded = json_decode($auction['ai_details'], true);
    if ($decoded && !empty($decoded['fields'])) {
        $aiDetails = $decoded['fields'];
    }
}

$isActive      = $auction['status'] === 'active';
$minNextBid    = (float)$auction['current_price'] + (float)$auction['bid_increment'];
$isLoggedIn    = is_logged_in();
$currentUserId = current_user_id();
$isSeller      = $isLoggedIn && (int)$currentUserId === (int)$auction['user_id'];

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

<!-- Main Card -->
<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">

        <!-- ── Images ── -->
        <div class="p-6 lg:border-r border-gray-100">
            <?php if (!empty($images)): ?>
                <div class="mb-3 rounded-xl overflow-hidden bg-gray-50 border border-gray-100" style="aspect-ratio:4/3;">
                    <img src="<?php echo htmlspecialchars($images[0]['image_path']); ?>"
                         alt="<?php echo htmlspecialchars($auction['title']); ?>"
                         class="w-full h-full object-contain" id="mainImage">
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="grid grid-cols-5 gap-2 mt-2">
                        <?php foreach ($images as $img): ?>
                            <button type="button"
                                    onclick="document.getElementById('mainImage').src = this.querySelector('img').src"
                                    class="rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 focus:border-blue-500 focus:outline-none transition-all"
                                    style="aspect-ratio:1/1;">
                                <img src="<?php echo htmlspecialchars($img['image_path']); ?>"
                                     alt="Kuva" class="w-full h-full object-cover">
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

        <!-- ── Right column ── -->
        <div class="p-6 flex flex-col">
            <!-- Category + status badges -->
            <div class="mb-3 flex flex-wrap gap-2">
                <span class="inline-flex items-center bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full border border-blue-100">
                    <?php echo htmlspecialchars($auction['category_name']); ?>
                </span>
                <?php if ($isActive): ?>
                    <span class="inline-flex items-center bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full border border-green-100">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>Aktiivinen
                    </span>
                <?php elseif ($auction['status'] === 'ended'): ?>
                    <span class="inline-flex items-center bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full border border-gray-200">
                        Päättynyt
                    </span>
                <?php endif; ?>
            </div>

            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-5 leading-tight">
                <?php echo htmlspecialchars($auction['title']); ?>
            </h1>

            <!-- Price + stats box -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 mb-5 border border-blue-100">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Aloitushinta</div>
                        <div class="text-base font-semibold text-gray-700">
                            <?php echo number_format($auction['starting_price'], 0, ',', ' '); ?> €
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Nykyinen hinta</div>
                        <div class="text-3xl font-extrabold text-blue-700" id="currentPriceDisplay">
                            <?php echo number_format($auction['current_price'], 0, ',', ' '); ?> €
                        </div>
                    </div>
                </div>

                <?php if ($auction['buy_now_price']): ?>
                    <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-xs text-green-700 uppercase tracking-wide font-semibold mb-1">Osta heti -hinta</div>
                        <div class="text-xl font-bold text-green-700">
                            <?php echo number_format($auction['buy_now_price'], 0, ',', ' '); ?> €
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Countdown -->
                <div class="mb-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Huutokauppa päättyy</div>
                    <div class="countdown text-xl font-bold text-red-600"
                         data-endtime="<?php echo htmlspecialchars(date('c', strtotime($auction['end_time'])), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        <?php echo date('d.m.Y H:i', strtotime($auction['end_time'])); ?>
                    </div>
                </div>

                <!-- Stats bar -->
                <div class="grid grid-cols-3 gap-2 text-center border-t border-blue-100 pt-4">
                    <div class="bg-white rounded-lg py-2 px-1 shadow-sm">
                        <div class="text-lg font-bold text-gray-900" id="bidCountDisplay"><?php echo $totalBidCount; ?></div>
                        <div class="text-xs text-gray-500">Tarjousta</div>
                    </div>
                    <div class="bg-white rounded-lg py-2 px-1 shadow-sm">
                        <div class="text-lg font-bold text-gray-900"><?php echo $auction['views']; ?></div>
                        <div class="text-xs text-gray-500">Katselukerrat</div>
                    </div>
                    <div class="bg-white rounded-lg py-2 px-1 shadow-sm">
                        <div class="text-lg font-bold text-gray-900"><?php echo $uniqueBidderCount; ?></div>
                        <div class="text-xs text-gray-500">Tarjoajaa</div>
                    </div>
                </div>
            </div>

            <!-- Seller -->
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

            <!-- Category-specific details panel (in sidebar) -->
            <?php if (!empty($aiDetails)): ?>
                <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Kohteen tiedot
                    </h3>
                    <dl class="space-y-1.5">
                        <?php foreach ($aiDetails as $field): ?>
                            <?php if (!empty($field['label']) && isset($field['value']) && $field['value'] !== ''): ?>
                                <div class="flex justify-between items-baseline text-sm">
                                    <dt class="text-gray-500 shrink-0 mr-2"><?php echo htmlspecialchars($field['label']); ?></dt>
                                    <dd class="font-semibold text-gray-900 text-right"><?php echo htmlspecialchars($field['value']); ?></dd>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </dl>
                </div>
            <?php endif; ?>

            <!-- Bid action buttons -->
            <div class="mt-auto space-y-3">
                <?php if ($auction['buy_now_price'] && $isActive && !$isSeller): ?>
                    <button onclick="<?php echo $isLoggedIn ? 'openBuyNowConfirm()' : "window.location='/auth/login.php'"; ?>"
                            class="w-full bg-green-600 text-white py-3 px-6 rounded-xl font-bold hover:bg-green-700 active:bg-green-800 transition-colors shadow-sm">
                        ⚡ Osta heti — <?php echo number_format($auction['buy_now_price'], 0, ',', ' '); ?> €
                    </button>
                <?php endif; ?>

                <?php if ($isActive && !$isSeller): ?>
                    <button onclick="<?php echo $isLoggedIn ? 'openBidModal()' : "window.location='/auth/login.php'"; ?>"
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-xl font-bold hover:bg-blue-700 active:bg-blue-800 transition-colors shadow-sm text-lg">
                        🔨 Tee tarjous
                    </button>
                    <?php if (!$isLoggedIn): ?>
                        <p class="text-center text-sm text-gray-500">
                            <a href="/auth/login.php" class="text-blue-600 hover:underline">Kirjaudu sisään</a> tehdäksesi tarjouksen
                        </p>
                    <?php else: ?>
                        <p class="text-center text-xs text-gray-400">
                            Vähimmäistarjous: <strong><?php echo number_format($minNextBid, 0, ',', ' '); ?> €</strong>
                            (korotus <?php echo number_format($auction['bid_increment'], 0, ',', ' '); ?> €)
                        </p>
                    <?php endif; ?>
                <?php elseif ($isSeller): ?>
                    <p class="text-center text-sm text-gray-500 py-2 bg-gray-50 rounded-xl border border-gray-200">
                        Tämä on oma kohteesi
                    </p>
                <?php elseif (!$isActive): ?>
                    <p class="text-center text-sm text-gray-500 py-2 bg-gray-50 rounded-xl border border-gray-200">
                        Huutokauppa on päättynyt
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /grid -->

    <!-- ── Description ── -->
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

    <!-- ── Auction Info Box ── -->
    <div class="border-t border-gray-100 p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Location -->
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    Kohteen sijainti
                </div>
                <?php if (!empty($auction['location'])): ?>
                    <p class="text-sm text-gray-800 font-medium mb-1"><?php echo htmlspecialchars($auction['location']); ?></p>
                <?php endif; ?>
                <a href="https://maps.google.com/?q=<?php echo urlencode($auction['location'] ?? ''); ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Avaa kartta
                </a>
            </div>

            <!-- Viewing & Contact -->
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Tiedustelut
                </div>
                <p class="text-sm font-semibold text-gray-800 mb-1">Kohteeseen tutustuminen</p>
                <p class="text-sm text-gray-600">Sopimuksen mukaan</p>
            </div>

            <!-- Payment method -->
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Maksutapa
                </div>
                <p class="text-sm text-gray-600">Verkkopankkimaksu, maksuaika 24 tuntia tarjouksen hyväksymisestä.</p>
            </div>
        </div>

        <!-- Last updated + Share + Report -->
        <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-gray-400 flex items-center">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Päivitetty viimeksi
                <?php echo !empty($auction['updated_at']) ? date('j.n.Y \k\l\o G.i', strtotime($auction['updated_at'])) : date('j.n.Y'); ?>
            </p>
            <div class="flex items-center gap-4">
                <button onclick="navigator.share ? navigator.share({title: <?php echo json_encode($auction['title']); ?>, url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Linkki kopioitu!'))"
                        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Jaa kohde kaverillesi
                </button>
                <button onclick="if(confirm('Haluatko ilmiantaa tämän ilmoituksen?')) alert('Ilmoitus lähetetty. Kiitos!')"
                        class="inline-flex items-center text-sm text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                    Ilmianna ilmoitus
                </button>
            </div>
        </div>
    </div>

    <!-- ── Bid History ── -->
    <div class="border-t border-gray-100 p-6" id="bidHistorySection">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Tarjoushistoria
            </h2>
            <div class="flex gap-4 text-sm text-gray-500">
                <span><strong class="text-gray-900" id="bidHistTotalCount"><?php echo $totalBidCount; ?></strong> tarjousta</span>
                <span><strong class="text-gray-900"><?php echo $uniqueBidderCount; ?></strong> tarjoajaa</span>
            </div>
        </div>

        <?php if (empty($bids)): ?>
            <div class="text-center py-10 text-gray-400">
                <svg class="mx-auto h-10 w-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                </svg>
                <p class="text-sm">Ei tarjouksia vielä. Ole ensimmäinen!</p>
            </div>
        <?php else: ?>
            <?php
            // Find the highest amount bid for the top-of-list highlight
            $maxAmount = 0;
            foreach ($bids as $b) { if ($b['amount'] > $maxAmount) $maxAmount = $b['amount']; }
            ?>
            <div class="bg-blue-600 text-white rounded-xl px-5 py-4 mb-4 flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide opacity-80 mb-1">Korkein tarjous</div>
                    <div class="text-3xl font-extrabold"><?php echo number_format($maxAmount, 0, ',', ' '); ?> €</div>
                </div>
                <?php if ($isActive): ?>
                    <button onclick="<?php echo $isLoggedIn ? 'openBidModal()' : "window.location='/auth/login.php'"; ?>"
                            class="bg-white text-blue-700 font-bold px-5 py-2.5 rounded-lg hover:bg-blue-50 transition-colors text-sm">
                        🔨 Tee tarjous
                    </button>
                <?php endif; ?>
            </div>

            <!-- Bid rows (newest first) -->
            <div class="divide-y divide-gray-100" id="bidList">
                <?php foreach ($bids as $index => $bid):
                    $bidderNum   = $bidderMap[$bid['user_id']] ?? '?';
                    $bidderLabel = 'Tarjoaja ' . $bidderNum;
                    if (!empty($bid['is_auto_bid'])) {
                        $bidderLabel .= ', korotusautomaatti';
                    }
                    $isHighest   = (float)$bid['amount'] === $maxAmount;
                    $bidTs       = strtotime($bid['bid_time']);
                    $dateStr     = date('j.n.', $bidTs);
                    $timeStr     = 'klo ' . date('G.i.s', $bidTs);
                ?>
                    <div class="flex items-center py-3 <?php echo $isHighest ? 'bg-blue-50 -mx-6 px-6 font-semibold' : ''; ?>">
                        <div class="flex-1">
                            <div class="text-<?php echo $isHighest ? 'blue-700 text-lg' : 'gray-900'; ?> font-bold">
                                <?php if ($isHighest): ?><span class="text-base mr-1">🏆</span><?php endif; ?>
                                <?php echo number_format($bid['amount'], 0, ',', ' '); ?> €
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                <?php echo htmlspecialchars($bidderLabel); ?>
                            </div>
                        </div>
                        <div class="text-right text-xs text-gray-400 ml-4">
                            <div class="font-medium text-gray-600"><?php echo $dateStr; ?></div>
                            <div><?php echo $timeStr; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div><!-- /bid history -->
</div><!-- /main card -->

<?php if ($isActive && $isLoggedIn && !$isSeller): ?>
<!-- ── Bid Modal ── -->
<div id="bidModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBidModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md pointer-events-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900">🔨 Tee tarjous</h3>
                <button onclick="closeBidModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 rounded-xl p-4 mb-5 border border-blue-100">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Nykyinen hinta</span>
                        <strong class="text-gray-900"><?php echo number_format($auction['current_price'], 0, ',', ' '); ?> €</strong>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Vähimmäistarjous</span>
                        <strong class="text-blue-700"><?php echo number_format($minNextBid, 0, ',', ' '); ?> €</strong>
                    </div>
                </div>

                <div id="bidError"   class="bg-red-50   text-red-700   px-4 py-3 rounded-xl mb-4 text-sm hidden"></div>
                <div id="bidSuccess" class="bg-green-50 text-green-800 px-4 py-3 rounded-xl mb-4 text-sm hidden"></div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">Tarjousmäärä (€)</label>
                <input type="number" id="bidAmount"
                       min="<?php echo $minNextBid; ?>"
                       step="<?php echo $auction['bid_increment']; ?>"
                       value="<?php echo $minNextBid; ?>"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-2xl font-bold text-center focus:border-blue-500 focus:outline-none mb-5"
                       oninput="validateBidAmount(this)">

                <input type="hidden" id="bidCsrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

                <button id="btnSubmitBid" onclick="submitBid()"
                        class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-700 transition-colors text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    Vahvista tarjous
                </button>
                <p class="text-center text-xs text-gray-400 mt-3">
                    Tarjoukset ovat sitovia
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
const BID_MIN  = <?php echo (float)$minNextBid; ?>;
const BID_STEP = <?php echo (float)$auction['bid_increment']; ?>;
const AUCTION_ID = <?php echo (int)$id; ?>;

function openBidModal() {
    const modal = document.getElementById('bidModal');
    if (!modal) return;
    document.getElementById('bidError').classList.add('hidden');
    document.getElementById('bidSuccess').classList.add('hidden');
    modal.classList.remove('hidden');
    document.getElementById('bidAmount').focus();
}

function closeBidModal() {
    const modal = document.getElementById('bidModal');
    if (modal) modal.classList.add('hidden');
}

function validateBidAmount(input) {
    const val = parseFloat(input.value);
    const btn = document.getElementById('btnSubmitBid');
    if (btn) btn.disabled = isNaN(val) || val < BID_MIN;
}

async function submitBid() {
    const amountInput = document.getElementById('bidAmount');
    const csrf        = document.getElementById('bidCsrf').value;
    const errorEl     = document.getElementById('bidError');
    const successEl   = document.getElementById('bidSuccess');
    const btn         = document.getElementById('btnSubmitBid');
    const amount      = parseFloat(amountInput.value);

    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');

    if (isNaN(amount) || amount < BID_MIN) {
        errorEl.textContent = '❌ Tarjous on liian pieni. Vähimmäistarjous: ' + BID_MIN.toLocaleString('fi-FI', {maximumFractionDigits: 0}) + ' €';
        errorEl.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    btn.textContent = '⏳ Lähetetään...';

    const formData = new FormData();
    formData.append('auction_id', AUCTION_ID);
    formData.append('amount', amount);
    formData.append('csrf_token', csrf);

    try {
        const response = await fetch('api_place_bid.php', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            successEl.textContent = '✅ ' + data.message;
            successEl.classList.remove('hidden');
            // Reload after short delay to show updated bid history
            setTimeout(() => location.reload(), 1200);
        } else {
            errorEl.textContent = '❌ ' + (data.error || 'Tarjouksen teko epäonnistui');
            errorEl.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = 'Vahvista tarjous';
        }
    } catch (e) {
        errorEl.textContent = '❌ Verkkovirhe. Yritä uudelleen.';
        errorEl.classList.remove('hidden');
        btn.disabled = false;
        btn.textContent = 'Vahvista tarjous';
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeBidModal();
});
</script>

<?php include SRC_PATH . '/views/footer.php'; ?>
