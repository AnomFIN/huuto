<?php
// Quick test of schema parsing
$schema = file_get_contents(__DIR__ . '/database/schema.sql');

// Count semicolons in original
$semicolons = substr_count($schema, ';');
echo "Original semicolons: $semicolons\n";

// Remove CREATE DATABASE and USE statements
$schema = preg_replace('/^CREATE DATABASE.*?;/mi', '', $schema);
$schema = preg_replace('/^USE .*?;/mi', '', $schema);

$semicolons_after = substr_count($schema, ';');
echo "Semicolons after cleanup: $semicolons_after\n";

// Count CREATE TABLE statements
preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $schema, $matches);
echo "CREATE TABLE statements found: " . count($matches[1]) . "\n";
echo "Tables: " . implode(', ', $matches[1]) . "\n";

// Check for required tables
$required = ['users', 'categories', 'auctions'];
$missing = array_diff($required, $matches[1]);
if (empty($missing)) {
    echo "✓ All required tables present in schema\n";
} else {
    echo "✗ Missing tables: " . implode(', ', $missing) . "\n";
}
?>