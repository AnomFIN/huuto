<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

if (function_exists('require_panel_admin')) {
  require_panel_admin();
}

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
<style>
        /* Edit auction specific styles */
        .edit-container {
            max-width: 1200px;
            margin: 2rem auto;
        }
        
        .edit-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .edit-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow-1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-900);
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
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
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .image-item {
            position: relative;
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .image-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        
        .image-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .primary-badge {
            background: var(--accent-600);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: var(--radius-pill);
            font-weight: 600;
        }
        
        .image-button {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            cursor: pointer;
            font-weight: 600;
        }
        
        .image-button:hover {
            background: white;
        }
        
        .required {
            color: #ef4444;
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
        
        .back-link {
            color: var(--accent-600);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1rem;
            display: inline-block;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .edit-container {
                margin: 1rem;
            }
            
            .edit-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .edit-card {
                padding: 1rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Image gallery styles */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
          align-items: start;
        }

        .image-item {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
            position: relative;
          background: var(--surface);
          display: flex;
          flex-direction: column;
        }

        .image-item img {
            width: 100%;
          height: 170px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
          display: block;
          background: #eef2f7;
        }

        .image-item:hover img {
            transform: scale(1.02);
        }

        .image-controls {
          position: static;
          left: auto;
          right: auto;
          bottom: auto;
          background: var(--surface);
          border-top: 1px solid var(--line);
            padding: 0.75rem;
          display: block;
        }

        .image-controls .js-caption-input {
          margin-top: .45rem !important;
        }

        .image-controls > div {
          display: flex;
          gap: .35rem;
          flex-wrap: wrap;
          margin-top: .55rem !important;
        }

        .image-filename {
            font-size: 0.8rem;
            color: var(--text-700);
            display: block;
            word-break: break-all;
            margin-bottom: 0.5rem;
        }

        .hidden {
          display: none !important;
        }

        .free-image-tools {
          border: 1px solid var(--line);
          border-radius: var(--radius);
          padding: 1rem;
          margin-bottom: 1rem;
          background: var(--surface-soft);
        }

        .free-image-row {
          display: grid;
          grid-template-columns: 1fr auto;
          gap: .5rem;
          margin-bottom: .75rem;
        }

        .free-image-note {
          margin: 0;
          font-size: .82rem;
          color: var(--text-700);
        }

        .free-image-results {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
          gap: .75rem;
          margin-top: .75rem;
        }

        .free-image-item {
          border: 1px solid var(--line);
          border-radius: 10px;
          overflow: hidden;
          background: var(--surface);
        }

        .free-image-item img {
          width: 100%;
          height: 120px;
          object-fit: cover;
        }

        .free-image-item-body {
          padding: .6rem;
        }

        .free-image-item-title {
          font-size: .8rem;
          color: var(--text-900);
          margin: 0 0 .35rem;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
          min-height: 2rem;
        }

        .free-image-item-meta {
          font-size: .72rem;
          color: var(--text-700);
          margin: 0 0 .5rem;
        }
    </style>

    <div class="edit-container">
      <a href="admin.php" class="back-link">← Takaisin admin-sivulle</a>
      
      <div style="margin-bottom: 2rem;">
        <h1 style="margin: 0 0 0.5rem; font-size: 2rem; font-weight: 800;">Muokkaa kohdetta</h1>
        <p style="margin: 0; color: var(--text-700);">Muokkaa kohteen #<?php echo $id; ?> tietoja ja kuvia</p>
      </div>

      <?php if ($success): ?>
        <div class="success-notice">
          <strong>Tallennettu!</strong> Kohteen tiedot päivitetty onnistuneesti.
          <a href="auction.php?id=<?php echo $id; ?>" style="text-decoration: underline; margin-left: 0.5rem;">Näytä kohde</a>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="error-notice">
          <strong>Virhe!</strong> <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <div class="edit-grid">
        <!-- Form Section -->
        <div class="edit-card">
          <h2 style="margin: 0 0 1rem; font-weight: 700;">Kohteen tiedot</h2>
          
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_auction">
            
            <div class="form-group">
              <label for="title" class="form-label">
                Otsikko <span class="required">*</span>
              </label>
              <input type="text" id="title" name="title" 
                     value="<?php echo htmlspecialchars($auction['title']); ?>"
                     class="form-input" required>
            </div>

            <div class="form-group">
              <label for="category_id" class="form-label">
                Kategoria <span class="required">*</span>
              </label>
              <select id="category_id" name="category_id" class="form-select" required>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo $category['id']; ?>" 
                          <?php echo $category['id'] == $auction['category_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="description" class="form-label">Kuvaus</label>
              <textarea id="description" name="description" class="form-textarea"
                        placeholder="Kerro kohteesta lisää..."><?php echo htmlspecialchars($auction['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="starting_price" class="form-label">
                  Aloitushinta (€) <span class="required">*</span>
                </label>
                <input type="number" id="starting_price" name="starting_price" 
                       value="<?php echo $auction['starting_price']; ?>"
                       step="0.01" min="0" class="form-input" required>
              </div>
              
              <div class="form-group">
                <label for="current_price" class="form-label">
                  Nykyinen hinta (€)
                </label>
                <input type="number" id="current_price" name="current_price" 
                       value="<?php echo $auction['current_price']; ?>"
                       step="0.01" min="0" class="form-input">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="reserve_price" class="form-label">
                  Lähtöhinta (€)
                </label>
                <input type="number" id="reserve_price" name="reserve_price" 
                       value="<?php echo $auction['reserve_price']; ?>"
                       step="0.01" min="0" class="form-input">
              </div>
              
              <div class="form-group">
                <label for="buy_now_price" class="form-label">
                  Osta heti -hinta (€)
                </label>
                <input type="number" id="buy_now_price" name="buy_now_price" 
                       value="<?php echo $auction['buy_now_price']; ?>"
                       step="0.01" min="0" class="form-input">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="status" class="form-label">Tila</label>
                <select id="status" name="status" class="form-select">
                  <option value="draft" <?php echo $auction['status'] == 'draft' ? 'selected' : ''; ?>>Luonnos</option>
                  <option value="active" <?php echo $auction['status'] == 'active' ? 'selected' : ''; ?>>Aktiivinen</option>
                  <option value="ended" <?php echo $auction['status'] == 'ended' ? 'selected' : ''; ?>>Päättynyt</option>
                  <option value="cancelled" <?php echo $auction['status'] == 'cancelled' ? 'selected' : ''; ?>>Peruttu</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="duration_days" class="form-label">
                  Uusi kesto (päiviä)
                </label>
                <input type="number" id="duration_days" name="duration_days" 
                       min="1" max="30" placeholder="Jätä tyhjäksi säilyttääksesi"
                       class="form-input">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="location" class="form-label">Sijainti</label>
                <input type="text" id="location" name="location" 
                       value="<?php echo htmlspecialchars($auction['location'] ?? ''); ?>"
                       class="form-input">
              </div>
              
              <div class="form-group">
                <label for="condition_description" class="form-label">Kunto</label>
                <input type="text" id="condition_description" name="condition_description" 
                       value="<?php echo htmlspecialchars($auction['condition_description'] ?? ''); ?>"
                       class="form-input">
              </div>
            </div>

            <div class="form-group">
              <label for="imageInput" class="form-label">
                Lisää uusia kuvia
              </label>
              <input type="file" id="imageInput" name="images[]" 
                     accept="image/*" multiple class="form-input">
              <p style="font-size: 0.85rem; color: var(--text-700); margin-top: 0.5rem;">Auto-upload päällä. Max 8 kuvaa / kohde, 10MB / kuva.</p>
              <p id="imageUploadError" class="hidden" style="font-size: 0.85rem; color: #ef4444; margin-top: 0.5rem;"></p>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
              <button type="submit" class="btn-primary">
                Tallenna muutokset
              </button>
              <a href="auction.php?id=<?php echo $id; ?>" class="btn-secondary" style="text-align: center;">
                Esikatselu
              </a>
            </div>
          </form>

          <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--line);">
            <h3 style="margin: 0 0 0.5rem; font-size: 1rem; font-weight: 700;">AI-lisätiedot</h3>
            <p style="font-size: 0.85rem; color: var(--text-700); margin: 0 0 0.75rem;">Generoi kategoria-spesifiset lisätiedot uudelleen kohteen kuvien perusteella. Hyödyllinen, jos lisätiedot ovat puutteelliset.</p>
            <button type="button" id="regenerateAiBtn" class="btn-secondary">
              🤖 Generoi AI-tiedot uudelleen
            </button>
            <p id="regenerateAiStatus" style="font-size: 0.85rem; margin-top: 0.5rem;"></p>
          </div>
        </div>

        <!-- Images Section -->
        <!-- Images Section -->
        <div class="edit-card">
          <h2 style="margin: 0 0 1rem; font-weight: 700;">Kuvien hallinta</h2>

          <div class="free-image-tools" data-auction-title="<?php echo htmlspecialchars((string)$auction['title'], ENT_QUOTES, 'UTF-8'); ?>">
            <h3 style="margin: 0 0 .5rem; font-size: 1rem; font-weight: 700;">Hae vapaasti käytettäviä kuvia</h3>
            <div class="free-image-row">
              <input
                type="text"
                id="freeImageQuery"
                class="form-input"
                value="<?php echo htmlspecialchars((string)$auction['title'], ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Hakusana, esim. Harley Davidson"
              />
              <button type="button" id="searchFreeImagesButton" class="btn-secondary">Hae kuva tälle kohteelle</button>
            </div>
            <p class="free-image-note">Lähde: Wikimedia Commons (vapaasti käytettävä sisältö). Tarkista tarvittaessa lisenssitiedot ennen julkaisemista.</p>
            <p id="freeImageStatus" class="free-image-note" style="margin-top:.4rem;"></p>
            <div id="freeImageResults" class="free-image-results"></div>
          </div>
          
          <div class="image-gallery" id="imageGallery" data-auction-id="<?php echo $id; ?>">
            <?php foreach ($images as $image): ?>
              <div class="image-item auction-image-item" data-image-id="<?php echo (int)$image['id']; ?>">
                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                     alt="Kuva" class="js-set-primary">

                <div class="image-controls">
                  <span class="image-filename"><?php echo basename($image['image_path']); ?></span>
                  <?php if ((int)$image['is_primary'] === 1): ?>
                    <span class="primary-badge js-primary-label">Pääkuva</span>
                  <?php else: ?>
                    <span class="primary-badge js-primary-label hidden">Pääkuva</span>
                  <?php endif; ?>

                  <input
                    type="text"
                    class="form-input js-caption-input"
                    value="<?php echo htmlspecialchars((string)($image['caption'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Kuvateksti (näkyy kohdesivulla)"
                    style="margin-top: .5rem;"
                  />
                  <p class="js-caption-status" style="font-size: .75rem; color: #15803d; margin-top: .25rem;"></p>
                  
                  <div style="display: flex; gap: 0.25rem; margin-top: 0.5rem;">
                    <button type="button" class="image-button js-set-primary">Aseta pääkuvaksi</button>
                    <button type="button" class="image-button js-save-caption">Tallenna teksti</button>
                    <button type="button" class="image-button js-delete-image" style="background: rgba(239,68,68,0.9); color: white;">Poista</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div id="emptyImageState" style="text-align: center; padding: 2rem; color: var(--text-700); <?php echo !empty($images) ? 'display: none;' : ''; ?>">
            <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; opacity: 0.5;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p>Ei kuvia lisätty</p>
            <p style="font-size: 0.85rem;">Lisää kuvia yllä olevalla lomakkeella</p>
          </div>
            </div>
    </div>
</div>

<script src="assets/js/edit_auction_images.js"></script>
<script>
(function () {
    const btn = document.getElementById('regenerateAiBtn');
    const status = document.getElementById('regenerateAiStatus');
    if (!btn || !status) return;

    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.textContent = '⏳ Generoidaan...';
        status.textContent = '';
        status.style.color = '';

        try {
            const formData = new FormData();
            formData.append('auction_id', '<?php echo (int)$id; ?>');

            const response = await fetch('api/regenerate_ai_details.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || 'AI-generointi epäonnistui');
            }

            status.style.color = '#15803d';
            status.textContent = '✅ ' + (data.message || 'AI-lisätiedot tallennettu!');
        } catch (err) {
            status.style.color = '#dc2626';
            status.textContent = '❌ ' + err.message;
        } finally {
            btn.disabled = false;
            btn.textContent = '🤖 Generoi AI-tiedot uudelleen';
        }
    });
}());
</script>

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
        formData.append('csrf_token', document.getElementById('csrfToken').value);

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
