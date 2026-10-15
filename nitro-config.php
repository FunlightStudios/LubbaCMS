<?php
// Generate dynamic Nitro configuration with SSO ticket
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get SSO ticket from session or generate a default one
session_start();

// Get auth_ticket from database for current user
require_once $_SERVER['DOCUMENT_ROOT'] . '/global.php';

$ssoTicket = 'NITRO-0-4-0'; // Default fallback

if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
    try {
        $stmt = $dbh->prepare("SELECT auth_ticket FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['auth_ticket'])) {
            $ssoTicket = $result['auth_ticket'];
        }
    } catch (Exception $e) {
        // Fallback to default
    }
}

// Read base configuration
$configPath = __DIR__ . '/nitro/configuration.json';
$config = json_decode(file_get_contents($configPath), true);

// Add SSO ticket to configuration
$config['sso.ticket'] = $ssoTicket;

// Output JSON
echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
