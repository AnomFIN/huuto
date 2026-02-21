<?php
/**
 * AI Product Analysis API
 * Uses OpenAI Vision API to analyze product images and generate descriptions
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
    // Get uploaded images
    if (empty($_FILES['images']['tmp_name'])) {
        throw new Exception('Ei kuvia löytynyt');
    }

    $images = [];
    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
            // Validate file type
            $fileType = mime_content_type($tmpName);
            if (!in_array($fileType, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])) {
                throw new Exception('Virheellinen tiedostotyyppi');
            }

            // Convert image to base64
            $imageData = file_get_contents($tmpName);
            $base64 = base64_encode($imageData);
            $mimeType = $fileType;
            
            $images[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:$mimeType;base64,$base64"
                ]
            ];
        }
    }

    if (empty($images)) {
        throw new Exception('Kelvollisia kuvia ei löytynyt');
    }

    // Prepare OpenAI API request
    $messages = [
        [
            'role' => 'system',
            'content' => 'Olet asiantuntija huutokauppa-arviointiin. Analysoi KAIKKI kuvat yhdessä ja anna tuotteelle kattavat tiedot SUOMEKSI. VASTAA VAIN PUHTAALLA JSON-MUODOSSA, EI MUUTA TEKSTIÄ! Kategoriavaihtoehtosi: 1=Kiinteiötöt, 2=Ajoneuvot, 3=Elektroniikka, 4=Kodin tavarat, 5=Urheilu, 6=Vaatteet, 7=Keräily, 8=Muut. JSON:
{
  "title": "tuotteen tarkka nimi suomeksi",
  "description": "yksityiskohtainen kuvaus suomeksi",
  "suggested_price": 150,
  "reserve_price": 200,
  "buy_now_price": 300,
  "category_suggestion": "numero 1-8 parhaiten sopiva kategoria",
  "condition": "kunnon arviointi suomeksi",
  "location": "arvioitu sijainti Suomessa",
  "duration_days": 7
}'
        ],
        [
            'role' => 'user',
            'content' => array_merge(
                [['type' => 'text', 'text' => 'Analysoi KAIKKI nämä kuvat yhtenä tuotteena. Arvio myös paras kategoria numeroina 1-8. Vastaa VAIN JSON-muodossa suomeksi, ei muuta tekstiä:']],
                $images
            )
        ]
    ];

    $requestData = [
        'model' => 'gpt-4o-mini',
        'messages' => $messages,
        'max_tokens' => 500,
        'temperature' => 0.7
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

    $content = $apiResponse['choices'][0]['message']['content'];
    
    // Clean and parse JSON from the response
    $content = trim($content);
    
    // Remove potential markdown code block markers
    $content = preg_replace('/^```json\s*/', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);
    
    // Try to extract JSON from text
    if (preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/', $content, $matches)) {
        $content = $matches[0];
    }
    
    $productData = json_decode($content, true);
    
    if (!$productData) {
        // More comprehensive fallback
        $productData = [
            'title' => 'AI-analysoitu tuote',
            'description' => 'AI-analyysi: ' . substr(trim($content), 0, 500),
            'suggested_price' => 50,
            'reserve_price' => 75,
            'buy_now_price' => 100,
            'category_suggestion' => 'muut',
            'condition' => 'hyvä',
            'location' => 'Suomi',
            'duration_days' => 7
        ];
    }

    // Ensure all required fields exist
    $productData = array_merge([
        'title' => '',
        'description' => '',
        'suggested_price' => 0,
        'reserve_price' => 0,
        'buy_now_price' => 0,
        'category_suggestion' => '',
        'condition' => '',
        'location' => 'Suomi',
        'duration_days' => 7
    ], $productData);

    // Add debug info if parsing failed
    if (isset($matches)) {
        $productData['_debug'] = 'JSON extracted from: ' . substr($apiResponse['choices'][0]['message']['content'], 0, 200);
    }
    
    // Add success flag
    $productData['success'] = true;
    $productData['message'] = 'AI-analyysi valmis! Tarkista ja muokkaa tietoja tarvittaessa.';

    echo json_encode($productData, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ], JSON_UNESCAPED_UNICODE);
}
?>