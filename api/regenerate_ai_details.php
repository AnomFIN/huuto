<?php
/**
 * Regenerate AI Product Details for an existing auction
 * Uses existing auction images to generate category-specific details
 */

require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Require admin access
if (!function_exists('is_admin') || !is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Ei oikeuksia']);
    exit;
}

// Load AI settings
$aiSettings = ['openai_api_key' => '', 'ai_enabled' => false];
$settingsFile = __DIR__ . '/../config/ai_settings.php';
if (file_exists($settingsFile)) {
    $loadedSettings = include $settingsFile;
    if (is_array($loadedSettings)) {
        $aiSettings = array_merge($aiSettings, $loadedSettings);
    }
}

if (!$aiSettings['ai_enabled'] || empty($aiSettings['openai_api_key'])) {
    echo json_encode(['error' => 'AI-generointi ei ole käytössä. Määritä OpenAI API-avain admin-paneelissa.']);
    exit;
}

try {
    $auctionId = isset($_POST['auction_id']) ? (int)$_POST['auction_id'] : 0;
    if (!$auctionId) {
        throw new Exception('Kohteen ID puuttuu');
    }

    $db = Database::getInstance()->getConnection();

    // Get auction data
    $stmt = $db->prepare("SELECT a.*, c.id as cat_id FROM auctions a LEFT JOIN categories c ON a.category_id = c.id WHERE a.id = ?");
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch();

    if (!$auction) {
        throw new Exception('Kohdetta ei löydy');
    }

    // Get auction images
    $stmt = $db->prepare("SELECT * FROM auction_images WHERE auction_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$auctionId]);
    $images = $stmt->fetchAll();

    if (empty($images)) {
        throw new Exception('Kohteella ei ole kuvia. Lisää kuvia ensin.');
    }

    // Load images as base64 (limit to 3 for API efficiency)
    $imageContents = [];
    foreach ($images as $image) {
        $filePath = __DIR__ . '/../' . ltrim((string)$image['image_path'], '/');
        if (file_exists($filePath)) {
            $fileType = mime_content_type($filePath);
            if (in_array($fileType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'], true)) {
                $imageData = file_get_contents($filePath);
                $base64 = base64_encode($imageData);
                $imageContents[] = [
                    'type' => 'image_url',
                    'image_url' => ['url' => "data:$fileType;base64,$base64"]
                ];
            }
        }
        if (count($imageContents) >= 3) {
            break;
        }
    }

    if (empty($imageContents)) {
        throw new Exception('Kuvatiedostoja ei löydy palvelimelta');
    }

    $categoryId = (string)($auction['cat_id'] ?? $auction['category_id'] ?? '8');
    $title = (string)($auction['title'] ?? '');
    $description = (string)($auction['description'] ?? '');
    $condition = (string)($auction['condition_description'] ?? '');

    // Category-specific prompts (mirrors api_ai_details.php)
    $categoryPrompts = [
        '1' => [
            'fields' => 'property_type, room_type, living_area, plot_area, build_year, energy_class, maintenance_fee, finance_fee',
            'description' => 'kiinteistön tiedot: myytävä omaisuus, huoneistotyyppi, asuinpinta-ala (m²), tontin pinta-ala (m²), rakennusvuosi, energialuokka (A-G), hoitovastike ja rahoitusvastike €/kk'
        ],
        '2' => [
            'fields' => 'vehicle_brand, vehicle_model, vehicle_year, mileage, engine, fuel_type, service_book, registered, traffic_insurance, inspected, next_inspection, key_count, vehicle_defects',
            'description' => 'ajoneuvon tiedot: merkki, malli, vuosimalli, kilometrit, moottori, polttoaine, huoltokirja (kyllä/ei), rekisterissä (kyllä/ei), vakuutus voimassa (kyllä/ei), katsastettu (kyllä/ei), seuraava katsastus (MM/YYYY), avainten määrä, viat ja huomautukset'
        ],
        '3' => [
            'fields' => 'electronics_brand, electronics_model, capacity, warranty_until, original_box, charger_included',
            'description' => 'elektroniikan tiedot: merkki, malli, kapasiteetti/koko, takuu voimassa (päiväys), alkuperäispakkaus (kyllä/ei), laturi mukana (kyllä/ei)'
        ],
        '4' => [
            'fields' => 'home_item_type, material, dimensions, manufacturer',
            'description' => 'kodin tavaran tiedot: tuotetyyppi, materiaali, mitat, valmistaja'
        ],
        '5' => [
            'fields' => 'sport_type, size, sports_brand, usage_frequency',
            'description' => 'urheiluvälineen tiedot: laji, koko/mitat, merkki, käyttötiheys (uusi/vähän/kohtuullisesti/paljon käytetty)'
        ],
        '6' => [
            'fields' => 'clothing_type, clothing_size, clothing_brand, color, clothing_material, clothing_condition',
            'description' => 'vaatteen tiedot: tyyppi, koko, merkki, väri, materiaali, kunto'
        ],
        '7' => [
            'fields' => 'collectible_type, age_period, creator, rarity',
            'description' => 'keräilyesineen tiedot: keräilytyyppi, ikä/ajanjakso, valmistaja/tekijä, harvinaisuus'
        ],
        '8' => [
            'fields' => 'general_type, weight, general_dimensions, country_origin',
            'description' => 'tuotteen yleiset tiedot: tuotetyyppi, paino (kg), mitat, valmistusmaa'
        ]
    ];

    $prompt = $categoryPrompts[$categoryId] ?? $categoryPrompts['8'];

    $context = 'Tuotteen perustiedot: ';
    if ($title) {
        $context .= "Nimi: $title. ";
    }
    if ($description) {
        $context .= "Kuvaus: $description. ";
    }
    if ($condition) {
        $context .= "Kunto: $condition. ";
    }

    $messages = [
        [
            'role' => 'system',
            'content' => "Olet asiantuntija joka täyttää kategoria-spesifisiä tuotetietoja huutokauppatuotteelle. VASTAA VAIN JSON-MUODOSSA SUOMEKSI! 

Täytettävät kentät: {$prompt['fields']}

JSON-formaatti:
{
  \"details\": {
    \"kentta_nimi\": \"arvo\",
    ...
  },
  \"message\": \"Lyhyt tiivistelmä täytetyistä tiedoista\"
}

Arvio tiedot kuvien ja kontekstin perusteella. Jos et ole varma, tee järkevä arvio. Käytä suomalaisia termejä ja yksiköitä."
        ],
        [
            'role' => 'user',
            'content' => array_merge(
                [['type' => 'text', 'text' => "Täytä {$prompt['description']} näiden kuvien ja kontekstin perusteella: $context"]],
                $imageContents
            )
        ]
    ];

    $requestData = [
        'model' => 'gpt-4o-mini',
        'messages' => $messages,
        'max_tokens' => 800,
        'temperature' => 0.3
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $aiSettings['openai_api_key']
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        throw new Exception('OpenAI API-kutsu epäonnistui (HTTP ' . $httpCode . ')');
    }

    $apiResponse = json_decode($response, true);

    if (!$apiResponse || !isset($apiResponse['choices'][0]['message']['content'])) {
        throw new Exception('Virheellinen vastaus OpenAI:lta');
    }

    $content = trim($apiResponse['choices'][0]['message']['content']);
    $content = preg_replace('/^```json\s*/', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);

    $result = json_decode($content, true);

    if (!$result || empty($result['details'])) {
        throw new Exception('AI-vastaus ei ollut kelvollista JSON-muotoa');
    }

    // Save metadata to database
    $auctionModel = new Auction();
    foreach ($result['details'] as $fieldName => $fieldValue) {
        if ($fieldName !== '' && $fieldValue !== null && $fieldValue !== '') {
            $auctionModel->addAuctionMetadata($auctionId, (string)$fieldName, (string)$fieldValue);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $result['message'] ?? 'AI-lisätiedot generoitu ja tallennettu onnistuneesti!',
        'details' => $result['details']
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ], JSON_UNESCAPED_UNICODE);
}
?>
