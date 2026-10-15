<?php
define('BRAIN_CMS', 1);
require_once '../global.php';

// Aktueller User
$currentUserId = User::userData('id');
$currentUsername = User::userData('username');

echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
th { background: #667eea; color: white; }
.badge-preview { width: 64px; height: 64px; background: #f0f0f0; }
.success { color: #28a745; }
.error { color: #dc3545; }
</style>";

echo "<h1>🔍 Gruppen-Badge Diagnose</h1>";
echo "<p><strong>Aktueller User:</strong> {$currentUsername} (ID: {$currentUserId})</p>";

// 1. Prüfe Config
echo "<div class='box'>";
echo "<h2>📋 1. Config-Prüfung</h2>";
echo "<p><strong>groupBadgeURL:</strong> " . htmlspecialchars($config['groupBadgeURL']) . "</p>";
echo "</div>";

// 2. Prüfe User's Gruppen
echo "<div class='box'>";
echo "<h2>👥 2. Gruppen des aktuellen Users</h2>";

$userGroups = $dbh->prepare("
    SELECT 
        g.id, 
        g.name, 
        g.badge, 
        g.desc,
        gm.member_rank,
        gm.member_since
    FROM group_memberships gm
    JOIN groups g ON gm.group_id = g.id
    WHERE gm.user_id = :user_id
    ORDER BY gm.member_since DESC
");
$userGroups->execute([':user_id' => $currentUserId]);

if ($userGroups->rowCount() > 0) {
    echo "<p class='success'>✅ User ist in " . $userGroups->rowCount() . " Gruppe(n)</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Badge Code</th><th>Rank</th><th>Badge URL</th><th>Preview</th></tr>";
    
    while ($group = $userGroups->fetch()) {
        $badgeUrl = $config['groupBadgeURL'] . htmlspecialchars($group['badge']);
        echo "<tr>";
        echo "<td>" . $group['id'] . "</td>";
        echo "<td>" . htmlspecialchars($group['name']) . "</td>";
        echo "<td><code>" . htmlspecialchars($group['badge']) . "</code></td>";
        echo "<td>" . $group['member_rank'] . "</td>";
        echo "<td><a href='{$badgeUrl}' target='_blank'>Öffnen</a></td>";
        echo "<td><img src='{$badgeUrl}' class='badge-preview' onerror=\"this.style.border='2px solid red'\"/></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ User ist in KEINER Gruppe!</p>";
    echo "<p>→ Tritt einer Gruppe bei oder erstelle eine!</p>";
}
echo "</div>";

// 3. Prüfe ALLE Gruppen (wie aktueller Query)
echo "<div class='box'>";
echo "<h2>🌍 3. Top 5 Gruppen (aktueller Query in me.php)</h2>";

$topGroups = $dbh->prepare("
    SELECT 
        g.id,
        g.name,
        g.badge,
        g.desc,
        COUNT(gm.user_id) AS count 
    FROM groups g
    LEFT JOIN group_memberships gm ON g.id = gm.group_id
    GROUP BY g.id
    ORDER BY count DESC 
    LIMIT 5
");
$topGroups->execute();

if ($topGroups->rowCount() > 0) {
    echo "<p class='success'>✅ " . $topGroups->rowCount() . " Gruppen gefunden</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Badge Code</th><th>Mitglieder</th><th>Badge Preview</th></tr>";
    
    while ($group = $topGroups->fetch()) {
        $badgeUrl = $config['groupBadgeURL'] . htmlspecialchars($group['badge']);
        echo "<tr>";
        echo "<td>" . $group['id'] . "</td>";
        echo "<td>" . htmlspecialchars($group['name']) . "</td>";
        echo "<td><code>" . htmlspecialchars($group['badge']) . "</code></td>";
        echo "<td>" . $group['count'] . "</td>";
        echo "<td><img src='{$badgeUrl}' class='badge-preview' onerror=\"this.style.border='2px solid red'\"/></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ Keine Gruppen in der Datenbank!</p>";
}
echo "</div>";

// 4. Teste Badge-Generator direkt
echo "<div class='box'>";
echo "<h2>🎨 4. Badge-Generator Test</h2>";

// Hole echten Badge-Code aus DB
$realBadge = $dbh->query("SELECT badge FROM groups LIMIT 1")->fetchColumn();
$testBadges = ['b0101', 'b01014s206054', 'b0101s02014s05013s06012'];
if ($realBadge) {
    array_unshift($testBadges, $realBadge);
    echo "<p><strong>Badge aus Datenbank:</strong> <code>{$realBadge}</code></p>";
}

echo "<table>";
echo "<tr><th>Badge Code</th><th>URL</th><th>Preview</th></tr>";

foreach ($testBadges as $badge) {
    $badgeUrl = $config['groupBadgeURL'] . $badge;
    echo "<tr>";
    echo "<td><code>{$badge}</code></td>";
    echo "<td><a href='{$badgeUrl}' target='_blank'>Öffnen</a></td>";
    echo "<td><img src='{$badgeUrl}' class='badge-preview' onerror=\"this.style.border='2px solid red'\"/></td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// 5. Prüfe Badge-Dateien
echo "<div class='box'>";
echo "<h2>📁 5. Badge-Dateien Check</h2>";

$baseDir = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/base/';
$templateDir = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badges/templates/';

$criticalFiles = [
    'base/base.gif' => $baseDir . 'base.gif',
    'base/01.gif' => $baseDir . '01.gif',
    'base/010.gif' => $baseDir . '010.gif',
    'templates/20.gif' => $templateDir . '20.gif',
    'templates/60.gif' => $templateDir . '60.gif'
];

echo "<table>";
echo "<tr><th>Datei</th><th>Status</th></tr>";

foreach ($criticalFiles as $name => $path) {
    $exists = file_exists($path);
    $status = $exists ? "<span class='success'>✅ Existiert</span>" : "<span class='error'>❌ Fehlt</span>";
    echo "<tr><td>{$name}</td><td>{$status}</td></tr>";
}

echo "</table>";
echo "</div>";

// 6. Error-Log prüfen
echo "<div class='box'>";
echo "<h2>📝 6. Badge Error-Log</h2>";

$errorLog = $_SERVER['DOCUMENT_ROOT'] . '/swf/habbo-imaging/badge-errors.log';

if (file_exists($errorLog)) {
    $errors = file_get_contents($errorLog);
    if (!empty($errors)) {
        echo "<pre style='background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;'>";
        echo htmlspecialchars($errors);
        echo "</pre>";
    } else {
        echo "<p class='success'>✅ Keine Fehler im Log</p>";
    }
} else {
    echo "<p>ℹ️ Error-Log existiert noch nicht</p>";
}

echo "</div>";

// 7. Zusammenfassung
echo "<div class='box'>";
echo "<h2>📊 Zusammenfassung</h2>";

$issues = [];

// Check 1: User in Gruppen?
$userGroups->execute([':user_id' => $currentUserId]);
if ($userGroups->rowCount() == 0) {
    $issues[] = "❌ User ist in keiner Gruppe → Keine Badges werden angezeigt";
}

// Check 2: Gruppen existieren?
$topGroups->execute();
if ($topGroups->rowCount() == 0) {
    $issues[] = "❌ Keine Gruppen in der Datenbank";
}

// Check 3: Badge-Dateien?
if (!file_exists($baseDir . 'base.gif')) {
    $issues[] = "❌ base.gif fehlt";
}

if (empty($issues)) {
    echo "<p class='success' style='font-size:18px;'>✅ Alles sieht gut aus!</p>";
    echo "<p>Wenn Badges trotzdem nicht angezeigt werden:</p>";
    echo "<ul>";
    echo "<li>Prüfe Browser-Konsole (F12) auf Fehler</li>";
    echo "<li>Prüfe ob Badge-URLs im HTML-Code korrekt sind</li>";
    echo "<li>Teste Badge-Generator direkt (siehe Test-Badges oben)</li>";
    echo "</ul>";
} else {
    echo "<p class='error' style='font-size:18px;'>⚠️ Probleme gefunden:</p>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>{$issue}</li>";
    }
    echo "</ul>";
}

echo "</div>";

echo "<hr>";
echo "<p><a href='/me'>→ Zurück zu me.php</a></p>";
?>
