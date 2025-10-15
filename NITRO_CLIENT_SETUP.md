# Nitro Client Setup - LubbaCMS

## ✅ Was wurde eingerichtet:

### **1. Modernisierte nitro.php**
- ✅ PHP 8+ kompatibel mit `declare(strict_types=1)`
- ✅ Saubere HTML5 Struktur
- ✅ Moderner Loading Screen mit Gradient
- ✅ SSO Token wird sicher übergeben
- ✅ Console Logging für Debugging

### **2. Konfiguration**
Die `configuration.json` ist bereits korrekt konfiguriert:
```json
{
    "socket.url": "ws://127.0.0.1:30000",
    "asset.url": "http://127.0.0.1/nitro",
    "image.library.url": "http://127.0.0.1/swf/c_images/"
}
```

## 🚀 Wie du den Client zum Laufen bringst:

### **Schritt 1: Emulator kompilieren**
1. Öffne dein Emulator-Projekt (PlusEMU/LubbaEMU)
2. Kompiliere es mit Visual Studio
3. Stelle sicher dass `MUS.exe` läuft (falls du MUS verwendest)

### **Schritt 2: Emulator starten**
1. Starte den Emulator (z.B. `PlusEmulator.exe`)
2. Er sollte auf Port **30000** lauschen
3. Prüfe die Console: "Listening on port 30000"

### **Schritt 3: Datenbank prüfen**
Stelle sicher dass diese Tabellen existieren:
- `users` - Mit `auth_ticket` Spalte
- `rooms` - Für Räume
- `items` - Für Möbel

### **Schritt 4: Client testen**
1. Gehe zu: `http://127.0.0.1/nitro`
2. Du solltest den Loading Screen sehen
3. Öffne Browser Console (F12)
4. Prüfe die Logs:
   - SSO Token sollte angezeigt werden
   - WebSocket URL sollte korrekt sein

## 🐛 Troubleshooting:

### **Problem: "WebSocket connection failed"**
**Ursache:** Emulator läuft nicht oder falscher Port

**Lösung:**
1. Starte den Emulator
2. Prüfe `emulator_settings` Tabelle:
   ```sql
   SELECT * FROM emulator_settings WHERE `key` = 'game.tcp.port';
   ```
3. Port sollte `30000` sein

### **Problem: "Invalid SSO Token"**
**Ursache:** Token ist abgelaufen oder falsch

**Lösung:**
1. Prüfe Browser Console für SSO Token
2. Prüfe DB: `SELECT auth_ticket FROM users WHERE id = YOUR_ID`
3. Token sollte nicht leer sein

### **Problem: "Client lädt nicht"**
**Ursache:** Assets fehlen oder falsche Pfade

**Lösung:**
1. Prüfe ob `/nitro/index.html` existiert
2. Prüfe ob `/nitro/main.js` existiert
3. Prüfe Browser Console für 404 Fehler

### **Problem: "Stuck at loading screen"**
**Ursache:** WebSocket Verbindung schlägt fehl

**Lösung:**
1. Öffne Browser Console (F12)
2. Gehe zu "Network" Tab
3. Filter auf "WS" (WebSocket)
4. Sollte Verbindung zu `ws://127.0.0.1:30000` zeigen

## 📝 Wichtige Dateien:

### **nitro.php**
```php
// Generiert SSO Token
Game::sso('client');

// Übergibt Token an Nitro
src="/nitro/index.html?sso=<?= $ssoToken ?>"
```

### **configuration.json**
```json
{
    "socket.url": "ws://127.0.0.1:30000",  // Emulator Port
    "asset.url": "http://127.0.0.1/nitro"  // Nitro Assets
}
```

## 🔧 Emulator Konfiguration:

### **emulator_settings Tabelle:**
Wichtige Settings:
```sql
-- WebSocket Port
UPDATE emulator_settings SET `value` = '30000' WHERE `key` = 'game.tcp.port';

-- MUS aktivieren (optional)
UPDATE emulator_settings SET `value` = '1' WHERE `key` = 'mus.enabled';
```

### **server_settings Tabelle (falls vorhanden):**
```sql
UPDATE server_settings SET `value` = '30000' WHERE `key` = 'game.port';
```

## 🎮 Nach dem Start:

### **Was du sehen solltest:**
1. **Loading Screen** - Lila Gradient mit Spinner
2. **Console Logs:**
   ```
   🎮 Lubba Hotel - Nitro Client
   SSO Token: [dein-token]
   WebSocket: ws://127.0.0.1:30000
   ```
3. **Nitro Client** - Sollte nach 1-2 Sekunden laden

### **Wenn alles funktioniert:**
- ✅ Du siehst das Hotel View
- ✅ Du kannst Räume betreten
- ✅ Chat funktioniert
- ✅ Möbel laden

## 🔐 Sicherheit:

### **Produktions-Setup:**
Wenn du live gehst, ändere:

1. **URLs in configuration.json:**
```json
{
    "socket.url": "wss://deine-domain.de:30000",
    "asset.url": "https://deine-domain.de/nitro"
}
```

2. **SSL/TLS:**
- Verwende `wss://` statt `ws://`
- Installiere SSL Zertifikat
- Konfiguriere Emulator für SSL

3. **Firewall:**
- Öffne Port 30000
- Nur für WebSocket Traffic

## 📊 Performance:

### **Optimierungen:**
1. **Asset Caching:**
   - Nitro Assets sollten gecached werden
   - Setze Cache-Headers in Apache/Nginx

2. **CDN:**
   - Lade Nitro Assets von CDN
   - Schnellere Ladezeiten

3. **Compression:**
   - Aktiviere Gzip für `.js` und `.css`
   - Reduziert Ladezeit um 70%

## 🆘 Support:

### **Logs prüfen:**
1. **Browser Console** (F12)
2. **Emulator Console**
3. **Apache Error Log** (`xampp/apache/logs/error.log`)

### **Häufige Fehler:**
- `ERR_CONNECTION_REFUSED` → Emulator läuft nicht
- `Invalid SSO` → Token Problem
- `404 Not Found` → Assets fehlen
- `CORS Error` → Domain-Konfiguration

## ✨ Fertig!

Sobald dein Emulator kompiliert und gestartet ist:
1. Gehe zu `/nitro`
2. Warte auf Loading Screen
3. Client sollte laden
4. Viel Spaß! 🎉

---

**Version:** 1.0  
**Datum:** 2025-09-29  
**Kompatibel mit:** PlusEMU, LubbaEMU, Arcturus
