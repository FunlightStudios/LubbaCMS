<?php
echo "<h2>FurnitureData.json Validation</h2>";

$file = 'c:/xampp/htdocs/nitro/gamedata/json/FurnitureData.json';
$content = file_get_contents($file);

echo "<p>File size: " . strlen($content) . " bytes</p>";

// Try to find syntax errors
echo "<h3>Checking for common JSON errors...</h3>";

// Check for trailing commas
if (preg_match('/,\s*}/', $content)) {
    echo "<p>⚠️ Found trailing commas before closing braces</p>";
}

if (preg_match('/,\s*\]/', $content)) {
    echo "<p>⚠️ Found trailing commas before closing brackets</p>";
}

// Try to parse with detailed error
ini_set('memory_limit', '512M');
$data = json_decode($content, true);

if ($data === null) {
    $error = json_last_error_msg();
    echo "<p>❌ JSON Parse Error: $error</p>";
    
    // Try to find the error position
    if (function_exists('json_last_error_msg')) {
        echo "<p>Error code: " . json_last_error() . "</p>";
    }
    
    // Try to validate with a smaller chunk
    echo "<h3>Testing first 1000 characters:</h3>";
    $chunk = substr($content, 0, 1000);
    echo "<pre>" . htmlspecialchars($chunk) . "</pre>";
    
} else {
    echo "<p>✅ JSON is valid!</p>";
    echo "<p>Total furniture items: " . count($data['roomitemtypes']['furnitype']) . "</p>";
    
    // Search for CF_10_coin_gold
    echo "<h3>Searching for CF_10_coin_gold (ID 2063)...</h3>";
    $found = false;
    foreach ($data['roomitemtypes']['furnitype'] as $item) {
        if ($item['id'] == 2063) {
            echo "<p>✅ Found item with ID 2063:</p>";
            echo "<pre>";
            print_r($item);
            echo "</pre>";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "<p>❌ Item with ID 2063 not found!</p>";
    }
}
?>
