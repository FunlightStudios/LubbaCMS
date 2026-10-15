<?php
define('BRAIN_CMS', 1);
require_once 'global.php';

echo "<h2>M3U Parser Test</h2>";

$testUrl = 'http://stream.srg-ssr.ch/drsvirus/mp3_128.m3u';

echo "<h3>Test URL:</h3>";
echo "<p><code>$testUrl</code></p>";

echo "<h3>M3U Inhalt:</h3>";
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]
]);

$m3uContent = @file_get_contents($testUrl, false, $context);

if ($m3uContent) {
    echo "<pre>" . htmlspecialchars($m3uContent) . "</pre>";
} else {
    echo "<p style='color:red;'>❌ Konnte M3U nicht laden!</p>";
}

echo "<h3>Geparste Stream-URL:</h3>";
$resolvedUrl = parseM3U($testUrl);
echo "<p><code>$resolvedUrl</code></p>";

echo "<h3>Test im Audio Player:</h3>";
echo "<audio controls autoplay style='width:100%;'>";
echo "<source src='$resolvedUrl' type='audio/mpeg'>";
echo "</audio>";

echo "<h3>Alle Radio Stationen:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Name</th><th>Original URL</th><th>Type</th><th>Resolved URL</th><th>Test</th></tr>";

foreach(getRadioStations() as $station) {
    $resolved = resolveStreamUrl($station['streamUrl'], $station['streamType']);
    $isM3u = ($station['streamType'] === 'm3u' || $station['streamType'] === 'm3u8');
    
    echo "<tr>";
    echo "<td><strong>" . htmlspecialchars($station['name']) . "</strong><br><small>" . htmlspecialchars($station['genre']) . "</small></td>";
    echo "<td><small>" . htmlspecialchars($station['streamUrl']) . "</small></td>";
    echo "<td>" . htmlspecialchars($station['streamType']) . ($isM3u ? ' 🔄' : '') . "</td>";
    echo "<td><small>" . htmlspecialchars($resolved) . "</small></td>";
    echo "<td><audio controls style='width:200px;'><source src='$resolved' type='audio/mpeg'></audio></td>";
    echo "</tr>";
}

echo "</table>";
?>
