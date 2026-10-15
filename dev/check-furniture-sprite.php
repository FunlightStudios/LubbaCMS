<?php
echo "<h2>Furniture Sprite ID Check</h2>";

// Database connection from brain-config
$host = 'localhost';
$dbname = 'lubba';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check CF_10_coin_gold
    $stmt = $pdo->prepare("SELECT * FROM furniture WHERE item_name LIKE '%coin%' LIMIT 10");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Item Name</th><th>Sprite ID</th><th>Public Name</th></tr>";
foreach ($items as $item) {
    echo "<tr>";
    echo "<td>{$item['id']}</td>";
    echo "<td>{$item['item_name']}</td>";
    echo "<td>{$item['sprite_id']}</td>";
    echo "<td>" . ($item['public_name'] ?? 'N/A') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Nitro Assets Check</h3>";
$assetPath = "c:/xampp/htdocs/nitro/furniture/";
if (file_exists($assetPath . "CF_10_coin_gold.nitro")) {
    echo "✓ CF_10_coin_gold.nitro EXISTS<br>";
} else {
    echo "✗ CF_10_coin_gold.nitro NOT FOUND<br>";
}

// List all coin assets
echo "<h3>Available Coin Assets:</h3>";
$files = glob($assetPath . "*coin*.nitro");
foreach ($files as $file) {
    echo basename($file) . "<br>";
}
?>
