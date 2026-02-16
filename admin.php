<?php
// Minimal admin.php to avoid 500 errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/models/Database.php'; 
require_once __DIR__ . '/src/models/Auction.php';

$success = '';
$error = '';

// Handle form submissions  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_openai_settings') {
    $settingsFile = __DIR__ . '/config/ai_settings.php';
    $settings = [
        'openai_api_key' => trim($_POST['openai_api_key'] ?? ''),
        'ai_enabled' => isset($_POST['ai_enabled']),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if (file_put_contents($settingsFile, "<?php\nreturn " . var_export($settings, true) . ";\n")) {
        $success = 'AI-asetukset tallennettu!';
    } else {
        $error = 'Tallentaminen epäonnistui';
    }
}

// Load AI settings
$aiSettings = ['openai_api_key' => '', 'ai_enabled' => false];
$settingsFile = __DIR__ . '/config/ai_settings.php';
if (file_exists($settingsFile)) {
    $loaded = include $settingsFile;
    if (is_array($loaded)) {
        $aiSettings = array_merge($aiSettings, $loaded);
    }
}

// Get auctions with simple query to avoid method issues
try {
    $auctionModel = new Auction();
    // Use direct database query instead of potentially missing method
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT a.*, c.name as category_name FROM auctions a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.id DESC LIMIT 50");
    $auctions = $stmt->fetchAll();
} catch (Exception $e) {
    $auctions = [];
    $error = 'Tietokantavirhe: ' . $e->getMessage();
}

$pageTitle = 'Admin - ' . SITE_NAME;
include __DIR__ . '/src/views/header.php';
?>

<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Kohteiden hallinta</h1>
    <p class="text-xl text-gray-600">Muokkaa ja hallitse huutokauppa kohteita</p>
</div>

<?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <strong>Onnistui!</strong> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <strong>Virhe!</strong> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- AI Settings Panel -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
    <div class="px-6 py-4 bg-blue-50 border-b border-blue-200 cursor-pointer" onclick="toggleOpenAISettings()">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-blue-900">🤖 AI-Generointi asetukset</h2>
                <p class="text-sm text-blue-700">Määritä OpenAI API-avain kuva-analyysiin ja automaattiseen tuotetietojen generointiin</p>
            </div>
            <div id="openai-toggle" class="text-blue-600">
                <svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div id="openai-settings" class="hidden">
        <div class="p-6">
            <form method="POST">
                <input type="hidden" name="action" value="save_openai_settings">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="openai_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                            OpenAI API-avain
                        </label>
                        <input type="password" id="openai_api_key" name="openai_api_key" 
                               value="<?php echo htmlspecialchars($aiSettings['openai_api_key']); ?>"
                               placeholder="sk-..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">
                            Hanki API-avain <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">OpenAI:lta</a>
                        </p>
                    </div>
                    
                    <div class="flex items-center">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="ai_enabled" value="1" 
                                   <?php echo $aiSettings['ai_enabled'] ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                Käytä AI-generointia
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">
                            Kun käytössä, AI analysoi kuvia ja ehdottaa tuotetietoja
                        </p>
                        
                        <?php if (!empty($aiSettings['openai_api_key'])): ?>
                            <div class="mt-2">
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                    ✓ API-avain määritetty
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Tallenna asetukset
                    </button>
                    
                    <div class="text-sm text-gray-500">
                        <strong>Ominaisuudet:</strong>
                        <ul class="list-disc list-inside">
                            <li>Automaattinen tuotteen nimeäminen kuvista</li>
                            <li>Älykäs hinnoitteluehdotus kategorian mukaan</li>
                            <li>Kuvaus generoitu kuva-analyysin perusteella</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="mb-6">
    <a href="add_product.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-semibold">
        + Lisää uusi kohde
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Kaikki kohteet (<?php echo count($auctions); ?>)</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuva</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Otsikko</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hinta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tila</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Päättyy</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toiminnot</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($auctions)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        Ei kohteita. <a href="add_product.php" class="text-blue-600 hover:underline">Lisää ensimmäinen kohde</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($auctions as $auction): ?>
                    <?php 
                    $images = $auctionModel->getAuctionImages($auction['id']);
                    $primaryImage = !empty($images) ? $images[0]['image_path'] : null;
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #<?php echo $auction['id']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($primaryImage): ?>
                                <img src="<?php echo htmlspecialchars($primaryImage); ?>" alt="Kuva" class="h-12 w-12 object-cover rounded">
                            <?php else: ?>
                                <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">Ei kuvaa</span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="auction.php?id=<?php echo $auction['id']; ?>" class="hover:text-blue-600">
                                    <?php echo htmlspecialchars(substr($auction['title'], 0, 50)); ?>
                                    <?php if (strlen($auction['title']) > 50): ?>...<?php endif; ?>
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">€<?php echo number_format($auction['current_price'], 2); ?></div>
                            <div class="text-xs text-gray-500">Aloitus: €<?php echo number_format($auction['starting_price'], 2); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?php 
                                switch($auction['status']) {
                                    case 'active': echo 'bg-green-100 text-green-800'; break;
                                    case 'ended': echo 'bg-red-100 text-red-800'; break;
                                    case 'draft': echo 'bg-yellow-100 text-yellow-800'; break;
                                    default: echo 'bg-gray-100 text-gray-800';
                                }
                                ?>">
                                <?php 
                                $statusLabels = [
                                    'active' => 'Aktiivinen',
                                    'ended' => 'Päättynyt',
                                    'draft' => 'Luonnos',
                                    'cancelled' => 'Peruttu'
                                ];
                                echo $statusLabels[$auction['status']] ?? $auction['status'];
                                ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo date('d.m.Y H:i', strtotime($auction['end_time'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="edit_auction.php?id=<?php echo $auction['id']; ?>" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Muokkaa
                            </a>
                            <a href="auction.php?id=<?php echo $auction['id']; ?>" 
                               class="text-green-600 hover:text-green-900">
                                Näytä
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 text-center text-gray-500">
    <p>Kohteiden hallinta - Muokkaa kohteita klikkaamalla "Muokkaa" -linkkiä</p>
</div>

<script>
function toggleOpenAISettings() {
    const settingsDiv = document.getElementById('openai-settings');
    const toggleIcon = document.getElementById('openai-toggle').querySelector('svg');
    
    if (settingsDiv.classList.contains('hidden')) {
        settingsDiv.classList.remove('hidden');
        toggleIcon.style.transform = 'rotate(180deg)';
    } else {
        settingsDiv.classList.add('hidden');
        toggleIcon.style.transform = 'rotate(0deg)';
    }
}

// Keep settings hidden by default
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('openai-settings').classList.add('hidden');
});
</script>

<?php include __DIR__ . '/src/views/footer.php'; ?>