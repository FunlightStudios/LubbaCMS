# WebSocket Implementation Plan für LubbaEMU

## 📋 Analyse Abgeschlossen

### **Aktuelle Architektur:**
- ✅ **TCP Socket Server** (`GameSocketManager.cs`)
- ✅ **Connection Handling** (`ConnectionHandling.cs`)
- ✅ **Packet Parser** (`InitialPacketParser.cs`, `GamePacketParser.cs`)
- ✅ **Client Manager** (`GameClientManager.cs`)

### **Was benötigt wird:**
1. **WebSocket Library** (Fleck oder WebSocketSharp)
2. **WebSocket Server** (parallel zum TCP Server)
3. **WebSocket Connection Wrapper**
4. **Nitro Packet Format** (JSON statt Binary)
5. **Dual-Protocol Support** (Flash + Nitro gleichzeitig)

## 🔧 Implementation Steps

### **Step 1: NuGet Package hinzufügen**
```powershell
Install-Package Fleck -Version 1.2.0
```

### **Step 2: WebSocket Server erstellen**
- `Communication/WebSocket/WebSocketServer.cs`
- `Communication/WebSocket/WebSocketConnection.cs`
- `Communication/WebSocket/WebSocketPacketParser.cs`

### **Step 3: Connection Manager erweitern**
- Beide Protokolle parallel laufen lassen
- TCP auf Port 30000 (Flash)
- WebSocket auf Port 30000 (mit Upgrade)

### **Step 4: Packet System anpassen**
- Nitro sendet JSON statt Binary
- Converter zwischen Flash-Packets und Nitro-Packets

### **Step 5: Config erweitern**
```ini
[websocket]
websocket.enabled=1
websocket.port=30000
```

## ⚠️ Wichtige Hinweise

### **Kompatibilität:**
- Flash Client funktioniert weiter (TCP)
- Nitro Client nutzt WebSocket
- Beide können gleichzeitig online sein

### **Performance:**
- WebSocket ist schneller als Flash
- Weniger Overhead
- Bessere Latenz

### **Sicherheit:**
- WebSocket nutzt gleiche Authentifizierung
- SSO Token bleibt gleich
- Encryption optional (WSS)

## 📝 Geschätzte Dateien

### **Neue Dateien:**
1. `Communication/WebSocket/WebSocketServer.cs` (~200 Zeilen)
2. `Communication/WebSocket/WebSocketConnection.cs` (~150 Zeilen)
3. `Communication/WebSocket/WebSocketPacketParser.cs` (~100 Zeilen)
4. `Communication/WebSocket/NitroPacketConverter.cs` (~300 Zeilen)

### **Geänderte Dateien:**
1. `Communication/ConnectionManager/ConnectionHandling.cs`
2. `PlusEnvironment.cs`
3. `config.ini`

### **Total:** ~750 Zeilen neuer Code

## 🚀 Zeitaufwand

- **Implementation:** ~2-3 Stunden
- **Testing:** ~1 Stunde
- **Debugging:** ~1-2 Stunden
- **Total:** ~4-6 Stunden

## ✅ Bereit zum Start?

Soll ich anfangen? Das wird eine große Änderung, aber ich mache es systematisch und sicher!
