<?php
/**
 * Prüft Badge-Dateien und zeigt sie direkt an
 */

echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background: #667eea; color: white; }
.success { color: #28a745; }
.error { color: #dc3545; }
img { background: #f0f0f0; border: 1px solid #ddd; }
</style>";

echo "<h1>🔍 Badge-Dateien Test</h1>";

$baseDir = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/base/';
$templateDir = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/templates/';

// 1. Prüfe base.gif
echo "<div class='box'>";
echo "<h2>📁 1. Haupt-Base (base.gif)</h2>";

$basePath = $baseDir . 'base.gif';
if (file_exists($basePath)) {
    $size = filesize($basePath);
    echo "<p class='success'>✅ base.gif existiert ({$size} bytes)</p>";
    echo "<img src='/swf/habbo-imaging/badges/base/base.gif' style='width:64px;height:64px;'/>";
    
    // Versuche zu laden
    $img = @imagecreatefromgif($basePath);
    if ($img) {
        echo "<p class='success'>✅ Kann mit imagecreatefromgif() geladen werden</p>";
        imagedestroy($img);
    } else {
        echo "<p class='error'>❌ FEHLER: imagecreatefromgif() schlägt fehl!</p>";
    }
} else {
    echo "<p class='error'>❌ base.gif FEHLT!</p>";
}
echo "</div>";

// 2. Prüfe Base-Shapes
echo "<div class='box'>";
echo "<h2>🎨 2. Base-Shapes (01.gif, 010.gif, etc.)</h2>";

$testBases = ['01', '010', '014', '02', '03'];
echo "<table>";
echo "<tr><th>Datei</th><th>Status</th><th>Größe</th><th>Preview</th><th>Ladbar?</th></tr>";

foreach ($testBases as $base) {
    $path = $baseDir . $base . '.gif';
    $exists = file_exists($path);
    $size = $exists ? filesize($path) : 0;
    $loadable = false;
    
    if ($exists) {
        $img = @imagecreatefromgif($path);
        $loadable = ($img !== false);
        if ($img) imagedestroy($img);
    }
    
    echo "<tr>";
    echo "<td>{$base}.gif</td>";
    echo "<td>" . ($exists ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td>";
    echo "<td>{$size} bytes</td>";
    echo "<td><img src='/swf/habbo-imaging/badges/base/{$base}.gif' style='width:32px;height:32px;'/></td>";
    echo "<td>" . ($loadable ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// 3. Prüfe Templates
echo "<div class='box'>";
echo "<h2>🎭 3. Templates (20.gif, 60.gif, etc.)</h2>";

$testTemplates = ['20', '60', '01', '02', '03'];
echo "<table>";
echo "<tr><th>Datei</th><th>Status</th><th>Größe</th><th>Preview</th><th>Ladbar?</th></tr>";

foreach ($testTemplates as $template) {
    $path = $templateDir . $template . '.gif';
    $exists = file_exists($path);
    $size = $exists ? filesize($path) : 0;
    $loadable = false;
    
    if ($exists) {
        $img = @imagecreatefromgif($path);
        $loadable = ($img !== false);
        if ($img) imagedestroy($img);
    }
    
    echo "<tr>";
    echo "<td>{$template}.gif</td>";
    echo "<td>" . ($exists ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td>";
    echo "<td>{$size} bytes</td>";
    echo "<td><img src='/swf/habbo-imaging/badges/templates/{$template}.gif' style='width:32px;height:32px;'/></td>";
    echo "<td>" . ($loadable ? "<span class='success'>✅</span>" : "<span class='error'>❌</span>") . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// 4. Teste Badge-Parsing
echo "<div class='box'>";
echo "<h2>🧪 4. Badge-Code Parsing Test</h2>";

$testBadge = "b01014s206054";
echo "<p><strong>Test Badge-Code:</strong> <code>{$testBadge}</code></p>";

// Parse wie badge.php
$badgedata = str_replace(array("b", "X"), "", $testBadge);
$layers = explode("s", $badgedata);

echo "<p><strong>Nach Parsing:</strong></p>";
echo "<pre>";
print_r($layers);
echo "</pre>";

echo "<p><strong>Interpretation:</strong></p>";
echo "<ul>";
echo "<li><strong>Layer 0 (Base):</strong> {$layers[0]}</li>";
if (isset($layers[1])) echo "<li><strong>Layer 1:</strong> {$layers[1]}</li>";
if (isset($layers[2])) echo "<li><strong>Layer 2:</strong> {$layers[2]}</li>";
echo "</ul>";

// Prüfe welche Dateien benötigt werden
$baseFile = substr($layers[0], 0, 2) . '.gif';
echo "<p><strong>Benötigte Base-Datei:</strong> {$baseFile}</p>";

if (isset($layers[1])) {
    $templateFile = substr($layers[1], 0, 2) . '.gif';
    echo "<p><strong>Benötigte Template-Datei:</strong> {$templateFile}</p>";
}

echo "</div>";

// 5. Teste Badge-Generator mit Debug
echo "<div class='box'>";
echo "<h2>🎨 5. Badge-Generator Test</h2>";

echo "<p><strong>Test 1: Einfacher Badge (b0101)</strong></p>";
echo "<img src='/swf/habbo-imaging/badge.php?badge=b0101' style='width:64px;height:64px;background:#ccc;'/>";

echo "<p><strong>Test 2: Badge aus DB (b01014s206054)</strong></p>";
echo "<img src='/swf/habbo-imaging/badge.php?badge=b01014s206054' style='width:64px;height:64px;background:#ccc;'/>";

echo "<p><strong>Test 3: Komplexer Badge</strong></p>";
echo "<img src='/swf/habbo-imaging/badge.php?badge=b0101s02014s05013s06012' style='width:64px;height:64px;background:#ccc;'/>";

echo "</div>";

// 6. Error-Log
echo "<div class='box'>";
echo "<h2>📝 6. Error-Log</h2>";

$errorLog = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badge-errors.log';
if (file_exists($errorLog)) {
    $errors = file_get_contents($errorLog);
    if (!empty($errors)) {
        echo "<pre style='background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;max-height:300px;'>";
        echo htmlspecialchars($errors);
        echo "</pre>";
        echo "<p><a href='?clear_log=1'>🗑️ Log löschen</a></p>";
    } else {
        echo "<p class='success'>✅ Log ist leer</p>";
    }
} else {
    echo "<p>ℹ️ Noch kein Error-Log vorhanden</p>";
}

// Clear log
if (isset($_GET['clear_log'])) {
    file_put_contents($errorLog, '');
    echo "<script>window.location='test-badge-files.php';</script>";
}

echo "</div>";

echo "<hr>";
echo "<p><a href='/dev/test-group-badges.php'>→ Zurück zum Gruppen-Test</a></p>";
?>
