<?php
/**
 * Teste direkten Bild-Zugriff
 */

echo "<h1>🖼️ Direkter Bild-Zugriff Test</h1>";

echo "<h2>Test 1: Direkter IMG-Tag</h2>";
echo "<img src='/swf/habbo-imaging/badges/base/base.gif' style='border:2px solid red;'/>";

echo "<h2>Test 2: Mit vollem Pfad</h2>";
echo "<img src='http://localhost/swf/habbo-imaging/badges/base/base.gif' style='border:2px solid blue;'/>";

echo "<h2>Test 3: Base 01.gif</h2>";
echo "<img src='/swf/habbo-imaging/badges/base/01.gif' style='border:2px solid green;'/>";

echo "<h2>Test 4: Template 20.gif</h2>";
echo "<img src='/swf/habbo-imaging/badges/templates/20.gif' style='border:2px solid orange;'/>";

echo "<h2>Test 5: Badge-Generator</h2>";
echo "<img src='/swf/habbo-imaging/badge.php?badge=b0101' style='border:2px solid purple;width:64px;height:64px;'/>";

echo "<hr>";
echo "<h2>📝 File Info</h2>";

$basePath = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/base/base.gif';
echo "<p><strong>Server Path:</strong> {$basePath}</p>";
echo "<p><strong>Existiert:</strong> " . (file_exists($basePath) ? "✅ JA" : "❌ NEIN") . "</p>";
echo "<p><strong>Lesbar:</strong> " . (is_readable($basePath) ? "✅ JA" : "❌ NEIN") . "</p>";
echo "<p><strong>Größe:</strong> " . filesize($basePath) . " bytes</p>";

echo "<hr>";
echo "<h2>🔍 Headers Check</h2>";

$url = 'http://localhost/swf/habbo-imaging/badges/base/base.gif';
$headers = @get_headers($url);
echo "<pre>";
print_r($headers);
echo "</pre>";

echo "<hr>";
echo "<h2>🧪 Badge.php Headers</h2>";

$badgeUrl = 'http://localhost/swf/habbo-imaging/badge.php?badge=b0101';
$badgeHeaders = @get_headers($badgeUrl);
echo "<pre>";
print_r($badgeHeaders);
echo "</pre>";

// Teste ob GD funktioniert
echo "<hr>";
echo "<h2>🎨 GD Library Test</h2>";

if (extension_loaded('gd')) {
    echo "<p class='success'>✅ GD Library ist geladen</p>";
    $gdInfo = gd_info();
    echo "<pre>";
    print_r($gdInfo);
    echo "</pre>";
} else {
    echo "<p class='error'>❌ GD Library ist NICHT geladen!</p>";
}

// Teste imagecreatefromgif direkt
echo "<hr>";
echo "<h2>🧪 imagecreatefromgif() Test</h2>";

$testPath = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/base/base.gif';
$img = @imagecreatefromgif($testPath);

if ($img) {
    echo "<p class='success'>✅ imagecreatefromgif() funktioniert!</p>";
    echo "<p>Breite: " . imagesx($img) . "px</p>";
    echo "<p>Höhe: " . imagesy($img) . "px</p>";
    
    // Gib Bild direkt aus
    echo "<h3>Direkte Ausgabe:</h3>";
    ob_start();
    imagegif($img);
    $imageData = ob_get_clean();
    $base64 = base64_encode($imageData);
    echo "<img src='data:image/gif;base64,{$base64}' style='border:2px solid green;'/>";
    
    imagedestroy($img);
} else {
    echo "<p class='error'>❌ imagecreatefromgif() schlägt fehl!</p>";
    echo "<p>Letzter Fehler: " . error_get_last()['message'] . "</p>";
}
?>

<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
h1, h2 { color: #333; }
.success { color: #28a745; }
.error { color: #dc3545; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
img { background: #fff; padding: 10px; margin: 10px 0; }
</style>
