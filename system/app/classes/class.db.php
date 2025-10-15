<?php
declare(strict_types=1);

if (!defined('BRAIN_CMS')) {
	die('Sorry but you cannot access this file!');
}

/**
 * Database Connection Handler
 * PHP 8+ compatible with proper error handling and security
 */
class Database {
	private static ?PDO $instance = null;
	
	/**
	 * Get PDO instance (Singleton pattern)
	 */
	public static function getInstance(array $config): PDO {
		if (self::$instance === null) {
			self::connect($config);
		}
		return self::$instance;
	}
	
	/**
	 * Establish database connection with security best practices
	 */
	private static function connect(array $config): void {
		try {
			$dsn = sprintf(
				'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
				$config['host'],
				$config['port'],
				$config['db']
			);
			
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
				PDO::ATTR_STRINGIFY_FETCHES => false,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
			];
			
			self::$instance = new PDO($dsn, $config['user'], $config['pass'], $options);
			
		} catch (PDOException $e) {
			// Log error securely
			error_log('Database connection failed: ' . $e->getMessage());
			
			// Display user-friendly error
			if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
				die('<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 5px; margin: 20px auto; max-width: 600px; font-family: Arial, sans-serif;">
					<h3>Database Connection Error</h3>
					<p>Unable to connect to the database. Please contact the administrator.</p>
				</div>');
			} else {
				die('<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 5px; margin: 20px auto; max-width: 600px; font-family: Arial, sans-serif;">
					<h3>LubbaCMS Database Connection Error</h3>
					<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
					<p>Please check your database configuration in <code>system/brain-config.php</code></p>
				</div>');
			}
		}
	}
}

// Initialize database connection
try {
	$dbh = Database::getInstance($db);
} catch (Throwable $e) {
	error_log('Critical database error: ' . $e->getMessage());
	die('Database initialization failed. Please check logs.');
}