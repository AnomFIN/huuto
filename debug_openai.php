<?php
/**
 * Simple OpenAI API test without images
 */
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json');

// Load AI settings
$aiSettings = ['openai_api_key' => '', 'ai_enabled' => false];
$settingsFile = __DIR__ . '/config/ai_settings.php';
if (file_exists($settingsFile)) {
    $loadedSettings = include $settingsFile;
    if (is_array($loadedSettings)) {
        $aiSettings = array_merge($aiSettings, $loadedSettings);
    }
}

if (empty($aiSettings['openai_api_key'])) {
    echo json_encode(['error' => 'API key not set']);
    exit;
}

$apiKey = trim($aiSettings['openai_api_key']);

// Test 1: Simple text completion
$data1 = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        [
            'role' => 'user',
            'content' => 'Say "Hello AI test"'
        ]
    ],
    'max_tokens' => 10
];

echo "<h2>Test 1: gpt-3.5-turbo (should work)</h2>";

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data1),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ],
    CURLOPT_TIMEOUT => 30
]);

$response1 = curl_exec($ch);
$httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode1</p>";
echo "<pre>" . htmlspecialchars($response1) . "</pre>";

// Test 2: Vision model
$data2 = [
    'model' => 'gpt-4-turbo',
    'messages' => [
        [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'What models are available for vision?'
                ]
            ]
        ]
    ],
    'max_tokens' => 50
];

echo "<h2>Test 2: gpt-4-turbo (vision capable)</h2>";

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data2),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ],
    CURLOPT_TIMEOUT => 30
]);

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode2</p>";
echo "<pre>" . htmlspecialchars($response2) . "</pre>";

// Test 3: Check API key format
echo "<h2>Test 3: API Key Info</h2>";
echo "<p>API Key starts with: " . substr($apiKey, 0, 8) . "...</p>";
echo "<p>API Key length: " . strlen($apiKey) . "</p>";
echo "<p>API Key format valid: " . (preg_match('/^sk-[A-Za-z0-9\-_]{20,}/', $apiKey) ? 'Yes' : 'No') . "</p>";
?>