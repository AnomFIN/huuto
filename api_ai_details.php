<?php
/**
 * AI Product Details Generator API
 * Uses OpenAI Vision API to generate category-specific product details
 */

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Load AI settings
$aiSettings = ['openai_api_key' => '', 'ai_enabled' => false];
$settingsFile = __DIR__ . '/config/ai_settings.php';
if (file_exists($settingsFile)) {
    $loadedSettings = include $settingsFile;
    if (is_array($loadedSettings)) {
        $aiSettings = array_merge($aiSettings, $loadedSettings);
    }
}

// Check if AI is enabled and API key is set
if (!$aiSettings['ai_enabled'] || empty($aiSettings['openai_api_key'])) {
    echo json_encode(['error' => 'AI-generointi ei ole käytössä. Määritä OpenAI API-avain admin-paneelissa.']);
    exit;
}

try {
    // Get form data
    $categoryId = $_POST['category_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $condition = $_POST['condition_description'] ?? '';
    
    if (empty($categoryId)) {
        throw new Exception('Kategoria puuttuu');
    }

    // Get uploaded images
    $images = [];
    if (!empty($_FILES['images']['tmp_name'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                // Validate file type
                $fileType = mime_content_type($tmpName);
                if (!in_array($fileType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])) {
                    continue;
                }

                // Convert image to base64
                $imageData = file_get_contents($tmpName);
                $base64 = base64_encode($imageData);
                
                $images[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:$fileType;base64,$base64"
                    ]
                ];
            }
        }
    }

    if (empty($images)) {
        throw new Exception('Kelvollisia kuvia ei löytynyt');
    }

    // Category-specific prompts
    $categoryPrompts = [
        '1' => [ // Kiinteistöt
            'fields' => 'property_type, room_type, living_area, plot_area, build_year, energy_class, maintenance_fee, finance_fee',
            'description' => 'kiinteistön tiedot: myytävä omaisuus, huoneistotyyppi, asuinpinta-ala (m²), tontin pinta-ala (m²), rakennusvuosi, energialuokka (A-G), hoitovastike ja rahoitusvastike €/kk'
        ],
        '2' => [ // Ajoneuvot
            'fields' => 'vehicle_brand, vehicle_model, vehicle_year, mileage, engine, fuel_type, service_book, registered, traffic_insurance, inspected, next_inspection, key_count, vehicle_defects',
            'description' => 'ajoneuvon tiedot: merkki, malli, vuosimalli, kilometrit, moottori, polttoaine, huoltokirja (kyllä/ei), rekisterissä (kyllä/ei), vakuutus voimassa (kyllä/ei), katsastettu (kyllä/ei), seuraava katsastus (MM/YYYY), avainten määrä, viat ja huomautukset'
        ],
        '3' => [ // Elektroniikka
            'fields' => 'electronics_brand, electronics_model, capacity, warranty_until, original_box, charger_included',
            'description' => 'elektroniikan tiedot: merkki, malli, kapasiteetti/koko, takuu voimassa (päiväys), alkuperäispakkaus (kyllä/ei), laturi mukana (kyllä/ei)'
        ],
        '4' => [ // Kodin tavarat
            'fields' => 'home_item_type, material, dimensions, manufacturer',
            'description' => 'kodin tavaran tiedot: tuotetyyppi, materiaali, mitat, valmistaja'
        ],
        '5' => [ // Urheilu
            'fields' => 'sport_type, size, sports_brand, usage_frequency',
            'description' => 'urheiluvälineen tiedot: laji, koko/mitat, merkki, käyttötiheys (uusi/vähän/kohtuullisesti/paljon käytetty)'
        ],
        '6' => [ // Vaatteet
            'fields' => 'clothing_type, clothing_size, clothing_brand, color, clothing_material, clothing_condition',
            'description' => 'vaatteen tiedot: tyyppi, koko, merkki, väri, materiaali, kunto'
        ],
        '7' => [ // Keräily
            'fields' => 'collectible_type, age_period, creator, rarity',
            'description' => 'keräilyesineen tiedot: keräilytyyppi, ikä/ajanjakso, valmistaja/tekijä, harvinaisuus'
        ],
        '8' => [ // Muut
            'fields' => 'general_type, weight, general_dimensions, country_origin',
            'description' => 'tuotteen yleiset tiedot: tuotetyyppi, paino (kg), mitat, valmistusmaa'
        ]
    ];

    $prompt = $categoryPrompts[$categoryId] ?? $categoryPrompts['8'];
    
    // Context from existing form data
    $context = "Tuotteen perustiedot: ";
    if ($title) $context .= "Nimi: $title. ";
    if ($description) $context .= "Kuvaus: $description. ";
    if ($condition) $context .= "Kunto: $condition. ";

    // Prepare OpenAI API request
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
                $images
            )
        ]
    ];

    $requestData = [
        'model' => 'gpt-4o-mini',
        'messages' => $messages,
        'max_tokens' => 800,
        'temperature' => 0.3
    ];

    // Make request to OpenAI API
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
    
    // Clean and parse JSON
    $content = preg_replace('/^```json\s*/', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);
    
    $result = json_decode($content, true);
    
    if (!$result) {
        // Fallback response
        $result = [
            'details' => ['error' => 'JSON parsing failed'],
            'message' => 'AI-vastaus ei ollut kelvollista JSON-muotoa'
        ];
    }

    // Add success flag
    $result['success'] = true;
    if (!isset($result['message'])) {
        $result['message'] = 'Kategoria-spesifiset lisätiedot generoitu onnistuneesti!';
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ], JSON_UNESCAPED_UNICODE);
}
?>