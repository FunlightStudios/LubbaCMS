<?php
/**
 * LubbaCMS Security Test Script
 * Run this script to verify security features are working
 * 
 * ⚠️ DELETE THIS FILE IN PRODUCTION!
 */

// Only allow in development
if (getenv('ENVIRONMENT') === 'production') {
    die('Security tests disabled in production mode.');
}

define('BRAIN_CMS', 1);
require_once __DIR__ . '/global.php';

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LubbaCMS Security Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
        .test { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .pass { background: #d4edda; border-color: #c3e6cb; }
        .fail { background: #f8d7da; border-color: #f5c6cb; }
        .warning { background: #fff3cd; border-color: #ffeaa7; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .result { font-weight: bold; }
    </style>
</head>
<body>
    <h1>🔒 LubbaCMS Security Test Suite</h1>
    <p><strong>⚠️ DELETE THIS FILE IN PRODUCTION!</strong></p>

    <h2>1. PHP Version Check</h2>
    <?php
    $phpVersion = PHP_VERSION;
    $phpVersionId = PHP_VERSION_ID;
    $pass = $phpVersionId >= 80000;
    ?>
    <div class="test <?= $pass ? 'pass' : 'fail' ?>">
        <strong>PHP Version:</strong> <?= $phpVersion ?><br>
        <span class="result"><?= $pass ? '✅ PASS' : '❌ FAIL' ?></span>
        <?php if (!$pass): ?>
            <p>PHP 8.0+ required. Current version is too old.</p>
        <?php endif; ?>
    </div>

    <h2>2. Database Connection</h2>
    <?php
    try {
        $stmt = $dbh->query("SELECT 1");
        $dbPass = true;
        $dbMessage = "Database connection successful";
    } catch (Exception $e) {
        $dbPass = false;
        $dbMessage = "Database connection failed: " . $e->getMessage();
    }
    ?>
    <div class="test <?= $dbPass ? 'pass' : 'fail' ?>">
        <strong>Database:</strong> <?= $dbMessage ?><br>
        <span class="result"><?= $dbPass ? '✅ PASS' : '❌ FAIL' ?></span>
    </div>

    <h2>3. PDO Configuration</h2>
    <?php
    $pdoConfig = [
        'ERRMODE' => $dbh->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_EXCEPTION,
        'EMULATE_PREPARES' => $dbh->getAttribute(PDO::ATTR_EMULATE_PREPARES) === false,
        'DEFAULT_FETCH_MODE' => $dbh->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE) === PDO::FETCH_ASSOC,
    ];
    $pdoPass = !in_array(false, $pdoConfig, true);
    ?>
    <div class="test <?= $pdoPass ? 'pass' : 'fail' ?>">
        <strong>PDO Configuration:</strong><br>
        - Error Mode (Exception): <?= $pdoConfig['ERRMODE'] ? '✅' : '❌' ?><br>
        - Emulate Prepares (Disabled): <?= $pdoConfig['EMULATE_PREPARES'] ? '✅' : '❌' ?><br>
        - Default Fetch Mode (Assoc): <?= $pdoConfig['DEFAULT_FETCH_MODE'] ? '✅' : '❌' ?><br>
        <span class="result"><?= $pdoPass ? '✅ PASS' : '❌ FAIL' ?></span>
    </div>

    <h2>4. Security Headers</h2>
    <?php
    ob_start();
    Security::setSecurityHeaders();
    $headers = headers_list();
    ob_end_clean();
    
    $requiredHeaders = [
        'X-Frame-Options',
        'X-XSS-Protection',
        'X-Content-Type-Options',
        'Referrer-Policy'
    ];
    
    $foundHeaders = [];
    foreach ($headers as $header) {
        foreach ($requiredHeaders as $required) {
            if (stripos($header, $required) !== false) {
                $foundHeaders[] = $required;
            }
        }
    }
    $headersPass = count($foundHeaders) >= 3;
    ?>
    <div class="test <?= $headersPass ? 'pass' : 'warning' ?>">
        <strong>Security Headers:</strong><br>
        <?php foreach ($requiredHeaders as $header): ?>
            - <?= $header ?>: <?= in_array($header, $foundHeaders) ? '✅' : '❌' ?><br>
        <?php endforeach; ?>
        <span class="result"><?= $headersPass ? '✅ PASS' : '⚠️ WARNING' ?></span>
    </div>

    <h2>5. CSRF Token Generation</h2>
    <?php
    $token1 = generateCsrfToken();
    $token2 = generateCsrfToken();
    $csrfPass = !empty($token1) && strlen($token1) === 64 && $token1 === $token2;
    ?>
    <div class="test <?= $csrfPass ? 'pass' : 'fail' ?>">
        <strong>CSRF Token:</strong> <?= substr($token1, 0, 20) ?>...<br>
        - Length: <?= strlen($token1) ?> (expected: 64)<br>
        - Consistent: <?= $token1 === $token2 ? '✅' : '❌' ?><br>
        <span class="result"><?= $csrfPass ? '✅ PASS' : '❌ FAIL' ?></span>
    </div>

    <h2>6. Password Hashing</h2>
    <?php
    $testPassword = 'TestPassword123!';
    $hash = User::hashed($testPassword);
    $hashPass = password_verify($testPassword, $hash) && str_starts_with($hash, '$2y$12$');
    ?>
    <div class="test <?= $hashPass ? 'pass' : 'fail' ?>">
        <strong>Password Hashing:</strong><br>
        - Algorithm: Bcrypt<br>
        - Cost: 12<br>
        - Hash: <?= substr($hash, 0, 30) ?>...<br>
        - Verification: <?= password_verify($testPassword, $hash) ? '✅' : '❌' ?><br>
        <span class="result"><?= $hashPass ? '✅ PASS' : '❌ FAIL' ?></span>
    </div>

    <h2>7. Input Sanitization</h2>
    <?php
    $xssTest = '<script>alert("XSS")</script>';
    $filtered = filter($xssTest);
    $sanitizePass = $filtered !== $xssTest && strpos($filtered, '<script>') === false;
    ?>
    <div class="test <?= $sanitizePass ? 'pass' : 'fail' ?>">
        <strong>XSS Protection:</strong><br>
        - Input: <code><?= htmlspecialchars($xssTest) ?></code><br>
        - Output: <code><?= $filtered ?></code><br>
        - Protected: <?= $sanitizePass ? '✅' : '❌' ?><br>
        <span class="result"><?= $sanitizePass ? '✅ PASS' : '❌ FAIL' ?></span>
    </div>

    <h2>8. IP Detection</h2>
    <?php
    $ip = userIp();
    $ipPass = filter_var($ip, FILTER_VALIDATE_IP) !== false;
    ?>
    <div class="test <?= $ipPass ? 'pass' : 'fail' ?>">
        <strong>IP Address:</strong> <?= $ip ?><br>
        - Valid Format: <?= $ipPass ? '✅' : '❌' ?><br>
        <span class="result"><?= $ipPass ? '✅ PASS' : '❌ FAIL' ?></span>
    </div>

    <h2>9. Session Security</h2>
    <?php
    $sessionSecure = [
        'httponly' => ini_get('session.cookie_httponly') == '1',
        'use_only_cookies' => ini_get('session.use_only_cookies') == '1',
        'started' => session_status() === PHP_SESSION_ACTIVE,
    ];
    $sessionPass = !in_array(false, $sessionSecure, true);
    ?>
    <div class="test <?= $sessionPass ? 'pass' : 'warning' ?>">
        <strong>Session Configuration:</strong><br>
        - HttpOnly Cookies: <?= $sessionSecure['httponly'] ? '✅' : '❌' ?><br>
        - Use Only Cookies: <?= $sessionSecure['use_only_cookies'] ? '✅' : '❌' ?><br>
        - Session Active: <?= $sessionSecure['started'] ? '✅' : '❌' ?><br>
        <span class="result"><?= $sessionPass ? '✅ PASS' : '⚠️ WARNING' ?></span>
    </div>

    <h2>10. File Permissions</h2>
    <?php
    $logsDir = __DIR__ . '/logs';
    $envFile = __DIR__ . '/.env';
    
    $logsWritable = is_writable($logsDir);
    $envExists = file_exists($envFile);
    $envReadable = $envExists && is_readable($envFile);
    
    $permPass = $logsWritable && $envExists;
    ?>
    <div class="test <?= $permPass ? 'pass' : 'warning' ?>">
        <strong>File Permissions:</strong><br>
        - Logs Directory Writable: <?= $logsWritable ? '✅' : '❌' ?><br>
        - .env File Exists: <?= $envExists ? '✅' : '❌' ?><br>
        - .env File Readable: <?= $envReadable ? '✅' : '❌' ?><br>
        <span class="result"><?= $permPass ? '✅ PASS' : '⚠️ WARNING' ?></span>
        <?php if (!$envExists): ?>
            <p><strong>Action Required:</strong> Copy <code>.env.example</code> to <code>.env</code></p>
        <?php endif; ?>
    </div>

    <h2>11. Rate Limiting</h2>
    <?php
    $rateLimitTests = [];
    for ($i = 0; $i < 6; $i++) {
        $rateLimitTests[] = Security::checkRateLimit('test_action', 5, 60);
    }
    $rateLimitPass = in_array(false, $rateLimitTests, true); // Should block after 5 attempts
    ?>
    <div class="test <?= $rateLimitPass ? 'pass' : 'fail' ?>">
        <strong>Rate Limiting:</strong><br>
        - Attempts: <?= count($rateLimitTests) ?><br>
        - Blocked: <?= in_array(false, $rateLimitTests) ? '✅' : '❌' ?><br>
        <span class="result"><?= $rateLimitPass ? '✅ PASS' : '❌ FAIL' ?></span>
    </div>

    <h2>12. Environment Configuration</h2>
    <?php
    $envMode = defined('ENVIRONMENT') ? ENVIRONMENT : 'undefined';
    $envPass = in_array($envMode, ['development', 'production'], true);
    ?>
    <div class="test <?= $envPass ? 'pass' : 'warning' ?>">
        <strong>Environment:</strong> <?= $envMode ?><br>
        <span class="result"><?= $envPass ? '✅ PASS' : '⚠️ WARNING' ?></span>
        <?php if ($envMode === 'production'): ?>
            <p><strong>⚠️ Production Mode:</strong> Ensure all security measures are in place!</p>
        <?php endif; ?>
    </div>

    <h2>📊 Summary</h2>
    <?php
    $totalTests = 12;
    $passedTests = 0;
    
    if ($pass) $passedTests++;
    if ($dbPass) $passedTests++;
    if ($pdoPass) $passedTests++;
    if ($headersPass) $passedTests++;
    if ($csrfPass) $passedTests++;
    if ($hashPass) $passedTests++;
    if ($sanitizePass) $passedTests++;
    if ($ipPass) $passedTests++;
    if ($sessionPass) $passedTests++;
    if ($permPass) $passedTests++;
    if ($rateLimitPass) $passedTests++;
    if ($envPass) $passedTests++;
    
    $percentage = round(($passedTests / $totalTests) * 100);
    $overallPass = $percentage >= 80;
    ?>
    <div class="test <?= $overallPass ? 'pass' : 'warning' ?>">
        <h3>Overall Score: <?= $passedTests ?>/<?= $totalTests ?> (<?= $percentage ?>%)</h3>
        <?php if ($overallPass): ?>
            <p>✅ <strong>Great!</strong> Your security configuration looks good.</p>
        <?php else: ?>
            <p>⚠️ <strong>Action Required:</strong> Please fix the failing tests above.</p>
        <?php endif; ?>
        
        <h4>Next Steps:</h4>
        <ul>
            <li>Review and fix any failed tests</li>
            <li>Create <code>.env</code> file if missing</li>
            <li>Set proper database credentials</li>
            <li>Test user registration and login</li>
            <li><strong>DELETE THIS FILE before going to production!</strong></li>
        </ul>
    </div>

    <hr>
    <p style="text-align: center; color: #666;">
        <small>LubbaCMS Security Test Suite v2.0 | Generated: <?= date('Y-m-d H:i:s') ?></small>
    </p>
</body>
</html>
