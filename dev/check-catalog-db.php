<?php
// Check catalog database structure
require_once('../global.php');

echo "<h2>Catalog Database Structure</h2>";

// Check if catalog tables exist
$tables = ['catalog_pages', 'catalog_items', 'furniture'];

foreach ($tables as $table) {
    $query = $pdo->query("SHOW TABLES LIKE '$table'");
    if ($query->rowCount() > 0) {
        echo "<h3>✓ Table: $table</h3>";
        
        // Show structure
        $structure = $pdo->query("DESCRIBE $table");
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
        }
        echo "</table>";
        
        // Show sample data
        $data = $pdo->query("SELECT * FROM $table LIMIT 5");
        echo "<h4>Sample Data (first 5 rows):</h4>";
        echo "<pre>";
        print_r($data->fetchAll(PDO::FETCH_ASSOC));
        echo "</pre>";
    } else {
        echo "<h3>✗ Table: $table (NOT FOUND)</h3>";
    }
}
?>
