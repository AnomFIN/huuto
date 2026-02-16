<?php
/**
 * Test the improved schema parsing logic
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Schema Parsing Test</h2>\n";

// Read schema file
$schemaFile = __DIR__ . '/database/schema.sql';
if (!file_exists($schemaFile)) {
    die("Schema file not found: $schemaFile");
}

$schema = file_get_contents($schemaFile);

// Process like the improved asennus.php does
$schema = preg_replace('/^CREATE DATABASE.*?;/mi', '', $schema);
$schema = preg_replace('/^USE .*?;/mi', '', $schema);

// Better SQL statement parsing - handle multi-line statements properly
$schema = str_replace(["\r\n", "\r"], "\n", $schema);

// Split statements more intelligently
$statements = [];
$current = '';
$inQuotes = false;
$quoteChar = '';
$parenLevel = 0;

for ($i = 0; $i < strlen($schema); $i++) {
    $char = $schema[$i];
    $current .= $char;
    
    if (!$inQuotes) {
        if ($char === '"' || $char === "'") {
            $inQuotes = true;
            $quoteChar = $char;
        } elseif ($char === '(') {
            $parenLevel++;
        } elseif ($char === ')') {
            $parenLevel--;
        } elseif ($char === ';' && $parenLevel === 0) {
            // End of statement
            $statement = trim($current);
            if (!empty($statement)) {
                $statements[] = $statement;
            }
            $current = '';
        }
    } else {
        if ($char === $quoteChar && ($i === 0 || $schema[$i-1] !== '\\')) {
            $inQuotes = false;
            $quoteChar = '';
        }
    }
}

// Add any remaining statement
$current = trim($current);
if (!empty($current)) {
    $statements[] = $current;
}

echo "<h3>Parsed Statements (" . count($statements) . " total):</h3>\n";
$tableCount = 0;

foreach ($statements as $i => $statement) {
    $statement = trim($statement);
    if (empty($statement) || preg_match('/^\s*--/', $statement)) {
        continue;
    }
    
    $isTableCreation = preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $statement, $matches);
    if ($isTableCreation) {
        $tableCount++;
        echo "<div style='border: 2px solid green; margin: 10px 0; padding: 10px;'>\n";
        echo "<strong>CREATE TABLE: {$matches[1]}</strong>\n";
    } else {
        echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>\n";
        echo "<strong>Statement " . ($i + 1) . "</strong>";
    }
    
    echo "<pre>" . htmlspecialchars(substr($statement, 0, 300));
    if (strlen($statement) > 300) echo "...";
    echo "</pre>\n";
    echo "</div>\n";
}

echo "<p><strong>Found $tableCount CREATE TABLE statements</strong></p>\n";

// Test for required tables
$requiredTables = ['users', 'categories', 'auctions'];
$foundTables = [];

foreach ($statements as $statement) {
    if (preg_match('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?/i', $statement, $matches)) {
        $tableName = $matches[1];
        if (in_array($tableName, $requiredTables)) {
            $foundTables[] = $tableName;
        }
    }
}

echo "<h3>Required Tables Check:</h3>\n";
foreach ($requiredTables as $table) {
    $found = in_array($table, $foundTables);
    $color = $found ? 'green' : 'red';
    echo "<p style='color: $color;'>$table: " . ($found ? 'FOUND' : 'MISSING') . "</p>\n";
}

if (count($foundTables) === count($requiredTables)) {
    echo "<p style='color: green; font-weight: bold;'>✓ All required tables found in schema!</p>\n";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Missing required tables</p>\n";
}
?>