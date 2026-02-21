<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success = false;
$error = '';

if (!$id) {
    header('Location: admin.php');
    exit;
}

// Get database connection
$db = Database::getInstance()->getConnection();

// Get auction data with simple query
$stmt = $db->prepare("SELECT a.*, c.name as category_name FROM auctions a LEFT JOIN categories c ON a.category_id = c.id WHERE a.id = ?");
$stmt->execute([$id]);
$auction = $stmt->fetch();

if (!$auction) {
    header('Location: admin.php');
    exit;
}

// Get auction images with simple query
$stmt = $db->prepare("SELECT * FROM auction_images WHERE auction_id = ? ORDER BY is_primary DESC, sort_order ASC");
$stmt->execute([$id]);
$images = $stmt->fetchAll();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'update_auction':
                    if (empty($_POST['title']) || empty($_POST['category_id']) || empty($_POST['starting_price'])) {
                        throw new Exception('Täytä vaaditut kentät');
                    }

                    // Update auction - direct SQL instead of method
                    $endTime = $auction['end_time'];
                    if (!empty($_POST['duration_days'])) {
                        $durationDays = (int)$_POST['duration_days'];
                        $randomSeconds = rand(1, 59); // Random seconds between 1-59
                        $endTime = date('Y-m-d H:i:s', strtotime("+{$durationDays} days") + $randomSeconds);
                    }

                    $stmt = $db->prepare("UPDATE auctions SET 
                        category_id = ?, title = ?, description = ?, starting_price = ?, current_price = ?,
                        reserve_price = ?, buy_now_price = ?, bid_increment = ?, end_time = ?, status = ?,
                        location = ?, condition_description = ?, updated_at = NOW()
                        WHERE id = ?");
                    
                    $stmt->execute([
                        (int)$_POST['category_id'],
                        trim($_POST['title']),
                        trim($_POST['description'] ?? ''),
                        (float)$_POST['starting_price'],
                        (float)($_POST['current_price'] ?? $auction['current_price']),
                        !empty($_POST['reserve_price']) ? (float)$_POST['reserve_price'] : null,
                        !empty($_POST['buy_now_price']) ? (float)$_POST['buy_now_price'] : null,
                        (float)($_POST['bid_increment'] ?? 1.00),
                        $endTime,
                        $_POST['status'] ?? $auction['status'],
                        trim($_POST['location'] ?? ''),
                        trim($_POST['condition_description'] ?? ''),
                        $id
                    ]);

                    $success = true;
                    
                    // Refresh auction data
                    $stmt = $db->prepare("SELECT a.*, c.name as category_name FROM auctions a LEFT JOIN categories c ON a.category_id = c.id WHERE a.id = ?");
                    $stmt->execute([$id]);
                    $auction = $stmt->fetch();
                    
                    $stmt = $db->prepare("SELECT * FROM auction_images WHERE auction_id = ? ORDER BY is_primary DESC, sort_order ASC");
                    $stmt->execute([$id]);
                    $images = $stmt->fetchAll();
                    break;

                case 'delete_image':
                    $imageId = (int)$_POST['image_id'];
                    // Get image path first
                    $stmt = $db->prepare("SELECT image_path, is_primary FROM auction_images WHERE id = ? AND auction_id = ?");
                    $stmt->execute([$imageId, $id]);
                    $image = $stmt->fetch();
                    
                    if ($image) {
                        // Delete file
                        $filePath = __DIR__ . '/' . ltrim($image['image_path'], '/');
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        
                        // Delete from database
                        $stmt = $db->prepare("DELETE FROM auction_images WHERE id = ?");
                        $stmt->execute([$imageId]);

                        // If deleted image was primary, promote first remaining image
                        if ((int)($image['is_primary'] ?? 0) === 1) {
                            $stmt = $db->prepare("SELECT id FROM auction_images WHERE auction_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1");
                            $stmt->execute([$id]);
                            $newPrimary = $stmt->fetch();

                            if ($newPrimary) {
                                $stmt = $db->prepare("UPDATE auction_images SET is_primary = 0 WHERE auction_id = ?");
                                $stmt->execute([$id]);

                                $stmt = $db->prepare("UPDATE auction_images SET is_primary = 1 WHERE id = ? AND auction_id = ?");
                                $stmt->execute([$newPrimary['id'], $id]);
                            }
                        }
                        
                        // Refresh images
                        $stmt = $db->prepare("SELECT * FROM auction_images WHERE auction_id = ? ORDER BY is_primary DESC, sort_order ASC");
                        $stmt->execute([$id]);
                        $images = $stmt->fetchAll();
                        
                        $success = true;
                    }
                    break;

                case 'set_primary_image':
                    $imageId = (int)$_POST['image_id'];
                    // First unset all primary
                    $stmt = $db->prepare("UPDATE auction_images SET is_primary = 0 WHERE auction_id = ?");
                    $stmt->execute([$id]);
                    
                    // Set new primary
                    $stmt = $db->prepare("UPDATE auction_images SET is_primary = 1 WHERE id = ? AND auction_id = ?");
                    $stmt->execute([$imageId, $id]);
                    
                    // Refresh images
                    $stmt = $db->prepare("SELECT * FROM auction_images WHERE auction_id = ? ORDER BY is_primary DESC, sort_order ASC");
                    $stmt->execute([$id]);
                    $images = $stmt->fetchAll();
                    
                    $success = true;
                    break;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get categories for the form
$categoryModel = new Category();
$categories = $categoryModel->getAllCategories();

$pageTitle = 'Muokkaa kohdetta: ' . $auction['title'] . ' - ' . SITE_NAME;
include SRC_PATH . '/views/header.php';
?>

<div class="mb-4">
    <a href="admin.php" class="text-blue-600 hover:underline">← Takaisin admin-sivulle</a>
</div>

<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Muokkaa kohdetta</h1>
    <p class="text-xl text-gray-600">Muokkaa kohteen #<?php echo $id; ?> tietoja ja kuvia</p>
</div>

<?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <strong>Tallennettu!</strong> Kohteen tiedot päivitetty onnistuneesti.
        <a href="auction.php?id=<?php echo $id; ?>" class="underline ml-2">Näytä kohde</a>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <strong>Virhe!</strong> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Kohteen tiedot</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_auction">
            
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Otsikko <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" 
                       value="<?php echo htmlspecialchars($auction['title']); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategoria <span class="text-red-500">*</span>
                </label>
                <select id="category_id" name="category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo $category['id'] == $auction['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Kuvaus</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Kerro kohteesta lisää..."><?php echo htmlspecialchars($auction['description'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="starting_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Aloitushinta (€) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="starting_price" name="starting_price" 
                           value="<?php echo $auction['starting_price']; ?>"
                           step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                
                <div>
                    <label for="current_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Nykyinen hinta (€)
                    </label>
                    <input type="number" id="current_price" name="current_price" 
                           value="<?php echo $auction['current_price']; ?>"
                           step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="reserve_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Lähtöhinta (€)
                    </label>
                    <input type="number" id="reserve_price" name="reserve_price" 
                           value="<?php echo $auction['reserve_price']; ?>"
                           step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="buy_now_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Osta heti -hinta (€)
                    </label>
                    <input type="number" id="buy_now_price" name="buy_now_price" 
                           value="<?php echo $auction['buy_now_price']; ?>"
                           step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Tila</label>
                    <select id="status" name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="draft" <?php echo $auction['status'] == 'draft' ? 'selected' : ''; ?>>Luonnos</option>
                        <option value="active" <?php echo $auction['status'] == 'active' ? 'selected' : ''; ?>>Aktiivinen</option>
                        <option value="ended" <?php echo $auction['status'] == 'ended' ? 'selected' : ''; ?>>Päättynyt</option>
                        <option value="cancelled" <?php echo $auction['status'] == 'cancelled' ? 'selected' : ''; ?>>Peruttu</option>
                    </select>
                </div>
                
                <div>
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                        Uusi kesto (päiviä)
                    </label>
                    <input type="number" id="duration_days" name="duration_days" 
                           min="1" max="30" placeholder="Jätä tyhjäksi säilyttääksesi"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Sijainti</label>
                    <input type="text" id="location" name="location" 
                           value="<?php echo htmlspecialchars($auction['location'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="condition_description" class="block text-sm font-medium text-gray-700 mb-2">Kunto</label>
                    <input type="text" id="condition_description" name="condition_description" 
                           value="<?php echo htmlspecialchars($auction['condition_description'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label for="imageInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Lisää uusia kuvia
                </label>
                <input type="file" id="imageInput" name="images[]" 
                       accept="image/*" multiple
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Auto-upload päällä. Max 8 kuvaa / kohde, 10MB / kuva.</p>
                <p id="imageUploadError" class="text-sm text-red-600 mt-2 hidden"></p>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Tallenna muutokset
                </button>
                <a href="auction.php?id=<?php echo $id; ?>" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors font-semibold text-center">
                    Esikatselu
                </a>
            </div>
        </form>

        <!-- AI Data Regeneration -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">🤖 AI-tiedot</h3>
            <p class="text-xs text-gray-500 mb-3">Generoi kohteelle kategoriasidonnaiset lisätiedot uudelleen OpenAI:lla (otsikon, kuvauksen ja kategorian perusteella).</p>
            <button type="button" id="btnRegenerateAI"
                    onclick="regenerateAIDetails()"
                    class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition-colors font-semibold text-sm">
                🔄 Generoi AI-tiedot uudelleen
            </button>
            <div id="aiRegenerateStatus" class="mt-2 text-sm hidden"></div>
        </div>
    </div>

    <!-- Images Section -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Kuvien hallinta</h2>
        
        <div id="imageGallery" class="space-y-4" data-auction-id="<?php echo $id; ?>">
            <?php foreach ($images as $image): ?>
                <div class="border border-gray-200 rounded-lg p-4 flex items-center space-x-4 auction-image-item" data-image-id="<?php echo (int)$image['id']; ?>">
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                         alt="Kuva" class="w-20 h-20 object-cover rounded-lg cursor-pointer js-set-primary">

                    <div class="flex-1">
                        <p class="text-sm text-gray-600">
                            <?php echo basename($image['image_path']); ?>
                        </p>
                        <p class="text-xs text-green-700 mt-1 js-primary-label <?php echo (int)$image['is_primary'] === 1 ? '' : 'hidden'; ?>">(Pääkuva)</p>
                    </div>

                    <div class="flex space-x-2">
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="set_primary_image">
                            <input type="hidden" name="image_id" value="<?php echo (int)$image['id']; ?>">
                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm js-set-primary">
                                Aseta pääkuvaksi
                            </button>
                        </form>

                        <form method="POST" class="inline" onsubmit="return confirm('Haluatko varmasti poistaa tämän kuvan?');">
                            <input type="hidden" name="action" value="delete_image">
                            <input type="hidden" name="image_id" value="<?php echo (int)$image['id']; ?>">
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm js-delete-image">
                                Poista
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="emptyImageState" class="text-center py-8 text-gray-500 <?php echo !empty($images) ? 'hidden' : ''; ?>">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="mt-2">Ei kuvia lisätty</p>
                <p class="text-sm">Lisää kuvia yllä olevalla lomakkeella</p>
            </div>
    </div>
</div>

<script src="/assets/js/edit_auction_images.js"></script>

<script>
async function regenerateAIDetails() {
    const btn = document.getElementById('btnRegenerateAI');
    const status = document.getElementById('aiRegenerateStatus');

    btn.disabled = true;
    btn.textContent = '⏳ Generoidaan...';
    status.className = 'mt-2 text-sm text-blue-600';
    status.textContent = '🤖 AI analysoi kohteen tietoja...';
    status.classList.remove('hidden');

    try {
        const formData = new FormData();
        formData.append('auction_id', <?php echo json_encode($id); ?>);

        const response = await fetch('api_ai_regenerate.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.error || 'AI-generointi epäonnistui');
        }

        status.className = 'mt-2 text-sm text-green-700';
        status.textContent = '✅ ' + (data.message || 'AI-tiedot päivitetty!') + ' Lataa sivu uudelleen nähdäksesi muutokset.';
    } catch (error) {
        status.className = 'mt-2 text-sm text-red-600';
        status.textContent = '❌ ' + error.message;
    } finally {
        btn.disabled = false;
        btn.textContent = '🔄 Generoi AI-tiedot uudelleen';
    }
}
</script>

<?php include SRC_PATH . '/views/footer.php'; ?>
