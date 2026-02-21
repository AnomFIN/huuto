<?php
// Test AI API response
header('Content-Type: text/html; charset=utf-8');

echo "<h2>AI API Test</h2>";

// Load AI settings
$aiSettings = ['openai_api_key' => '', 'ai_enabled' => false];
$settingsFile = __DIR__ . '/config/ai_settings.php';
if (file_exists($settingsFile)) {
    $loadedSettings = include $settingsFile;
    if (is_array($loadedSettings)) {
        $aiSettings = array_merge($aiSettings, $loadedSettings);
    }
}

if (!$aiSettings['ai_enabled']) {
    echo "<p style='color: red;'>❌ AI not enabled in config</p>";
    echo "<p>Please enable AI in admin panel</p>";
} else if (empty($aiSettings['openai_api_key'])) {
    echo "<p style='color: red;'>❌ OpenAI API key missing</p>";
    echo "<p>Please set OpenAI API key in admin panel</p>";
} else {
    echo "<p style='color: green;'>✅ AI configuration looks good</p>";
    echo "<p style='color: blue;'>API Key: " . substr($aiSettings['openai_api_key'], 0, 10) . "...</p>";
}

// Test JSON parsing
$testJson = '{"title": "Test Product", "description": "Test description", "suggested_price": 100}';
$parsed = json_decode($testJson, true);
if ($parsed) {
    echo "<p style='color: green;'>✅ JSON parsing works</p>";
} else {
    echo "<p style='color: red;'>❌ JSON parsing failed</p>";
}

echo "<br><a href='add_product.php'>Go to Add Product</a>";
?>