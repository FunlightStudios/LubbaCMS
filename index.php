<?php
declare(strict_types=1);

// Environment-based error handling
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');

if (ENVIRONMENT === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/logs/php_errors.log');
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

define('BRAIN_CMS', 1);

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/global.php';
    
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="shortcut icon" href="' . htmlspecialchars($config['favicon'] ?? '', ENT_QUOTES, 'UTF-8') . '"/>';
    echo '</head>';
    echo '<body>';
    
    Html::page();
    
    echo '</body></html>';
} catch (Throwable $e) {
    if (ENVIRONMENT === 'production') {
        error_log('Fatal error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
        echo '<h1>An error occurred</h1><p>Please try again later.</p>';
        echo '</body></html>';
    } else {
        echo '<pre>Error: ' . htmlspecialchars($e->getMessage()) . "\n";
        echo 'File: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . "\n";
        echo 'Trace: ' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    exit(1);
}
