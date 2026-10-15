# 💎 VIP Payment Configuration Guide

## 🎯 Übersicht

Du kannst jetzt flexibel einstellen, welche Zahlungsmethoden für VIP verfügbar sein sollen!

---

## ⚙️ Config-Einstellungen

Öffne `system/brain-config.php` und finde diese Zeilen:

```php
/* VIP Payment Methods - Choose which payment methods to enable */
$config['vipPaymentDiamonds'] = true; // Enable payment with Diamonds
$config['vipPaymentCrypto'] = true; // Enable payment with Cryptocurrency
```

---

## 📋 Mögliche Kombinationen:

### **Option 1: Nur Diamanten** 💎
```php
$config['vipPaymentDiamonds'] = true;
$config['vipPaymentCrypto'] = false;
```
**Ergebnis:** User können VIP nur mit Diamanten kaufen (klassisch)

---

### **Option 2: Nur Crypto** 🚀
```php
$config['vipPaymentDiamonds'] = false;
$config['vipPaymentCrypto'] = true;
```
**Ergebnis:** User können VIP nur mit Kryptowährung kaufen

---

### **Option 3: Beide Methoden** 💎🚀 (Empfohlen!)
```php
$config['vipPaymentDiamonds'] = true;
$config['vipPaymentCrypto'] = true;
```
**Ergebnis:** User können wählen zwischen Diamanten oder Crypto

---

## ⚠️ WICHTIG:

**Mindestens eine Zahlungsmethode muss aktiviert sein!**

Wenn beide auf `false` gesetzt sind, wird eine Fehlermeldung angezeigt:
```
FEHLER: Keine Zahlungsmethode aktiviert! Bitte kontaktiere einen Administrator.
```

---

## 🎨 Wie es aussieht:

### **Beide Methoden aktiviert:**
```
┌─────────────────────────────────┐
│ VIP Kaufen (Grün)               │
├─────────────────────────────────┤
│ [💎 Mit 100 Diamanten kaufen]   │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Mit Kryptowährung bezahlen (Lila)│
├─────────────────────────────────┤
│ Nur 5.00€                       │
│ BTC ETH USDT USDC LTC           │
│ [🚀 Jetzt mit Crypto bezahlen]  │
└─────────────────────────────────┘
```

### **Nur Diamanten:**
```
┌─────────────────────────────────┐
│ VIP Kaufen (Grün)               │
├─────────────────────────────────┤
│ [💎 Mit 100 Diamanten kaufen]   │
└─────────────────────────────────┘
```

### **Nur Crypto:**
```
┌─────────────────────────────────┐
│ Mit Kryptowährung bezahlen (Lila)│
├─────────────────────────────────┤
│ Nur 5.00€                       │
│ BTC ETH USDT USDC LTC           │
│ [🚀 Jetzt mit Crypto bezahlen]  │
└─────────────────────────────────┘
```

---

## 💰 Preise anpassen:

### **Diamanten-Preis:**
```php
$config['vipCost'] = "100"; // Anzahl Diamanten
```

### **Crypto-Preis:**
```php
$config['vipCostEuro'] = "5.00"; // Preis in Euro
```

---

## 🔧 Weitere Einstellungen:

### **VIP Rank:**
```php
$config['vipRankToGet'] = "1"; // Welcher Rank nach VIP-Kauf
// 1 = SilverVIP
// 2 = GoldVIP
// 3 = EventStaff
```

### **VIP Badge:**
```php
$config['vipBadge'] = "vip"; // Badge-Code der vergeben wird
```

---

## 🎯 Empfohlene Einstellung:

Für maximale Flexibilität und mehr Einnahmen:
```php
$config['vipPaymentDiamonds'] = true;  // ✅ Aktiviert
$config['vipPaymentCrypto'] = true;    // ✅ Aktiviert
$config['vipCost'] = "100";            // 100 Diamanten
$config['vipCostEuro'] = "5.00";       // 5€ in Crypto
```

So können User selbst wählen, wie sie bezahlen möchten! 🎉

---

## 📞 Support:

Bei Fragen oder Problemen melde dich!
