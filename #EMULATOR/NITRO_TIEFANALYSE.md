# Tiefgründige Analyse: Nitro-Implementierungsstatus im Lubba Emulator

**Datum:** 13. Oktober 2026  
**Analyst:** Devin (automatisierte Code-Analyse)  
**Untersuchte Verzeichnisse:**
- `C:\xampp\htdocs\#EMULATOR` (Lubba Emulator, PlusEMU-basiert)
- `C:\xampp\htdocs\nitro` (Nitro Web-Client)
- `C:\xampp\htdocs\templates\brain\nitro.php` (CMS-Integration)

**Wichtiger Hinweis vorab:** Die bestehenden Dokumente (`NITRO_IMPLEMENTATION_STATUS.md`, `NITRO_ANALYSIS_2025.md`, `TODO.md`) enthalten erhebliche Übertreibungen und Inkonsistenzen. Diese Analyse basiert ausschließlich auf dem tatsächlich im Repository vorhandenen Code, nicht auf den Statusmeldungen der Dokumente.

---

## 1. Executive Summary

Der Lubba Emulator hat eine **funktionierende Basis-Infrastruktur** für Nitro-WebSocket-Clients, die deutlich über einem Prototypen steht. Kernfeatures wie Authentifizierung, Raumbetritt, Bewegung mit Kollisionserkennung, Chat, Navigator und ein großer Teil des Katalogs sind tatsächlich implementiert.

Allerdings gibt es **kritische Lücken** und **Dokumentations-Irreführungen**:
- **Möbel-Placement & Pickup sind NICHT implementiert**, obwohl mehrere Dokumente dies behaupten.
- **`:pickall` ist NICHT implementiert**, obwohl es als implementiert geführt wird.
- **Der WebSocket-Port ist hartkodiert** (2096) und die `config.ini` wird nicht vollständig ausgewertet.
- **Die Code-Qualität des zentralen Handlers ist problematisch** (3.026 Zeilen in einer einzigen Klasse, duplizierte Logik, Reflexion-Brücken).
- **Möbel-Placement ist clientseitig blockiert**; der bereitgestellte Patch wurde nicht angewendet.

**Realistischer Gesamtstatus:** ca. **35–40 %** der für einen spielbaren Nitro-Client nötigen Features sind vorhanden. Die Kern-Infrastruktur steht, die täglichen Gameplay-Features (Inventar benutzen, Möbel platzieren/picken, Trading, viele Sozial-Features) fehlen weitgehend.

---

## 2. Architektur & beteiligte Dateien

### 2.1 Verbindungsfluss

```
Nitro Client (Browser, ws://localhost:2096)
    ↓
WebSocketServer.cs (Fleck 1.2.0)
    ↓
WebSocketConnectionWrapper.cs (Binary-Framing)
    ↓
WebSocketConnectionHandler.cs (3026 Zeilen, zentraler Router)
    ↓
NitroClient.cs (Session / Habbo)
    ↓
NitroGameClientWrapper.cs (GameClient-Adapter per Reflection)
    ↓
Bestehende PlusEMU-Manager (RoomManager, Catalog, Messenger, ...)
```

### 2.2 Wichtige Dateien

| Datei | Rolle | Zeilen | Status |
|-------|-------|--------|--------|
| `Communication/WebSocket/WebSocketServer.cs` | Fleck-Server | 229 | OK |
| `Communication/WebSocket/WebSocketConnectionWrapper.cs` | Binary-Wrapper | 278 | OK |
| `Communication/WebSocket/WebSocketConnectionHandler.cs` | Packet-Router | 3026 | Zu groß |
| `Communication/WebSocket/NitroClient.cs` | Session / Auth | 288 | OK mit Workarounds |
| `Communication/WebSocket/NitroGameClientWrapper.cs` | eingebettet im Handler | ~80 | Fragil (Reflection) |
| `Communication/Packets/Outgoing/Handshake/NitroServerPacket.cs` | Packet-Builder | - | OK |
| `Communication/Packets/Outgoing/Handshake/NitroPacketHeaders.cs` | Header-Enum | 78 | Unvollständig |
| `templates/brain/nitro.php` | CMS-Einbettung | 89 | Funktioniert |
| `nitro/index.html` | Client-Einstieg | 158 | SSO-Workaround |
| `nitro/configuration.json` | Client-Konfiguration | ~600 | Auf 2096 konfiguriert |
| `Communication/ConnectionManager/ConnectionHandling.cs` | Startet beide Server | 95 | Port hartkodiert |

---

## 3. Was tatsächlich implementiert ist

### 3.1 Infrastruktur (100 %)

- **Dual-Protocol:** Flash TCP (Port 30000) und Nitro WebSocket (Port 2096) laufen parallel.
- **WebSocket-Server:** Fleck 1.2.0, Bindung an `ws://0.0.0.0:2096`.
- **Binary-Protokoll:** Habbo-kompatibles `[Länge:4][Header:2][Daten:n]` Big-Endian-Format.
- **Verbindungsmanagement:** IP-basierte Limits, Connection-IDs, Disconnect-Handling.

### 3.2 Authentifizierung (ca. 90 %)

- **SSO-Token-Login** über Paket `2419` / `4000` / `415`.
- **Nitro-Default-Token-Workaround:** Der Client sendet initial `NITRO-0-4-0`; der Emulator wartet auf das echte Ticket (`NitroClient.cs:80`).
- **User-Loading:** `UserDataFactory.GetUserData`, Inventory-Initialisierung per Reflection.
- **GameClient-Anbindung:** `NitroGameClientWrapper` erlaubt die Nutzung bestehender Manager.
- **Eingeschränkt:** Online-Status wird in DB gesetzt; Multi-Session / Disconnect-Race-Conditionen werden adressiert, aber nicht elegant gelöst.

### 3.3 Raumsystem & Bewegung (ca. 75 %)

- **Raumbetritt:** `2312` sendet `RoomForward`, `RoomModel`, `HeightMap`, `RoomDoor`, `RoomPaint`, `RoomThickness`, `RoomReady`, `RoomFloorItems`.
- **Raumerstellung:** `2752` erstellt Räume direkt per SQL (bypassed `CreateFlatEvent`), sendet `FlatCreated` und aktualisiert Navigator.
- **Rauminformation:** `2230` sendet `RoomInfo`, `RoomModel`, `RoomDoor`, etc.
- **Bewegung:** `3320` mit kundenspezifischer A*-Pfadfindung, Kollisionserkennung, Möbel-Kollision, Z-Höhenberechnung, Rotation.
- **Türen & Höhenunterschiede:** Werden korrekt berücksichtigt.
- **Einschränkungen:**
  - Pfadfindung ist eine einfache Greedy-A*-Mischung, keine echte A* mit Diagonalen.
  - Bewegung wird rein serverseitig getaktet (500 ms pro Tile); Abweichungen zwischen Client- und Serverzustand sind möglich.
  - Mehrere Nutzer im selben Raum werden nicht richtig synchronisiert (`BroadcastToNitroClientsInRoom` broadcastet zwar, aber es gibt keine Flash↔Nitro-Synchronisation).

### 3.4 Avatar-Aktionen (ca. 60 %)

- **Chat:** `1314` (NitroUnitChatComposer, Header `1314`).
- **Commands:** Nur `:commands`, `:about`/`:info`. `:pickall`, `:sit`, `:stand` sind **Dummy-Implementierungen** (geben nur Chat-Feedback).
- **Emotes / Aktionen:** `2091` (`NitroUnitExpressionComposer`).
- **Tanz:** `2080` (`NitroUnitDanceComposer`).
- **Schilder:** `1975` (`NitroUnitStatusComposer` mit `/sign X/`, Auto-Clear nach 2 s).
- **Blickrichtung:** `3301` (nur geloggt, nicht broadcastet).
- **Sitzen / Liegen / Aufstehen:** `2235` und `2456` sind implementiert und aktualisieren `RoomUser.Statusses` sowie broadcasten `UnitStatus`.
- **Sitzen/Liegen auf Möbeln:** Teilweise implementiert (Queue-Mechanismus, Chair/Bed-Erkennung, Seat-Lift-Berechnung).

### 3.5 Navigator (ca. 80 %)

- **Suche / Popular / My Rooms:** `3878`/`2690` (`NitroNavigatorSearchResultsComposer`).
- **Raum-Erstellungsdialog:** `2110` (`NitroCanCreateRoomComposer`).
- **Einstellungen:** `1782` (`NitroNavigatorSettingsComposer`), wird aber initial nicht automatisch gesendet (auskommentiert in `NitroClient.cs:229`).

### 3.6 Inventar (ca. 50 %)

- **Möbel-Inventar:** `3150`/`3500` (Header im Handler vertauscht kommentiert) sendet `NitroFurniListComposer` (Header `994`).
- **Bot-/Pet-Inventar:** Stubs vorhanden.
- **Badges:** Werden beim Login gesendet.

### 3.7 Katalog (ca. 60–70 %)

- **Katalog-Index:** `3848` (`NitroCatalogIndexComposer`, Header `1032`) und bei `1195` zusätzlich `NitroCatalogPagesComposer` (ebenfalls Header `1032`).
- **Seitenladen:** `412` (`NitroCatalogPageComposer`, Header `804`) plus pro Item `NitroCatalogOfferComposer`.
- **Kauf:** `3492` (`HandleCatalogPurchase`) mit Currency-Check, Limited-Edition-Check, Fehlercodes und `NitroPurchaseOKComposer` (Header `869`).
- **Probleme:**
  - `HandleCatalogPurchase` ruft `PurchaseFromCatalogEvent.Parse` über den `NitroGameClientWrapper` auf – eine Brückenlösung, keine native Nitro-Implementierung.
  - Es gibt keinen wirklichen `NitroPurchaseErrorComposer`; Fehler werden über einen handgeschriebenen Header-869-Packet mit Integer gesendet.
  - `NitroCatalogIndexComposer` und `NitroCatalogPagesComposer` senden beide beim gleichen `1195`-Request; das kann beim Client zu doppelten/überschriebenen Daten führen.

### 3.8 Messenger / Freunde (ca. 40 %)

- **Init & Liste:** `2781`, `1523` (`NitroMessengerInitComposer`, `NitroFriendListUpdateComposer`).
- **Freundschaftsanfrage, Akzeptieren, Ablehnen, Entfernen:** Implementiert.
- **Chat, Suche, Folgen, Raum-Einladung:** Handler existieren, aber Suche und Einladung sind Stubs (nur Logging).

### 3.9 Moderation (ca. 30 %)

- **Mod-Tool-Init:** Wird Moderatoren beim Login gesendet.
- **Einige Mod-Tool-Pakete:** Handler und Composer vorhanden, aber weitgehend ungetestet.

---

## 4. Was NICHT implementiert ist (trotz Gegenteiliger Behauptungen)

### 4.1 Möbel-Placement & Pickup (kritisch)

**Behauptung in `NITRO_IMPLEMENTATION_STATUS.md`:**
> - ✅ **Furniture Placement (Packet 1809)** - NEW!
> - ✅ **Furniture Pickup (Packet 3456)** - NEW!
> - ✅ **Furniture Move/Rotation (Packet 3665)** - NEW!

**Realität:**
- Im `WebSocketConnectionHandler`-Switch existieren **keine** Cases für `1258` (FURNITURE_PLACE) oder `3456` (FURNITURE_PICKUP).
- Die Logs (`bin/Debug/logs/console.log`) zeigen wiederholt:
  ```
  Received packet 1258 (12 bytes) from Nitro client X
  Unhandled Nitro packet: 3456 - Data: ...
  ```
- Move/Rotate ist tatsächlich für `248` implementiert (`HandleFurnitureFloorUpdate`), aber Placement/Pickup fehlen komplett.
- Der Client wurde auch **nicht** gepatcht: In `nitro/main.js` fehlt der String `"PATCHED: Always allow placement"`. Der Patch-Skripte (`patch-placement.py`, `patch-placement.ps1`) wurden offenbar nie ausgeführt.
- **Folge:** Der Nutzer kann Möbel aus dem Inventar nicht in den Raum ziehen und auch nicht zurück in das Inventar nehmen.

### 4.2 `:pickall` Befehl

**Behauptung:**
> - ✅ **`:pickall`** - Pick up all furniture - NEW!

**Realität:**
```csharp
case "pickall":
    client.SendPacket(NitroUnitChatComposer.Compose(1, "Pickall command not yet implemented for Nitro"));
    break;
```
Der Befehl gibt nur eine Chat-Nachricht aus.

### 4.3 Trading-System

**Behauptung:** teilweise in Status-Dokumenten als "In Progress".

**Realität:** Keine Trading-Handler im Switch; keine Trading-Composer werden genutzt.

### 4.4 Raumrechte, Raumeinstellungen, Bann/Mute

**Realität:**
- `ROOM_SETTINGS` (`3129`), `ROOM_SETTINGS_SAVE` (`1969`), `ROOM_RIGHTS_GIVE` (`808`), etc. sind nicht im Switch.
- Composer (`NitroRoomRightsComposer`, `NitroRoomRightsOwnerComposer`) existieren, werden aber nirgends aufgerufen.

### 4.5 Wired, Pets, Groups, Achievements, Games

**Realität:** Keine eingehenden Handler; nur Composer-Dateien existieren teilweise.

### 4.6 Avatar-Editor / Kleidung

**Realität:** Es gibt keinen Handler für `USER_FIGURE` (`2730`) oder ähnliche Pakete. Der Avatar wird beim Login einmalig über `NitroUserObjectComposer` gesendet.

---

## 5. Kritische Bugs & Architekturprobleme

### 5.1 Fehlende Furniture-Placement/Pickup-Handler

**Schweregrad:** 🔴 Kritisch  
**Beweis:** `WebSocketConnectionHandler.cs` Switch enthält keine Cases `1258`/`3456`. Logs bestätigen unbehandelte Pakete.  
**Auswirkung:** Inventar-Möbel können nicht genutzt werden. Der Katalog-Kauf ist damit praktisch wertlos.

### 5.2 Hartkodierter WebSocket-Port

**Schweregrad:** 🟡 Hoch  
**Beweis:** `Communication/ConnectionManager/ConnectionHandling.cs:27`
```csharp
int wsPort = 2096;
```
`config.ini` enthält zwar `websocket.enabled=1`, aber keinen Port-Eintrag. Änderungen an der INI haben keine Auswirkung auf den Nitro-Port.

### 5.3 `WebSocketConnectionHandler.cs` ist monolithisch

**Schweregrad:** 🟡 Hoch  
- 3.026 Zeilen in einer einzigen Klasse.
- Enthält Bewegung, Chat, Katalog, Messenger, Möbel, Raumlogik, statische Client-Verwaltung.
- Hohe Kopplung, schwer wartbar, fehleranfällig bei parallelen Änderungen.
- Vorgeschlagene Aufteilung:
  - `NitroPacketRouter` (Switch)
  - `NitroRoomHandler`
  - `NitroCatalogHandler`
  - `NitroMessengerHandler`
  - `NitroFurnitureHandler`
  - `NitroMovementService`

### 5.4 `NitroGameClientWrapper` per Reflection

**Schweregrad:** 🟡 Hoch  
- Setzt private Felder (`_habbo`, `_client`, `InventoryComponent`) per Reflection.
- Bricht leicht, wenn sich die `GameClient`- oder `Habbo`-Interna ändern.
- Bessere Lösung: `NitroClient` direkt als `GameClient`-Ersatz konzipieren oder dedizierte Nitro-Handler ohne GameClient-Adapter schreiben.

### 5.5 Doppelter Katalog-Index

**Schweregrad:** 🟡 Mittel  
`HandleCatalogModeRequest` sendet sowohl `NitroCatalogPagesComposer` als auch `NitroCatalogIndexComposer`. Beide verwenden Header `1032` bzw. unterschiedliche Formatierung. Abhängig vom Nitro-Client kann das zu Konflikten führen.

### 5.6 Inkorrekte / unvollständige Header-Enum

**Schweregrad:** 🟡 Mittel  
`NitroPacketHeaders.cs` listet viele Header, aber:
- `UnitChat` ist `1446` (Nitro erwartet typischerweise `1314` für Chat und `1446` für Shout/Whisper – hier inkonsistent genutzt).
- Manche Header (z. B. Furniture-Placement `1258`, Pickup `3456`) fehlen gänzlich.
- `Ping` und `Pong` teilen sich den gleichen Wert `3928`.

### 5.7 Fehlerhafte Inventar-Header

**Schweregrad:** 🟡 Mittel  
Im Handler werden `3150` als "Request Bot Inventory" und `2769` als "Request Pet Inventory" behandelt. Laut `NITRO_PACKET_ANALYSIS.md` ist das vertauscht (`USER_FURNITURE = 3150`, `USER_PETS = 3095`, `USER_BOTS = 3848`). Die aktuelle Implementierung sendet daher möglicherweise Bot/Pet-Inventar bei falschen Requests.

### 5.8 `:sit`/`:stand` Befehle sind nicht implementiert

**Schweregrad:** 🟢 Niedrig  
Die Befehle existieren nur als Chat-Feedback. Der Posture-Handler selbst (`2235`/`2456`) funktioniert jedoch.

---

## 6. Inkonsistenzen in der Dokumentation

| Dokument | Behauptung | Realität |
|----------|------------|----------|
| `NITRO_IMPLEMENTATION_STATUS.md` | Furniture Placement/Pickup implementiert | Fehlende Handler `1258`/`3456` |
| `NITRO_IMPLEMENTATION_STATUS.md` | `:pickall` implementiert | Nur Chat-Feedback |
| `NITRO_IMPLEMENTATION_STATUS.md` | 55 % Gesamtfortschritt | Realistisch eher 35–40 % |
| `NITRO_ANALYSIS_2025.md` | Catalog 0 % done | Tatsächlich ca. 60–70 % |
| `NITRO_CATALOG_ANALYSIS.md` | Catalog 70 %, Header-869-Fehler | Header 869 ist inzwischen korrekt (`NitroPurchaseOKComposer`) |
| `TODO.md` | Furniture System implementiert | Placement/Pickup fehlen |
| `dev/NITRO_CLIENT_SETUP.md` | WebSocket auf `ws://localhost:30000` | Tatsächlich `ws://localhost:2096` (korrekt in `nitro/configuration.json`) |

---

## 7. Web-Client-Integration

### 7.1 `nitro/configuration.json`

- `socket.url`: `ws://localhost:2096` ✅ korrekt
- `asset.url`: `http://localhost/nitro` ✅ korrekt
- `avatar.figuredata.url`: `http://localhost/swf/gamedata/figuredata.xml` (muss vorhanden sein)
- `furnidata.url`: `/nitro/gamedata/json/FurnitureData.json` (muss vorhanden sein)

### 7.2 `nitro/index.html`

- Verwendet `configurationUrl: '/nitro/configuration.json'`.
- Setzt SSO aus URL-Parameter `?sso=...` oder per `postMessage` vom Parent.
- Fallback `NITRO-0-4-0`, wenn kein SSO vorhanden.
- **Problem:** Die Datei lädt **nicht** `nitro-config.php`, sondern die statische `configuration.json`. Das `nitro-config.php` existiert, wird aber nicht genutzt.
- Enthält Debug-Code für FurnitureData (`ID 2063`), der für Produktion entfernt werden sollte.

### 7.3 `templates/brain/nitro.php`

- Einbettung per `<iframe src="/nitro/index.html?sso=...">`.
- Sendet SSO zusätzlich per `postMessage`.
- Lädt aber Flash-Client-Skripte (`flashclient.js`, `flash_detect_min.js`) – das ist ungenutzter Ballast.

### 7.4 Patch-Dateien

- `patch-placement.py` / `patch-placement.ps1`: Nicht ausgeführt.
- `catalog-icon-patch.js`: Unvollständig (versucht, Katalogpakete zu parsen, macht aber nichts Nützliches).
- `debug-catalog.html`: Debug-Seite, nicht produktiv.

---

## 8. Build- & Projektstatus

- Alle 67 `Nitro*.cs`-Dateien sind im `Plus Emulator.csproj` enthalten.
- Das Projekt verwendet .NET Framework 4.8 (laut `README.md`).
- **Nicht geprüft:** Ob das Projekt aktuell fehlerfrei kompiliert. Da viele Composer-Dateien existieren, aber der zentrale Handler sie teilweise nicht aufruft, kann es ungenutzte Warnungen geben.
- **Empfehlung:** Ein `Rebuild Solution` in Visual Studio durchführen und Warnungen/Fehler dokumentieren.

---

## 9. Empfohlene nächste Schritte (priorisiert)

### Phase 1: Kritisch (sofort)
1. **Furniture-Placement-Handler für Header `1258` implementieren.**
   - Parsen von `itemId`, `x`, `y`, `rotation` aus dem Paket.
   - Inventar-Item entfernen, Raum-Item erstellen, `NitroFloorItemAddComposer` senden.
2. **Furniture-Pickup-Handler für Header `3456` implementieren.**
   - Item aus Raum entfernen, in Inventar legen, `NitroObjectRemoveComposer` + Inventar-Update senden.
3. **`:pickall` entweder entfernen oder vollständig implementieren.**
4. **Client-Placement-Patch anwenden** (`patch-placement.py` ausführen) oder main.js manuell korrigieren.

### Phase 2: Hoch (diese Woche)
5. **WebSocket-Port aus `config.ini` lesen** (z. B. `websocket.port=2096`).
6. **`WebSocketConnectionHandler.cs` aufteilen** in feature-spezifische Handler.
7. **Katalog-Dopplung bereinigen:** Entscheiden, ob `NitroCatalogIndexComposer` oder `NitroCatalogPagesComposer` für `1195` verwendet wird.
8. **Inventar-Header korrigieren:** `3150` → Furniture, `3848` → Bots, `3095` → Pets.
9. **Fehlerbehandlung beim Kauf verbessern:** Echte Nitro-Fehlerpakete mit korrekten Codes senden.

### Phase 3: Mittel (nächste Woche)
10. **Trading-System implementieren** (Handler + Composer).
11. **Raumeinstellungen & Rechte** implementieren.
12. **User-Figure-Update** (`2730`) unterstützen.
13. **Avatar-Look** (`3301`) tatsächlich broadcasten, nicht nur loggen.

### Phase 4: Langfristig
14. **Wired, Pets, Groups, Achievements, Games** – jeweils eigenständige Module.
15. **Flash↔Nitro-Synchronisation** verbessern (z. B. beide Protokolle im selben Raum gleichzeitig).
16. **Logging-Bomben reduzieren:** Der Handler loggt extrem viel (`log.Info` pro Paket). Für Produktion auf `log.Debug` umstellen.

---

## 10. Fazit

Die Nitro-Integration im Lubba Emulator ist ein **solides Fundament**, aber keineswegs „Production Ready“ oder „55 % complete“, wie einige Dokumente suggerieren. Die Infrastruktur steht, Authentifizierung, Räume, Bewegung, Chat, Navigator und Katalog funktionern größtenteils. Das entscheidende Gameplay-Feature **Möbel platzieren und aufheben** fehlt jedoch komplett – und wird in den Statusdokumenten irreführenderweise als fertig dargestellt.

**Empfohlene unmittelbare Maßnahme:** Die Dokumentation korrigieren und Furniture Placement/Pickup als oberste Priorität behandeln. Ohne dieses Feature bleibt der Nitro-Client ein schöner, aber nicht wirklich spielbarer Viewer.

---

## Anhang A: Relevante Code-Zeilen

- WebSocket-Port hartkodiert: `Communication/ConnectionManager/ConnectionHandling.cs:27`
- Nitro-Auth-Workaround: `Communication/WebSocket/NitroClient.cs:80`
- Fehlende Furniture-Placement/Pickup-Cases: `Communication/WebSocket/WebSocketConnectionHandler.cs:199–399`
- `:pickall` Dummy: `Communication/WebSocket/WebSocketConnectionHandler.cs:1543–1544`
- Katalog-Kauf mit Wrapper: `Communication/WebSocket/WebSocketConnectionHandler.cs:2057–2111`
- Doppelter Katalog-Index: `Communication/WebSocket/WebSocketConnectionHandler.cs:2025–2036`
- Sitzen/Liegen auf Möbeln: `Communication/WebSocket/WebSocketConnectionHandler.cs:2199–2474`
- Client-PostMessage-SSO: `templates/brain/nitro.php:66–83`
- Client-Config: `nitro/configuration.json:1–10`
