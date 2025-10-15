# WebSocket Dateien zum Projekt hinzufügen

## Problem
Die neuen WebSocket-Dateien sind noch nicht im Visual Studio Projekt registriert.

## Lösung

### **Option 1: Automatisch (EMPFOHLEN)**

1. Öffne **Visual Studio**
2. Rechtsklick auf `Communication` Ordner im Solution Explorer
3. `Add` → `Existing Item...`
4. Navigiere zu `Communication\WebSocket\`
5. Wähle **ALLE** `.cs` Dateien aus:
   - `WebSocketServer.cs`
   - `WebSocketConnectionWrapper.cs`
   - `WebSocketConnectionHandler.cs`
6. Klicke `Add`

### **Option 2: Manuell in .csproj**

Öffne die `.csproj` Datei und füge hinzu:

```xml
<Compile Include="Communication\WebSocket\WebSocketServer.cs" />
<Compile Include="Communication\WebSocket\WebSocketConnectionWrapper.cs" />
<Compile Include="Communication\WebSocket\WebSocketConnectionHandler.cs" />
```

### **Option 3: Neu erstellen**

Falls die Dateien nicht gefunden werden, erstelle sie neu in Visual Studio:

1. Rechtsklick auf `Communication` → `New Folder` → Name: `WebSocket`
2. Rechtsklick auf `WebSocket` → `Add` → `Class`
3. Kopiere den Code aus den erstellten Dateien

## Nach dem Hinzufügen

1. `Build` → `Clean Solution`
2. `Build` → `Rebuild Solution`

Die Fehler sollten verschwinden!
