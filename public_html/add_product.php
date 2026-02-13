<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/models/Database.php';
require_once __DIR__ . '/../src/models/Auction.php';
require_once __DIR__ . '/../src/models/Category.php';

$auctionModel = new Auction();
$categoryModel = new Category();

$success = false;
$error = '';
$newAuctionId = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_auction') {
        try {
            // Validate required fields
            if (empty($_POST['title']) || empty($_POST['category_id']) || empty($_POST['starting_price'])) {
                throw new Exception('Täytä vaaditut kentät: Otsikko, Kategoria ja Aloitushinta');
            }

            // Handle image uploads
            $uploadedImages = [];
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = UPLOAD_DIR;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = MAX_UPLOAD_SIZE;

                foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        // Validate file type
                        $fileType = mime_content_type($tmpName);
                        if (!in_array($fileType, $allowedTypes)) {
                            throw new Exception('Virheellinen tiedostotyyppi. Sallitut: JPEG, PNG, GIF, WebP');
                        }

                        // Validate file size
                        if ($_FILES['images']['size'][$key] > $maxFileSize) {
                            throw new Exception('Tiedosto on liian suuri. Maksimikoko: 5MB');
                        }

                        $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                        $targetPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $uploadedImages[] = '/uploads/' . $fileName;
                        }
                    }
                }
            }

            // Calculate end time
            $durationDays = (int)($_POST['duration_days'] ?? 7);
            $endTime = date('Y-m-d H:i:s', strtotime("+{$durationDays} days"));

            // Get default test user
            $userId = $auctionModel->getDefaultTestUser();

            // Prepare auction data
            $auctionData = [
                'user_id' => $userId,
                'category_id' => (int)$_POST['category_id'],
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description'] ?? ''),
                'starting_price' => (float)$_POST['starting_price'],
                'reserve_price' => !empty($_POST['reserve_price']) ? (float)$_POST['reserve_price'] : null,
                'buy_now_price' => !empty($_POST['buy_now_price']) ? (float)$_POST['buy_now_price'] : null,
                'bid_increment' => (float)($_POST['bid_increment'] ?? 1.00),
                'end_time' => $endTime,
                'status' => 'active',
                'location' => trim($_POST['location'] ?? ''),
                'condition_description' => trim($_POST['condition_description'] ?? ''),
                'images' => $uploadedImages
            ];

            // Create auction
            $newAuctionId = $auctionModel->createAuction($auctionData);
            $success = true;

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get categories for the form
$categories = $categoryModel->getAllCategories();

$pageTitle = 'Lisää tuote - ' . SITE_NAME;
include __DIR__ . '/../src/views/header.php';
?>

<style>
    .image-preview {
        display: inline-block;
        position: relative;
        margin: 10px;
    }
    .image-preview img {
        max-width: 150px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    .image-preview .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        background: red;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: bold;
    }
    .ai-generating {
        opacity: 0.6;
        pointer-events: none;
    }
</style>

<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Lisää uusi tuote</h1>
    <p class="text-xl text-gray-600">Helppo työkalu tuotteiden lisäämiseen testaustarkoituksiin</p>
</div>

<?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <strong>Onnistui!</strong> Tuote lisätty onnistuneesti. 
        <a href="/auction.php?id=<?php echo $newAuctionId; ?>" class="underline font-semibold">Näytä tuote</a>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <strong>Virhe!</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-lg p-6">
    <form method="POST" enctype="multipart/form-data" id="addProductForm">
        <input type="hidden" name="action" value="create_auction">

        <!-- Image Upload Section with AI -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <label class="block text-lg font-semibold text-gray-900 mb-2">
                📸 Kuvat (AI luo kuvauksen automaattisesti)
            </label>
            <p class="text-sm text-gray-600 mb-4">Lataa kuvia ja AI generoi tuotteen otsikon ja kuvauksen automaattisesti!</p>
            
            <div class="flex items-center space-x-4">
                <label class="cursor-pointer bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <span>Valitse kuvat</span>
                    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="hidden" onchange="handleImageSelection(event)">
                </label>
                <button type="button" onclick="generateAIDescription()" id="aiButton" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                    🤖 Generoi AI-kuvaus
                </button>
            </div>

            <div id="imagePreview" class="mt-4"></div>
            <div id="aiStatus" class="mt-4 text-sm"></div>
        </div>

        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Otsikko * <span class="text-xs text-gray-500">(AI täyttää automaattisesti)</span>
                </label>
                <input type="text" name="title" id="title" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="esim. iPhone 14 Pro 256GB">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategoria *</label>
                <select name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Valitse kategoria</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['icon'] . ' ' . $category['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Kuvaus <span class="text-xs text-gray-500">(AI täyttää automaattisesti)</span>
            </label>
            <textarea name="description" id="description" rows="6"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Tuotteen yksityiskohtainen kuvaus..."></textarea>
        </div>

        <!-- Pricing -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Aloitushinta * (€)</label>
                <input type="number" name="starting_price" step="0.01" min="0" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Varausmäärä (€)</label>
                <input type="number" name="reserve_price" step="0.01" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Valinnainen">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Osta heti -hinta (€)</label>
                <input type="number" name="buy_now_price" step="0.01" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Valinnainen">
            </div>
        </div>

        <!-- Additional Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tarjouksen lisäys (€)</label>
                <input type="number" name="bid_increment" step="0.01" min="0.01" value="1.00"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Kesto (päivää)</label>
                <select name="duration_days"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="1">1 päivä</option>
                    <option value="3">3 päivää</option>
                    <option value="7" selected>7 päivää</option>
                    <option value="14">14 päivää</option>
                    <option value="30">30 päivää</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sijainti</label>
                <input type="text" name="location"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="esim. Helsinki">
            </div>
        </div>

        <!-- Condition -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kunto</label>
            <select name="condition_description"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Valitse kunto</option>
                <option value="Uusi">Uusi</option>
                <option value="Erinomainen">Erinomainen</option>
                <option value="Hyvä">Hyvä</option>
                <option value="Tyydyttävä">Tyydyttävä</option>
                <option value="Välttävä">Välttävä</option>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="flex space-x-4">
            <button type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                ✨ Luo tuote
            </button>
            <a href="/"
               class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                Peruuta
            </a>
        </div>
    </form>
</div>

<script>
let selectedFiles = [];

function handleImageSelection(event) {
    const files = Array.from(event.target.files);
    selectedFiles = files;
    displayImagePreviews(files);
}

function displayImagePreviews(files) {
    const previewContainer = document.getElementById('imagePreview');
    previewContainer.innerHTML = '';

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'image-preview';
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}">
                <div class="remove-image" onclick="removeImage(${index})">×</div>
            `;
            previewContainer.appendChild(preview);
        };
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    displayImagePreviews(selectedFiles);
    
    // Update file input
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    document.getElementById('imageInput').files = dataTransfer.files;
}

async function generateAIDescription() {
    if (selectedFiles.length === 0) {
        alert('Valitse ensin vähintään yksi kuva!');
        return;
    }

    const aiButton = document.getElementById('aiButton');
    const aiStatus = document.getElementById('aiStatus');
    const form = document.getElementById('addProductForm');
    
    aiButton.disabled = true;
    aiButton.classList.add('ai-generating');
    aiButton.innerHTML = '⏳ Generoidaan...';
    aiStatus.innerHTML = '<span class="text-blue-600">🤖 AI analysoi kuvia...</span>';

    try {
        // Simulate AI processing (in production, this would call an AI service)
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Generate mock AI description based on random templates
        const templates = [
            {
                title: 'Laadukas {item} - Erinomainen kunto',
                description: 'Tarjolla tämä upea {item}. Tuote on erinomaisessa kunnossa ja huolella hoidettu. Kaikki toiminnot testattu ja toimivat moitteettomasti. Mukana alkuperäispakkaus ja tarvikkeet.\n\nTuote on käytetty vain vähän ja näyttää lähes uudelta. Ei naarmuja tai kolhuja. Ihanteellinen valinta laadun ja toimivuuden arvostajalle.'
            },
            {
                title: '{item} - Hieno lisä kotiin',
                description: 'Myydään toimiva {item}. Käytetty säännöllisesti, mutta hyvin huollettu. Toimii täydellisesti ja on valmis uuteen kotiin.\n\nTuote on pestty ja huollettu ammattimaisesti. Muutama pieni käytön jälki, mutta ei vaikuta toimivuuteen. Hinta-laatusuhteeltaan erinomainen!'
            },
            {
                title: 'Tyylikäs {item} myytävänä',
                description: 'Myynnissä tämä kaunis {item}. Ostettu muutama vuosi sitten, mutta edelleen hyvässä kunnossa. Laadukas ja kestävä.\n\nTilaa vapautunut, siksi myyntiin. Toimitetaan puhtaana ja valmiina käyttöön. Katso kuvat tarkemmin!'
            }
        ];

        const items = ['tuote', 'esine', 'kohde', 'kappale', 'laite', 'väline'];
        const randomTemplate = templates[Math.floor(Math.random() * templates.length)];
        const randomItem = items[Math.floor(Math.random() * items.length)];

        const title = randomTemplate.title.replace('{item}', randomItem);
        const description = randomTemplate.description.replace(/{item}/g, randomItem);

        // Fill in the form
        document.getElementById('title').value = title;
        document.getElementById('description').value = description;

        aiStatus.innerHTML = '<span class="text-green-600">✅ AI-kuvaus generoitu onnistuneesti! Voit muokata tekstiä ennen tallentamista.</span>';
    } catch (error) {
        aiStatus.innerHTML = '<span class="text-red-600">❌ Virhe AI-kuvauksessa. Yritä uudelleen.</span>';
    } finally {
        aiButton.disabled = false;
        aiButton.classList.remove('ai-generating');
        aiButton.innerHTML = '🤖 Generoi AI-kuvaus';
    }
}

// Auto-suggest pricing based on category
document.querySelector('select[name="category_id"]').addEventListener('change', function() {
    const categoryId = parseInt(this.value);
    const priceInput = document.querySelector('input[name="starting_price"]');
    
    // Suggest prices based on category
    const priceSuggestions = {
        1: 50000, // Kiinteistöt
        2: 5000,  // Ajoneuvot
        3: 200,   // Elektroniikka
        4: 100,   // Kodin tavarat
        5: 50,    // Urheilu
        6: 30,    // Vaatteet
        7: 100,   // Keräily
        8: 20     // Muut
    };
    
    if (priceSuggestions[categoryId] && !priceInput.value) {
        priceInput.value = priceSuggestions[categoryId];
    }
});
</script>

<?php include __DIR__ . '/../src/views/footer.php'; ?>
