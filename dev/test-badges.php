<?php
define('BRAIN_CMS', 1);
require_once 'global.php';

echo "<h2>Gruppen-Badge Test</h2>";
echo "<p>Badge URL: " . $config['groupBadgeURL'] . "</p>";

$getem = $dbh->prepare("SELECT groups.id, groups.name, groups.badge, COUNT(*) AS count FROM groups, group_memberships WHERE groups.id = group_memberships.group_id GROUP BY group_memberships.group_id ORDER BY count DESC LIMIT 5");
$getem->execute();

echo "<h3>Gruppen in der Datenbank:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Badge Code</th><th>Members</th><th>Badge URL</th><th>Badge Preview</th></tr>";

while ($row = $getem->fetch()) {
    $badgeCode = htmlspecialchars($row['badge']);
    $badgeUrl = $config['groupBadgeURL'] . $badgeCode;
    
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . $badgeCode . "</td>";
    echo "<td>" . $row['count'] . "</td>";
    echo "<td><a href='" . $badgeUrl . "' target='_blank'>Link</a></td>";
    echo "<td><img src='" . $badgeUrl . "' alt='Badge' style='width:64px;height:64px;background:#ccc;'/></td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Badge-Code Analyse:</h3>";
$testBadge = "b01014s206054";
echo "<p><strong>Badge-Code aus DB:</strong> " . $testBadge . "</p>";

// Parse Badge-Code
$badgedata = str_replace(array("b", "X"), "", $testBadge);
$layers = explode("s", $badgedata);
echo "<p><strong>Layers nach Parsing:</strong></p>";
echo "<pre>";
print_r($layers);
echo "</pre>";

echo "<h3>Test Badge Rendering:</h3>";
echo "<p>Badge aus DB (b01014s206054):</p>";
echo "<img src='" . $config['groupBadgeURL'] . "b01014s206054' style='width:64px;height:64px;background:#ccc;border:1px solid red;'/>";

echo "<p>Test Badge (b0101s02014s05013s06012):</p>";
echo "<img src='" . $config['groupBadgeURL'] . "b0101s02014s05013s06012' style='width:64px;height:64px;background:#ccc;border:1px solid green;'/>";

echo "<p>Einfacher Test Badge (b0101):</p>";
echo "<img src='" . $config['groupBadgeURL'] . "b0101' style='width:64px;height:64px;background:#ccc;border:1px solid blue;'/>";

echo "<h3>Badge-Dateien Check:</h3>";
$baseFiles = ['01.gif', '010.gif', '014.gif', '20.gif', '60.gif'];
foreach ($baseFiles as $file) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/base/' . $file;
    $exists = file_exists($path) ? '✅ Existiert' : '❌ Fehlt';
    echo "<p><strong>{$file}:</strong> {$exists}</p>";
}
?>
