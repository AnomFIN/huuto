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
            'content' => 'Olet asiantuntija huutokauppa-arviointiin. Analysoi kuvia ja anna tuotteelle nimi, kuvaus ja hinnoitteluehdotus euroina. Vastaa JSON-muodossa suomeksi: {"title": "tuotteen nimi", "description": "yksityiskohtainen kuvaus", "suggested_price": 150, "category_suggestion": "ehdotettu kategoria", "condition": "kunnon arviointi"}'
        ],
        [
            'role' => 'user',
            'content' => array_merge(
                [['type' => 'text', 'text' => 'Analysoi nämä kuvat ja ehdota huutokauppatuotteelle sopivat tiedot:']],
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
    
    // Try to parse JSON from the response
    $productData = json_decode($content, true);
    
    if (!$productData) {
        // If JSON parsing fails, create a fallback response
        $productData = [
            'title' => 'AI-analysoitu tuote',
            'description' => trim($content),
            'suggested_price' => 50,
            'category_suggestion' => 'muut',
            'condition' => 'hyvä'
        ];
    }

    // Ensure all required fields exist
    $productData = array_merge([
        'title' => '',
        'description' => '',
        'suggested_price' => 0,
        'category_suggestion' => '',
        'condition' => ''
    ], $productData);

    // Add success flag
    $productData['success'] = true;
    $productData['message'] = 'AI-analyysi valmis! Tarkista ja muokkaa tietoja tarvittaessa.';

    echo json_encode($productData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}
?>