<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/db.php';
require_once __DIR__ . '/src/models/Database.php';
require_once __DIR__ . '/src/models/Auction.php';
require_once __DIR__ . '/src/models/Category.php';
require_once __DIR__ . '/app/auth.php';
require_once __DIR__ . '/app/helpers.php';

// Initialize with empty arrays in case of errors
$closingSoonAuctions = [];
$popularAuctions = [];
$moreClosingAuctions = [];
$featuredAuctions = [];
$categories = [];

try {
    $auctionModel = new Auction();
    $categoryModel = new Category();
    
    // Get closing soon auctions for carousel
    $closingSoonAuctions = $auctionModel->getClosingSoonAuctions(5);
    // Get popular auctions
    $popularAuctions = $auctionModel->getPopularAuctions(20);
    // Get more closing soon auctions for the bottom section
    $moreClosingAuctions = $auctionModel->getClosingSoonAuctions(20);
    // Get featured auctions
    $featuredAuctions = $auctionModel->getActiveAuctions(12);
    $categories = $categoryModel->getAllCategories();
} catch (Exception $e) {
    // Log the error but don't crash the page
    error_log("Error loading homepage data: " . $e->getMessage());
}

$pageTitle = SITE_NAME . ' - Etusivu';
include __DIR__ . '/src/views/header.php';
?>

<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Tervetuloa Huutoon</h1>
    <p class="text-xl text-gray-600">Suomen suurin verkkohuutokauppa-alusta</p>
</div>

<!-- Top Section: Half-width Carousel + Login/Categories -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <!-- Left: Closing Soon Carousel -->
    <div>
        <?php if (!empty($closingSoonAuctions)): ?>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Sulkeutuu pian</h2>
        <div class="relative bg-white rounded-lg shadow-lg p-6">
            <!-- Previous button -->
            <button id="prev-btn" class="absolute left-2 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <!-- Next button -->
            <button id="next-btn" class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full shadow-lg p-2 hover:bg-gray-100 transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            
            <div class="overflow-hidden" id="carousel-container">
                <div class="flex transition-transform duration-500 ease-in-out" id="carousel-track">
                    <?php foreach ($closingSoonAuctions as $index => $auction): ?>
                        <div class="flex-none w-full px-2 carousel-item">
                            <a href="/auction.php?id=<?php echo $auction['id']; ?>" 
                               class="block bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
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
                                            <div class="text-xs text-gray-500">Sulkeutuu</div>
                                            <?php
                                                $endTimeTimestamp = strtotime($auction['end_time']);
                                                $endTimeAttribute = $endTimeTimestamp !== false
                                                    ? date('c', $endTimeTimestamp)
                                                    : $auction['end_time'];
                                            ?>
                                            <div class="countdown-detailed text-sm font-semibold text-red-600" 
                                                 data-endtime="<?php echo htmlspecialchars($endTimeAttribute, ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Carousel indicators -->
            <div class="flex justify-center mt-4 space-x-2">
                <?php for ($i = 0; $i < count($closingSoonAuctions); $i++): ?>
                    <button class="carousel-indicator w-2 h-2 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors" 
                            data-slide="<?php echo $i; ?>"></button>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Right: Login/Register + Categories -->
    <div class="space-y-8">
        <!-- Login/Register Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
                <?php $user = current_user(); ?>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Tervetuloa, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!</h3>
                <div class="space-y-3">
                    <a href="/add_product.php" class="block w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors text-center font-semibold">
                        + Lisää tuote myyntiin
                    </a>
                    <a href="/my-auctions.php" class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center">
                        Omat huutokaupat
                    </a>
                    <a href="/auth/logout.php" class="block w-full text-gray-600 hover:text-red-600 text-center">
                        Kirjaudu ulos
                    </a>
                </div>
            <?php else: ?>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Liity Huutoon</h3>
                <p class="text-gray-600 mb-6">Rekisteröidy käyttäjäksi ja aloita ostaminen ja myyminen!</p>
                <div class="space-y-3">
                    <a href="/auth/register.php" class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center font-semibold">
                        Rekisteröidy ilmaiseksi
                    </a>
                    <a href="/auth/login.php" class="block w-full border border-blue-600 text-blue-600 py-3 px-4 rounded-lg hover:bg-blue-50 transition-colors text-center">
                        Kirjaudu sisään
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Categories Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Kategoriat</h3>
            <div class="grid grid-cols-2 gap-3">
                <?php foreach (array_slice($categories, 0, 8) as $category): ?>
                    <a href="/category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>" 
                       class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="text-2xl mr-3"><?php echo htmlspecialchars($category['icon']); ?></div>
                        <div>
                            <h4 class="font-medium text-gray-900 text-sm"><?php echo htmlspecialchars($category['name']); ?></h4>
                            <p class="text-xs text-gray-500"><?php echo $category['active_count']; ?> kohdetta</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="mt-4">
                <a href="/category.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Näytä kaikki kategoriat →
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Popular Auctions -->
<?php if (!empty($popularAuctions)): ?>
<div class="mb-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Suosituimmat kohteet</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        <?php foreach ($popularAuctions as $auction): ?>
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
                            <?php
                                $endTimeTimestamp = strtotime($auction['end_time']);
                                $endTimeAttribute = $endTimeTimestamp !== false
                                    ? date('c', $endTimeTimestamp)
                                    : $auction['end_time'];
                            ?>
                            <div class="countdown text-sm font-semibold text-red-600" 
                                 data-endtime="<?php echo htmlspecialchars($endTimeAttribute, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- More Closing Soon Auctions -->
<?php if (!empty($moreClosingAuctions)): ?>
<div class="mb-12">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Päättyvät pian</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        <?php foreach ($moreClosingAuctions as $auction): ?>
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
                            <div class="text-xs text-gray-500">Sulkeutuu</div>
                            <?php
                                $endTimeTimestamp = strtotime($auction['end_time']);
                                $endTimeAttribute = $endTimeTimestamp !== false
                                    ? date('c', $endTimeTimestamp)
                                    : $auction['end_time'];
                            ?>
                            <div class="countdown-detailed text-sm font-semibold text-red-600" 
                                 data-endtime="<?php echo htmlspecialchars($endTimeAttribute, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script>
// Carousel functionality
let currentSlide = 0;
let autoScrollInterval;
const totalSlides = <?php echo count($closingSoonAuctions); ?>;

function updateCarousel() {
    const track = document.getElementById('carousel-track');
    const indicators = document.querySelectorAll('.carousel-indicator');
    
    if (!track || totalSlides === 0) return;
    
    // Move to current slide (show 1 item at a time)
    const translateX = -(currentSlide * 100);
    track.style.transform = `translateX(${translateX}%)`;
    
    // Update indicators
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('bg-blue-500', index === currentSlide);
        indicator.classList.toggle('bg-gray-300', index !== currentSlide);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
}

function resetAutoScroll() {
    clearInterval(autoScrollInterval);
    autoScrollInterval = setInterval(nextSlide, 5000);
}

// Initialize carousel
if (totalSlides > 0) {
    updateCarousel(); // Initial update
    resetAutoScroll(); // Start auto-scroll
    
    // Add click handlers for navigation buttons
    document.getElementById('prev-btn').addEventListener('click', () => {
        prevSlide();
        resetAutoScroll(); // Reset timer when user interacts
    });
    
    document.getElementById('next-btn').addEventListener('click', () => {
        nextSlide();
        resetAutoScroll(); // Reset timer when user interacts
    });
    
    // Add click handlers for indicators
    document.querySelectorAll('.carousel-indicator').forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentSlide = index;
            updateCarousel();
            resetAutoScroll(); // Reset timer when user interacts
        });
    });
}

// Enhanced countdown functionality
function formatCountdown(targetTime) {
    const now = new Date().getTime();
    const target = new Date(targetTime).getTime();
    const difference = target - now;

    if (difference <= 0) {
        return "Päättynyt";
    }

    const days = Math.floor(difference / (1000 * 60 * 60 * 24));
    const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((difference % (1000 * 60)) / 1000);

    // If less than 1 hour, show minutes and seconds
    if (days === 0 && hours === 0) {
        return `${minutes}min ${seconds}sek`;
    }
    // If less than 1 day, show hours and minutes
    else if (days === 0) {
        return `${hours}h ${minutes}min`;
    }
    // Otherwise show days and hours
    else {
        return `${days}pv ${hours}h`;
    }
}

function updateCountdowns() {
    // Update regular countdowns
    document.querySelectorAll('.countdown').forEach(element => {
        const endTime = element.getAttribute('data-endtime');
        if (endTime) {
            element.textContent = formatCountdown(endTime);
        }
    });
    
    // Update detailed countdowns (for carousel)
    document.querySelectorAll('.countdown-detailed').forEach(element => {
        const endTime = element.getAttribute('data-endtime');
        if (endTime) {
            element.textContent = formatCountdown(endTime);
        }
    });
}

// Update countdowns every second
updateCountdowns(); // Initial update
setInterval(updateCountdowns, 1000);
</script>

<?php include __DIR__ . '/src/views/footer.php'; ?>
