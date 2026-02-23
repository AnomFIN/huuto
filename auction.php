<?php
// Bootstrap the application
require_once __DIR__ . '/bootstrap.php';

$auctionModel = new Auction();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

$auction = $auctionModel->getAuctionById($id);

if (!$auction) {
    header('Location: index.php');
    exit;
}

// Increment view count
$auctionModel->incrementViews($id);
$auction['views'] = (isset($auction['views']) ? (int)$auction['views'] : 0) + 1;

$images = $auctionModel->getAuctionImages($id);
$bids = $auctionModel->getAuctionBids($id);
$metadata = $auctionModel->getAuctionMetadata($id);

if (!is_array($metadata)) {
    $metadata = [];
}

$categoryName = trim((string)($auction['category_name'] ?? 'Muut'));
$categoryKey = mb_strtolower($categoryName, 'UTF-8');

if ($categoryKey === 'työkoneet') {
    $categoryKey = 'ajoneuvot';
}

$auctionEndUnix = strtotime((string)$auction['end_time']);
if ($auctionEndUnix === false) {
    $auctionEndUnix = time();
}

function meta_value(array $metadata, string $key, string $fallback = '-'): string
{
    $value = isset($metadata[$key]) ? trim((string)$metadata[$key]) : '';
    return $value !== '' ? $value : $fallback;
}

function meta_bool(array $metadata, string $key, string $yes = 'Kyllä', string $no = 'Ei'): string
{
    $value = mb_strtolower(trim((string)($metadata[$key] ?? '')), 'UTF-8');
    if ($value === '') {
        return '-';
    }

    $truthy = ['1', 'true', 'yes', 'kyllä', 'kylla'];
    $falsy = ['0', 'false', 'no', 'ei'];

    if (in_array($value, $truthy, true)) {
        return $yes;
    }

    if (in_array($value, $falsy, true)) {
        return $no;
    }

    return ucfirst((string)$metadata[$key]);
}

function render_specs_table(array $rows): void
{
    echo '<table class="specs-table">';
    foreach ($rows as $label => $value) {
        echo '<tr>';
        echo '<th>' . htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') . '</th>';
        echo '<td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

$pageTitle = $auction['title'] . ' - ' . SITE_NAME;
include SRC_PATH . '/views/header.php';
?>

<style>
    .auction-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .auction-breadcrumb {
        margin-bottom: 1.5rem;
    }

    .auction-breadcrumb a {
        color: var(--accent-600);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .auction-breadcrumb a:hover {
        color: var(--accent-700);
    }

    .auction-category-badge {
        display: inline-block;
        background: var(--accent-600);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .auction-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-900);
        margin: 0.4rem 0 0.75rem;
        line-height: 1.2;
    }

    .auction-subtitle {
        font-size: 1.1rem;
        color: var(--text-700);
        margin-bottom: 0.5rem;
    }

    .auction-stats {
        display: inline-flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.9rem;
        color: var(--text-600);
        margin-bottom: 1.5rem;
    }

    .auction-admin-actions {
        margin: 0 0 1rem;
        display: flex;
        justify-content: flex-start;
        gap: .6rem;
    }

    .auction-admin-edit {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: #111827;
        color: #fff;
        text-decoration: none;
        border-radius: 999px;
        padding: .5rem .9rem;
        font-size: .82rem;
        font-weight: 700;
    }

    .auction-admin-edit:hover {
        opacity: .9;
    }

    .auction-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .auction-main {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .auction-sidebar {
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .auction-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-1);
    }

    .auction-card h2, .auction-card h3 {
        margin: 0 0 1rem;
        font-weight: 700;
        color: var(--text-900);
    }

    .auction-card h2 {
        font-size: 1.25rem;
    }

    .auction-card h3 {
        font-size: 1.1rem;
    }

    .image-gallery {
        margin-bottom: 1rem;
    }

    .main-image {
        width: 100%;
        height: min(62vh, 560px);
        object-fit: contain;
        background: #f2f5f9;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        margin-bottom: 1rem;
        cursor: zoom-in;
    }

    .image-thumbnails {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
        gap: 0.5rem;
    }

    .image-caption {
        margin-top: .5rem;
        font-size: .9rem;
        color: var(--text-700);
    }

    .thumbnail {
        width: 100%;
        height: 60px;
        object-fit: contain;
        background: #f2f5f9;
        border-radius: 4px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    .thumbnail:hover, .thumbnail.active {
        border-color: var(--accent-600);
    }

    .price-section {
        background: var(--bg-neutral-50);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .price-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .price-item {
        text-align: center;
    }

    .price-label {
        font-size: 0.8rem;
        color: var(--text-600);
        margin-bottom: 0.25rem;
    }

    .price-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--accent-600);
    }

    .auction-deadline {
        text-align: center;
        margin: 1rem auto;
        width: 200px;
        min-height: 200px;
        border-radius: 999px;
        border: 2px solid rgba(201,138,45,.45);
        background: radial-gradient(circle at 30% 25%, rgba(255,255,255,.85), rgba(250,239,220,.65));
        display: grid;
        place-content: center;
        padding: .8rem;
    }

    .auction-deadline-label {
        font-size: 0.8rem;
        color: var(--text-600);
        margin-bottom: 0.25rem;
        font-weight: 600;
    }

    .auction-deadline-time {
        font-size: 1.25rem;
        font-weight: 700;
        color: #dc2626;
        line-height: 1.28;
    }

    .auction-stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        text-align: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--line);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-weight: 700;
        color: var(--text-900);
        font-size: 1.1rem;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-600);
    }

    .bid-button {
        width: 100%;
        padding: 0.75rem 1rem;
        background: var(--accent-600);
        color: white;
        border: none;
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        margin-top: 1rem;
        transition: all 0.2s;
    }

    .bid-button:hover {
        background: var(--accent-700);
    }

    .specs-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .specs-table th, .specs-table td {
        padding: 0.5rem;
        text-align: left;
        border-bottom: 1px solid var(--line);
        font-size: 0.9rem;
    }

    .specs-table th {
        font-weight: 600;
        color: var(--text-700);
        width: 40%;
    }

    .specs-table td {
        color: var(--text-900);
    }

    .equipment-list {
        margin-top: 1rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.5rem;
    }

    .equipment-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-700);
    }

    .equipment-item::before {
        content: '✓';
        color: #16a34a;
        font-weight: bold;
    }

    .condition-section {
        margin-top: 1rem;
    }

    .condition-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--line);
        font-size: 0.9rem;
    }

    .condition-item:last-child {
        border-bottom: none;
    }

    .condition-label {
        font-weight: 600;
        color: var(--text-700);
        flex: 1;
    }

    .condition-value {
        color: var(--text-900);
        flex: 2;
        text-align: right;
    }

    .terms-section {
        background: var(--bg-neutral-50);
        border-radius: var(--radius);
        padding: 1rem;
        margin-top: 1rem;
        font-size: 0.85rem;
        line-height: 1.5;
        color: var(--text-700);
    }

    .terms-section h4 {
        margin: 0 0 0.5rem;
        font-weight: 700;
        color: var(--text-900);
    }

    .bids-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .bids-table th, .bids-table td {
        padding: 0.75rem 0.5rem;
        text-align: left;
        border-bottom: 1px solid var(--line);
        font-size: 0.9rem;
    }

    .bids-table th {
        font-weight: 600;
        color: var(--text-700);
        background: var(--bg-neutral-50);
    }

    .seller-info {
        background: var(--bg-neutral-50);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .seller-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }

    .lightbox {
        position: fixed;
        inset: 0;
        z-index: 3000;
        display: none;
        background: rgba(8, 12, 22, .88);
        backdrop-filter: blur(4px);
    }

    .lightbox.open {
        display: grid;
        grid-template-rows: auto 1fr auto;
    }

    .lightbox-top {
        display: flex;
        justify-content: flex-end;
        padding: .9rem 1rem 0;
    }

    .lightbox-close {
        border: 1px solid rgba(255,255,255,.4);
        background: rgba(255,255,255,.1);
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 999px;
        cursor: pointer;
        font-size: 1.1rem;
    }

    .lightbox-main {
        display: grid;
        grid-template-columns: 56px 1fr 56px;
        align-items: center;
        gap: .8rem;
        width: min(1600px, 96vw);
        margin: 0 auto;
    }

    .lightbox-image-wrap {
        display: grid;
        place-items: center;
        min-height: 72vh;
    }

    .lightbox-image {
        max-width: 100%;
        max-height: 75vh;
        object-fit: contain;
        border-radius: 12px;
        background: #0d1424;
    }

    .lightbox-arrow {
        border: 1px solid rgba(255,255,255,.35);
        background: rgba(255,255,255,.12);
        color: #fff;
        width: 46px;
        height: 46px;
        border-radius: 999px;
        cursor: pointer;
        font-size: 1.25rem;
    }

    .lightbox-meta {
        color: #e6edf8;
        text-align: center;
        padding: 0 1rem 1rem;
    }

    .lightbox-caption {
        margin: 0 0 .35rem;
        font-size: .95rem;
    }

    .lightbox-index {
        font-size: .85rem;
        opacity: .86;
    }

    .rating-stars {
        color: #f59e0b;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .auction-grid {
            grid-template-columns: 1fr;
        }
        
        .auction-sidebar {
            position: static;
        }
        
        .price-grid {
            grid-template-columns: 1fr;
        }
        
        .auction-stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="auction-container">
    <div class="auction-breadcrumb">
        <a href="index.php">← Takaisin etusivulle</a>
    </div>

    <div style="margin-bottom: 0.5rem;">
        <span class="auction-category-badge"><?php echo htmlspecialchars($auction['category_name'] ?? 'Muut'); ?></span>
    </div>
    <h1 class="auction-title"><?php echo htmlspecialchars($auction['title']); ?></h1>

    <?php if (function_exists('is_admin') && is_admin()): ?>
        <div class="auction-admin-actions">
            <a class="auction-admin-edit" href="/edit_auction.php?id=<?php echo (int)$auction['id']; ?>">✏️ Muokkaa kohdetta</a>
        </div>
    <?php endif; ?>

    <div class="auction-stats">
        <span>👁 <?php echo number_format($auction['views']); ?> katselua</span>
        <span>📍 <?php echo htmlspecialchars($auction['location'] ?? 'Ei sijaintia'); ?></span>
    </div>

    <div class="auction-grid">
        <div class="auction-main">
            <!-- Images Section -->
            <div class="auction-card">
                <?php if (!empty($images)): ?>
                    <div class="image-gallery">
                        <img src="<?php echo htmlspecialchars($images[0]['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($auction['title']); ?>"
                                class="main-image" id="mainImage" onclick="openLightbox(currentImageIndex)">
                        <p class="image-caption" id="mainImageCaption"><?php echo htmlspecialchars((string)($images[0]['caption'] ?? '')); ?></p>
                        
                        <?php if (count($images) > 1): ?>
                            <div class="image-thumbnails">
                                <?php foreach ($images as $index => $image): ?>
                                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                         alt="Kuva <?php echo $index + 1; ?>"
                                         class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                         data-index="<?php echo (int)$index; ?>"
                                         data-caption="<?php echo htmlspecialchars((string)($image['caption'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                         onclick="setMainImage(this, '<?php echo htmlspecialchars($image['image_path']); ?>', this.dataset.caption || '', <?php echo (int)$index; ?>)">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div id="galleryImageCount" style="text-align: center; margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-600);">1 / <?php echo count($images); ?></div>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-600);">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
                        <p>Ei kuvia saatavilla</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <?php if (!empty(trim((string)($auction['description'] ?? '')))): ?>
            <div class="auction-card">
                <h2>Kuvaus</h2>
                <p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-800); white-space: pre-wrap; margin: 0;">
                    <?php echo nl2br(htmlspecialchars($auction['description'])); ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- Category Specific Details -->
            <div class="auction-card">
                <?php if ($categoryKey === 'ajoneuvot'): ?>
                    <h2>Ajoneuvon tiedot</h2>
                    <?php
                    $vehicleRows = [
                        'Merkki' => meta_value($metadata, 'vehicle_brand'),
                        'Malli' => meta_value($metadata, 'vehicle_model', $auction['title'] ?? '-'),
                        'Vuosimalli' => meta_value($metadata, 'vehicle_year'),
                        'Mittarilukema' => meta_value($metadata, 'mileage', '-') === '-' ? '-' : meta_value($metadata, 'mileage') . ' km',
                        'Moottori' => meta_value($metadata, 'engine'),
                        'Polttoaine' => meta_value($metadata, 'fuel_type'),
                        'Huoltokirja' => meta_bool($metadata, 'service_book'),
                        'Rekisterissä' => meta_bool($metadata, 'registered'),
                        'Liikennevakuutus voimassa' => meta_bool($metadata, 'traffic_insurance'),
                        'Katsastettu' => meta_bool($metadata, 'inspected'),
                        'Seuraava katsastus' => meta_value($metadata, 'next_inspection'),
                        'Avainten lukumäärä' => meta_value($metadata, 'key_count'),
                    ];
                    render_specs_table($vehicleRows);
                    ?>
                <?php elseif ($categoryKey === 'kiinteistöt' || $categoryKey === 'asunnot'): ?>
                    <h2>Kiinteistötiedot</h2>
                    <?php
                    $propertyRows = [
                        'Myytävä omaisuus' => meta_value($metadata, 'property_type'),
                        'Huoneistotyyppi' => meta_value($metadata, 'room_type'),
                        'Asuinpinta-ala' => meta_value($metadata, 'living_area', '-') === '-' ? '-' : meta_value($metadata, 'living_area') . ' m²',
                        'Tontin pinta-ala' => meta_value($metadata, 'plot_area', '-') === '-' ? '-' : meta_value($metadata, 'plot_area') . ' m²',
                        'Rakennusvuosi' => meta_value($metadata, 'build_year'),
                        'Energialuokka' => meta_value($metadata, 'energy_class'),
                        'Hoitovastike' => meta_value($metadata, 'maintenance_fee', '-') === '-' ? '-' : meta_value($metadata, 'maintenance_fee') . ' €/kk',
                        'Rahoitusvastike' => meta_value($metadata, 'finance_fee', '-') === '-' ? '-' : meta_value($metadata, 'finance_fee') . ' €/kk',
                    ];
                    render_specs_table($propertyRows);
                    ?>
                <?php elseif ($categoryKey === 'elektroniikka'): ?>
                    <h2>Elektroniikan tiedot</h2>
                    <?php
                    $electronicsRows = [
                        'Merkki' => meta_value($metadata, 'electronics_brand'),
                        'Malli' => meta_value($metadata, 'electronics_model', $auction['title'] ?? '-'),
                        'Kapasiteetti / Koko' => meta_value($metadata, 'capacity'),
                        'Takuu voimassa' => meta_value($metadata, 'warranty_until'),
                        'Alkuperäispakkaus' => meta_bool($metadata, 'original_box'),
                        'Laturi mukana' => meta_bool($metadata, 'charger_included'),
                    ];
                    render_specs_table($electronicsRows);
                    ?>
                <?php elseif ($categoryKey === 'kodin tavarat'): ?>
                    <h2>Kodin tavaroiden tiedot</h2>
                    <?php
                    $homeRows = [
                        'Tuotetyyppi' => meta_value($metadata, 'home_item_type'),
                        'Materiaali' => meta_value($metadata, 'material'),
                        'Mitat' => meta_value($metadata, 'dimensions'),
                        'Valmistaja' => meta_value($metadata, 'manufacturer'),
                    ];
                    render_specs_table($homeRows);
                    ?>
                <?php elseif ($categoryKey === 'urheilu'): ?>
                    <h2>Urheiluvälineen tiedot</h2>
                    <?php
                    $sportsRows = [
                        'Laji' => meta_value($metadata, 'sport_type'),
                        'Koko / Mitat' => meta_value($metadata, 'size'),
                        'Merkki' => meta_value($metadata, 'sports_brand'),
                        'Käyttötiheys' => meta_value($metadata, 'usage_frequency'),
                    ];
                    render_specs_table($sportsRows);
                    ?>
                <?php elseif ($categoryKey === 'vaatteet'): ?>
                    <h2>Vaatteiden tiedot</h2>
                    <?php
                    $clothingRows = [
                        'Tyyppi' => meta_value($metadata, 'clothing_type'),
                        'Koko' => meta_value($metadata, 'clothing_size'),
                        'Merkki' => meta_value($metadata, 'clothing_brand'),
                        'Väri' => meta_value($metadata, 'color'),
                        'Materiaali' => meta_value($metadata, 'clothing_material'),
                        'Kunto' => meta_value($metadata, 'clothing_condition'),
                    ];
                    render_specs_table($clothingRows);
                    ?>
                <?php elseif ($categoryKey === 'keräily'): ?>
                    <h2>Keräilykohteen tiedot</h2>
                    <?php
                    $collectibleRows = [
                        'Keräilytyyppi' => meta_value($metadata, 'collectible_type'),
                        'Ikä / Ajanjakso' => meta_value($metadata, 'age_period'),
                        'Tekijä / Valmistaja' => meta_value($metadata, 'creator'),
                        'Harvinaisuus' => meta_value($metadata, 'rarity'),
                    ];
                    render_specs_table($collectibleRows);
                    ?>
                <?php else: ?>
                    <h2>Kohteen lisätiedot</h2>
                    <?php
                    $generalRows = [
                        'Tuotetyyppi' => meta_value($metadata, 'general_type'),
                        'Paino' => meta_value($metadata, 'weight', '-') === '-' ? '-' : meta_value($metadata, 'weight') . ' kg',
                        'Mitat' => meta_value($metadata, 'general_dimensions'),
                        'Valmistusmaa' => meta_value($metadata, 'country_origin'),
                        'Sijainti' => meta_value($metadata, 'detailed_address', $auction['location'] ?? '-'),
                        'Lisätietoja sijainnista' => meta_value($metadata, 'location_notes'),
                    ];
                    render_specs_table($generalRows);
                    ?>
                <?php endif; ?>

                <div style="margin-top: 1.25rem;">
                    <h3>Kunnon kuvaus</h3>
                    <p style="font-size: 0.9rem; line-height: 1.5; color: var(--text-700);">
                        <?php echo nl2br(htmlspecialchars($auction['condition_description'] ?? 'Ei erillistä kunnon kuvausta.')); ?>
                    </p>
                </div>
            </div>

            <!-- Bid History -->
            <?php if (!empty($bids)): ?>
                <div class="auction-card">
                    <h2>Tarjoushistoria</h2>
                    <table class="bids-table">
                        <thead>
                            <tr>
                                <th>Tarjous</th>
                                <th>Tarjoaja</th>
                                <th>Aika</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bids as $bid): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--accent-600);">
                                        <?php echo number_format($bid['amount'], 2, ',', ' '); ?> €
                                    </td>
                                    <td><?php echo htmlspecialchars($bid['username']); ?></td>
                                    <td style="color: var(--text-600);">
                                        <?php echo date('d.m.Y H:i', strtotime($bid['bid_time'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Terms and Conditions -->
            <div class="auction-card">
                <h2>Myyntiehdot</h2>
                <div class="terms-section">
                    <h4>Yleistä</h4>
                    <p>Myyjä sitoutuu myymään kohteen korkeimman tarjouksen tekijälle.</p>
                    
                    <h4>Maksu ja nouto</h4>
                    <p>Ajoneuvo noudettava 5 arkipäivän kuluessa huutokaupan päättymisestä, ellei toisin sovita. Tämän ylittävät päivät varastointi kulu on 40€/päivä.</p>
                    
                    <h4>Huomioitavaa</h4>
                    <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                        <li>Huutokauppaan osallistuminen ilman ostoaikomusta on kiellettyä</li>
                        <li>Sinun tulee tutustua kohteeseen ennen tarjoamista</li>
                        <li>Kohteet myydään sellaisena kuin ovat</li>
                        <li>Kohde on maksettava yhden (1) arkipäivän kuluessa</li>
                    </ul>
                    
                    <p>Tutustuthan huutokauppaehtoihin ennen tarjouksen tekemistä.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="auction-sidebar">
            <div class="auction-card">
                <div class="price-section">
                    <div class="price-grid">
                        <div class="price-item">
                            <div class="price-label">Aloitushinta</div>
                            <div class="price-value" style="font-size: 1.1rem; color: var(--text-700);">
                                <?php echo number_format($auction['starting_price'], 2, ',', ' '); ?> €
                            </div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Nykyinen hinta</div>
                            <div class="price-value">
                                <?php echo number_format($auction['current_price'], 2, ',', ' '); ?> €
                            </div>
                        </div>
                    </div>

                    <?php if ($auction['buy_now_price']): ?>
                        <div style="text-align: center; margin: 1rem 0;">
                            <div class="price-label">Osta heti -hinta</div>
                            <div class="price-value" style="color: #16a34a;">
                                <?php echo number_format($auction['buy_now_price'], 2, ',', ' '); ?> €
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="auction-deadline">
                        <div class="auction-deadline-label">Huutokauppa päättyy</div>
                        <div class="auction-deadline-time" data-endtime="<?php echo (int)$auctionEndUnix; ?>">
                            <!-- JavaScript will populate this -->
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-600); margin-top: 0.25rem;">
                            <?php echo date('d.m.Y H:i', strtotime($auction['end_time'])); ?>
                        </div>
                    </div>

                    <div class="auction-stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $auction['bid_count'] ?? 0; ?></div>
                            <div class="stat-label">Tarjousta</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $auction['views']; ?></div>
                            <div class="stat-label">Katselua</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $auction['watch_count'] ?? 0; ?></div>
                            <div class="stat-label">Seuraajaa</div>
                        </div>
                    </div>
                </div>

                <button class="bid-button">Tee tarjous</button>
                
                <?php if ($auction['buy_now_price']): ?>
                    <button class="bid-button" style="background: #16a34a; margin-top: 0.5rem;">
                        Osta heti
                    </button>
                <?php endif; ?>
            </div>

            <!-- Seller Information -->
            <div class="auction-card" style="margin-top: 1rem;">
                <h3>Myyjä</h3>
                <div class="seller-info">
                    <div style="font-weight: 600; color: var(--text-900);">
                        <?php echo htmlspecialchars($auction['seller_username'] ?? 'Huutokaupat.com'); ?>
                    </div>
                    <div class="seller-rating">
                        <span class="rating-stars">★★★★☆</span>
                        <span>3,8</span>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-600); margin-top: 0.5rem;">
                        <div>44 ilmoitusta tällä hetkellä</div>
                        <div>7 701 myytyä kohdetta</div>
                        <div>98% hyväksyntäaste</div>
                    </div>
                </div>
            </div>
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
</div>

<?php if (!empty($images)): ?>
<div class="lightbox" id="galleryLightbox" aria-hidden="true">
    <div class="lightbox-top">
        <button type="button" class="lightbox-close" onclick="closeLightbox()" aria-label="Sulje kuva">✕</button>
    </div>
    <div class="lightbox-main">
        <button type="button" class="lightbox-arrow" onclick="showPrevImage()" aria-label="Edellinen kuva">←</button>
        <div class="lightbox-image-wrap">
            <img src="" alt="Suurennettu kuva" class="lightbox-image" id="lightboxImage">
        </div>
        <button type="button" class="lightbox-arrow" onclick="showNextImage()" aria-label="Seuraava kuva">→</button>
    </div>
    <div class="lightbox-meta">
        <p class="lightbox-caption" id="lightboxCaption"></p>
        <div class="lightbox-index" id="lightboxIndex"></div>
    </div>
</div>
<?php endif; ?>

<script>
const galleryImages = <?php
echo json_encode(array_map(static function ($image): array {
    return [
        'src' => (string)($image['image_path'] ?? ''),
        'caption' => (string)($image['caption'] ?? ''),
    ];
}, $images), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>;

let currentImageIndex = 0;

function updateGalleryCounter() {
    const node = document.getElementById('galleryImageCount');
    if (node && galleryImages.length > 0) {
        node.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
    }
}

function setMainImage(thumbnail, imagePath, caption, index) {
    document.getElementById('mainImage').src = imagePath;
    const captionNode = document.getElementById('mainImageCaption');
    if (captionNode) {
        captionNode.textContent = caption || '';
    }
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    thumbnail.classList.add('active');

    if (Number.isInteger(index) && index >= 0) {
        currentImageIndex = index;
        updateGalleryCounter();
    }
}

function openLightbox(index = 0) {
    if (!galleryImages.length) {
        return;
    }
    currentImageIndex = Math.max(0, Math.min(index, galleryImages.length - 1));
    const lightbox = document.getElementById('galleryLightbox');
    lightbox.classList.add('open');
    lightbox.setAttribute('aria-hidden', 'false');
    renderLightboxImage();
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('galleryLightbox');
    if (!lightbox) {
        return;
    }
    lightbox.classList.remove('open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

function renderLightboxImage() {
    const data = galleryImages[currentImageIndex];
    if (!data) {
        return;
    }
    const img = document.getElementById('lightboxImage');
    const caption = document.getElementById('lightboxCaption');
    const indexNode = document.getElementById('lightboxIndex');
    img.src = data.src;
    caption.textContent = data.caption || '';
    indexNode.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
}

function showPrevImage() {
    if (!galleryImages.length) {
        return;
    }
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    renderLightboxImage();
}

function showNextImage() {
    if (!galleryImages.length) {
        return;
    }
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    renderLightboxImage();
}

function updateCountdown() {
    const countdownElements = document.querySelectorAll('.auction-deadline-time[data-endtime]');
    
    countdownElements.forEach(element => {
        const endTimeUnix = Number(element.getAttribute('data-endtime'));
        if (!Number.isFinite(endTimeUnix) || endTimeUnix <= 0) {
            element.textContent = 'Aika puuttuu';
            element.style.color = '#6b7280';
            return;
        }

        const endTimeMs = endTimeUnix * 1000;
        const nowMs = Date.now();
        const timeDiff = endTimeMs - nowMs;
        
        if (timeDiff <= 0) {
            element.textContent = 'Päättynyt';
            element.style.color = '#6b7280';
            return;
        }
        
        const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
        
        if (days > 0) {
            element.textContent = `${days} päivää ${hours} tuntia`;
        } else if (hours > 0) {
            element.textContent = `${hours} tuntia ${minutes} minuuttia`;
        } else if (minutes > 0) {
            element.textContent = `${minutes} minuuttia ${seconds} sekuntia`;
        } else {
            element.textContent = `${seconds} sekuntia`;
        }
    });
}

// Update countdown every second
updateCountdown();
setInterval(updateCountdown, 1000);

document.addEventListener('keydown', function (event) {
    const lightbox = document.getElementById('galleryLightbox');
    if (!lightbox || !lightbox.classList.contains('open')) {
        return;
    }

    if (event.key === 'Escape') {
        closeLightbox();
    } else if (event.key === 'ArrowLeft') {
        showPrevImage();
    } else if (event.key === 'ArrowRight') {
        showNextImage();
    }
});

const lightbox = document.getElementById('galleryLightbox');
if (lightbox) {
    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });
}

updateGalleryCounter();
</script>

<?php include SRC_PATH . '/views/footer.php'; ?>
