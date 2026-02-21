<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

$success = '';
$error = '';
$adminPassword = 'huutojussi';

if (isset($_GET['logout_admin']) && $_GET['logout_admin'] === '1') {
    unset($_SESSION['panel_admin_authenticated'], $_SESSION['panel_admin_login_at']);
    header('Location: /admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['action'] ?? '') === 'admin_login') {
    $submittedPassword = (string)($_POST['admin_password'] ?? '');
    if (hash_equals($adminPassword, $submittedPassword)) {
        $_SESSION['panel_admin_authenticated'] = true;
        $_SESSION['panel_admin_login_at'] = time();

        $next = trim((string)($_POST['next'] ?? ''));
        if ($next !== '' && str_starts_with($next, '/')) {
            header('Location: ' . $next);
            exit;
        }

        header('Location: /admin.php');
        exit;
    }

    $error = 'Virheellinen salasana.';
}

$adminPanelAuthenticated = function_exists('is_panel_admin_authenticated') && is_panel_admin_authenticated();

if (!$adminPanelAuthenticated) {
    $pageTitle = 'Admin kirjautuminen - ' . SITE_NAME;
    include SRC_PATH . '/views/header.php';
    $nextValue = trim((string)($_GET['next'] ?? ''));
    ?>
    <section style="max-width:420px; margin:2rem auto; background:var(--surface); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow-1); padding:1.4rem;">
      <h1 style="margin:0 0 .6rem;">Admin kirjautuminen</h1>
      <p style="margin:0 0 1rem; color:var(--text-700);">Kirjaudu admin-paneeliin salasanalla.</p>
      <?php if ($error): ?>
        <div style="background:#fee2e2; border:1px solid #ef4444; color:#991b1b; padding:.7rem .8rem; border-radius:8px; margin-bottom:.8rem;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="admin_login">
        <input type="hidden" name="next" value="<?php echo htmlspecialchars($nextValue, ENT_QUOTES, 'UTF-8'); ?>">
        <label for="admin_password" style="display:block; margin-bottom:.4rem; font-weight:600;">Salasana</label>
        <input id="admin_password" name="admin_password" type="password" required style="width:100%; padding:.7rem; border:1px solid var(--line); border-radius:8px; background:var(--surface); margin-bottom:.8rem;">
        <button type="submit" class="btn-primary">Kirjaudu adminiin</button>
      </form>
    </section>
    <?php
    include SRC_PATH . '/views/footer.php';
    exit;
}

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

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_category') {
    try {
      $db = Database::getInstance()->getConnection();
      $db->exec('ALTER TABLE categories ADD COLUMN IF NOT EXISTS description TEXT NULL');
      $db->exec('ALTER TABLE categories ADD COLUMN IF NOT EXISTS logo VARCHAR(255) NULL');

      $name = trim((string)($_POST['category_name'] ?? ''));
      $description = trim((string)($_POST['category_description'] ?? ''));
      $logo = trim((string)($_POST['category_logo'] ?? ''));

      if ($name === '') {
        throw new RuntimeException('Kategorian nimi on pakollinen.');
      }

      $stmt = $db->prepare('INSERT INTO categories (name, description, logo) VALUES (?, ?, ?)');
      $stmt->execute([$name, $description !== '' ? $description : null, $logo !== '' ? $logo : null]);
      $success = 'Kategoria lisätty.';
    } catch (Throwable $exception) {
      $error = 'Kategorian lisäys epäonnistui: ' . $exception->getMessage();
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_category') {
    try {
      $db = Database::getInstance()->getConnection();
      $db->exec('ALTER TABLE categories ADD COLUMN IF NOT EXISTS description TEXT NULL');
      $db->exec('ALTER TABLE categories ADD COLUMN IF NOT EXISTS logo VARCHAR(255) NULL');

      $categoryId = (int)($_POST['category_id'] ?? 0);
      $name = trim((string)($_POST['category_name'] ?? ''));
      $description = trim((string)($_POST['category_description'] ?? ''));
      $logo = trim((string)($_POST['category_logo'] ?? ''));

      if ($categoryId <= 0 || $name === '') {
        throw new RuntimeException('Kategorian ID ja nimi ovat pakollisia.');
      }

      $stmt = $db->prepare('UPDATE categories SET name = ?, description = ?, logo = ? WHERE id = ?');
      $stmt->execute([$name, $description !== '' ? $description : null, $logo !== '' ? $logo : null, $categoryId]);
      $success = 'Kategoria päivitetty.';
    } catch (Throwable $exception) {
      $error = 'Kategorian päivitys epäonnistui: ' . $exception->getMessage();
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_category') {
    try {
      $db = Database::getInstance()->getConnection();
      $categoryId = (int)($_POST['category_id'] ?? 0);
      if ($categoryId <= 0) {
        throw new RuntimeException('Virheellinen kategorian ID.');
      }

      $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
      $stmt->execute([$categoryId]);
      $success = 'Kategoria poistettu.';
    } catch (Throwable $exception) {
      $error = 'Kategorian poisto epäonnistui: ' . $exception->getMessage();
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
  $db->exec('ALTER TABLE categories ADD COLUMN IF NOT EXISTS description TEXT NULL');
  $db->exec('ALTER TABLE categories ADD COLUMN IF NOT EXISTS logo VARCHAR(255) NULL');
    $stmt = $db->query("SELECT a.*, c.name as category_name FROM auctions a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.id DESC LIMIT 50");
    $auctions = $stmt->fetchAll();
  $categoryStmt = $db->query('SELECT id, name, description, logo FROM categories ORDER BY id ASC');
  $categories = $categoryStmt->fetchAll();
} catch (Exception $e) {
    $auctions = [];
  $categories = [];
    $error = 'Tietokantavirhe: ' . $e->getMessage();
}

$pageTitle = 'Admin - ' . SITE_NAME;
include SRC_PATH . '/views/header.php';
?>

<style>
  .admin-container { max-width: 1200px; margin: 0 auto; }
  .admin-card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 1.4rem; box-shadow: var(--shadow-1); margin-bottom: 1rem; }
  .admin-table { width: 100%; border-collapse: collapse; }
  .admin-table th, .admin-table td { padding: .8rem .7rem; text-align: left; border-bottom: 1px solid var(--line); }
  .admin-table th { color: var(--text-900); font-weight: 700; font-size: .86rem; background: var(--surface-soft); }
  .admin-table td { color: var(--text-700); font-size: .9rem; }
  .status-badge { display:inline-block; padding:.2rem .55rem; border-radius:999px; font-size:.75rem; font-weight:700; }
  .status-active { background:#dcfce7; color:#166534; }
  .status-ended { background:#fee2e2; color:#991b1b; }
  .status-draft { background:#fef3c7; color:#92400e; }
  .action-link { color: var(--accent-600); text-decoration: none; font-weight: 600; margin-right: .75rem; }
  .action-link:hover { text-decoration: underline; }
  .form-row { display:grid; grid-template-columns: 1fr 1fr; gap:1rem; }
  .form-input { width:100%; padding:.7rem; border:1px solid var(--line); border-radius:8px; background:var(--surface); }
  .category-table-form input { width:100%; padding:.55rem; border:1px solid var(--line); border-radius:8px; background:var(--surface); font-size:.86rem; }
  .category-table-form button { border:0; border-radius:8px; padding:.5rem .65rem; font-weight:700; cursor:pointer; }
  .btn-save { background:#dbeafe; color:#1e40af; }
  .btn-delete { background:#fee2e2; color:#b91c1c; }
  .btn-logout { background:#111827; color:#fff; text-decoration:none; padding:.48rem .75rem; border-radius:8px; font-size:.84rem; }
  .ai-box { border:1px solid #93c5fd; background: linear-gradient(135deg, #dbeafe, #e0f2fe); }
  @media (max-width: 900px) { .form-row { grid-template-columns: 1fr; } }
</style>

<section class="admin-container">
  <div style="margin-bottom:1rem;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:.8rem;">
      <h1 style="margin:0 0 .35rem; font-size:1.9rem; font-weight:800;">Kohteiden hallinta</h1>
      <a href="/admin.php?logout_admin=1" class="btn-logout">Kirjaudu ulos</a>
    </div>
    <p style="margin:0; color:var(--text-700);">Muokkaa kohteita ja AI-asetuksia.</p>
  </div>

  <?php if ($success): ?>
    <div style="background:#d1fae5; border:1px solid #34d399; color:#065f46; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem;"><strong>Onnistui!</strong> <?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div style="background:#fee2e2; border:1px solid #ef4444; color:#991b1b; padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem;"><strong>Virhe!</strong> <?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="admin-card ai-box">
    <div style="display:flex; justify-content:space-between; align-items:center; cursor:pointer;" onclick="toggleOpenAISettings()">
      <div>
        <h2 style="margin:0 0 .2rem;">🤖 AI-Generointi asetukset</h2>
        <p style="margin:0; color:var(--text-700); font-size:.9rem;">OpenAI API-avain kuva-analyysiin ja tuotetekstien generointiin.</p>
      </div>
      <div id="openai-toggle" aria-hidden="true">▾</div>
    </div>
    <div id="openai-settings" style="margin-top:1rem; display:none;">
      <form method="POST">
        <input type="hidden" name="action" value="save_openai_settings">
        <div class="form-row">
          <div>
            <label for="openai_api_key" style="display:block; margin-bottom:.4rem; font-weight:600;">OpenAI API-avain</label>
            <input type="password" id="openai_api_key" name="openai_api_key" value="<?php echo htmlspecialchars($aiSettings['openai_api_key']); ?>" placeholder="sk-..." class="form-input">
          </div>
          <div>
            <label style="display:block; margin-bottom:.4rem; font-weight:600;">AI käytössä</label>
            <label style="display:flex; align-items:center; gap:.55rem;">
              <input type="checkbox" name="ai_enabled" <?php echo $aiSettings['ai_enabled'] ? 'checked' : ''; ?>>
              <span>Käytä AI:ta tuotteiden analysointiin</span>
            </label>
          </div>
        </div>
        <div style="margin-top:.8rem;">
          <button type="submit" class="btn-primary">Tallenna asetukset</button>
        </div>
      </form>
    </div>
  </div>

  <div class="admin-card">
    <h2 style="margin:0 0 .7rem;">Kategorioiden hallinta</h2>
    <p style="margin:0 0 .8rem; color:var(--text-700);">Hallinnoi kategorioita (id, nimi, kuvaus, logo).</p>

    <form method="POST" style="display:grid; grid-template-columns: 1fr 2fr 1.5fr auto; gap:.6rem; margin-bottom:.9rem; align-items:end;">
      <input type="hidden" name="action" value="create_category">
      <div>
        <label style="display:block; font-size:.8rem; margin-bottom:.25rem;">Nimi</label>
        <input class="form-input" type="text" name="category_name" required>
      </div>
      <div>
        <label style="display:block; font-size:.8rem; margin-bottom:.25rem;">Kuvaus</label>
        <input class="form-input" type="text" name="category_description">
      </div>
      <div>
        <label style="display:block; font-size:.8rem; margin-bottom:.25rem;">Logo (URL/polku)</label>
        <input class="form-input" type="text" name="category_logo" placeholder="/assets/logo.png">
      </div>
      <button type="submit" class="btn-primary" style="height:40px;">Lisää</button>
    </form>

    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nimi</th>
            <th>Kuvaus</th>
            <th>Logo</th>
            <th>Toiminnot</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categories)): ?>
            <tr><td colspan="5" style="text-align:center;">Ei kategorioita.</td></tr>
          <?php else: ?>
            <?php foreach ($categories as $category): ?>
              <tr>
                <form method="POST" class="category-table-form">
                  <input type="hidden" name="category_id" value="<?php echo (int)$category['id']; ?>">
                  <td>#<?php echo (int)$category['id']; ?></td>
                  <td><input type="text" name="category_name" value="<?php echo htmlspecialchars((string)($category['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required></td>
                  <td><input type="text" name="category_description" value="<?php echo htmlspecialchars((string)($category['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></td>
                  <td><input type="text" name="category_logo" value="<?php echo htmlspecialchars((string)($category['logo'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></td>
                  <td style="display:flex; gap:.4rem;">
                    <button class="btn-save" type="submit" name="action" value="update_category">Tallenna</button>
                    <button class="btn-delete" type="submit" name="action" value="delete_category" onclick="return confirm('Poistetaanko kategoria varmasti?');">Poista</button>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:.8rem; margin-bottom:.6rem;">
      <h2 style="margin:0;">Kohteet</h2>
      <a href="/add_product.php" class="btn-primary" style="text-decoration:none;">+ Lisää uusi kohde</a>
    </div>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Otsikko</th>
            <th>Hinnat</th>
            <th>Tila</th>
            <th>Päättyy</th>
            <th>Toiminnot</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($auctions)): ?>
            <tr><td colspan="6" style="text-align:center;">Ei kohteita.</td></tr>
          <?php else: ?>
            <?php foreach ($auctions as $auction): ?>
              <tr>
                <td>#<?php echo (int)$auction['id']; ?></td>
                <td>
                  <a href="/auction.php?id=<?php echo (int)$auction['id']; ?>" class="action-link"><?php echo htmlspecialchars(mb_substr((string)$auction['title'], 0, 55)); ?><?php if (mb_strlen((string)$auction['title']) > 55): ?>...<?php endif; ?></a>
                </td>
                <td>
                  <div style="font-weight:700;">€<?php echo number_format((float)$auction['current_price'], 2); ?></div>
                  <div style="font-size:.78rem;">Aloitus: €<?php echo number_format((float)$auction['starting_price'], 2); ?></div>
                </td>
                <td>
                  <span class="status-badge <?php
                    switch ($auction['status']) {
                      case 'active': echo 'status-active'; break;
                      case 'ended': echo 'status-ended'; break;
                      case 'draft': echo 'status-draft'; break;
                      default: echo 'status-ended';
                    }
                  ?>">
                    <?php
                      $statusLabels = ['active' => 'Aktiivinen', 'ended' => 'Päättynyt', 'draft' => 'Luonnos', 'cancelled' => 'Peruttu'];
                      echo htmlspecialchars($statusLabels[$auction['status']] ?? (string)$auction['status']);
                    ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars(date('d.m.Y H:i', strtotime((string)$auction['end_time']))); ?></td>
                <td>
                  <a href="/edit_auction.php?id=<?php echo (int)$auction['id']; ?>" class="action-link">Muokkaa</a>
                  <a href="/auction.php?id=<?php echo (int)$auction['id']; ?>" class="action-link" style="color:#16a34a;">Näytä</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
  function toggleOpenAISettings() {
    const settings = document.getElementById('openai-settings');
    const toggle = document.getElementById('openai-toggle');
    const isOpen = settings.style.display === 'block';
    settings.style.display = isOpen ? 'none' : 'block';
    toggle.textContent = isOpen ? '▾' : '▴';
  }
</script>

<?php include SRC_PATH . '/views/footer.php'; ?>