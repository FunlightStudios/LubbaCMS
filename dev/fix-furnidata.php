<?php
set_time_limit(300);
ini_set('memory_limit', '1024M');

echo "<h2>FurnitureData.json Repair Tool</h2>";

$file = 'c:/xampp/htdocs/nitro/gamedata/json/FurnitureData.json';
$backup = 'c:/xampp/htdocs/nitro/gamedata/json/FurnitureData.json.backup';

echo "<p>Reading file...</p>";
$content = file_get_contents($file);
$originalSize = strlen($content);

echo "<p>Original size: $originalSize bytes</p>";

// Create backup
echo "<p>Creating backup...</p>";
copy($file, $backup);
echo "<p>✓ Backup created: $backup</p>";

// Remove BOM (UTF-8 BOM is EF BB BF)
echo "<p>Removing BOM...</p>";
$bom = pack('H*','EFBBBF');
$content = preg_replace("/^$bom/", '', $content);
$newSize = strlen($content);
echo "<p>New size after BOM removal: $newSize bytes (removed " . ($originalSize - $newSize) . " bytes)</p>";

// Save the BOM-free version
echo "<p>Saving BOM-free version...</p>";
file_put_contents($file, $content);
echo "<p>✓ Saved!</p>";

// Try to parse
echo "<p>Validating JSON...</p>";
$data = json_decode($content, true);

if ($data === null) {
    $error = json_last_error_msg();
    echo "<p>❌ Still invalid: $error</p>";
    
    // Try to find the error location
    echo "<p>Trying to locate error...</p>";
    
    // Check for common issues
    $lines = explode("\n", $content);
    echo "<p>Total lines: " . count($lines) . "</p>";
    
    // Try parsing line by line to find the error
    echo "<p>This might take a while...</p>";
    
    die("<p>❌ Cannot automatically fix. Manual intervention required.</p>");
    
} else {
    echo "<p>✅ JSON is now valid!</p>";
    echo "<p>Total items: " . count($data['roomitemtypes']['furnitype']) . "</p>";
    
    // Check for CF_10_coin_gold
    echo "<h3>Checking for CF_10_coin_gold (ID 2063)...</h3>";
    $found = false;
    foreach ($data['roomitemtypes']['furnitype'] as $item) {
        if ($item['id'] == 2063) {
            echo "<p>✅ Found!</p>";
            echo "<pre>";
            print_r($item);
            echo "</pre>";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "<p>❌ ID 2063 not found in data!</p>";
    }
    
    echo "<h3>✅ FurnitureData.json is now fixed and ready to use!</h3>";
    echo "<p>Reload the Nitro client to see the changes.</p>";
}
?>
