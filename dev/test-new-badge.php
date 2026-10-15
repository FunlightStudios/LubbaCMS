<!DOCTYPE html>
<html>
<head>
    <title>Badge Generator Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
        img { background: #fff; border: 2px solid #ddd; margin: 10px; padding: 5px; }
    </style>
</head>
<body>
    <h1>🎨 Neue Badge.php Test</h1>
    
    <div class="box">
        <h2>Test 1: Einfacher Badge (b0101)</h2>
        <img src="/swf/habbo-imaging/badge_NEW.php?badge=b0101" />
    </div>
    
    <div class="box">
        <h2>Test 2: Badge mit Template (b01014s206054)</h2>
        <img src="/swf/habbo-imaging/badge_NEW.php?badge=b01014s206054" />
    </div>
    
    <div class="box">
        <h2>Test 3: Komplexer Badge</h2>
        <img src="/swf/habbo-imaging/badge_NEW.php?badge=b0101s02014s05013s06012" />
    </div>
    
    <div class="box">
        <h2>Vergleich: Alte vs Neue badge.php</h2>
        <p><strong>ALT:</strong> <img src="/swf/habbo-imaging/badge.php?badge=b0101" /></p>
        <p><strong>NEU:</strong> <img src="/swf/habbo-imaging/badge_NEW.php?badge=b0101" /></p>
    </div>
    
    <hr>
    <p>Wenn die neue Version funktioniert, ersetze die alte:</p>
    <p><code>badge.php → badge_OLD.php</code></p>
    <p><code>badge_NEW.php → badge.php</code></p>
</body>
</html>
