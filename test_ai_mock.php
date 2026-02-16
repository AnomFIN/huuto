<?php
/**
 * Test OpenAI API mock for debugging
 */
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json');

// Simple mock response for testing
$mockResponse = [
    'object' => 'chat.completion',
    'choices' => [
        [
            'message' => [
                'content' => '{"title":"Test Product","description":"This is a test product analyzed by the AI system. The image shows a white background with the text \'TEST\'.","category":"Electronics","startPrice":10.00,"buyNowPrice":25.00}'
            ]
        ]
    ]
];

// Simulate some processing time
sleep(1);

echo json_encode([
    'success' => true,
    'title' => 'Test Product',
    'description' => 'This is a test product analyzed by the AI system. The image shows a white background with the text "TEST".',
    'category' => 'Electronics', 
    'startPrice' => 10.00,
    'buyNowPrice' => 25.00
]);
?>