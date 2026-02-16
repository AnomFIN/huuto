<?php
/**
 * AI Product Analysis API - TEST VERSION WITH MOCK RESPONSE
 * Uses OpenAI Vision API to analyze product images and generate descriptions
 */

// Check if installation is complete
$lockFile = __DIR__ . '/config/installed.lock';
if (!file_exists($lockFile)) {
    http_response_code(503);
    echo json_encode(['error' => 'Service not available']);
    exit;
}

require_once __DIR__ . '/app/config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// MOCK TESTING: Skip actual validation and return test data
if (isset($_POST['test_mode']) && $_POST['test_mode'] === '1') {
    sleep(2); // Simulate processing time
    echo json_encode([
        'success' => true,
        'title' => 'Test Product - AI Generated',
        'description' => 'This is a mock response from the AI system. The test image has been "analyzed" and this description was generated automatically. In a real scenario, OpenAI would analyze the actual product image and provide detailed descriptions.',
        'category' => 'Electronics',
        'startPrice' => 15.00,
        'buyNowPrice' => 35.00
    ]);
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

# Validate API key format
$apiKey = trim($aiSettings['openai_api_key']);
if (!preg_match('/^sk-[A-Za-z0-9\-_]{20,}/', $apiKey)) {
    echo json_encode(['error' => 'OpenAI API-avaimen formaatti on virheellinen. Pitää alkaa "sk-".']);
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
        throw new Exception('Ei valideja kuvia löytynyt');
    }

    // Prepare OpenAI API request  
    $data = [
        'model' => 'gpt-4o',
        'messages' => [
            [
                'role' => 'user',
                'content' => array_merge([
                    [
                        'type' => 'text',
                        'text' => 'Analysoi tämä tuotekuva ja tuota seuraavat tiedot JSON-muodossa:
- title: tuotteen nimi/otsikko
- description: yksityiskohtainen tuotekuvaus (2-3 kappaletta)  
- category: arvioitu tuotekategoria (valitse: Taide, Autot, Elektroniikka, Kodin tavarat, Harrastukset, Muoti, Urheilu, Musiikki, Kirjat, Lelut, Keräily, Muu)
- startPrice: arvioit aloitushinta euroina (numero)
- buyNowPrice: arvioitu osta heti -hinta euroina (numero)

Vastaa ainoastaan JSON-objektina.'
                    ]
                ], $images)
            ]
        ],
        'max_tokens' => 500
    ];

    // Make API call
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception('Verkkovirhe: ' . $curlError);
    }

    if ($httpCode !== 200) {
        $errorMessages = [
            400 => 'Virheellinen pyyntö (400)',
            401 => 'API-avain ei kelpaa (401)', 
            403 => 'Käyttöoikeus evätty (403)',
            404 => 'API-endpoint ei löydy (404)',
            429 => 'Liian monta pyyntöä (429)',
            500 => 'OpenAI palvelinvirhe (500)',
            503 => 'Palvelu ei käytettävissä (503)'
        ];
        
        $errorMsg = $errorMessages[$httpCode] ?? "HTTP $httpCode";
        throw new Exception("OpenAI API-kutsu epäonnistui ($errorMsg)");
    }

    $result = json_decode($response, true);
    if (!$result || !isset($result['choices'][0]['message']['content'])) {
        throw new Exception('Virheellinen vastaus OpenAI:lta');
    }

    // Parse the AI response
    $content = trim($result['choices'][0]['message']['content']);
    $productData = json_decode($content, true);

    if (!$productData) {
        throw new Exception('AI-vastauksen jakaminen epäonnistui');
    }

    // Return formatted response
    echo json_encode([
        'success' => true,
        'title' => $productData['title'] ?? 'Tunnistamaton tuote',
        'description' => $productData['description'] ?? 'Tuotekuvausta ei voitu generoida.',
        'category' => $productData['category'] ?? 'Muu',
        'startPrice' => floatval($productData['startPrice'] ?? 1.0),
        'buyNowPrice' => floatval($productData['buyNowPrice'] ?? 5.0)
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>