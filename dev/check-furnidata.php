<?php
echo "<h2>FurnitureData.json Check</h2>";

$furnidata = file_get_contents('c:/xampp/htdocs/nitro/gamedata/json/FurnitureData.json');

echo "<p>File size: " . strlen($furnidata) . " bytes</p>";

// Try to find CF_10_coin_gold directly in the raw JSON
if (strpos($furnidata, 'CF_10_coin_gold') !== false) {
    echo "<p>✓ CF_10_coin_gold FOUND in raw JSON</p>";
    
    // Extract the section around it
    $pos = strpos($furnidata, 'CF_10_coin_gold');
    $start = max(0, $pos - 500);
    $length = 1000;
    $snippet = substr($furnidata, $start, $length);
    
    echo "<h3>Snippet around CF_10_coin_gold:</h3>";
    echo "<pre>" . htmlspecialchars($snippet) . "</pre>";
} else {
    echo "<p>❌ CF_10_coin_gold NOT FOUND in raw JSON</p>";
}

// Try parsing with error handling
$data = json_decode($furnidata, true);
$error = json_last_error();

if ($error !== JSON_ERROR_NONE) {
    echo "<p>JSON Parse Error: " . json_last_error_msg() . "</p>";
    die();
}

echo "<h3>Searching for CF_10_coin_gold...</h3>";

$found = false;
foreach ($data['roomitemtypes']['furnitype'] as $item) {
    if (strpos($item['classname'], 'coin') !== false && strpos($item['classname'], 'CF_10') !== false) {
        echo "<pre>";
        print_r($item);
        echo "</pre>";
        $found = true;
    }
}

if (!$found) {
    echo "<p>❌ CF_10_coin_gold NOT FOUND in FurnitureData.json</p>";
    echo "<h3>Searching for any 'coin' items (first 5):</h3>";
    $count = 0;
    foreach ($data['roomitemtypes']['furnitype'] as $item) {
        if (strpos($item['classname'], 'coin') !== false) {
            echo "<pre>";
            print_r($item);
            echo "</pre>";
            $count++;
            if ($count >= 5) break;
        }
    }
}

echo "<h3>Total items in FurnitureData.json: " . count($data['roomitemtypes']['furnitype']) . "</h3>";
?>
