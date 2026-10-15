<?php
/**
 * Coinbase Commerce Webhook Handler
 * Empfängt Zahlungsbestätigungen von Coinbase
 */

define('BRAIN_CMS', 1);

try {
    require_once 'global.php';
    
    // Lade Coinbase Class
    require_once 'system/app/classes/class.coinbase.php';
    
    // Hole Webhook Daten
    $payload = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_CC_WEBHOOK_SIGNATURE'] ?? '';
    
    // Verarbeite Webhook
    $coinbase = new CoinbaseCommerce();
    $success = $coinbase->processWebhook($payload, $signature);
    
    // Sende Response
    http_response_code($success ? 200 : 400);
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Webhook processed successfully' : 'Webhook processing failed'
    ]);
    
} catch (Exception $e) {
    // Log Error
    error_log('Webhook Error: ' . $e->getMessage());
    
    // Sende Error Response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
