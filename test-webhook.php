<?php
/**
 * Test-Script für Coinbase Commerce Webhooks
 * Simuliert eine erfolgreiche Zahlung ohne echtes Geld
 */

// Simuliere Webhook Payload
$testPayload = json_encode([
    'event' => [
        'type' => 'charge:confirmed',
        'data' => [
            'id' => 'TEST_CHARGE_' . time(),
            'code' => 'TEST123',
            'name' => 'Lubba VIP',
            'description' => 'VIP Subscription for TestUser',
            'pricing' => [
                'local' => [
                    'amount' => '5.00',
                    'currency' => 'EUR'
                ]
            ],
            'metadata' => [
                'user_id' => 1, // Deine Test-User-ID
                'username' => 'TestUser'
            ],
            'timeline' => [
                [
                    'status' => 'COMPLETED',
                    'time' => date('c')
                ]
            ]
        ]
    ]
]);

// Sende Request an Webhook-Handler
$ch = curl_init('http://localhost/vip-webhook.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $testPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-CC-Webhook-Signature: test_signature_' . hash_hmac('sha256', $testPayload, 'test_secret')
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Prüfe Datenbank-Tabellen
define('BRAIN_CMS', 1);
require_once 'global.php';

$dbChecks = [];

// Prüfe ob Tabellen existieren
try {
    $stmt = $dbh->query("SHOW TABLES LIKE 'vip_payments'");
    $dbChecks['vip_payments_exists'] = $stmt->rowCount() > 0;
} catch (Exception $e) {
    $dbChecks['vip_payments_exists'] = false;
    $dbChecks['vip_payments_error'] = $e->getMessage();
}

try {
    $stmt = $dbh->query("SHOW TABLES LIKE 'coinbase_webhook_logs'");
    $dbChecks['webhook_logs_exists'] = $stmt->rowCount() > 0;
} catch (Exception $e) {
    $dbChecks['webhook_logs_exists'] = false;
    $dbChecks['webhook_logs_error'] = $e->getMessage();
}

// Hole letzte Webhook-Logs
$webhookLogs = [];
if ($dbChecks['webhook_logs_exists']) {
    try {
        $stmt = $dbh->query("SELECT * FROM coinbase_webhook_logs ORDER BY id DESC LIMIT 3");
        $webhookLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $webhookLogs = ['error' => $e->getMessage()];
    }
}

// Hole letzte Payments
$payments = [];
if ($dbChecks['vip_payments_exists']) {
    try {
        $stmt = $dbh->query("SELECT * FROM vip_payments ORDER BY id DESC LIMIT 3");
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $payments = ['error' => $e->getMessage()];
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Webhook Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .result {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 12px;
        }
        table th {
            background: #667eea;
            color: white;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-pending { background: #ffc107; color: #000; }
        .status-completed { background: #28a745; color: #fff; }
        .status-failed { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="result">
        <h1>🧪 Coinbase Webhook Test</h1>
        
        <?php if ($httpCode === 200): ?>
            <p class="success">✅ <strong>Webhook erfolgreich gesendet!</strong></p>
            <p>HTTP Status: <?= $httpCode ?></p>
        <?php else: ?>
            <p class="error">❌ <strong>Webhook fehlgeschlagen!</strong></p>
            <p>HTTP Status: <?= $httpCode ?></p>
        <?php endif; ?>
        
        <h3>Response:</h3>
        <pre><?= htmlspecialchars($response) ?></pre>
    </div>
    
    <div class="result">
        <h2>📊 Datenbank-Status</h2>
        
        <table>
            <tr>
                <th>Tabelle</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>vip_payments</td>
                <td><?= $dbChecks['vip_payments_exists'] ? '<span class="success">✅ Existiert</span>' : '<span class="error">❌ Fehlt!</span>' ?></td>
            </tr>
            <tr>
                <td>coinbase_webhook_logs</td>
                <td><?= $dbChecks['webhook_logs_exists'] ? '<span class="success">✅ Existiert</span>' : '<span class="error">❌ Fehlt!</span>' ?></td>
            </tr>
        </table>
        
        <?php if (!$dbChecks['vip_payments_exists'] || !$dbChecks['webhook_logs_exists']): ?>
            <p class="error"><strong>⚠️ FEHLER: Datenbank-Tabellen fehlen!</strong></p>
            <p>Bitte importiere <code>install_crypto_payment.sql</code> in phpMyAdmin!</p>
        <?php endif; ?>
    </div>
    
    <?php if ($dbChecks['webhook_logs_exists'] && !empty($webhookLogs)): ?>
    <div class="result">
        <h2>📝 Letzte Webhook-Logs</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Event Type</th>
                <th>Charge ID</th>
                <th>Processed</th>
                <th>Zeit</th>
            </tr>
            <?php foreach ($webhookLogs as $log): ?>
            <tr>
                <td><?= $log['id'] ?></td>
                <td><?= htmlspecialchars($log['event_type']) ?></td>
                <td><?= htmlspecialchars($log['charge_id']) ?></td>
                <td><?= $log['processed'] ? '✅ Ja' : '❌ Nein' ?></td>
                <td><?= $log['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
    
    <?php if ($dbChecks['vip_payments_exists'] && !empty($payments)): ?>
    <div class="result">
        <h2>💳 Letzte Payments</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Methode</th>
                <th>Betrag</th>
                <th>Status</th>
                <th>Zeit</th>
            </tr>
            <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= $payment['id'] ?></td>
                <td><?= htmlspecialchars($payment['username']) ?></td>
                <td><?= $payment['payment_method'] ?></td>
                <td><?= $payment['amount'] ?> <?= $payment['currency'] ?></td>
                <td><span class="status-badge status-<?= $payment['status'] ?>"><?= strtoupper($payment['status']) ?></span></td>
                <td><?= $payment['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
    
    <div class="result">
        <h3>Gesendete Daten:</h3>
        <pre><?= htmlspecialchars(json_encode(json_decode($testPayload), JSON_PRETTY_PRINT)) ?></pre>
        
        <hr>
        <p><a href="test-webhook.php" style="display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;">🔄 Nochmal testen</a></p>
    </div>
</body>
</html>
