<?php
echo "<h2>FurnitureData.json Access Test</h2>";

$file = 'c:/xampp/htdocs/nitro/gamedata/json/FurnitureData.json';
$url = 'http://localhost/nitro/gamedata/json/FurnitureData.json';

echo "<h3>File Check:</h3>";
if (file_exists($file)) {
    echo "<p>✓ File exists</p>";
    echo "<p>Size: " . filesize($file) . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($file)) . "</p>";
    
    // Check first bytes for BOM
    $handle = fopen($file, 'rb');
    $firstBytes = fread($handle, 10);
    fclose($handle);
    
    echo "<p>First bytes (hex): " . bin2hex($firstBytes) . "</p>";
    
    if (substr($firstBytes, 0, 3) === "\xEF\xBB\xBF") {
        echo "<p>❌ BOM still present!</p>";
    } else {
        echo "<p>✓ No BOM</p>";
    }
} else {
    echo "<p>❌ File not found</p>";
}

echo "<h3>HTTP Access Test:</h3>";
echo "<p>URL: <a href='$url' target='_blank'>$url</a></p>";

// Test if accessible via HTTP
$headers = @get_headers($url);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "<p>✓ Accessible via HTTP</p>";
} else {
    echo "<p>❌ Not accessible via HTTP</p>";
    echo "<pre>";
    print_r($headers);
    echo "</pre>";
}

// Check configuration.json
echo "<h3>Configuration Check:</h3>";
$config = file_get_contents('c:/xampp/htdocs/nitro/configuration.json');
$configData = json_decode($config, true);
echo "<p>Furnidata URL in config: " . $configData['furnidata.url'] . "</p>";

// Resolve the URL
$gameDataUrl = str_replace('${asset.url}', 'http://localhost/nitro', $configData['gamedata.url']);
$furnidataUrl = str_replace('${gamedata.url}', $gameDataUrl, $configData['furnidata.url']);
echo "<p>Resolved URL: $furnidataUrl</p>";

echo "<h3>Test Direct Load:</h3>";
echo "<button onclick='testLoad()'>Test Load FurnitureData.json</button>";
echo "<div id='result'></div>";

echo "<script>
function testLoad() {
    fetch('$url')
        .then(response => {
            document.getElementById('result').innerHTML = '<p>Status: ' + response.status + '</p>';
            return response.json();
        })
        .then(data => {
            document.getElementById('result').innerHTML += '<p>✓ JSON loaded successfully</p>';
            document.getElementById('result').innerHTML += '<p>Total items: ' + data.roomitemtypes.furnitype.length + '</p>';
            
            // Find ID 2063
            const item = data.roomitemtypes.furnitype.find(i => i.id === 2063);
            if (item) {
                document.getElementById('result').innerHTML += '<p>✓ Found ID 2063: ' + item.classname + '</p>';
            } else {
                document.getElementById('result').innerHTML += '<p>❌ ID 2063 not found</p>';
            }
        })
        .catch(error => {
            document.getElementById('result').innerHTML += '<p>❌ Error: ' + error + '</p>';
        });
}
</script>";
?>
