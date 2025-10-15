<?php
declare(strict_types=1);

if (!defined('BRAIN_CMS')) {
	die('Sorry but you cannot access this file!');
}

/**
 * Configuration Manager
 * Handles environment variables and secure configuration loading
 * 
 * @package LubbaCMS
 * @version 2.0
 */
class Config {
	
	private static array $config = [];
	private static bool $loaded = false;
	
	/**
	 * Load configuration from .env file
	 * 
	 * @param string $envPath Path to .env file
	 * @return void
	 */
	public static function loadEnv(string $envPath = '.env'): void {
		if (self::$loaded) {
			return;
		}
		
		$fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $envPath;
		
		if (!file_exists($fullPath)) {
			error_log('Warning: .env file not found at ' . $fullPath);
			return;
		}
		
		$lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		
		foreach ($lines as $line) {
			// Skip comments
			if (str_starts_with(trim($line), '#')) {
				continue;
			}
			
			// Parse KEY=VALUE
			if (strpos($line, '=') !== false) {
				list($key, $value) = explode('=', $line, 2);
				$key = trim($key);
				$value = trim($value);
				
				// Remove quotes if present
				if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
					$value = $matches[2];
				}
				
				self::$config[$key] = $value;
				
				// Also set as environment variable
				if (!getenv($key)) {
					putenv("$key=$value");
				}
			}
		}
		
		self::$loaded = true;
	}
	
	/**
	 * Get configuration value
	 * 
	 * @param string $key Configuration key
	 * @param mixed $default Default value if not found
	 * @return mixed Configuration value
	 */
	public static function get(string $key, mixed $default = null): mixed {
		// Try .env config first
		if (isset(self::$config[$key])) {
			return self::$config[$key];
		}
		
		// Try environment variable
		$envValue = getenv($key);
		if ($envValue !== false) {
			return $envValue;
		}
		
		return $default;
	}
	
	/**
	 * Get boolean configuration value
	 * 
	 * @param string $key Configuration key
	 * @param bool $default Default value
	 * @return bool Configuration value
	 */
	public static function getBool(string $key, bool $default = false): bool {
		$value = self::get($key, $default);
		
		if (is_bool($value)) {
			return $value;
		}
		
		return in_array(strtolower((string)$value), ['true', '1', 'yes', 'on'], true);
	}
	
	/**
	 * Get integer configuration value
	 * 
	 * @param string $key Configuration key
	 * @param int $default Default value
	 * @return int Configuration value
	 */
	public static function getInt(string $key, int $default = 0): int {
		return (int) self::get($key, $default);
	}
	
	/**
	 * Check if configuration key exists
	 * 
	 * @param string $key Configuration key
	 * @return bool True if exists
	 */
	public static function has(string $key): bool {
		return isset(self::$config[$key]) || getenv($key) !== false;
	}
	
	/**
	 * Set configuration value (runtime only)
	 * 
	 * @param string $key Configuration key
	 * @param mixed $value Configuration value
	 * @return void
	 */
	public static function set(string $key, mixed $value): void {
		self::$config[$key] = $value;
	}
}
