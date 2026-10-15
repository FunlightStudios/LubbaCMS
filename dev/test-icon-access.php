<?php
echo "<h2>Icon Access Test</h2>";

$iconPath = "c:/xampp/htdocs/nitro/assets/images/icons/CF_10_coin_gold_icon.png";
$iconUrl = "http://localhost/nitro/assets/images/icons/CF_10_coin_gold_icon.png";

echo "<h3>File System Check:</h3>";
if (file_exists($iconPath)) {
    echo "<p>✓ Icon file EXISTS at: $iconPath</p>";
    echo "<p>File size: " . filesize($iconPath) . " bytes</p>";
} else {
    echo "<p>❌ Icon file NOT FOUND at: $iconPath</p>";
}

echo "<h3>HTTP Access Check:</h3>";
echo "<p>Expected URL: $iconUrl</p>";
echo "<img src='$iconUrl' style='border:2px solid red;background:#fff;padding:10px;' onerror=\"this.outerHTML='❌ Failed to load image'\" />";

echo "<h3>Test Direct Access:</h3>";
echo "<a href='$iconUrl' target='_blank'>Click to test direct access</a>";

// Test configuration.json icon URL format
$configIconUrl = "http://localhost/nitro/assets/images/icons/CF_10_coin_gold_icon.png";
echo "<h3>Config Format Test:</h3>";
echo "<p>Config URL format: $configIconUrl</p>";
echo "<img src='$configIconUrl' style='border:2px solid blue;background:#fff;padding:10px;' onerror=\"this.outerHTML='❌ Failed to load from config format'\" />";
?>
