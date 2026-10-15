<?php
// Check if FurnitureData.json contains the correct IDs

$json = file_get_contents('c:/xampp/htdocs/nitro/gamedata/json/FurnitureData.json');
$data = json_decode($json, true);

if (!$data) {
    die("ERROR: Could not parse JSON!\n");
}

echo "=== FIRST 5 FLOOR ITEMS ===\n";
$count = 0;
foreach ($data['roomitemtypes']['furnitype'] as $item) {
    if ($count >= 5) break;
    echo "ID: {$item['id']}, Classname: {$item['classname']}\n";
    $count++;
}

echo "\n=== SEARCHING FOR CF_10_coin_gold ===\n";
foreach ($data['roomitemtypes']['furnitype'] as $item) {
    if ($item['classname'] === 'CF_10_coin_gold') {
        echo "FOUND!\n";
        echo "ID: {$item['id']}\n";
        echo "Classname: {$item['classname']}\n";
        echo "Name: {$item['name']}\n";
        break;
    }
}

echo "\n=== SEARCHING FOR ID 2063 ===\n";
foreach ($data['roomitemtypes']['furnitype'] as $item) {
    if ($item['id'] == 2063) {
        echo "FOUND!\n";
        echo "ID: {$item['id']}\n";
        echo "Classname: {$item['classname']}\n";
        echo "Name: {$item['name']}\n";
        break;
    }
}
