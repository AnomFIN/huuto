<?php




require_once __DIR__ . '/bootstrap.php';

if (function_exists('require_panel_admin')) {
    require_panel_admin();
}

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
              $uploadDir = rtrim(UPLOAD_DIR, '/') . '/auctions/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = defined('UPLOAD_MAX_SIZE')
                    ? (int)UPLOAD_MAX_SIZE
                    : (defined('MAX_UPLOAD_SIZE') ? (int)MAX_UPLOAD_SIZE : 5 * 1024 * 1024);

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

                        $originalName = basename((string)$_FILES['images']['name'][$key]);
                        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
                        if ($safeName === null || $safeName === '') {
                          $safeName = 'image';
                        }

                                                $baseName = uniqid('', true) . '_' . pathinfo($safeName, PATHINFO_FILENAME);
                                                $extension = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
                                                if ($extension === '' || !in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                                                        $mimeToExt = [
                                                                'image/jpeg' => 'jpg',
                                                                'image/jpg' => 'jpg',
                                                                'image/png' => 'png',
                                                                'image/gif' => 'gif',
                                                                'image/webp' => 'webp',
                                                        ];
                                                        $extension = $mimeToExt[$fileType] ?? 'jpg';
                                                }

                                                $originalFileName = $baseName . '_orig.' . $extension;
                                                $watermarkedFileName = $baseName . '_wm.' . $extension;
                                                $minFileName = $baseName . '_min.' . $extension;
                                                $originalPath = $uploadDir . $originalFileName;
                                                $watermarkedPath = $uploadDir . $watermarkedFileName;
                                                $minPath = $uploadDir . $minFileName;
                        $caption = isset($_POST['image_captions'][$key]) ? trim((string)$_POST['image_captions'][$key]) : null;
                        
                                                if (move_uploaded_file($tmpName, $originalPath)) {
                                                    if (!create_watermarked_variant($originalPath, $watermarkedPath)) {
                                                        throw new Exception('Vesileimakuvan luonti epäonnistui.');
                                                    }

                                                    if (!create_listing_thumbnail($originalPath, $minPath)) {
                                                        throw new Exception('Listauskuvan luonti epäonnistui.');
                                                    }

                          $uploadedImages[] = [
                                                        'path' => '/uploads/auctions/' . $watermarkedFileName,
                            'caption' => $caption !== '' ? mb_substr($caption, 0, 255) : null,
                          ];
                        }
                    }
                }
            }

            // Calculate end time with random seconds for realism
            $durationDays = (int)($_POST['duration_days'] ?? 7);
            $randomSeconds = rand(1, 59); // Random seconds between 1-59
            $endTime = date('Y-m-d H:i:s', strtotime("+{$durationDays} days") + $randomSeconds);

            // Get default test user
            $userId = $auctionModel->getDefaultTestUser();
            
            $sanitizeTextField = static function ($value): string {
                return trim(strip_tags((string)$value));
            };
            
            // Collect category-specific metadata
            $metadata = [];
            
            // Vehicle metadata
            $vehicleFields = ['vehicle_brand', 'vehicle_model', 'vehicle_year', 'mileage', 'engine', 'fuel_type', 
                            'service_book', 'registered', 'traffic_insurance', 'inspected', 'next_inspection', 
                            'key_count', 'vehicle_defects'];
                            
            // Real estate metadata
            $realEstateFields = ['property_type', 'room_type', 'living_area', 'plot_area', 'build_year', 
                               'energy_class', 'maintenance_fee', 'finance_fee', 'property_manager'];
                               
            // Electronics metadata
            $electronicsFields = ['electronics_brand', 'electronics_model', 'capacity', 'warranty_until', 
                                'original_box', 'charger_included'];
                                
            // Home items metadata
            $homeItemsFields = ['home_item_type', 'material', 'dimensions', 'manufacturer'];
            
            // Sports metadata
            $sportsFields = ['sport_type', 'size', 'sports_brand', 'usage_frequency'];
            
            // Clothing metadata
            $clothingFields = ['clothing_type', 'clothing_size', 'clothing_brand', 'color', 
                              'clothing_material', 'clothing_condition'];
                              
            // Collectibles metadata
            $collectiblesFields = ['collectible_type', 'age_period', 'creator', 'rarity'];
            
            // General metadata
            $generalFields = ['general_type', 'weight', 'general_dimensions', 'country_origin'];
            
            // Location metadata
            $locationFields = ['detailed_address', 'location_notes'];
            
            // Collect all possible metadata fields
            $allFields = array_merge($vehicleFields, $realEstateFields, $electronicsFields, 
                                   $homeItemsFields, $sportsFields, $clothingFields, 
                                   $collectiblesFields, $generalFields, $locationFields);
            
            foreach ($allFields as $field) {
                if (!empty($_POST[$field])) {
                    if (is_array($_POST[$field])) {
                        $metadata[$field] = implode(', ', $_POST[$field]);
                    } else {
                        $metadata[$field] = $sanitizeTextField($_POST[$field]);
                    }
                }
            }

            if (!empty($_POST['reserve_price']) && (float)$_POST['reserve_price'] < (float)$_POST['starting_price']) {
                throw new Exception('Varahinta ei voi olla pienempi kuin aloitushinta');
            }

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
                'seller_commitment' => isset($_POST['seller_commitment']) ? 1 : 0,
                'images' => $uploadedImages,
                'metadata' => $metadata
            ];

            // Create auction
            $newAuctionId = $auctionModel->createAuction($auctionData);
            $success = true;

        } catch (Throwable $e) {
            $error = $e->getMessage();
            error_log('[add_product] create_auction failed: ' . $e->getMessage());
        }
    }
}

// Get categories for the form
$categories = $categoryModel->getAllCategories();

$pageTitle = 'Lisää tuote - ' . SITE_NAME;
include SRC_PATH . '/views/header.php';
?>
<style>
        /* Form specific styles */
        .form-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow-1);
        }
        
        .form-grid {
            display: grid;
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-900);
        }
        
        .form-input, .form-select, .form-textarea {
            padding: 0.75rem;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            font-size: 0.95rem;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--accent-600);
            box-shadow: 0 0 0 3px rgba(38,104,255,.16);
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-grid-2 {
            grid-template-columns: 1fr 1fr;
        }
        
        .form-grid-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }
        
        .image-upload {
            border: 2px dashed var(--line);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            background: var(--surface-soft);
            transition: border-color 0.2s;
        }
        
        .image-upload:hover {
            border-color: var(--accent-600);
        }
        
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
            border: 1px solid var(--line);
        }
        
        .image-preview .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            border: 2px solid white;
        }
        
        .ai-generating {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .success-notice {
            background: #d1fae5;
            border: 1px solid #34d399;
            color: #065f46;
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
        }
        
        .error-notice {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
        }
        
        .required {
            color: #ef4444;
        }
        
        @media (max-width: 768px) {
            .form-grid-2, .form-grid-3 {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                margin: 1rem;
                padding: 1rem;
            }
        }
        </style>

            <div class="form-container">
        <div style="margin-bottom: 2rem;">
          <h1 style="margin: 0 0 0.5rem; font-size: 2rem; font-weight: 800;">Lisää uusi tuote</h1>
          <p style="margin: 0; color: var(--text-700);">Helppo työkalu tuotteiden lisäämiseen testaustarkoituksiin</p>
        </div>

        <?php if ($success): ?>
          <div class="success-notice">
            <strong>Onnistui!</strong> Tuote lisätty onnistuneesti. 
            <a href="/auction.php?id=<?php echo $newAuctionId; ?>" style="text-decoration: underline; font-weight: 600;">Näytä tuote</a>
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="error-notice">
            <strong>Virhe!</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="addProductForm" class="form-grid">
          <input type="hidden" name="action" value="create_auction">

        <!-- Image Upload Section with AI -->
          <div class="image-upload" style="grid-column: 1/-1;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">📸 Kuvat (OpenAI analysoi kuvat automaattisesti)</h3>
            <p style="margin: 0 0 1.5rem; color: var(--text-700); font-size: 0.9rem;">Lataa kuvia ja OpenAI Vision API generoi tuotteen nimen, kuvauksen ja hinnoitteluehdotuksen automaattisesti kuvien perusteella!</p>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 1rem;">
              <label style="cursor: pointer;" class="btn-primary">
                <span>Valitse kuvat</span>
                <input type="file" name="images[]" id="imageInput" multiple accept="image/*" style="display: none;" onchange="handleImageSelection(event)">
              </label>
              <input type="hidden" id="primaryImageIndex" name="primary_image_index" value="0">
            </div>

            <div id="imagePreview"></div>
            
            <div style="margin-top: 1rem; text-align: center;">
              <button type="button" onclick="generateAIDescription()" id="aiButton" class="btn-secondary" style="background: linear-gradient(135deg, #16c760, #14b854); color: white;">
                🤖 Analysoi kuvat AI:lla
              </button>
            </div>
            <div id="aiStatus" style="margin-top: 1rem; font-size: 0.9rem;"></div>
          </div>
          <!-- Basic Information -->
          <div class="form-grid form-grid-2" style="grid-column: 1/-1;">
            <div class="form-group">
              <label class="form-label">
                Otsikko <span class="required">*</span> 
                <span style="font-size: 0.8rem; color: var(--text-700); font-weight: normal;">(AI täyttää automaattisesti)</span>
              </label>
              <input type="text" name="title" id="title" required class="form-input"
                     placeholder="esim. iPhone 14 Pro 256GB">
            </div>

            <div class="form-group">
              <label class="form-label">Kategoria <span class="required">*</span></label>
              <select id="category_id" name="category_id" required class="form-select">
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
          <div class="form-group" style="grid-column: 1/-1;">
            <label class="form-label">
              Kuvaus <span style="font-size: 0.8rem; color: var(--text-700); font-weight: normal;">(AI täyttää automaattisesti)</span>
            </label>
            <textarea name="description" id="description" class="form-textarea"
                      placeholder="Kerro tuotteesta lisää..."></textarea>
          </div>

          <!-- Pricing -->
          <div class="form-grid form-grid-3" style="grid-column: 1/-1;">
            <div class="form-group">
              <label class="form-label">
                Aloitushinta <span class="required">*</span> 
                <span style="font-size: 0.8rem; color: var(--text-700); font-weight: normal;">(AI ehdottaa)</span>
              </label>
              <input type="number" name="starting_price" id="starting_price" step="0.01" min="0.01" required class="form-input"
                     placeholder="esim. 50.00">
            </div>

            <div class="form-group">
              <label class="form-label">Varahinta (valinnainen)</label>
              <input type="number" name="reserve_price" id="reserve_price" step="0.01" min="0" class="form-input"
                     placeholder="esim. 100.00" onchange="validateReservePrice()">
              <small style="color: var(--text-600); font-size: 0.8rem;">Varahinta ei voi olla pienempi kuin aloitushinta</small>
            </div>

            <div class="form-group">
              <label class="form-label">"Osta heti" hinta (valinnainen)</label>
              <input type="number" name="buy_now_price" step="0.01" min="0" class="form-input"
                     placeholder="esim. 200.00">
            </div>
          </div>

          <!-- Location Details -->
          <div class="form-grid form-grid-2" style="grid-column: 1/-1;">
            <div class="form-group">
              <label class="form-label">Sijainti</label>
              <input type="text" name="location" id="location" class="form-input" placeholder="esim. Helsinki"
                     value="Testisijainti">
            </div>

            <div class="form-group">
              <label class="form-label">Keston päivät</label>
              <select name="duration_days" id="duration_days" class="form-select">
                <option value="1">1 päivä</option>
                <option value="3">3 päivää</option>
                <option value="7" selected>7 päivää</option>
                <option value="14">14 päivää</option>
                <option value="30">30 päivää</option>
              </select>
            </div>
          </div>
          
          <div class="form-group" style="grid-column: 1/-1;">
            <label class="form-label">Tarkempi osoite (näkyy kohdesivulla)</label>
            <input type="text" name="detailed_address" class="form-input" placeholder="esim. Keskuskatu 12, 00100 Helsinki">
          </div>
          
          <div class="form-group" style="grid-column: 1/-1;">
            <label class="form-label">Lisätietoja sijainnista</label>
            <textarea name="location_notes" class="form-textarea" rows="2" placeholder="esim. Lähellä rautatieasemaa, helppo pääsy julkisilla..." style="min-height: 60px;"></textarea>
          </div>

          <div class="form-group" style="grid-column: 1/-1;">
            <label class="form-label">Kunnon kuvaus</label>
            <textarea name="condition_description" id="condition_description" class="form-textarea"
                      placeholder="Kerro tuotteen kunnosta..."></textarea>
          </div>
          
          <!-- Category Specific Forms -->
          <div style="grid-column: 1/-1; margin: 1.5rem 0;">
            <div style="text-align: center; margin-bottom: 1rem;">
              <button type="button" onclick="generateAIDetails()" id="aiDetailsButton" class="btn-secondary" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;" disabled>
                🤖 Täytä lisätiedot AI:lla
              </button>
              <div id="aiDetailsStatus" style="margin-top: 0.5rem; font-size: 0.9rem;"></div>
            </div>
          </div>
          <div id="categorySpecificForms" style="grid-column: 1/-1;"></div>
          
          <!-- Sales Terms -->
          <div class="form-group" style="grid-column: 1/-1;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">📋 Myyntiehdot</h3>
            <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line);">
              <div style="margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                  <input type="checkbox" name="seller_commitment" id="seller_commitment" required>
                  <span>Myyjä sitoutuu myymään kohteen korkeimman tarjouksen tekijälle.</span>
                </label>
              </div>
              
              <div style="font-size: 0.9rem; line-height: 1.5; color: var(--text-700);">
                <p><strong>Huutokauppaehdot:</strong></p>
                <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                  <li>Huutokauppaan osallistuminen ilman ostoaikomusta on kiellettyä</li>
                  <li>Tutustunhan kohteeseen ennen tarjoamista</li>
                  <li>Käytetyt kohteet myydään "sellaisena kuin on" -ehdoin</li>
                  <li>Kohde on maksettava 1 arkipäivän kuluessa</li>
                  <li>Kuluttajilla 14 vrk peruuttamisoikeus</li>
                </ul>
              </div>
            </div>
          </div>

          <div style="grid-column: 1/-1; margin-top: 1rem;">
            <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
              Lisää tuote huutokauppaan
            </button>
          </div>
        </div>
    </form>
</div>

<script>
let selectedFiles = [];
let primaryImageIndex = 0;

function handleImageSelection(event) {
  const files = Array.from(event.target.files || []);
  if (!files.length) {
    return;
  }

  selectedFiles = selectedFiles.concat(files);
  updateFileInput();
  displayImagePreviews();
}

function updateFileInput() {
  const dataTransfer = new DataTransfer();
  selectedFiles.forEach((file) => dataTransfer.items.add(file));
  document.getElementById('imageInput').files = dataTransfer.files;
  const primaryInput = document.getElementById('primaryImageIndex');
  if (primaryInput) {
    primaryInput.value = String(primaryImageIndex);
  }
}

function setPrimaryImage(index) {
  if (index < 0 || index >= selectedFiles.length) {
    return;
  }

  if (index === primaryImageIndex) {
    return;
  }

  const [selected] = selectedFiles.splice(index, 1);
  selectedFiles.unshift(selected);
  primaryImageIndex = 0;
  updateFileInput();
  displayImagePreviews();
}

function displayImagePreviews() {
  const previewContainer = document.getElementById('imagePreview');
  previewContainer.innerHTML = '';

  selectedFiles.forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.createElement('div');
      preview.className = 'image-preview';
      preview.innerHTML = `
        <img src="${e.target.result}" alt="Preview ${index + 1}">
        <div class="remove-image" onclick="removeImage(${index})">×</div>
        <div style="margin-top: 6px; display: flex; flex-direction: column; gap: 6px;">
          <button type="button" class="btn-secondary" style="padding: 6px 10px; font-size: .75rem;" onclick="setPrimaryImage(${index})">
            ${index === primaryImageIndex ? 'Pääkuva ✓' : 'Aseta pääkuvaksi'}
          </button>
          <input type="text" name="image_captions[]" class="form-input" style="font-size:.8rem;" placeholder="Kuvateksti (näkyy kohdesivulla)">
        </div>
      `;
      previewContainer.appendChild(preview);
    };
    reader.readAsDataURL(file);
  });
}

function removeImage(index) {
  selectedFiles.splice(index, 1);

  if (primaryImageIndex >= selectedFiles.length) {
    primaryImageIndex = Math.max(0, selectedFiles.length - 1);
  }

  updateFileInput();
  displayImagePreviews();
}

async function generateAIDescription() {
    if (selectedFiles.length === 0) {
        alert('Valitse ensin vähintään yksi kuva!');
        return;
    }

    const aiButton = document.getElementById('aiButton');
    const aiStatus = document.getElementById('aiStatus');
    
    aiButton.disabled = true;
    aiButton.classList.add('ai-generating');
    aiButton.innerHTML = '⏳ Generoidaan...';
    aiStatus.innerHTML = '<span class="text-blue-600">🤖 AI analysoi kaikkia kuvia (' + selectedFiles.length + ' kpl) OpenAI:lla...</span>';

    try {
        // Prepare form data with ALL images at once
        const formData = new FormData();
        selectedFiles.forEach((file, index) => {
            formData.append('images[]', file);
        });

        // Send request to AI analysis API
        const response = await fetch('api_ai_analyze.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.error || 'AI-analyysi epäonnistui');
        }
        
        // Debug: log the response
        console.log('AI Response:', data);
        if (data._debug) {
            console.log('Debug info:', data._debug);
        }

        // Fill in ALL form fields with AI-generated data
        document.getElementById('title').value = data.title || '';
        document.getElementById('description').value = data.description || '';
        
        // Set suggested prices - AI should fill all price fields
        if (data.suggested_price && data.suggested_price > 0) {
            document.getElementById('starting_price').value = data.suggested_price;
        }
        
        if (data.reserve_price && data.reserve_price > 0) {
            const reserveInput = document.querySelector('input[name="reserve_price"]');
            if (reserveInput) reserveInput.value = data.reserve_price;
        }
        
        if (data.buy_now_price && data.buy_now_price > 0) {
            const buyNowInput = document.querySelector('input[name="buy_now_price"]');
            if (buyNowInput) buyNowInput.value = data.buy_now_price;
        }
        
        // Set condition
        if (data.condition) {
            const conditionInput = document.getElementById('condition_description');
            if (conditionInput) {
                conditionInput.value = data.condition;
            }
        }
        
        // Set location if provided
        if (data.location) {
            const locationInput = document.getElementById('location');
            if (locationInput) {
                locationInput.value = data.location;
            }
        }
        
        // Set duration if provided
        if (data.duration_days) {
            const durationSelect = document.getElementById('duration_days');
            if (durationSelect) {
                durationSelect.value = data.duration_days;
            }
        }

        // Try to match category suggestion and show specific form
        if (data.category_suggestion) {
            const categorySelect = document.getElementById('category_id');
            
            if (categorySelect) {
                // Map AI numeric suggestion to category ID
                const categoryMapping = {
                    '1': '1', // Kiinteistöt
                    '2': '2', // Ajoneuvot  
                    '3': '3', // Elektroniikka
                    '4': '4', // Kodin tavarat
                    '5': '5', // Urheilu
                    '6': '6', // Vaatteet
                    '7': '7', // Keräily
                    '8': '8'  // Muut
                };
                
                const categoryId = categoryMapping[data.category_suggestion.toString()] || '8';
                categorySelect.value = categoryId;
                // Trigger category change to show specific forms
                categorySelect.dispatchEvent(new Event('change'));
            }
        }

        aiStatus.innerHTML = '<span class="text-green-600">✅ ' + (data.message || 'AI-analyysi valmis! Kaikki kentät täytetty automaattisesti.') + '</span>';
        
        // Show additional info if available
        if (data.category_suggestion) {
            aiStatus.innerHTML += '<br><span class="text-sm text-gray-600">💡 Ehdotettu kategoria: ' + data.category_suggestion + '</span>';
        }

    } catch (error) {
        console.error('AI Analysis Error:', error);
        aiStatus.innerHTML = '<span class="text-red-600">❌ ' + error.message + '</span>';
        
        // Fallback to basic generation if OpenAI fails
        if (error.message.includes('AI-generointi ei ole käytössä')) {
            aiStatus.innerHTML += '<br><span class="text-sm text-yellow-600">💡 Määritä OpenAI API-avain admin-paneelissa käyttääksesi AI-generointia.</span>';
        }
    } finally {
        aiButton.disabled = false;
        aiButton.classList.remove('ai-generating');
        aiButton.innerHTML = '🤖 Analysoi kuvat OpenAI:lla';
    }
}

// Auto-suggest pricing based on category and show category-specific forms
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
    
    // Show category-specific forms
    showCategorySpecificForm(categoryId);
    
    // Enable AI details button if category is selected
    const aiDetailsButton = document.getElementById('aiDetailsButton');
    if (aiDetailsButton && categoryId > 0) {
        aiDetailsButton.disabled = false;
    }
});

// Validate reserve price
function validateReservePrice() {
    const startingPrice = parseFloat(document.getElementById('starting_price').value) || 0;
    const reservePrice = parseFloat(document.getElementById('reserve_price').value) || 0;
    
    if (reservePrice > 0 && reservePrice < startingPrice) {
        alert('Varahinta ei voi olla pienempi kuin aloitushinta!');
        document.getElementById('reserve_price').value = startingPrice;
    }
}

// Also validate when starting price changes
document.addEventListener('DOMContentLoaded', function() {
    const startingPriceInput = document.getElementById('starting_price');
    if (startingPriceInput) {
        startingPriceInput.addEventListener('change', validateReservePrice);
    }
});

// Generate AI details for category-specific fields
async function generateAIDetails() {
    const categoryId = document.getElementById('category_id').value;
    if (!categoryId || selectedFiles.length === 0) {
        alert('Valitse ensin kategoria ja lataa kuvat!');
        return;
    }
    
    const aiDetailsButton = document.getElementById('aiDetailsButton');
    const aiDetailsStatus = document.getElementById('aiDetailsStatus');
    
    aiDetailsButton.disabled = true;
    aiDetailsButton.innerHTML = '⏳ Generoidaan lisätietoja...';
    aiDetailsStatus.innerHTML = '<span style="color: #2563eb;">🤖 AI generoi kategoria-spesifisiä lisätietoja...</span>';
    
    try {
        // Get current form data for context
        const formData = new FormData();
        selectedFiles.forEach((file, index) => {
            formData.append('images[]', file);
        });
        
        // Add current form values as context
        formData.append('category_id', categoryId);
        formData.append('title', document.getElementById('title').value || '');
        formData.append('description', document.getElementById('description').value || '');
        formData.append('condition_description', document.getElementById('condition_description').value || '');
        
        const response = await fetch('api_ai_details.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.error || 'AI lisätietojen generointi epäonnistui');
        }
        
        // Fill category-specific form fields
        fillCategorySpecificFields(data.details);
        
        aiDetailsStatus.innerHTML = '<span style="color: #16a34a;">✅ ' + (data.message || 'AI lisätiedot generoitu!') + '</span>';
        
    } catch (error) {
        console.error('AI Details Error:', error);
        aiDetailsStatus.innerHTML = '<span style="color: #dc2626;">❌ ' + error.message + '</span>';
    } finally {
        aiDetailsButton.disabled = false;
        aiDetailsButton.innerHTML = '🤖 Täytä lisätiedot AI:lla';
    }
}

function fillCategorySpecificFields(details) {
    if (!details) return;
    
    // Fill any matching form fields
    Object.keys(details).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input && details[fieldName]) {
            if (input.type === 'checkbox') {
                input.checked = details[fieldName] === '1' || details[fieldName] === 'true' || details[fieldName] === 'kyllä';
            } else {
                input.value = details[fieldName];
            }
        }
    });
}

function showCategorySpecificForm(categoryId) {
    const container = document.getElementById('categorySpecificForms');
    container.innerHTML = '';
    
    if (categoryId == 1) { // Kiinteistöt
        container.innerHTML = getRealEstateForm();
    } else if (categoryId == 2) { // Ajoneuvot
        container.innerHTML = getVehicleForm();
    } else if (categoryId == 3) { // Elektroniikka
        container.innerHTML = getElectronicsForm();
    } else if (categoryId == 4) { // Kodin tavarat
        container.innerHTML = getHomeItemsForm();
    } else if (categoryId == 5) { // Urheilu
        container.innerHTML = getSportsForm();
    } else if (categoryId == 6) { // Vaatteet
        container.innerHTML = getClothingForm();
    } else if (categoryId == 7) { // Keräily
        container.innerHTML = getCollectiblesForm();
    } else if (categoryId == 8) { // Muut
        container.innerHTML = getGeneralForm();
    }
}

function getRealEstateForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">🏠 Kiinteistötiedot</h3>
            <div class="form-grid form-grid-3" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Myytävä omaisuus</label>
                    <select name="property_type" class="form-select">
                        <option value="">Valitse</option>
                        <option value="omakotitalo">Omakotitalo</option>
                        <option value="kerrostalo">Kerrostalohuoneisto</option>
                        <option value="rivitalo">Rivitalo</option>
                        <option value="tontti">Tontti</option>
                        <option value="vapaa-ajan">Vapaa-ajan asunto</option>
                        <option value="toimitila">Toimitila</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Huoneistotyyppi</label>
                    <input type="text" name="room_type" class="form-input" placeholder="esim. 3h+k+s">
                </div>
                <div class="form-group">
                    <label class="form-label">Asuinpinta-ala (m²)</label>
                    <input type="number" name="living_area" step="0.5" class="form-input" placeholder="esim. 85.5">
                </div>
                <div class="form-group">
                    <label class="form-label">Tontin pinta-ala (m²)</label>
                    <input type="number" name="plot_area" class="form-input" placeholder="esim. 800">
                </div>
                <div class="form-group">
                    <label class="form-label">Rakennusvuosi</label>
                    <input type="number" name="build_year" min="1800" max="2026" class="form-input" placeholder="esim. 1985">
                </div>
                <div class="form-group">
                    <label class="form-label">Energialuokka</label>
                    <select name="energy_class" class="form-select">
                        <option value="">Valitse</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Hoitovastike (€/kk)</label>
                    <input type="number" name="maintenance_fee" step="0.01" class="form-input" placeholder="esim. 245.50">
                </div>
                <div class="form-group">
                    <label class="form-label">Rahoitusvastike (€/kk)</label>
                    <input type="number" name="finance_fee" step="0.01" class="form-input" placeholder="esim. 56.80">
                </div>
                <div class="form-group">
                    <label class="form-label">Isännöitsijä</label>
                    <input type="text" name="property_manager" class="form-input" placeholder="esim. Isännöitsijätoimisto Oy">
                </div>
            </div>
        </div>
    `;
}

function getVehicleForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">🚗 Ajoneuvotiedot</h3>
            <div class="form-grid form-grid-3" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Merkki</label>
                    <input type="text" name="vehicle_brand" class="form-input" placeholder="esim. Volkswagen">
                </div>
                <div class="form-group">
                    <label class="form-label">Malli</label>
                    <input type="text" name="vehicle_model" class="form-input" placeholder="esim. Transporter">
                </div>
                <div class="form-group">
                    <label class="form-label">Vuosimalli</label>
                    <input type="number" name="vehicle_year" min="1950" max="2026" class="form-input" placeholder="esim. 2006">
                </div>
                <div class="form-group">
                    <label class="form-label">Kilometrit</label>
                    <input type="number" name="mileage" class="form-input" placeholder="esim. 150000">
                </div>
                <div class="form-group">
                    <label class="form-label">Moottori</label>
                    <input type="text" name="engine" class="form-input" placeholder="esim. 2.5 TDI 130hv">
                </div>
                <div class="form-group">
                    <label class="form-label">Polttoaine</label>
                    <select name="fuel_type" class="form-select">
                        <option value="">Valitse</option>
                        <option value="bensiini">Bensiini</option>
                        <option value="diesel">Diesel</option>
                        <option value="hybridi">Hybridi</option>
                        <option value="sahko">Sähkö</option>
                        <option value="kaasu">Kaasu</option>
                        <option value="etanoli">Etanoli</option>
                    </select>
                </div>
            </div>
            
            <h4 style="margin: 1.5rem 0 1rem; font-weight: 600;">🔧 Tekninen kunto</h4>
            <div class="form-grid form-grid-2" style="gap: 1rem;">
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="service_book" value="1">
                        <span>Huoltokirja kyllä</span>
                    </label>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="registered" value="1">
                        <span>Rekisterissä kyllä</span>
                    </label>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="traffic_insurance" value="1">
                        <span>Liikennevakuutus voimassa</span>
                    </label>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="inspected" value="1">
                        <span>Katsastettu</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-label">Seuraava katsastus</label>
                    <input type="month" name="next_inspection" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Avainten lukumäärä</label>
                    <input type="number" name="key_count" min="0" max="10" class="form-input" placeholder="esim. 2">
                </div>
            </div>
            
            <h4 style="margin: 1.5rem 0 1rem; font-weight: 600;">⚠️ Viat ja huomautukset</h4>
            <div class="form-group">
                <textarea name="vehicle_defects" class="form-textarea" placeholder="Kerro mahdollisista vioista, kulumista, korjaustarpeista..."></textarea>
            </div>
        </div>
    `;
}

function getElectronicsForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">📱 Elektroniikka</h3>
            <div class="form-grid form-grid-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Merkki</label>
                    <input type="text" name="electronics_brand" class="form-input" placeholder="esim. Apple">
                </div>
                <div class="form-group">
                    <label class="form-label">Malli</label>
                    <input type="text" name="electronics_model" class="form-input" placeholder="esim. iPhone 14 Pro">
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasiteetti/Koko</label>
                    <input type="text" name="capacity" class="form-input" placeholder="esim. 256GB">
                </div>
                <div class="form-group">
                    <label class="form-label">Takuu voimassa</label>
                    <input type="date" name="warranty_until" class="form-input">
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="original_box" value="1">
                        <span>Alkuperäispakkaus</span>
                    </label>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="charger_included" value="1">
                        <span>Laturi mukana</span>
                    </label>
                </div>
            </div>
        </div>
    `;
}

function getHomeItemsForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">🏡 Kodin tavarat</h3>
            <div class="form-grid form-grid-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Tuotetyyppi</label>
                    <select name="home_item_type" class="form-select">
                        <option value="">Valitse</option>
                        <option value="huonekalu">Huonekalu</option>
                        <option value="sisustus">Sisustusesine</option>
                        <option value="kodinkone">Kodinkone</option>
                        <option value="tekstiili">Tekstiili</option>
                        <option value="astia">Astiat/ruokailuvälineet</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Materiaali</label>
                    <input type="text" name="material" class="form-input" placeholder="esim. Tammi">
                </div>
                <div class="form-group">
                    <label class="form-label">Mitat (p x l x k)</label>
                    <input type="text" name="dimensions" class="form-input" placeholder="esim. 120 x 80 x 75 cm">
                </div>
                <div class="form-group">
                    <label class="form-label">Valmistaja</label>
                    <input type="text" name="manufacturer" class="form-input" placeholder="esim. IKEA">
                </div>
            </div>
        </div>
    `;
}

function getSportsForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">⚽ Urheilu</h3>
            <div class="form-grid form-grid-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Laji</label>
                    <input type="text" name="sport_type" class="form-input" placeholder="esim. Jalkapallo">
                </div>
                <div class="form-group">
                    <label class="form-label">Koko/Mitat</label>
                    <input type="text" name="size" class="form-input" placeholder="esim. L tai 56cm">
                </div>
                <div class="form-group">
                    <label class="form-label">Merkki</label>
                    <input type="text" name="sports_brand" class="form-input" placeholder="esim. Adidas">
                </div>
                <div class="form-group">
                    <label class="form-label">Käyttötiheys</label>
                    <select name="usage_frequency" class="form-select">
                        <option value="">Valitse</option>
                        <option value="uusi">Käyttämätön</option>
                        <option value="vahän">Vähän käytetty</option>
                        <option value="kohtuullisesti">Kohtuullisesti käytetty</option>
                        <option value="paljon">Paljon käytetty</option>
                    </select>
                </div>
            </div>
        </div>
    `;
}

function getClothingForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">👕 Vaatteet</h3>
            <div class="form-grid form-grid-3" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Tyyppi</label>
                    <select name="clothing_type" class="form-select">
                        <option value="">Valitse</option>
                        <option value="paita">Paita</option>
                        <option value="housut">Housut</option>
                        <option value="takki">Takki</option>
                        <option value="kengat">Kengät</option>
                        <option value="mekko">Mekko</option>
                        <option value="alusvaatteet">Alusvaatteet</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Koko</label>
                    <input type="text" name="clothing_size" class="form-input" placeholder="esim. L tai 42">
                </div>
                <div class="form-group">
                    <label class="form-label">Merkki</label>
                    <input type="text" name="clothing_brand" class="form-input" placeholder="esim. H&M">
                </div>
                <div class="form-group">
                    <label class="form-label">Väri</label>
                    <input type="text" name="color" class="form-input" placeholder="esim. Sininen">
                </div>
                <div class="form-group">
                    <label class="form-label">Materiaali</label>
                    <input type="text" name="clothing_material" class="form-input" placeholder="esim. 100% puuvilla">
                </div>
                <div class="form-group">
                    <label class="form-label">Kunto</label>
                    <select name="clothing_condition" class="form-select">
                        <option value="">Valitse</option>
                        <option value="uusi">Uusi lapuilla</option>
                        <option value="kertakaytto">Kerran käytetty</option>
                        <option value="hyva">Hyvä kunto</option>
                        <option value="kohtuullinen">Kohtuullinen kunto</option>
                        <option value="kayttajaljia">Käyttöjälkiä</option>
                    </select>
                </div>
            </div>
        </div>
    `;
}

function getCollectiblesForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">🎭 Keräily</h3>
            <div class="form-grid form-grid-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Keräilytyyppi</label>
                    <select name="collectible_type" class="form-select">
                        <option value="">Valitse</option>
                        <option value="postimerkit">Postimerkit</option>
                        <option value="kolikot">Kolikot</option>
                        <option value="kirjat">Kirjat</option>
                        <option value="lelut">Lelut</option>
                        <option value="antiikki">Antiikki</option>
                        <option value="taide">Taide</option>
                        <option value="korut">Korut</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ikä/Ajanjakso</label>
                    <input type="text" name="age_period" class="form-input" placeholder="esim. 1950-luku">
                </div>
                <div class="form-group">
                    <label class="form-label">Valmistaja/Tekijä</label>
                    <input type="text" name="creator" class="form-input" placeholder="esim. Arabia">
                </div>
                <div class="form-group">
                    <label class="form-label">Harvinaisuus</label>
                    <select name="rarity" class="form-select">
                        <option value="">Valitse</option>
                        <option value="yleinen">Yleinen</option>
                        <option value="melko">Melko harvinainen</option>
                        <option value="harvinainen">Harvinainen</option>
                        <option value="eritt">Erittäin harvinainen</option>
                    </select>
                </div>
            </div>
        </div>
    `;
}

function getGeneralForm() {
    return `
        <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--line); margin-bottom: 1rem;">
            <h3 style="margin: 0 0 1rem; font-weight: 700; color: var(--text-900);">📦 Yleiset tiedot</h3>
            <div class="form-grid form-grid-2" style="gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Tuotetyyppi</label>
                    <input type="text" name="general_type" class="form-input" placeholder="Kerro tuotteen tyyppi">
                </div>
                <div class="form-group">
                    <label class="form-label">Paino (kg)</label>
                    <input type="number" name="weight" step="0.1" class="form-input" placeholder="esim. 2.5">
                </div>
                <div class="form-group">
                    <label class="form-label">Mitat</label>
                    <input type="text" name="general_dimensions" class="form-input" placeholder="esim. 30 x 20 x 10 cm">
                </div>
                <div class="form-group">
                    <label class="form-label">Valmistusmaa</label>
                    <input type="text" name="country_origin" class="form-input" placeholder="esim. Suomi">
                </div>
            </div>
        </div>
    `;
}
</script>

<?php include SRC_PATH . '/views/footer.php'; ?>
