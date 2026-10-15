<?php
/**
 * Teste ob GIF-Dateien valide sind
 */

header('Content-Type: text/html; charset=utf-8');

echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
.success { color: #28a745; }
.error { color: #dc3545; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background: #667eea; color: white; }
</style>";

echo "<h1>🔍 GIF-Validierung Test</h1>";

$baseDir = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/base/';
$templateDir = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/templates/';

// Test GD Extension
echo "<div class='box'>";
echo "<h2>1. GD Extension</h2>";
if (extension_loaded('gd')) {
    echo "<p class='success'>✅ GD ist geladen</p>";
    $info = gd_info();
    echo "<p>GIF Support: " . ($info['GIF Read Support'] ? '✅' : '❌') . "</p>";
    echo "<p>GIF Create Support: " . ($info['GIF Create Support'] ? '✅' : '❌') . "</p>";
} else {
    echo "<p class='error'>❌ GD ist NICHT geladen!</p>";
}
echo "</div>";

// Test base.gif
echo "<div class='box'>";
echo "<h2>2. base.gif Validierung</h2>";

$basePath = $baseDir . 'base.gif';
echo "<p><strong>Pfad:</strong> {$basePath}</p>";
echo "<p><strong>Existiert:</strong> " . (file_exists($basePath) ? '✅' : '❌') . "</p>";

if (file_exists($basePath)) {
    $size = filesize($basePath);
    echo "<p><strong>Größe:</strong> {$size} bytes</p>";
    
    // Versuche zu laden
    $img = @imagecreatefromgif($basePath);
    if ($img) {
        echo "<p class='success'>✅ imagecreatefromgif() erfolgreich!</p>";
        echo "<p>Breite: " . imagesx($img) . "px, Höhe: " . imagesy($img) . "px</p>";
        
        // Gib als Base64 aus
        ob_start();
        imagegif($img);
        $data = ob_get_clean();
        $base64 = base64_encode($data);
        
        echo "<p><strong>Preview (Base64):</strong></p>";
        echo "<img src='data:image/gif;base64,{$base64}' style='border:2px solid green; background:#fff; padding:10px;'/>";
        
        imagedestroy($img);
    } else {
        echo "<p class='error'>❌ imagecreatefromgif() fehlgeschlagen!</p>";
        
        // Prüfe Datei-Header
        $handle = fopen($basePath, 'rb');
        $header = fread($handle, 6);
        fclose($handle);
        
        echo "<p><strong>Datei-Header:</strong> " . bin2hex($header) . "</p>";
        echo "<p><strong>Erwartet:</strong> 474946383961 oder 474946383761 (GIF89a oder GIF87a)</p>";
        
        if (substr($header, 0, 3) !== 'GIF') {
            echo "<p class='error'>❌ Keine valide GIF-Datei!</p>";
        }
    }
}
echo "</div>";

// Test mehrere Badge-Dateien
echo "<div class='box'>";
echo "<h2>3. Badge-Dateien Validierung</h2>";

$testFiles = [
    'base/01.gif',
    'base/010.gif',
    'base/014.gif',
    'templates/20.gif',
    'templates/60.gif'
];

echo "<table>";
echo "<tr><th>Datei</th><th>Größe</th><th>Header</th><th>Ladbar</th><th>Preview</th></tr>";

foreach ($testFiles as $file) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/' . $file;
    
    if (file_exists($path)) {
        $size = filesize($path);
        
        // Header prüfen
        $handle = fopen($path, 'rb');
        $header = fread($handle, 6);
        fclose($handle);
        $headerHex = bin2hex($header);
        $isGif = (substr($header, 0, 3) === 'GIF');
        
        // Versuche zu laden
        $img = @imagecreatefromgif($path);
        $loadable = ($img !== false);
        
        $preview = '';
        if ($img) {
            ob_start();
            imagegif($img);
            $data = ob_get_clean();
            $base64 = base64_encode($data);
            $preview = "<img src='data:image/gif;base64,{$base64}' style='width:32px;height:32px;background:#fff;'/>";
            imagedestroy($img);
        }
        
        echo "<tr>";
        echo "<td>{$file}</td>";
        echo "<td>{$size}b</td>";
        echo "<td>" . ($isGif ? "✅ GIF" : "❌ {$headerHex}") . "</td>";
        echo "<td>" . ($loadable ? "✅" : "❌") . "</td>";
        echo "<td>{$preview}</td>";
        echo "</tr>";
    }
}

echo "</table>";
echo "</div>";

// Test Badge-Generator Output
echo "<div class='box'>";
echo "<h2>4. Badge-Generator Output Test</h2>";

echo "<p>Teste ob badge.php korrekte GIF-Daten ausgibt...</p>";

$testUrl = 'http://localhost/swf/habbo-imaging/badge.php?badge=b0101';

$ch = curl_init($testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

echo "<p><strong>Response Headers:</strong></p>";
echo "<pre>" . htmlspecialchars($headers) . "</pre>";

echo "<p><strong>Body Größe:</strong> " . strlen($body) . " bytes</p>";

if (strlen($body) > 0) {
    $bodyHeader = substr($body, 0, 6);
    $isGif = (substr($bodyHeader, 0, 3) === 'GIF');
    
    echo "<p><strong>Body Header:</strong> " . bin2hex($bodyHeader) . "</p>";
    echo "<p><strong>Ist GIF:</strong> " . ($isGif ? '✅ JA' : '❌ NEIN') . "</p>";
    
    if ($isGif) {
        $base64 = base64_encode($body);
        echo "<p><strong>Preview:</strong></p>";
        echo "<img src='data:image/gif;base64,{$base64}' style='border:2px solid green; background:#fff; padding:10px;'/>";
    }
} else {
    echo "<p class='error'>❌ Keine Daten empfangen!</p>";
}

echo "</div>";

echo "<hr>";
echo "<p><a href='/dev/test-badge-files.php'>← Zurück</a></p>";
?>
