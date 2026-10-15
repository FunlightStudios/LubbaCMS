# LubbaCMS Database Schema Reference

## 📊 Wichtige Tabellen und Spalten

### **users** Tabelle
```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(125) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT 'defaultuser@meth0d.org',
  `auth_ticket` varchar(60) NOT NULL,
  `rank` int(1) UNSIGNED DEFAULT 1,
  `rank_vip` int(1) DEFAULT 1,
  `credits` int(11) DEFAULT 50000,
  `vip_points` int(11) DEFAULT 0,
  `activity_points` int(11) DEFAULT 5000,
  `look` char(255) DEFAULT NULL,
  `gender` enum('M','F') DEFAULT 'M',
  `motto` char(50) DEFAULT NULL,
  `account_created` char(12) DEFAULT '0',
  `last_online` int(11) DEFAULT 0,
  `online` enum('0','1') DEFAULT '0',
  `ip_last` varchar(45) DEFAULT '',
  `ip_reg` varchar(45) DEFAULT NULL,
  `home_room` int(10) DEFAULT 0,
  `is_muted` enum('0','1') DEFAULT '0',
  `block_newfriends` enum('0','1') DEFAULT '0',
  `hide_online` enum('0','1') DEFAULT '0',
  `hide_inroom` enum('0','1') DEFAULT '0',
  `vip` enum('0','1') DEFAULT '1',
  `volume` varchar(15) DEFAULT '100,100,100',
  `last_change` int(20) DEFAULT 0,
  `machine_id` varchar(125) DEFAULT '',
  `focus_preference` enum('0','1') DEFAULT '0',
  `chat_preference` enum('0','1') DEFAULT '0',
  `pets_muted` enum('0','1') DEFAULT '0',
  `bots_muted` enum('0','1') DEFAULT '0',
  `advertising_report_blocked` enum('0','1') DEFAULT '0',
  `gotw_points` int(11) DEFAULT 0,
  `ignore_invites` enum('0','1') DEFAULT '0',
  `time_muted` double DEFAULT 0,
  `allow_gifts` enum('0','1') DEFAULT '1',
  `trading_locked` double DEFAULT 0,
  `friend_bar_state` enum('0','1') NOT NULL DEFAULT '1',
  `disable_forced_effects` enum('0','1') NOT NULL DEFAULT '0',
  `allow_mimic` enum('1','0') NOT NULL DEFAULT '1',
  `user_likes` int(11) DEFAULT 0,
  `pin` varchar(4) DEFAULT NULL,
  `teamrank` int(1) DEFAULT 0,
  `fbid` varchar(255) DEFAULT NULL,
  `fbenable` enum('0','1','2') DEFAULT '1',
  PRIMARY KEY (`id`)
)
```

### **cms_news** Tabelle
```sql
CREATE TABLE `cms_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL DEFAULT '0',
  `shortstory` text NOT NULL,
  `longstory` text NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT 'Tom',
  `date` int(11) NOT NULL DEFAULT 0,        -- ⚠️ UNIX Timestamp, NICHT 'timestamp'
  `type` varchar(100) NOT NULL DEFAULT '1',
  `roomid` varchar(100) NOT NULL DEFAULT '1',
  `updated` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
```

### **cms_news_like** Tabelle
```sql
CREATE TABLE `cms_news_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(255) DEFAULT NULL,
  `newsid` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
```

### **cms_news_message** Tabelle (Kommentare)
```sql
CREATE TABLE `cms_news_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT 0,        -- UNIX Timestamp
  `newsid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `message` varchar(250) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
```

### **user_relationships** Tabelle (Freundschaften)
```sql
CREATE TABLE `user_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `type` int(11) NOT NULL,                  -- 1=Love, 2=Best Friend, 3=Hot
  PRIMARY KEY (`id`)
)
```

### **referrer** Tabelle (Werbesystem)
```sql
CREATE TABLE `referrer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refid` int(11) NOT NULL,                 -- User ID des Werbers
  `userid` int(11) NOT NULL,                -- User ID des Geworbenen
  PRIMARY KEY (`id`)
)
```

### **referrerbank** Tabelle (Diamanten Bank)
```sql
CREATE TABLE `referrerbank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `diamonds` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
```

### **users_currency** Tabelle (Arcturus Emulator)
```sql
CREATE TABLE `users_currency` (
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,                  -- 0=activity_points, 5=vip_points
  `amount` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`, `type`)
)
```

## ⚠️ Wichtige Hinweise

### Zeitstempel
- **KEINE** `timestamp` Spalte in `cms_news`!
- Stattdessen: `date` als `int(11)` (UNIX Timestamp)
- Konvertierung: `date('d.m.Y H:i', $row['date'])`

### Währungen
- **credits**: Direkt in `users` Tabelle
- **activity_points**: 
  - Arcturus: `users_currency` mit `type=0`
  - Standard: `users` Tabelle
- **vip_points**: 
  - Arcturus: `users_currency` mit `type=5`
  - Standard: `users` Tabelle

### Freundschaftstypen
- `type=1`: Love (Herz)
- `type=2`: Best Friend (Stern)
- `type=3`: Hot (Flamme)

### Online Status
- `online='1'`: Online
- `online='0'`: Offline
- Typ: `enum('0','1')`

### Ranks
- `rank=1`: User
- `rank=2-6`: Staff Ranks
- `rank=7+`: Admin/Management
- `rank=9`: Owner

## 📝 Beispiel Queries

### News abrufen
```php
$sql = $dbh->prepare("SELECT id, title, image, shortstory, longstory, author, date FROM cms_news ORDER BY id DESC LIMIT 10");
$sql->execute();
while ($news = $sql->fetch()) {
    $timestamp = date('d.m.Y H:i', $news['date']);
    // ...
}
```

### User Daten
```php
$sql = $dbh->prepare("SELECT username, motto, credits, activity_points, vip_points, look, online, rank FROM users WHERE id = :id");
$sql->bindParam(':id', $userId, PDO::PARAM_INT);
$sql->execute();
$user = $sql->fetch();
```

### Freunde zählen
```php
$sql = $dbh->prepare("SELECT COUNT(*) as count FROM user_relationships WHERE user_id = :id AND type = :type");
$sql->bindParam(':id', $userId, PDO::PARAM_INT);
$sql->bindParam(':type', $type, PDO::PARAM_INT); // 1, 2, or 3
$sql->execute();
$count = $sql->fetch()['count'];
```

### Referrals
```php
$sql = $dbh->prepare("SELECT COUNT(*) as count FROM referrer WHERE refid = :refid");
$sql->bindParam(':refid', $_SESSION['id'], PDO::PARAM_INT);
$sql->execute();
$refCount = $sql->fetch()['count'];
```

### Diamanten Bank
```php
$sql = $dbh->prepare("SELECT diamonds FROM referrerbank WHERE userid = :userid");
$sql->bindParam(':userid', $_SESSION['id'], PDO::PARAM_INT);
$sql->execute();
$bank = $sql->fetch();
$diamonds = $bank ? $bank['diamonds'] : 0;
```

## 🔒 Sicherheit

### Prepared Statements verwenden
```php
// ✅ RICHTIG
$sql = $dbh->prepare("SELECT * FROM users WHERE username = :username");
$sql->bindParam(':username', $username, PDO::PARAM_STR);

// ❌ FALSCH
$sql = $dbh->query("SELECT * FROM users WHERE username = '$username'");
```

### Parameter Types
- `PDO::PARAM_INT` - für Integers (id, rank, etc.)
- `PDO::PARAM_STR` - für Strings (username, motto, etc.)
- `PDO::PARAM_BOOL` - für Booleans

### Output Escaping
```php
// Immer escapen bei Ausgabe
echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
```

## 📊 Datentypen Referenz

### INT Felder
- `id`, `user_id`, `target`, `newsid`, `refid`, `userid`
- `credits`, `activity_points`, `vip_points`
- `rank`, `teamrank`, `home_room`
- `date` (UNIX Timestamp)

### VARCHAR/CHAR Felder
- `username` (125)
- `password` (255) - Bcrypt Hash
- `mail` (255)
- `motto` (50)
- `look` (255)
- `ip_last`, `ip_reg` (45)

### ENUM Felder
- `online`: '0', '1'
- `gender`: 'M', 'F'
- `vip`: '0', '1'
- Alle boolean-ähnlichen Felder

### TEXT Felder
- `shortstory`, `longstory`
- `message`

---

**Version:** 1.0  
**Letzte Aktualisierung:** 2025-09-29  
**Basierend auf:** LubbaFixed.sql
