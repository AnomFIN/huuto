<?php
/**
 * AI Product Analysis API
 * Uses OpenAI Vision API to analyze product images and generate descriptions
 */

// Check if installation is complete
$lockFile = __DIR__ . '/config/installed.lock';
if (!file_exists($lockFile)) {
    http_response_code(503);
    echo json_encode(['error' => 'Service not available']);
    exit;
}

require_once __DIR__ . '/config/config.php';

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
            
            // Check image size (OpenAI has 20MB limit)
            if (strlen($imageData) > 20 * 1024 * 1024) {
                throw new Exception('Kuva on liian suuri (max 20MB)');
            }
            
            $base64 = base64_encode($imageData);
            $mimeType = $fileType;
            
            $images[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:$mimeType;base64,$base64",
                    'detail' => 'low'  // Use 'low' detail for cost efficiency, 'high' for more detailed analysis
                ]
            ];
        }
    }

    if (empty($images)) {
        throw new Exception('Kelvollisia kuvia ei löytynyt');
    }

    // Prepare OpenAI API request with correct structure for vision models
    $data = [
        'model' => 'gpt-4o',  // Use the latest vision model since your API key supports project-based access
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

    // Make request to OpenAI API
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
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
    $curlInfo = curl_getinfo($ch);
    curl_close($ch);

    // Debug logging (remove in production)
    error_log("OpenAI API Debug - HTTP Code: $httpCode, URL: " . $curlInfo['url']);
    error_log("OpenAI API Debug - Request data: " . json_encode($data));
    if ($response) {
        error_log("OpenAI API Debug - Response: " . substr($response, 0, 500) . "...");
    }

    if ($response === false) {
        throw new Exception('OpenAI API-kutsu epäonnistui: ' . $curlError);
    }

    if ($httpCode !== 200) {
        // Try to decode error response for better error message
        $errorResponse = json_decode($response, true);
        $errorMessage = 'OpenAI API-kutsu epäonnistui (HTTP ' . $httpCode . ')';
        
        if ($errorResponse && isset($errorResponse['error']['message'])) {
            $errorMessage .= ': ' . $errorResponse['error']['message'];
        } else if ($httpCode === 404) {
            $errorMessage = 'OpenAI API-endpoint ei löytynyt. Tarkista että API-avain on oikea ja malli on käytettävissä.';
        } else if ($httpCode === 401) {
            $errorMessage = 'OpenAI API-avain on virheellinen tai vanhentunut.';
        } else if ($httpCode === 429) {
            $errorMessage = 'OpenAI API-käyttöraja ylitetty. Yritä myöhemmin uudelleen.';
        }
        
        throw new Exception($errorMessage);
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
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}
?>