<?php
/**
 * AI Regenerate Details API
 * Generates category-specific AI details for an existing auction using OpenAI
 */

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json');

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

if (!$aiSettings['ai_enabled'] || empty($aiSettings['openai_api_key'])) {
    echo json_encode(['error' => 'AI-generointi ei ole käytössä. Määritä OpenAI API-avain admin-paneelissa.']);
    exit;
}

$auctionId = isset($_POST['auction_id']) ? (int)$_POST['auction_id'] : 0;
if (!$auctionId) {
    http_response_code(400);
    echo json_encode(['error' => 'Kohteen ID puuttuu']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // Get auction data with category
    $stmt = $db->prepare(
        "SELECT a.*, c.name as category_name, c.slug as category_slug
         FROM auctions a
         JOIN categories c ON a.category_id = c.id
         WHERE a.id = ?"
    );
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch();

    if (!$auction) {
        http_response_code(404);
        echo json_encode(['error' => 'Kohdetta ei löydy']);
        exit;
    }

    // Get primary image for the auction
    $stmt = $db->prepare(
        "SELECT image_path FROM auction_images WHERE auction_id = ? AND is_primary = 1 LIMIT 1"
    );
    $stmt->execute([$auctionId]);
    $primaryImagePath = $stmt->fetchColumn();

    // Category-specific field hints for the AI prompt
    $categoryFields = [
        'elektroniikka'  => 'merkki, malli, vuosimalli, tallennustila, muisti, näyttökoko, käyttöjärjestelmä, liitännät, väri',
        'ajoneuvot'      => 'merkki, malli, vuosimalli, kilometrit, moottori, vaihteisto, väri, rekisterinumero, katsastus',
        'kiinteistot'    => 'tyyppi, pinta-ala, huoneluku, rakennusvuosi, sijainti, energialuokka, vastike, velaton hinta',
        'kodin-tavarat'  => 'materiaali, mitat (pituus × leveys × korkeus), väri, merkki, ikä, käyttötarkoitus',
        'urheilu'        => 'laji, merkki, koko, materiaali, ikä, käyttötarkoitus',
        'vaatteet'       => 'merkki, koko, materiaali, väri, ikä, pesutapa',
        'keraily'        => 'tyyppi, vuosi, kunto, alkuperä, aitoussertifikaatti, arvioitu arvo',
    ];

    $categorySlug = $auction['category_slug'] ?? '';
    $categoryName = $auction['category_name'] ?? '';
    $fieldsHint   = $categoryFields[$categorySlug]
        ?? 'tyyppi, merkki, malli, koko tai mitat, materiaali, väri, ikä tai vuosimalli';

    $systemPrompt = "Olet asiantuntija tuoteanalyysissa huutokauppoja varten. "
        . "Analysoi annettu tuotteen otsikko ja kuvaus ja generoi kategorialle \"{$categoryName}\" sopivat lisätiedot. "
        . "Palauta VAIN JSON-muodossa: {\"fields\": [{\"label\": \"Kenttä\", \"value\": \"Arvo\"}, ...]}\n"
        . "Generoi seuraavat kentät jos tiedot löytyvät: {$fieldsHint}. "
        . "Älä lisää kenttiä joihin ei ole tietoa. Vastaa suomeksi.";

    $userText = "Kategoria: {$categoryName}\n"
        . "Otsikko: {$auction['title']}\n"
        // Truncate description to avoid excessive token usage and API costs
        . "Kuvaus: " . substr($auction['description'] ?? '', 0, 1000);

    $userContent = [['type' => 'text', 'text' => $userText]];

    // Attach primary image if it exists on disk
    if ($primaryImagePath) {
        $fullPath = __DIR__ . '/' . ltrim($primaryImagePath, '/');
        if (file_exists($fullPath)) {
            $mimeType  = mime_content_type($fullPath);
            $base64    = base64_encode(file_get_contents($fullPath));
            $userContent[] = [
                'type'      => 'image_url',
                'image_url' => ['url' => "data:{$mimeType};base64,{$base64}"]
            ];
        }
    }

    $requestData = [
        'model'       => 'gpt-4o-mini',
        'messages'    => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userContent],
        ],
        'max_tokens'  => 500,
        'temperature' => 0.3,
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($requestData),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $aiSettings['openai_api_key'],
        ],
        CURLOPT_TIMEOUT => 30,
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

    // Strip markdown code fences if present
    if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $content, $matches)) {
        $content = $matches[1];
    } elseif (preg_match('/\{.*\}/s', $content, $matches)) {
        $content = $matches[0];
    }

    $detailsData = json_decode($content, true);
    if (!$detailsData || !isset($detailsData['fields'])) {
        $detailsData = ['fields' => []];
    }

    // Persist to database
    $aiDetailsJson = json_encode($detailsData, JSON_UNESCAPED_UNICODE);
    $stmt = $db->prepare("UPDATE auctions SET ai_details = ? WHERE id = ?");
    $stmt->execute([$aiDetailsJson, $auctionId]);

    echo json_encode([
        'success'    => true,
        'ai_details' => $detailsData,
        'message'    => 'AI-tiedot generoitu onnistuneesti!',
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => $e->getMessage(),
        'success' => false,
    ]);
}
