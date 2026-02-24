<?php
// Security-first. Creator-ready. Future-proof.
declare(strict_types=1);

session_start();

const MAX_TEXT_LENGTH = 5000;
const SETTINGS_FILE = __DIR__ . '/data/admin-settings.json';

function jsonLog(string $event, array $context = []): void
{
    $payload = [
        'event' => $event,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'time' => gmdate('c'),
        'context' => $context,
    ];

    error_log(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function parseEnvFile(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        return [];
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    $values = [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);
        $value = trim($value, "\"'");
        if ($key !== '') {
            $values[$key] = $value;
        }
    }

    return $values;
}

function getAdminPassword(): ?string
{
    $envCandidates = [
        __DIR__ . '/.env',
        dirname(__DIR__) . '/.env',
    ];

    foreach ($envCandidates as $path) {
        $parsed = parseEnvFile($path);
        if (isset($parsed['ADMIN_PANEL_PASSWORD']) && trim($parsed['ADMIN_PANEL_PASSWORD']) !== '') {
            return $parsed['ADMIN_PANEL_PASSWORD'];
        }
    }

    return null;
}

function getDefaultSettings(): array
{
    return [
        'site' => [
            'brandName' => 'Asetukku',
            'supportEmail' => 'tuki@asetukku.fi',
            'supportPhone' => '+358 40 123 4567',
            'announcement' => 'Maaliskuun kampanja: +100 € extrahyvitys 31.3. asti.',
            'maintenanceMode' => false,
        ],
        'campaign' => [
            'enabled' => true,
            'title' => 'Lähetä aseesi arvioitavaksi – +100 € extrahyvitys',
            'description' => 'Kampanja voimassa 31.3. asti. Arvio yleensä 24h sisällä.',
            'deadlineIso' => '2026-03-31T23:59:59+02:00',
            'cooldownDays' => 2,
        ],
        'cookies' => [
            'bannerEnabled' => true,
            'message' => 'Käytämme välttämättömiä evästeitä toiminnallisuuksiin ja analytiikkaa palvelun kehittämiseen.',
            'policyUrl' => 'privacy.html',
        ],
        'ui' => [
            'defaultTheme' => 'dark',
            'heroCtaPrimaryText' => 'Lähetä arvioon',
            'heroCtaPrimaryUrl' => 'sell.html',
        ],
    ];
}

function loadSettings(): array
{
    $defaults = getDefaultSettings();
    if (!is_file(SETTINGS_FILE)) {
        return $defaults;
    }

    $content = file_get_contents(SETTINGS_FILE);
    if ($content === false) {
        return $defaults;
    }

    $decoded = json_decode($content, true);
    if (!is_array($decoded)) {
        return $defaults;
    }

    return array_replace_recursive($defaults, $decoded);
}

function normalizeText(string $value, int $maxLength = MAX_TEXT_LENGTH): string
{
    $clean = trim($value);
    if (mb_strlen($clean) > $maxLength) {
        $clean = mb_substr($clean, 0, $maxLength);
    }
    return $clean;
}

function normalizeBool(mixed $value): bool
{
    return $value === '1' || $value === 1 || $value === true || $value === 'on';
}

function normalizeInt(mixed $value, int $min, int $max, int $fallback): int
{
    if (!is_numeric($value)) {
        return $fallback;
    }

    $intValue = (int) $value;
    if ($intValue < $min || $intValue > $max) {
        return $fallback;
    }

    return $intValue;
}

function normalizeIsoDate(string $value, string $fallback): string
{
    $clean = normalizeText($value, 40);
    $date = date_create($clean);
    if ($date === false) {
        return $fallback;
    }

    return $date->format(DATE_ATOM);
}

function validateAndBuildSettings(array $input, array $current): array
{
    return [
        'site' => [
            'brandName' => normalizeText((string)($input['site_brandName'] ?? $current['site']['brandName']), 120),
            'supportEmail' => filter_var((string)($input['site_supportEmail'] ?? ''), FILTER_VALIDATE_EMAIL)
                ?: $current['site']['supportEmail'],
            'supportPhone' => normalizeText((string)($input['site_supportPhone'] ?? $current['site']['supportPhone']), 80),
            'announcement' => normalizeText((string)($input['site_announcement'] ?? $current['site']['announcement']), 400),
            'maintenanceMode' => normalizeBool($input['site_maintenanceMode'] ?? false),
        ],
        'campaign' => [
            'enabled' => normalizeBool($input['campaign_enabled'] ?? false),
            'title' => normalizeText((string)($input['campaign_title'] ?? $current['campaign']['title']), 180),
            'description' => normalizeText((string)($input['campaign_description'] ?? $current['campaign']['description']), 450),
            'deadlineIso' => normalizeIsoDate((string)($input['campaign_deadlineIso'] ?? $current['campaign']['deadlineIso']), $current['campaign']['deadlineIso']),
            'cooldownDays' => normalizeInt($input['campaign_cooldownDays'] ?? 2, 1, 30, 2),
        ],
        'cookies' => [
            'bannerEnabled' => normalizeBool($input['cookies_bannerEnabled'] ?? false),
            'message' => normalizeText((string)($input['cookies_message'] ?? $current['cookies']['message']), 400),
            'policyUrl' => normalizeText((string)($input['cookies_policyUrl'] ?? $current['cookies']['policyUrl']), 120),
        ],
        'ui' => [
            'defaultTheme' => in_array(($input['ui_defaultTheme'] ?? ''), ['dark', 'light'], true)
                ? $input['ui_defaultTheme']
                : $current['ui']['defaultTheme'],
            'heroCtaPrimaryText' => normalizeText((string)($input['ui_heroCtaPrimaryText'] ?? $current['ui']['heroCtaPrimaryText']), 60),
            'heroCtaPrimaryUrl' => normalizeText((string)($input['ui_heroCtaPrimaryUrl'] ?? $current['ui']['heroCtaPrimaryUrl']), 120),
        ],
    ];
}

function saveSettings(array $settings): bool
{
    $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    return file_put_contents(SETTINGS_FILE, $json . PHP_EOL, LOCK_EX) !== false;
}

function ensureCsrfToken(): string
{
    if (!isset($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function isAuthenticated(): bool
{
    return isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
}

$adminPassword = getAdminPassword();
if ($adminPassword === null) {
    http_response_code(500);
    echo 'ADMIN_PANEL_PASSWORD puuttuu .env-tiedostosta.';
    exit;
}

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $passwordInput = (string)($_POST['password'] ?? '');
        if (!hash_equals($adminPassword, $passwordInput)) {
            $errors[] = 'Virheellinen salasana.';
            jsonLog('admin_login_failed');
        } else {
            $_SESSION['admin_authenticated'] = true;
            jsonLog('admin_login_success');
            header('Location: admin.php');
            exit;
        }
    }

    if ($action === 'logout') {
        session_destroy();
        header('Location: admin.php');
        exit;
    }

    if ($action === 'save' && isAuthenticated()) {
        $token = (string)($_POST['csrf'] ?? '');
        if (!hash_equals((string)($_SESSION['csrf'] ?? ''), $token)) {
            $errors[] = 'Virheellinen CSRF-tunniste.';
            jsonLog('admin_save_failed', ['reason' => 'csrf']);
        } else {
            $current = loadSettings();
            $next = validateAndBuildSettings($_POST, $current);

            if (!saveSettings($next)) {
                $errors[] = 'Asetusten tallennus epäonnistui.';
                jsonLog('admin_save_failed', ['reason' => 'write']);
            } else {
                $message = 'Asetukset tallennettiin onnistuneesti.';
                jsonLog('admin_save_success');
            }
        }
    }
}

$settings = loadSettings();
$csrf = ensureCsrfToken();
?>
<!doctype html>
<html lang="fi">
<head>
  <title>Asetukku Admin</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body { margin:0; font-family: Inter, sans-serif; background:#070d14; color:#e8f0ff; }
    .container { max-width:1000px; margin:0 auto; padding:24px; }
    .card { background:linear-gradient(170deg,#0d1824,#111f2c); border:1px solid rgba(94,234,212,.25); border-radius:16px; padding:20px; margin-bottom:18px; }
    h1,h2 { margin:0 0 10px; }
    .small { color:#a8b7c9; font-size:14px; }
    label { display:block; margin-top:12px; font-weight:600; }
    input,textarea,select { width:100%; box-sizing:border-box; margin-top:6px; border-radius:10px; border:1px solid rgba(148,163,184,.35); background:#0a1320; color:#e8f0ff; padding:11px; }
    textarea { min-height:90px; }
    .row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .btn { border:none; border-radius:10px; padding:11px 16px; font-weight:700; cursor:pointer; }
    .btn.primary { background:#22d3ee; color:#04233a; }
    .btn.ghost { background:transparent; border:1px solid rgba(148,163,184,.4); color:#d6e4ff; }
    .msg { border-radius:10px; padding:10px; margin-bottom:12px; }
    .msg.ok { background:#0f2f2a; border:1px solid #2dd4bf; }
    .msg.err { background:#331111; border:1px solid #f87171; }
    @media (max-width:800px){ .row { grid-template-columns:1fr; } }
  </style>
</head>
<body>
  <main class="container">
    <section class="card">
      <h1>Asetukku Admin</h1>
      <p class="small">Syötä salasana avataksesi hallintapaneelin. Asetukset tallennetaan tiedostoon <code>asetukku/data/admin-settings.json</code>.</p>
    </section>

    <?php if (!isAuthenticated()): ?>
      <section class="card">
        <?php foreach ($errors as $error): ?>
          <div class="msg err"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endforeach; ?>

        <form method="post" autocomplete="off">
          <input type="hidden" name="action" value="login">
          <label for="password">Syötä salasana</label>
          <input id="password" name="password" type="password" required minlength="4" maxlength="200">
          <div style="margin-top:14px;">
            <button class="btn primary" type="submit">Avaa paneeli</button>
          </div>
        </form>
      </section>
    <?php else: ?>
      <section class="card">
        <?php if ($message !== ''): ?><div class="msg ok"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        <?php foreach ($errors as $error): ?>
          <div class="msg err"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endforeach; ?>

        <form method="post">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">

          <h2>Sivusto</h2>
          <div class="row">
            <div>
              <label>Brändin nimi</label>
              <input name="site_brandName" value="<?php echo htmlspecialchars($settings['site']['brandName'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
              <label>Tuki sähköposti</label>
              <input name="site_supportEmail" type="email" value="<?php echo htmlspecialchars($settings['site']['supportEmail'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
          <div class="row">
            <div>
              <label>Tuki puhelin</label>
              <input name="site_supportPhone" value="<?php echo htmlspecialchars($settings['site']['supportPhone'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
              <label>Huoltotila</label>
              <select name="site_maintenanceMode">
                <option value="0" <?php echo $settings['site']['maintenanceMode'] ? '' : 'selected'; ?>>Pois</option>
                <option value="1" <?php echo $settings['site']['maintenanceMode'] ? 'selected' : ''; ?>>Päällä</option>
              </select>
            </div>
          </div>
          <label>Tiedotepalkki</label>
          <textarea name="site_announcement"><?php echo htmlspecialchars($settings['site']['announcement'], ENT_QUOTES, 'UTF-8'); ?></textarea>

          <h2>Kampanja-popup</h2>
          <div class="row">
            <div>
              <label>Käytössä</label>
              <select name="campaign_enabled">
                <option value="1" <?php echo $settings['campaign']['enabled'] ? 'selected' : ''; ?>>Kyllä</option>
                <option value="0" <?php echo $settings['campaign']['enabled'] ? '' : 'selected'; ?>>Ei</option>
              </select>
            </div>
            <div>
              <label>Cooldown (päivää)</label>
              <input name="campaign_cooldownDays" type="number" min="1" max="30" value="<?php echo (int)$settings['campaign']['cooldownDays']; ?>">
            </div>
          </div>
          <label>Otsikko</label>
          <input name="campaign_title" value="<?php echo htmlspecialchars($settings['campaign']['title'], ENT_QUOTES, 'UTF-8'); ?>">
          <label>Kuvaus</label>
          <textarea name="campaign_description"><?php echo htmlspecialchars($settings['campaign']['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
          <label>Deadline (ISO)</label>
          <input name="campaign_deadlineIso" value="<?php echo htmlspecialchars($settings['campaign']['deadlineIso'], ENT_QUOTES, 'UTF-8'); ?>">

          <h2>Evästeet</h2>
          <div class="row">
            <div>
              <label>Evästebanneri käytössä</label>
              <select name="cookies_bannerEnabled">
                <option value="1" <?php echo $settings['cookies']['bannerEnabled'] ? 'selected' : ''; ?>>Kyllä</option>
                <option value="0" <?php echo $settings['cookies']['bannerEnabled'] ? '' : 'selected'; ?>>Ei</option>
              </select>
            </div>
            <div>
              <label>Privacy URL</label>
              <input name="cookies_policyUrl" value="<?php echo htmlspecialchars($settings['cookies']['policyUrl'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
          <label>Evästebannerin teksti</label>
          <textarea name="cookies_message"><?php echo htmlspecialchars($settings['cookies']['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>

          <h2>UI</h2>
          <div class="row">
            <div>
              <label>Oletusteema</label>
              <select name="ui_defaultTheme">
                <option value="dark" <?php echo $settings['ui']['defaultTheme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                <option value="light" <?php echo $settings['ui']['defaultTheme'] === 'light' ? 'selected' : ''; ?>>Light</option>
              </select>
            </div>
            <div>
              <label>Hero CTA teksti</label>
              <input name="ui_heroCtaPrimaryText" value="<?php echo htmlspecialchars($settings['ui']['heroCtaPrimaryText'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
          <label>Hero CTA URL</label>
          <input name="ui_heroCtaPrimaryUrl" value="<?php echo htmlspecialchars($settings['ui']['heroCtaPrimaryUrl'], ENT_QUOTES, 'UTF-8'); ?>">

          <div style="display:flex; gap:10px; margin-top:18px;">
            <button class="btn primary" type="submit">Tallenna asetukset</button>
          </div>
        </form>
      </section>

      <section class="card">
        <form method="post">
          <input type="hidden" name="action" value="logout">
          <button class="btn ghost" type="submit">Kirjaudu ulos</button>
        </form>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
