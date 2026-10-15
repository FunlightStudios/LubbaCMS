# Reverse Engineering: Nitro Client (`C:\xampp\htdocs\nitro`)

**Datum:** 13. Oktober 2026  
**Analyst:** Devin (automatisierte Code-Analyse)  
**Hinweis:** Dieses Dokument basiert auf der Analyse des **kompilierten Builds** (`main.js`, `vendor.js`, `runtime.js`, `polyfills.js`, `main.js.map`) und der Konfiguration. Keine TypeScript-Quelldateien liegen offen vor.

---

## 1. Übersicht: Build-Struktur & Architektur

### 1.1 Gelieferte Dateien

| Datei | Größe (ca.) | Bedeutung |
|-------|-------------|-----------|
| `index.html` | 8 KB | Einstiegspunkt, SSO-Initialisierung, Debug-Overlays |
| `configuration.json` | 30 KB | Laufzeit-Konfiguration (URLs, Assets, Chat-Stile, …) |
| `runtime.js` | klein | Webpack-Runtime |
| `polyfills.js` | mittel | Browser-Polyfills |
| `vendor.js` | groß | Angular, RxJS, PixiJS, Bootstrap, externe Bibliotheken |
| `main.js` | ~7 MB | Haupt-Client-Code (`src/client/nitro` + `src/app`) |
| `main.js.map` | 7,1 MB | Source-Map mit vollständigen TypeScript-Quellen in `sourcesContent` |
| `0.worker.js` | - | Web Worker (vermutlich für Asset-Dekodierung) |
| `catalog-icon-patch.js` | - | Unvollständiges/verwaistes Patch-Skript |
| `templates/brain/nitro.php` | 89 Zeilen | CMS-Einbettung mit iframe + postMessage SSO |

### 1.2 Technologie-Stack

- **Framework:** Angular 8+ (NgZone, ComponentFactoryResolver, RxJS)
- **Rendering:** PixiJS (WebGL/Canvas 2D)
- **Raum-Engine:** Eigene, Habbo-Flash-inspirierte `RoomEngine` / `RoomManager`
- **Kommunikation:** WebSocket, binäres EvaWire-Protokoll (Big-Endian `[Länge:4][Header:2][Payload:n]`)
- **Build:** Webpack 4, Module per 4-Zeichen-Base64-ID referenziert

### 1.3 Gesamtarchitektur

```
index.html
    ├─ app-root (Angular Root)
    │   ├─ HotelView, Toolbar, Navigator, Inventory, Catalog, Friendlist, Room, …
    │   └─ Services (CatalogService, FriendListService, NavigatorService, …)
    ├─ client/nitro/Nitro.ts (Singleton)
    │   ├─ NitroCommunicationManager
    │   │   ├─ SocketConnection (WebSocket)
    │   │   ├─ MessageClassManager (Header → Event/Composer)
    │   │   └─ NitroMessages (Konfiguration — fehlt im Build!)
    │   ├─ RoomEngine
    │   │   ├─ RoomManager / RoomInstance
    │   │   ├─ RoomObjectEventHandler (Maus/Objekt-Interaktion)
    │   │   ├─ RoomMessageHandler (empfängt Raum-Pakete)
    │   │   ├─ RoomContentLoader (Asset-Ladung)
    │   │   └─ RoomObjectLogicFactory / RoomObjectVisualizationFactory
    │   ├─ RoomSessionManager / RoomSession
    │   ├─ SessionDataManager (Furniture/Figure/Product-Daten)
    │   ├─ AvatarRenderManager (Avatar-Zeichnung)
    │   └─ NitroConfiguration (aus configuration.json)
    └─ client/core (generische Engine-Schicht)
        ├─ CommunicationManager / SocketConnection
        ├─ MessageEvent / MessageClassManager
        └─ EvaWireFormat / EvaWireDataWrapper
```

### 1.4 Kritische Build-Lücke

**Wichtig:** Das ausgelieferte `main.js` ist **unvollständig**. Etliche Module werden importiert, aber ihre Modul-Definition fehlt im Bundle. Dazu gehören:
- `client/nitro/communication/NitroMessages.ts` (Header-Registrierung)
- `client/nitro/communication/messages/incoming/IncomingHeader.ts` (alle eingehenden Header)
- `demo/NitroCommunicationDemo*.ts`
- Mehrere wichtige Events/Parser: `RoomModelEvent`, `RoomHeightMapEvent`, `RoomPaintEvent`, `RoomDoorEvent`, `ObjectsRollingEvent`, `FurnitureFloorEvent`, …

Die **Source-Map `main.js.map`** enthält jedoch die vollständigen `sourcesContent` für alle diese Module. Ohne die Source-Map läuft der Client nicht; mit ihr kann der Browser die fehlenden Module zur Laufzeit nicht rekonsturieren (Source-Maps dienen nur dem Debugger). Das deutet auf einen **kaputten oder abgeschnittenen Build** hin.

---

## 2. Kommunikationssystem

### 2.1 Initialisierung

```
NitroCommunicationManager.onInit()
    → SocketConnection.init('ws://localhost:2096')
    → SocketConnection.registerMessages(NitroMessages)
    → MessageClassManager.registerMessages({ events, composers })
```

`NitroMessages` ist ein Objekt mit zwei `Map`s:
- `events`: `Map<number, typeof MessageEvent>` — eingehende Header → Event-Klasse
- `composers`: `Map<number, typeof MessageComposer>` — ausgehende Header → Composer-Klasse

### 2.2 `MessageClassManager` (main.js L23646–23754)

```typescript
class MessageClassManager {
    _messageIdByEvent: Map<Class, number>
    _messageIdByComposer: Map<Class, number>
    _messageInstancesById: Map<number, MessageEvent[]>

    registerMessages(configuration) {
        for (const [header, handler] of configuration.events)
            this.registerMessageEventClass(header, handler)
        for (const [header, handler] of configuration.composers)
            this.registerMessageComposerClass(header, handler)
    }

    registerMessageEvent(event) {
        const header = this.getEventId(event)  // aus der Event-Klasse
        let existing = this._messageInstancesById.get(header)
        if (!existing) {
            existing = []
            this._messageInstancesById.set(header, existing)
            event.parser = new event.parserClass()
        } else {
            event.parser = existing[0].parser
        }
        existing.push(event)
    }

    getEvents(header) {
        return this._messageInstancesById.get(header)
    }
}
```

Das bedeutet:
- **Jeder Header** kann mehrere Callbacks haben (Array in `_messageInstancesById`).
- **Parser-Instanzen** werden pro Header geteilt.
- Empfangene Pakete werden per Header aufgelöst und an alle registrierten `MessageEvent`-Callbacks weitergegeben.

### 2.3 `SocketConnection`

- Verbindet sich mit `config.socket.url`.
- Sendet Composers per `connection.send(composer)`.
- Empfängt binäre Pakete und delegiert an `MessageClassManager.getEvents(header)`.
- Eingehende Events erhalten automatisch `.connection` gesetzt.

### 2.4 `MessageEvent` und `MessageComposer`

```typescript
class MessageEvent {
    constructor(callBack, parserClass)
    getParser() { return this.parser }
}

class MessageComposer {
    constructor(...args)
    getMessageArray(): any[]   // wird serialisiert
    dispose()
}
```

Jeder Composer implementiert `getMessageArray()` und gibt ein Array von Werten zurück, das `EvaWireFormat` serialisiert.

---

## 3. Ausgehende Header (`OutgoingHeader`)

Alle 185 ausgehenden Header sind in `main.js` L29619–29850 definiert. Sie bilden den kompletten Satz an Paketen, die der Client an den Emulator senden kann.

### 3.1 Komplette Tabelle

| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `RELEASE_VERSION` | 4000 | Client-Version senden (alt) |
| `SECURITY_TICKET` | 2419 | SSO-Ticket senden |
| `CLIENT_PONG` | 2596 | Pong-Antwort |
| `CLIENT_LATENCY` | 295 | Latenz-Messung |
| `CLIENT_VARIABLES` | 1053 | Client-Variablen |

#### Navigator
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `NAVIGATOR_INIT` | 2110 | Kann-Raum-erstellen + Einstellungen anfordern |
| `NAVIGATOR_SEARCH` | 249 | Raumsuche |
| `NAVIGATOR_SEARCH_OPEN` | 637 | Suche öffnen |
| `NAVIGATOR_SEARCH_CLOSE` | 1834 | Suche schließen |
| `NAVIGATOR_SEARCH_SAVE` | 2226 | Suche speichern |
| `NAVIGATOR_CATEGORIES` | 3027 | Kategorien anfordern |
| `NAVIGATOR_SETTINGS` | 1782 | Navigator-Einstellungen anfordern |
| `NAVIGATOR_SETTINGS_SAVE` | 3159 | Navigator-Einstellungen speichern |
| `ROOM_CREATE` | 2752 | Raum erstellen |
| `ROOM_DELETE` | 532 | Raum löschen |
| `ROOM_INFO` | 2230 | Rauminfo anfordern |
| `ROOM_ENTER` | 2312 | Raum betreten |
| `ROOM_MODEL` | 2300 | Raum-Modell anfordern |
| `ROOM_FAVORITE` | 3817 | Favorisieren |
| `ROOM_FAVORITE_REMOVE` | 309 | Favorit entfernen |
| `ROOM_LIKE` | 3582 | Raum liken |
| `ROOM_SETTINGS` | 3129 | Raumeinstellungen anfordern |
| `ROOM_SETTINGS_SAVE` | 1969 | Raumeinstellungen speichern |
| `ROOM_RIGHTS_GIVE` | 808 | Rechte geben |
| `ROOM_RIGHTS_REMOVE` | 2064 | Rechte entfernen |
| `ROOM_RIGHTS_REMOVE_ALL` | 2683 | Alle Rechte entfernen |
| `ROOM_KICK` | 1320 | User kicken |
| `ROOM_MUTE` | 3637 | Raum stummschalten |
| `ROOM_MUTE_USER` | 3485 | User stummschalten |
| `ROOM_BAN_GIVE` | 1477 | User bannen |
| `ROOM_DOORBELL` | 1644 | Klingeln |

#### Raum-Möbel (Placement & Interaktion)
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `FURNITURE_PLACE` | 1258 | **Möbel platzieren** |
| `FURNITURE_PICKUP` | 3456 | **Möbel aufheben** |
| `FURNITURE_FLOOR_UPDATE` | 248 | Möbel verschieben/rotieren (Floor) |
| `FURNITURE_WALL_UPDATE` | 168 | Wand-Möbel verschieben |
| `FURNITURE_MULTISTATE` | 99 | Möbel-State wechseln |
| `FURNITURE_WALL_MULTISTATE` | 210 | Wand-Möbel-State wechseln |
| `FURNITURE_RANDOMSTATE` | 3617 | Zufalls-State |
| `FURNITURE_ALIASES` | 3898 | Möbel-Aliase anfordern |
| `ITEM_PAINT` | 711 | Farbe auftragen |
| `ITEM_STACK_HELPER` | 3839 | Stacking-Höhe setzen |
| `ITEM_DIMMER_SETTINGS` | 2813 | Dimmer-Einstellungen anfordern |
| `ITEM_DIMMER_SAVE` | 1648 | Dimmer speichern |
| `ITEM_DIMMER_TOGGLE` | 2296 | Dimmer an/aus |
| `ITEM_DICE_CLICK` | 1990 | Würfel klicken |
| `ITEM_EXCHANGE_REDEEM` | 3115 | Einlösen |
| `PRESENT_OPEN_PRESENT` | 3558 | Geschenk öffnen |
| `LOVELOCK_START_CONFIRM` | 3775 | LoveLock bestätigen |
| `MANNEQUIN_SAVE_NAME` | 2850 | Mannequin-Name speichern |
| `MANNEQUIN_SAVE_LOOK` | 2209 | Mannequin-Look speichern |
| `ROOM_TONER_APPLY` | 2880 | Toner anwenden |
| `REMOVE_WALL_ITEM` | 3336 | Wand-Möbel entfernen |
| `MODIFY_WALL_ITEM_DATA` | 3666 | Wand-Möbel-Daten ändern |

#### Bot / Pet
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `BOT_PLACE` | 1592 | Bot platzieren |
| `BOT_PICKUP` | 3323 | Bot aufheben |
| `BOT_CONFIGURATION` | 1986 | Bot-Konfiguration anfordern |
| `BOT_SKILL_SAVE` | 2624 | Bot-Skill speichern |
| `PET_PLACE` | 2647 | Pet platzieren |
| `PET_PICKUP` | 1581 | Pet aufheben |
| `PET_INFO` | 2934 | Pet-Info anfordern |
| `PET_RESPECT` | 3202 | Pet respektieren |
| `PET_RIDE` | 1036 | Pet reiten |
| `PET_MOVE` | 3449 | Pet bewegen |

#### Katalog
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `CATALOG_MODE` | 1195 | Katalog-Modus/Index anfordern |
| `CATALOG_PAGE` | 412 | Katalog-Seite anfordern |
| `CATALOG_PURCHASE` | 3492 | Kauf durchführen |
| `CATALOG_PURCHASE_GIFT` | 1411 | Kauf als Geschenk |
| `CATALOG_SEARCH` | 2594 | Katalog-Suche |
| `CATALOG_CLUB` | 3285 | Club-Angebote |
| `CATALOG_CLUB_GIFTS` | 487 | Club-Geschenke |
| `CATALOG_REDEEM_VOUCHER` | 339 | Voucher einlösen |
| `CATALOG_SELECT_VIP_GIFT` | 2276 | VIP-Geschenk auswählen |
| `GIFT_CONFIG` | 418 | Geschenk-Konfiguration anfordern |
| `MARKETPLACE_CONFIG` | 2597 | Marketplace-Konfiguration |

#### Inventar
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `USER_FURNITURE` | 3150 | Möbel-Inventar anfordern (im Raum) |
| `USER_FURNITURE2` | 3500 | Möbel-Inventar anfordern (außerhalb Raum) |
| `USER_PETS` | 3095 | Pet-Inventar |
| `USER_BOTS` | 3848 | Bot-Inventar |
| `USER_BADGES` | 2769 | Badges anfordern |
| `USER_BADGES_CURRENT` | 2091 | Aktuelle Badges |
| `USER_BADGES_CURRENT_UPDATE` | 644 | Aktuelle Badges aktualisieren |
| `REQUEST_BADGES` | - | Badges anfordern (siehe `app`) |

#### Avatar / User
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `UNIT_WALK` | 3320 | Laufen |
| `UNIT_CHAT` | 1314 | Chat |
| `UNIT_CHAT_SHOUT` | 2085 | Schreien |
| `UNIT_CHAT_WHISPER` | 1543 | Flüstern |
| `UNIT_DANCE` | 2080 | Tanz |
| `UNIT_ACTION` | 2456 | Aktion/Emote |
| `UNIT_SIGN` | 1975 | Schild |
| `UNIT_POSTURE` | 2235 | Haltung (sit/lie/stand) |
| `UNIT_LOOK` | 3301 | Blickrichtung |
| `UNIT_TYPING` | 1597 | Tippt beginnen |
| `UNIT_TYPING_STOP` | 1474 | Tippt stoppen |
| `UNIT_DROP_HAND_ITEM` | 2814 | Hand-Item fallen lassen |
| `UNIT_GIVE_HANDITEM` | 2941 | Hand-Item geben |
| `USER_FIGURE` | 2730 | Aussehen ändern |
| `USER_MOTTO` | 2228 | Motto ändern |
| `USER_HOME_ROOM` | 1740 | Home-Room setzen |
| `USER_INFO` | 357 | User-Info anfordern |
| `USER_PROFILE` | 3265 | Profil anfordern |
| `USER_RESPECT` | 2694 | Respekt geben |
| `USER_SETTINGS` | 2388 | Einstellungen anfordern |
| `USER_SETTINGS_CAMERA` | 1461 | Kamera-Follow |
| `USER_SETTINGS_CHAT_STYLE` | 1030 | Chat-Stil |
| `USER_SETTINGS_INVITES` | 1086 | Raum-Einladungen |
| `USER_SETTINGS_OLD_CHAT` | 1262 | Old-Chat |
| `USER_SETTINGS_VOLUME` | 1367 | Lautstärke |
| `USER_SUBSCRIPTION` | 3166 | Abonnement-Info |
| `USER_OUTFIT_SAVE` | 800 | Outfit speichern |
| `USER_OUTFITS` | 2742 | Outfits anfordern |
| `USER_WARDROBE_PAGE` | 2742 | Garderobe-Seite |
| `USER_WARDROBE_SAVE` | 800 | Garderobe speichern |
| `USER_VISIT` | 2970 | User besuchen |
| `USER_IGNORE` | 1117 | Ignorieren |
| `USER_UNIGNORE` | 2061 | Nicht mehr ignorieren |
| `USER_IGNORE_ID` | 3314 | Per ID ignorieren |

#### Messenger / Freunde
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `MESSENGER_INIT` | 2781 | Messenger initialisieren |
| `MESSENGER_FRIENDS` | 1523 | Freundesliste anfordern |
| `MESSENGER_CHAT` | 3567 | Privatnachricht senden |
| `MESSENGER_REQUEST` | 3157 | Freundschaftsanfrage senden |
| `MESSENGER_REQUESTS` | 2448 | Anfragen anfordern |
| `MESSENGER_ACCEPT` | 137 | Anfrage annehmen |
| `MESSENGER_DECLINE` | 2890 | Anfrage ablehnen |
| `MESSENGER_REMOVE` | 1689 | Freund entfernen |
| `MESSENGER_FOLLOW` | 3997 | Freund folgen |
| `MESSENGER_SEARCH` | 1210 | User suchen |
| `MESSENGER_UPDATES` | 1419 | Updates anfordern |
| `MESSENGER_RELATIONSHIPS` | 2138 | Beziehungen |
| `MESSENGER_RELATIONSHIPS_UPDATE` | 3768 | Beziehung ändern |
| `MESSENGER_ROOM_INVITE` | 1276 | Raum-Einladung senden |
| `FIND_FRIENDS` | 516 | Freunde finden |

#### Moderation
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `MOD_TOOL_USER_INFO` | 3295 | User-Info anfordern |
| `MODTOOL_REQUEST_ROOM_INFO` | 707 | Raum-Info anfordern |
| `MODTOOL_REQUEST_ROOM_CHATLOG` | 2587 | Raum-Chatlog |
| `MODTOOL_REQUEST_USER_CHATLOG` | 1391 | User-Chatlog |
| `MODTOOL_REQUEST_USER_ROOMS` | 3526 | User-Räume |
| `MODTOOL_CHANGE_ROOM_SETTINGS` | 3260 | Raum-Einstellungen ändern |
| `MODTOOL_SANCTION_ALERT` | 229 | Verwarnung |
| `MODTOOL_SANCTION_KICK` | 2582 | Kick |
| `MODTOOL_SANCTION_BAN` | 2766 | Ban |
| `MODTOOL_SANCTION_MUTE` | 1945 | Mute |
| `MODTOOL_SANCTION_TRADELOCK` | 3742 | Trade-Lock |
| `MODTOOL_ROOM_ALERT` | 3842 | Raum-Alert |
| `MODTOOL_ALERTEVENT` | 1840 | Event-Alert |

#### Gruppen
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `GROUP_CREATE_OPTIONS` | 798 | Erstellungsoptionen |
| `GROUP_BUY` | 230 | Gruppe kaufen |
| `GROUP_INFO` | 2991 | Info anfordern |
| `GROUP_SETTINGS` | 1004 | Einstellungen |
| `GROUP_MEMBERS` | 312 | Mitglieder |
| `GROUP_MEMBERSHIPS` | 367 | Mitgliedschaften |
| `GROUP_REQUEST` | 998 | Beitritt anfragen |
| `GROUP_REQUEST_ACCEPT` | 3386 | Beitritt annehmen |
| `GROUP_REQUEST_DECLINE` | 1894 | Beitritt ablehnen |
| `GROUP_ADMIN_ADD` | 2894 | Admin ernennen |
| `GROUP_ADMIN_REMOVE` | 722 | Admin entfernen |
| `GROUP_MEMBER_REMOVE` | 593 | Mitglied entfernen |
| `GROUP_DELETE` | 1134 | Gruppe löschen |
| `GROUP_SAVE_INFORMATION` | 3137 | Info speichern |
| `GROUP_SAVE_BADGE` | 1991 | Badge speichern |
| `GROUP_SAVE_COLORS` | 1764 | Farben speichern |
| `GROUP_SAVE_PREFERENCES` | 3435 | Einstellungen speichern |
| `GROUP_PARTS` | 813 | Badge-Parts |

#### Wired
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `WIRED_OPEN` | 768 | Wired öffnen |
| `WIRED_TRIGGER_SAVE` | 1520 | Trigger speichern |
| `WIRED_CONDITION_SAVE` | 3203 | Bedingung speichern |
| `WIRED_ACTION_SAVE` | 2281 | Aktion speichern |
| `WIRED_APPLY_SNAPSHOT` | 3373 | Snapshot anwenden |

#### Sonstige
| Konstante | Header | Bedeutung |
|-----------|--------|-----------|
| `REPORT` | 1691 | Melden |
| `CAMERA_SAVE` | 3226 | Kamera-Bild speichern |
| `CAMERA_THUMBNAIL` | 1982 | Kamera-Thumbnail |
| `CAMERA_PRICE` | 796 | Kamera-Preis |
| `EVENT_TRACKER` | 3457 | Event-Tracking |
| `ACHIEVEMENT_LIST` | 219 | Achievements |
| `FIND_FRIENDS` | 516 | Freunde finden |
| `CONVERT_GLOBAL_ROOM_ID` | 314 | Globale Raum-ID konvertieren |
| `GET_ITEM_DATA` | 3964 | Item-Daten |
| `ONE_WAY_DOOR_CLICK` | 2765 | Einbahn-Tür |
| `ITEM_COLOR_WHEEL_CLICK` | 2144 | Farbrad |
| `ITEM_DICE_CLOSE` | 1533 | Würfel schließen |
| `ITEM_CLOTHING_REDEEM` | 3374 | Kleidung einlösen |
| `ITEM_SAVE_BACKGROUND` | 3608 | Hintergrund speichern |
| `DISCOUNT_CONFIG` | 223 | Rabatt-Konfiguration |
| `CLIENT_POLICY` | 26979 | Client-Richtlinie |
| `DESKTOP_CAMPAIGNS` | 2912 | Kampagnen |
| `DESKTOP_NEWS` | 1827 | News |
| `DESKTOP_VIEW` | 105 | Desktop-Ansicht |
| `CLIENT_TOOLBAR_TOGGLE` | 2313 | Toolbar umschalten |
| `CLIENT_LATENCY_MEASURE` | 96 | Latenz messen |
| `SECURITY_MACHINE` | 2490 | Security-Maschine |
| `INVENTORY_UNKNOWN` | 1371 | Unbekannt |

---

## 4. Eingehende Header (`IncomingHeader`)

### 4.1 Problem: Fehlt im Bundle

Die Datei `src/client/nitro/communication/messages/incoming/IncomingHeader.ts` ist **nicht im `main.js`-Bundle enthalten**. Die Header-Zuordnung erfolgt stattdessen über die `NitroMessages`-Klasse, die ebenfalls fehlt.

### 4.2 Bekannte eingehende Events (direkte Beobachtung)

Aus den Handler-Registrierungen und Widgets lassen sich folgende eingehende Header ableiten (die exakten Zahlen müssen aus der Source-Map extrahiert werden):

| Event-Klasse | Vermutlicher Header | Auslöser/Wirkung |
|--------------|---------------------|------------------|
| `AuthenticatedEvent` | 2491 | Login erfolgreich |
| `UserInfoEvent` | 2725 | Eigenes User-Objekt |
| `UserFigureEvent` | - | Figure-Update |
| `UserSettingsEvent` | - | User-Einstellungen |
| `UserCreditsEvent` | - | Credits-Update |
| `UserCurrencyEvent` | - | Währungen |
| `UserPerksEvent` | - | Perks |
| `UserPermissionsEvent` | - | Rechte |
| `UserSubscriptionEvent` | - | HC/VIP-Status |
| `AvailabilityStatusMessageEvent` | - | Hotel-Verfügbarkeit |
| `RoomEnterEvent` | 758 | Raum-Eingang bestätigt |
| `RoomEnterErrorEvent` | - | Fehler beim Betreten |
| `RoomModelNameEvent` | - | Modell-Name |
| `RoomModelEvent` | 1301 | Raum-Modell/Heightmap |
| `RoomPaintEvent` | - | Boden/Wand-Farben |
| `RoomThicknessEvent` | - | Wanddicke |
| `RoomHeightMapEvent` | 2753 | Höhenkarte |
| `RoomHeightMapUpdateEvent` | - | Höhenkarten-Update |
| `RoomDoorEvent` | 1664 | Tür-Position |
| `RoomInfoEvent` | - | Raum-Info |
| `FurnitureFloorEvent` | 1778 | Floor-Items-Liste |
| `FurnitureFloorAddEvent` | - | Floor-Item hinzugefügt |
| `FurnitureFloorUpdateEvent` | - | Floor-Item aktualisiert |
| `FurnitureFloorRemoveEvent` | - | Floor-Item entfernt |
| `FurnitureWallEvent` | - | Wall-Items |
| `RoomUnitEvent` | - | User im Raum |
| `RoomUnitRemoveEvent` | - | User verlässt Raum |
| `RoomUnitStatusEvent` | - | Status-Update (x,y,z,posture) |
| `RoomUnitChatEvent` | 1314 | Chat |
| `RoomUnitDanceEvent` | - | Tanz |
| `RoomUnitExpressionEvent` | - | Emote |
| `CatalogPageEvent` | 804 | Katalog-Seite |
| `CatalogPurchaseEvent` | 869 | Kauf-OK |
| `MessengerInitEvent` | 1605 | Messenger-Init |
| `FriendListUpdateEvent` | 2013 | Freundesliste |
| `NavigatorSearchEvent` | - | Suchergebnisse |
| `NavigatorSettingsEvent` | - | Navigator-Einstellungen |
| `GenericErrorEvent` | - | Generische Fehler |
| `FloodControlEvent` | - | Chat-Flood |

**Empfohlene Aktion:** Die `IncomingHeader.ts` aus der Source-Map extrahieren mit:
```bash
node -e "const m=require('C:/xampp/htdocs/nitro/main.js.map'); const i=m.sources.findIndex(s=>s.endsWith('IncomingHeader.ts')); console.log(m.sourcesContent[i]);" > IncomingHeader.ts
```

---

## 5. Raum-Engine & Session-Handling

### 5.1 `RoomSessionManager` (main.js L35512)

Zentrale Verwaltung der aktuellen Raumsession.

```typescript
class RoomSessionManager {
    _sessions: Map<string, RoomSession>
    _handlers: BaseHandler[]
    _pendingSession: RoomSession
    _viewerSession: RoomSession

    createHandlers() {
        this._handlers = [
            new RoomChatHandler(connection, this),
            new RoomDataHandler(connection, this),  // Anm.: im Subagent als RoomMessageHandler geführt
            new RoomDimmerPresetsHandler(connection, this),
            new RoomPermissionsHandler(connection, this),
            new RoomSessionHandler(connection, this),
            new RoomUsersHandler(connection, this),
            new RoomPresentHandler(connection, this),
            new GenericErrorHandler(connection, this)
        ]
    }

    createSession(roomId, password = null) {
        const session = new RoomSession()
        session.roomId = roomId
        session.password = password
        return this.addSession(session)
    }

    getRoomId(id) {
        return 'hard_coded_room_id'  // bemerkenswerter Workaround
    }
}
```

**Wichtig:** `getRoomId()` gibt immer `'hard_coded_room_id'` zurück. Der Client verwendet intern nur **einen** Raum-Instance-Slot, unabhängig von der tatsächlichen Raum-ID.

### 5.2 `RoomSession` (main.js L41479)

Repräsentiert den Zustand im aktuellen Raum.

```typescript
class RoomSession extends Disposable {
    _roomId: number
    _password: string
    _state: RoomSessionEvent.CREATED | STARTED | ENDED
    _tradeMode: RoomTradingLevelEnum
    _doorMode: number
    _controllerLevel: RoomControllerLevel
    _ownRoomIndex: number
    _isRoomOwner: boolean
    _isGuildRoom: boolean
    _userData: UserDataManager

    start() {
        if (this._state !== CREATED || !this._connection) return false
        this._state = STARTED
        return this.enterRoom()
    }

    enterRoom() {
        this._connection.send(new RoomEnterComposer(this._roomId, this._password))
    }

    sendChatMessage(text, styleId) { send(RoomUnitChatComposer(text, styleId)) }
    sendShoutMessage(text, styleId) { send(RoomUnitChatShoutComposer(text, styleId)) }
    sendWhisperMessage(recipient, text, styleId) { send(RoomUnitChatWhisperComposer(recipient, text, styleId)) }
    sendDanceMessage(danceId) { send(RoomUnitDanceComposer(danceId)) }
    sendExpressionMessage(expression) { send(RoomUnitActionComposer(expression)) }
    sendSignMessage(sign) { send(RoomUnitSignComposer(sign)) }
    sendPostureMessage(posture) { send(RoomUnitPostureComposer(posture)) }
    sendKickMessage(userId) { send(RoomKickUserComposer(userId)) }
    sendMuteMessage(userId, minutes) { send(RoomMuteUserComposer(userId, minutes, this._roomId)) }
    sendBanMessage(userId, type) { send(RoomBanUserComposer(userId, this._roomId, type)) }
    sendGiveRightsMessage(userId) { send(RoomGiveRightsComposer(userId)) }
    sendTakeRightsMessage(userId) { send(RoomTakeRightsComposer(userId)) }
    pickupPet(id) { send(PetRemoveComposer(id)) }
    pickupBot(id) { send(BotRemoveComposer(id)) }
    updateMoodlightData(...) { send(MoodlightSettingsSaveComposer(...)) }
}
```

### 5.3 `RoomEngine` (main.js L10343)

Die zentrale Render- und Interaktions-Engine.

**Wichtige Methoden:**

| Methode | Funktion |
|---------|----------|
| `onInit()` | Initialisiert `RoomManager`, `RoomContentLoader`, Ticker, Visibility-Listener |
| `createRoomInstance(roomId, roomMap)` | Erstellt PixiJS-Instanz mit Höhenkarte |
| `setupRoomInstance(...)` | Setzt Wände, Boden, Landschaft |
| `setActiveRoomId(roomId)` | Aktiven Raum setzen |
| `processRoomObjectEvent(event)` | Maus-Events auf Objekte verarbeiten |
| `processRoomObjectOperation(id, category, operation)` | Verschieben/Rotieren/Picken ausführen |
| `useRoomObject(id, category)` | Möbel benutzen |
| `getRoomInstance(roomId)` | Instanz abrufen |
| `removeRoomInstance(roomId)` | Instanz zerstören |

**Objekt-Kategorien (`RoomObjectCategory`):**
- `ROOM = 0`
- `FLOOR = 1`
- `WALL = 2`
- `UNIT = 3`
- `CURSOR = 4`

**Objekt-Operationen (`RoomObjectOperationType`):**
- `OBJECT_MOVE`
- `OBJECT_PLACE`
- `OBJECT_ROTATE_POSITIVE`
- `OBJECT_PICKUP`
- `OBJECT_PICKUP_BOT`
- `OBJECT_PICKUP_PET`
- `OBJECT_EJECT`
- `OBJECT_SAVE_STUFF_DATA`

### 5.4 `RoomMessageHandler` (main.js L17386)

Verarbeitet fast alle raumbezogenen eingehenden Pakete und aktualisiert die `RoomEngine`.

Registrierte Events (Auswahl):
- `UserInfoEvent` → eigene User-ID speichern
- `RoomModelNameEvent` → Modell-Name setzen; initial sendet es `FurnitureAliasesComposer`, danach `RoomModelComposer`
- `RoomPaintEvent` → Boden/Wand/Landschafts-Farben aktualisieren
- `RoomModelEvent` → TileMap aufbauen, Türen finden, `createRoomInstance(roomMap)`
- `RoomHeightMapEvent` → `FurnitureStackingHeightMap` setzen
- `RoomHeightMapUpdateEvent` → einzelne Tiles aktualisieren
- `RoomThicknessEvent` → Wanddicke setzen
- `RoomDoorEvent` → Eingangsposition
- `FurnitureAliasesEvent` → Möbel-Aliase
- `FurnitureFloorAddEvent` → Floor-Item hinzufügen
- `FurnitureFloorEvent` → Floor-Items-Liste
- `FurnitureFloorUpdateEvent` → Floor-Item aktualisieren
- `FurnitureFloorRemoveEvent` → Floor-Item entfernen
- `FurnitureWallEvent` / Add / Update / Remove
- `FurnitureDataEvent` / `FurnitureItemDataEvent`
- `FurnitureStateEvent` / `FurnitureState2Event`
- `RoomUnitEvent` → User erscheint
- `RoomUnitRemoveEvent` → User verschwindet
- `RoomUnitStatusEvent` → Position/Haltung aktualisieren
- `RoomUnitChatEvent` → Chat anzeigen
- `RoomUnitDanceEvent` → Tanz
- `RoomUnitExpressionEvent` → Emote
- `RoomUnitInfoEvent` → User-Info
- `PetFigureUpdateEvent`
- `YouArePlayingGameEvent`
- `IgnoreResultEvent`

### 5.5 Handler-Struktur (`BaseHandler`)

```typescript
class BaseHandler {
    constructor(connection, listener)
    setRoomId(roomId)
    getRoomSession()
}
```

Jeder Handler registriert beim Konstruktor die für ihn relevanten `MessageEvent`s. Die Listener (`RoomSessionManager` oder Widget-Handler) reagieren dann auf die geparsten Daten.

---

## 6. Möbel-System: Placement, Pickup, Move, Rotate

### 6.1 Zentrale Dateien

| Klasse | Pfad | Funktion |
|--------|------|----------|
| `FurniturePlaceComposer` | `communication/messages/outgoing/room/furniture/FurniturePlaceComposer.ts` | Platzierung senden |
| `FurnitureFloorUpdateComposer` | `communication/messages/outgoing/room/furniture/floor/FurnitureFloorUpdateComposer.ts` | Floor-Item Update senden |
| `FurniturePickupComposer` | `communication/messages/outgoing/room/furniture/FurniturePickupComposer.ts` | **Fehlt im Bundle!** |
| `FurnitureWallUpdateComposer` | `communication/messages/outgoing/room/furniture/FurnitureWallUpdateComposer.ts` | Wand-Möbel Update |

### 6.2 `FurniturePlaceComposer` (main.js L33–65)

```typescript
class FurniturePlaceComposer {
    constructor(itemId, category, wallLocation, x, y, direction)

    getMessageArray() {
        switch (this._category) {
            case RoomObjectCategory.FLOOR:
                return [`${this._itemId} ${this._x} ${this._y} ${this._direction}`]
            case RoomObjectCategory.WALL:
                return [`${this._itemId} ${this._wallLocation} `]
            default:
                return []
        }
    }
}
```

**Kritisch für den Emulator:**
- **Floor-Placement** sendet **einen einzigen String**: `"itemId x y direction"`.
- **Wall-Placement** sendet ebenfalls einen String: `"itemId wallLocation "`.
- Der Lubba-Emulator erwartet für `FURNITURE_PLACE` (1258) aber eine **binäre Struktur** oder zumindest einzelne Integers. Das ist ein Protokoll-Mismatch.

### 6.3 `FurnitureFloorUpdateComposer` (main.js L59032)

```typescript
class FurnitureFloorUpdateComposer {
    constructor(itemId, x, y, direction)
    getMessageArray() {
        return [itemId, x, y, direction]
    }
}
```

Hier werden **vier separate Integer-Werte** gesendet. Der Emulator behandelt Header `248` korrekt als binär `[itemId:4][x:4][y:4][rotation:4]`.

### 6.4 Pickup-Flow im Client

1. User klickt auf Möbel → Infostand öffnet sich.
2. Button "Pickup" → `RoomInfoStandFurniComponent.processButtonAction('pickup')`.
3. Wenn `pickupMode === PICKUP_MODE_FULL`, wird `RoomWidgetFurniActionMessage.RWFAM_PICKUP` erzeugt.
4. `InfoStandWidgetHandler` verarbeitet die Nachricht:
   ```typescript
   case RWFAM_PICKUP:
       roomEngine.processRoomObjectOperation(id, category, OBJECT_PICKUP)
       break
   ```
5. `RoomEngine.processRoomObjectOperation` delegiert an `RoomObjectEventHandler`.
6. Im normalen Nitro würde `RoomObjectEventHandler` nun `FurniturePickupComposer` senden.

**Problem:** `FurniturePickupComposer` ist im Bundle **nicht definiert** und `FURNITURE_PICKUP` (3456) wird in `main.js` nur im `OutgoingHeader`-Enum erwähnt, aber nirgendwo instanziiert. Das bedeutet:
- Entweder der Client kann in dieser Build-Version **gar nicht** Möbel aufheben.
- Oder der Pickup-Flow ist unvollständig und müsste in der Source-Map ergänzt werden.

**Fazit für den Emulator:** Selbst wenn der Emulator einen Handler für `3456` hätte, sendet dieser Client-Build den Header nicht. Das erklärt, warum im Emulator-Log `Unhandled Nitro packet: 3456` **nicht** erscheint, sondern nur `1258` (Placement) unbehandelt ist — denn Placement wird tatsächlich gesendet.

### 6.5 Move / Rotate Flow

- Maus-Event `REQUEST_MOVE`/`REQUEST_ROTATE` → `RoomEngine.processRoomObjectOperation(id, category, OBJECT_MOVE / OBJECT_ROTATE_POSITIVE)`.
- `FurnitureFloorUpdateComposer` wird gesendet (Header 248).
- Der Emulator verarbeitet diesen Header bereits.

---

## 7. Avatar-System

### 7.1 Zentrale Klassen

| Klasse | Funktion |
|--------|----------|
| `AvatarRenderManager` | Verwaltet Avatar-Assets, Geometry, FigureMap, EffectMap |
| `AvatarImage` | Rendert einzelnen Avatar |
| `AvatarStructure` | Verarbeitet FigureData |
| `AvatarFigureContainer` | Hält aktuelle Figure-Teile |
| `FigureDataView` | UI-Datenmodell für Avatar-Editor |
| `AvatarAssetDownloadManager` | Lädt `.nitro`-Asset-Bundles |

### 7.2 Asset-Ladung

- `avatar.figuredata.url`: `http://localhost/swf/gamedata/figuredata.xml`
- `avatar.figuremap.url`: `/nitro/gamedata/json/FigureMap.json`
- `avatar.effectmap.url`: `/nitro/gamedata/json/EffectMap.json`
- `avatar.asset.url`: `/nitro/figure/%libname%.nitro`
- `avatar.asset.effect.url`: `/nitro/effect/%libname%.nitro`

### 7.3 Figure-Update

- Ausgehend: `USER_FIGURE` (2730) sendet neues Figure-String.
- Eingehend: `UserFigureEvent` aktualisiert den lokalen Avatar.

---

## 8. Katalog-System

### 8.1 Zentrale Dateien

| Klasse | Pfad | Funktion |
|--------|------|----------|
| `CatalogService` | `app/components/catalog/services/catalog.service.ts` | Angular-Service |
| `CatalogPageComposer` | `communication/messages/outgoing/catalog/CatalogPageComposer.ts` | Seite anfordern |
| `CatalogPurchaseComposer` | `communication/messages/outgoing/catalog/CatalogPurchaseComposer.ts` | Kauf senden |
| `CatalogModeComposer` | `communication/messages/outgoing/catalog/CatalogModeComposer.ts` | Index anfordern |
| `CatalogSearchComposer` | `communication/messages/outgoing/catalog/CatalogSearchComposer.ts` | Suche |
| `CatalogPageOfferData` | `communication/messages/parser/catalog/utils/CatalogPageOfferData.ts` | Angebots-Daten |
| `CatalogPurchaseEvent` / `CatalogPageEvent` | incoming/catalog | Kauf-OK / Seiten-Daten |

### 8.2 Katalog-Init-Flow

1. UI öffnet Katalog.
2. `CatalogService` sendet `CatalogModeComposer` (Header 1195).
3. Server antwortet mit Katalog-Index.
4. `CatalogService` sendet `CatalogPageComposer` (Header 412) für die gewählte Seite.
5. Server antwortet mit `CatalogPageEvent` (Header 804) + einzelnen Offers.
6. `CatalogPageOfferData` parst Produkte, Preise, Currencies.

### 8.3 Kauf-Flow

1. User wählt Offer und Menge.
2. `CatalogPurchaseComponent` sendet `CatalogPurchaseComposer` (Header 3492) mit:
   - pageId
   - offerId
   - extraParameter (optional, z. B. Farbe)
   - quantity
3. Server antwortet mit `CatalogPurchaseEvent` (Header 869) bei Erfolg.
4. `CatalogService` zeigt Erfolg oder Fehler an.

---

## 9. Inventar-System

### 9.1 Zentrale Dateien

| Klasse | Pfad | Funktion |
|--------|------|----------|
| `FurnitureService` | `app/components/inventory/services/furniture.service.ts` | UI-Logik |
| `FurnitureListComposer` | `communication/messages/outgoing/inventory/furni/FurnitureListComposer.ts` | Inventar anfordern |
| `FurnitureList2Composer` | `communication/messages/outgoing/inventory/furni/FurnitureList2Composer.ts` | Inventar anfordern (außerhalb Raum) |
| `FurnitureListItemParser` | `communication/messages/parser/inventory/furniture/utils/FurnitureListItemParser.ts` | Item parsen |
| `FurnitureListAddOrUpdateEvent` | incoming/inventory/furni | Hinzufügen/Update |
| `FurnitureListRemovedEvent` | incoming/inventory/furni | Entfernen |

### 9.2 Inventar-Anforderung

- **Im Raum:** Header `3150` (`USER_FURNITURE`)
- **Außerhalb Raum:** Header `3500` (`USER_FURNITURE2`)

**Hinweis:** Der Lubba-Emulator vertauscht in Kommentaren Header `3150` und `2769` (Bot/Pet). Tatsächlich ist `3150` laut Client das Möbel-Inventar.

### 9.3 Möbel platzieren aus dem Inventar

1. User zieht Möbel aus Inventar auf die Karte.
2. `FurnitureService` / Drag-Handler erzeugt `FurniturePlaceComposer`.
3. Header `1258` wird gesendet.
4. Server muss das Möbel erstellen und `FurnitureFloorAddEvent` zurücksenden.

---

## 10. Messenger & Freundesystem

### 10.1 Zentrale Dateien

| Klasse | Pfad | Funktion |
|--------|------|----------|
| `FriendListService` | `app/components/friendlist/services/friendlist.service.ts` | UI-Service |
| `MessengerInitEvent` | incoming/friendlist | Init-Daten |
| `FriendListUpdateEvent` | incoming/friendlist | Freundesliste |
| `NewFriendRequestEvent` | incoming/friendlist | Neue Anfrage |
| `RoomInviteEvent` / `RoomInviteErrorEvent` | incoming/friendlist | Raum-Einladung |
| `NewConsoleMessageParser` | parser/friendlist | Privatnachricht |
| `MessengerThread`, `MessengerChat` | app/common | Chat-Modell |

### 10.2 Init-Flow

1. Nach Login sendet Client `MESSENGER_INIT` (2781) und `MESSENGER_FRIENDS` (1523).
2. Server antwortet mit `MessengerInitEvent` (1605) und `FriendListUpdateEvent` (2013).
3. `FriendListService` baut Freunde- und Anfragen-Maps auf.

### 10.3 Aktionen

| Aktion | Header | Composer |
|--------|--------|----------|
| Freundschaftsanfrage senden | 3157 | `MessengerRequestComposer` |
| Anfrage annehmen | 137 | `MessengerAcceptComposer` |
| Anfrage ablehnen | 2890 | `MessengerDeclineComposer` |
| Freund entfernen | 1689 | `RemoveFriendComposer` |
| Privatnachricht | 3567 | `MessengerChatComposer` |
| Folgen | 3997 | `FollowFriendComposer` |
| User suchen | 1210 | `MessengerSearchComposer` |
| Raum einladen | 1276 | `SendRoomInviteComposer` |
| Beziehung setzen | 3768 | `SetRelationshipStatusComposer` |

---

## 11. Konfiguration & Asset-Ladung

### 11.1 `configuration.json` (Auswahl)

| Schlüssel | Wert | Bedeutung |
|-----------|------|-----------|
| `socket.url` | `ws://localhost:2096` | WebSocket-Endpunkt |
| `asset.url` | `http://localhost/nitro` | Basis-Asset-URL |
| `image.library.url` | `http://localhost/swf/c_images/` | Habbo-Bilder-Bibliothek |
| `furnidata.url` | `${gamedata.url}/json/FurnitureData.json` | Möbel-Daten |
| `productdata.url` | `${gamedata.url}/json/ProductData.json` | Produkt-Daten |
| `external.texts.url` | `${gamedata.url}/json/ExternalTexts.json` | Übersetzungen |
| `avatar.figuredata.url` | `http://localhost/swf/gamedata/figuredata.xml` | Avatar-XML |
| `avatar.figuremap.url` | `${gamedata.url}/json/FigureMap.json` | Avatar-Mapping |
| `avatar.effectmap.url` | `${gamedata.url}/json/EffectMap.json` | Effekt-Mapping |
| `furni.asset.url` | `${asset.url}/furniture/%libname%.nitro` | Möbel-Assets |
| `room.asset.url` | `${asset.url}/room/%libname%/%libname%.json` | Raum-Assets |
| `pet.asset.url` | `${asset.url}/pet/%libname%.nitro` | Pet-Assets |
| `badge.asset.url` | `${image.library.url}album1584/%badgename%.gif` | Badges |
| `catalog.asset.image.url` | `${catalog.asset.url}/%name%.gif` | Katalog-Bilder |
| `animation.fps` | 24 | Animations-FPS |
| `limits.fps` | true | FPS-Limit |
| `communication.pong.interval.ms` | 20000 | Pong-Intervall |
| `communication.pong.manually` | true | Pong manuell senden |
| `system.currency.types` | `[-1, 0, 5, 101]` | Unterstützte Währungen |
| `chat.input.maxlength` | 100 | Chat-Maximallänge |

### 11.2 `index.html` SSO-Initialisierung

```javascript
// 1. SSO aus URL oder per postMessage
var ssoParam = new URLSearchParams(window.location.search).get('sso')
if (!ssoParam) ssoParam = 'NITRO-0-4-0'

// 2. postMessage-Listener für Parent (CMS)
window.addEventListener('message', function(event) {
    if (event.data.type === 'SSO_TICKET' && event.data.sso) {
        ssoParam = event.data.sso
        window.NitroConfig = { configurationUrl: '/nitro/configuration.json', sso: ssoParam }
    }
})

// 3. Fallback nach 2 Sekunden
setTimeout(initNitro, 2000)
```

Der Client setzt also global `window.NitroConfig.sso`, das von `Nitro.ts` beim Start gelesen wird.

### 11.3 Asset-Bundles (`.nitro`)

- Möbel, Pets, Avatare und Effekte werden als `.nitro`-Dateien geladen.
- Format: vermutlich zlib-komprimierte JSON/SWF-ähnliche Daten (siehe `NitroBundle` in `client/core/asset`).
- Texturen werden als PNG/JSON-Spritesheets geladen.

### 11.4 `RoomContentLoader`

Lädt und cached:
- `FurnitureData.json` → Typ-zu-Classname-Mapping
- `FigureMap.json` → Avatar-Part-Mapping
- `EffectMap.json` → Effekt-Mapping
- Möbel-Assets on-demand
- Raum-Assets (`room/%name%/%name%.json`)

---

## 12. Wichtige UI-Komponenten & Widgets

### 12.1 Room-Widgets

| Widget | Handler | Funktion |
|--------|---------|----------|
| Chat | `ChatWidgetHandler` | Chat-Anzeige, Eingabe |
| InfoStand | `InfoStandWidgetHandler` | Möbel/User/Pet-Info |
| AvatarInfo | `AvatarInfoWidgetHandler` | Kontext-Menü |
| FurnitureChooser | `FurniChooserWidgetHandler` | Möbel-Auswahl |
| UserChooser | `UserChooserWidgetHandler` | User-Auswahl |
| RoomTools | `RoomToolsWidgetHandler` | Raum-Werkzeuge |
| Doorbell | `DoorbellWidgetHandler` | Türklingel |
| Dimmer | `FurnitureDimmerWidgetHandler` | Licht-Dimmer |
| Present | `FurniturePresentWidgetHandler` | Geschenk öffnen |
| FriendRequest | `FriendRequestHandler` | Freundschaftsanfragen |
| Stickies | eingebettet | Post-It-Notizen |
| HighScore | `FurnitureHighScoreWidgetHandler` | Highscore-Anzeige |
| CustomStackHeight | `FurnitureCustomStackHeightWidgetHandler` | Stacking-Höhe |

### 12.2 Widget-Nachrichten

Widgets kommunizieren über `RoomWidgetMessage`-Unterklassen:
- `RoomWidgetFurniActionMessage` (move, rotate, pickup, eject, use, save stuff data)
- `RoomWidgetUserActionMessage` (friend request, whisper, kick, ban, give rights, trade, …)
- `RoomWidgetRoomObjectMessage` (get info, get name)
- `RoomWidgetChangeMottoMessage`
- `RoomWidgetFurniToWidgetMessage` (open stickie, trophy, credit furni, …)

---

## 13. Sicherheits- & Protokoll-Fundstücke

### 13.1 SSO-Token-Handling

- `index.html` loggt den SSO-Token **unkritisch** in die Browser-Konsole.
- `templates/brain/nitro.php` übergibt das Ticket per URL **und** per `postMessage`.
- Der Client sendet initial `NITRO-0-4-0` als Fallback.

### 13.2 Protokollbeobachtungen

- **Pong:** `CLIENT_PONG` (2596), Intervall 20 s (`communication.pong.interval.ms`).
- **Packet-Log:** `communication.packet.log: false` (kann auf `true` gesetzt werden für Debugging).
- **EvaWire:** Big-Endian, `Int` = 4 Byte, `Short` = 2 Byte, `String` = Länge (Short) + UTF-8.

### 13.3 Build-Mängel

- `main.js` enthält ~60 fehlende Module.
- `IncomingHeader.ts` und `NitroMessages.ts` fehlen.
- Viele Events/Parser sind nur als Import-Einträge, aber ohne Modul-Body vorhanden.
- Es ist unklar, ob der Client in dieser Form tatsächlich im Browser startet.

---

## 14. Matrix: Ausgehende Header → Emulator-Handler-Status

| Header | Name | Wird vom Client gesendet? | Hat Lubba-Emu Handler? | Status |
|--------|------|---------------------------|------------------------|--------|
| 2419 | SECURITY_TICKET | Ja | Ja | OK |
| 2596 | CLIENT_PONG | Ja | Ja | OK |
| 2110 | NAVIGATOR_INIT | Ja | Ja | OK |
| 249 | NAVIGATOR_SEARCH | Ja | Ja | OK |
| 2752 | ROOM_CREATE | Ja | Ja | OK |
| 2312 | ROOM_ENTER | Ja | Ja | OK |
| 2230 | ROOM_INFO | Ja | Ja | OK |
| 2300 | ROOM_MODEL | Ja | Ja | OK |
| 3320 | UNIT_WALK | Ja | Ja | OK |
| 1314 | UNIT_CHAT | Ja | Ja | OK |
| 2080 | UNIT_DANCE | Ja | Ja | OK |
| 2456 | UNIT_ACTION | Ja | Ja | OK |
| 1975 | UNIT_SIGN | Ja | Ja | OK |
| 2235 | UNIT_POSTURE | Ja | Ja | OK |
| 1195 | CATALOG_MODE | Ja | Ja | OK |
| 412 | CATALOG_PAGE | Ja | Ja | OK |
| 3492 | CATALOG_PURCHASE | Ja | Ja (via Wrapper) | Teilweise |
| 3150 | USER_FURNITURE | Ja | Ja (falsch kommentiert) | Teilweise |
| 3500 | USER_FURNITURE2 | Ja | Nein | Fehlt |
| 2781 | MESSENGER_INIT | Ja | Ja | OK |
| 1523 | MESSENGER_FRIENDS | Ja | Ja | OK |
| 3567 | MESSENGER_CHAT | Ja | Ja | OK |
| **1258** | **FURNITURE_PLACE** | **Ja** | **Nein** | **Kritisch** |
| **3456** | **FURNITURE_PICKUP** | **Nein im Bundle** | **Nein** | **Blockiert** |
| 248 | FURNITURE_FLOOR_UPDATE | Ja | Ja | OK |
| 168 | FURNITURE_WALL_UPDATE | Ja | Nein | Fehlt |
| 99 | FURNITURE_MULTISTATE | Ja | Ja | OK |
| 3129 | ROOM_SETTINGS | Nein offensichtlich | Nein | Fehlt |
| 1969 | ROOM_SETTINGS_SAVE | Nein offensichtlich | Nein | Fehlt |
| 1481 | TRADE | Nein offensichtlich | Nein | Fehlt |
| 2730 | USER_FIGURE | Ja (Avatar-Editor) | Nein | Fehlt |

---

## 15. Empfohlene nächste Schritte für die Entwicklung

1. **Source-Map auswerten:** `IncomingHeader.ts` und `NitroMessages.ts` aus `main.js.map` extrahieren, um alle eingehenden Header zu erhalten.
2. **Build reparieren:** Prüfen, warum `main.js` die `NitroMessages`- und Event-Module nicht enthält. Vermutlich wurde der Build abgeschnitten oder ein Chunk fehlt.
3. **Placement-Protokoll klären:** Der Emulator muss den String `"itemId x y direction"` für Header 1258 parsen können.
4. **Pickup-Handler:** Sobald der Client-Build korrigiert ist, Handler für `3456` implementieren.
5. **Fehlende Module verifizieren:** Prüfen, ob der Client im Browser tatsächlich lädt oder JavaScript-Fehler wegen fehlender Module wirft.

---

## Anhang A: Wichtige Code-Zeilen

- `OutgoingHeader`: `nitro/main.js:29619–29850`
- `FurniturePlaceComposer`: `nitro/main.js:33–65`
- `FurnitureFloorUpdateComposer`: `nitro/main.js:59024–59042`
- `NitroCommunicationManager`: `nitro/main.js:4443–4525`
- `MessageClassManager`: `nitro/main.js:23646–23754`
- `RoomEngine`: `nitro/main.js:10187–10542`
- `RoomMessageHandler`: `nitro/main.js:17386–17450`
- `RoomSessionManager`: `nitro/main.js:35512–35640`
- `RoomSession`: `nitro/main.js:41479–41678`
- `configuration.json`: `nitro/configuration.json`
- `index.html` SSO-Logik: `nitro/index.html:12–98`
