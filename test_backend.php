<?php
// Test backend functionality
require_once __DIR__ . '/bootstrap.php';

echo "=== Backend Functionality Test ===\n";

// Test 1: Database Connection
echo "1. Testing database connection...\n";
try {
    $db = db();
    $stmt = $db->query("SELECT 1");
    echo "   ✓ Database connection successful\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 2: Check if required tables exist
echo "\n2. Checking required tables...\n";
try {
    $tables = ['users', 'auctions', 'categories', 'auction_images'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->fetch()) {
            echo "   ✓ Table '$table' exists\n";
        } else {
            echo "   ✗ Table '$table' missing\n";
        }
    }
} catch (Exception $e) {
    echo "   ✗ Error checking tables: " . $e->getMessage() . "\n";
}

// Test 3: Test upload directory
echo "\n3. Checking upload directory...\n";
if (is_dir(UPLOAD_DIR)) {
    echo "   ✓ Upload directory exists: " . UPLOAD_DIR . "\n";
    if (is_writable(UPLOAD_DIR)) {
        echo "   ✓ Upload directory is writable\n";
    } else {
        echo "   ✗ Upload directory is not writable\n";
    }
} else {
    echo "   ✗ Upload directory does not exist: " . UPLOAD_DIR . "\n";
}

// Test 4: Check OpenAI configuration
echo "\n4. Checking OpenAI configuration...\n";
if (defined('OPENAI_API_KEY') && OPENAI_API_KEY !== '') {
    echo "   ✓ OpenAI API key is configured\n";
} else {
    echo "   ⚠ OpenAI API key is not configured (optional)\n";
}

// Test 5: Test auth functions
echo "\n5. Testing auth functions...\n";
try {
    $auth = auth();
    echo "   ✓ Auth object created successfully\n";
} catch (Exception $e) {
    echo "   ✗ Error creating auth object: " . $e->getMessage() . "\n";
}

// Test 6: Check if config constants are defined
echo "\n6. Checking configuration constants...\n";
$requiredConstants = ['SITE_NAME', 'BASE_URL', 'UPLOAD_DIR', 'MAX_UPLOAD_SIZE'];
foreach ($requiredConstants as $constant) {
    if (defined($constant)) {
        echo "   ✓ $constant is defined: " . constant($constant) . "\n";
    } else {
        echo "   ✗ $constant is not defined\n";
    }
}

echo "\n=== Test Complete ===\n";
?>