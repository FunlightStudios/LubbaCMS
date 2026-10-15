<?php
// Check what IDs are in the database

$conn = new mysqli('localhost', 'root', '', 'lubba');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== CHECKING FURNITURE TABLE ===\n";
$result = $conn->query("SELECT id, sprite_id, item_name FROM furniture WHERE item_name = 'CF_10_coin_gold' LIMIT 1");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "FOUND CF_10_coin_gold:\n";
    echo "  Database ID (furniture.id): {$row['id']}\n";
    echo "  Sprite ID (furniture.sprite_id): {$row['sprite_id']}\n";
    echo "  Item Name: {$row['item_name']}\n";
} else {
    echo "NOT FOUND in furniture table!\n";
}

echo "\n=== FIRST 5 FURNITURE ITEMS ===\n";
$result = $conn->query("SELECT id, sprite_id, item_name FROM furniture ORDER BY id ASC LIMIT 5");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, SpriteID: {$row['sprite_id']}, Name: {$row['item_name']}\n";
    }
}

echo "\n=== CHECKING CATALOG_ITEMS TABLE ===\n";
$result = $conn->query("SELECT ci.id, ci.item_id, f.id as furniture_id, f.sprite_id, f.item_name 
                        FROM catalog_items ci 
                        JOIN furniture f ON ci.item_id = f.id 
                        WHERE f.item_name = 'CF_10_coin_gold' 
                        LIMIT 1");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "FOUND in catalog:\n";
    echo "  Catalog Item ID: {$row['id']}\n";
    echo "  Item ID (references furniture.id): {$row['item_id']}\n";
    echo "  Furniture ID: {$row['furniture_id']}\n";
    echo "  Sprite ID: {$row['sprite_id']}\n";
    echo "  Item Name: {$row['item_name']}\n";
} else {
    echo "NOT FOUND in catalog!\n";
}

$conn->close();
