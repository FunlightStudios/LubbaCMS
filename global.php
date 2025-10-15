<?php
declare(strict_types=1);

if (!defined('BRAIN_CMS')) {
	die('Sorry but you cannot access this file!');
}

// PHP Version Check
if (PHP_VERSION_ID < 80000) {
	die('LubbaCMS requires PHP 8.0 or higher. Current version: ' . PHP_VERSION);
}

// Start output buffering
ob_start();

// Define directory constants
define('Z', $_SERVER['DOCUMENT_ROOT'] . '/');
define('A', Z . 'system/');
define('B', 'app/');
define('C', 'classes/');
define('E', 'languages/');
define('G', 'content/');
define('H', 'templates/');
define('I', 'maintenance/');
define('J', Z . 'adminpan/');
define('K', 'plugins/');
define('L', Z . 'housekeeping/');

// Load configuration
require_once A . 'brain-config.php';

// Load new classes first
if (file_exists(A . B . C . 'class.config.php')) {
	require_once A . B . C . 'class.config.php';
	Config::loadEnv('.env');
}

if (file_exists(A . B . C . 'class.security.php')) {
	require_once A . B . C . 'class.security.php';
}

// Load core classes
require_once A . B . C . 'functions.php';
require_once A . B . C . 'class.db.php';
require_once A . B . C . 'class.user.php';
require_once A . B . C . 'class.game.php';
require_once A . B . C . 'class.html.php';
require_once A . B . C . 'class.admin.php';

// Load language file
$lang_file = A . E . '/' . ($config['lang'] ?? 'de') . '.php';
if (file_exists($lang_file)) {
	require_once $lang_file;
} else {
	error_log('Language file not found: ' . $lang_file);
	require_once A . E . '/de.php'; // Fallback to German
}

// Define skin constant
define('S', $config['skin'] ?? 'brain');

// Initialize security
if (class_exists('Security')) {
	Security::init();
}

// Start secure session
session_start();

// Load plugins
Html::loadPlugins();
