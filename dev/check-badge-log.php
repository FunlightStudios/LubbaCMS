<?php
$logPath = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badge-errors.log';

echo "<h1>📝 Badge Error Log</h1>";

if (file_exists($logPath)) {
    $content = file_get_contents($logPath);
    if (!empty($content)) {
        echo "<pre style='background:#f8f9fa;padding:20px;border-radius:5px;'>";
        echo htmlspecialchars($content);
        echo "</pre>";
    } else {
        echo "<p>✅ Log ist leer</p>";
    }
} else {
    echo "<p>❌ Log-Datei existiert nicht</p>";
}

// Teste badge.php direkt
echo "<hr>";
echo "<h2>Test badge.php direkt:</h2>";
echo "<img src='/swf/habbo-imaging/badge.php?badge=b0101&test=" . time() . "' style='border:2px solid red;background:#fff;padding:10px;'/>";
?>
