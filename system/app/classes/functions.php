<?php
declare(strict_types=1);

if (!defined('BRAIN_CMS')) {
	die('Sorry but you cannot access this file!');
}

/**
 * Core utility functions for LubbaCMS
 * PHP 8+ compatible with strict types and return type declarations
 */

/**
 * Filter and sanitize data for output (XSS protection)
 * 
 * @param string $data The data to filter
 * @return string Sanitized data
 */
function filter(string $data): string {
	return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Advanced input filtering with allowed tags (for rich content)
 * 
 * @param string $data The data to filter
 * @return string Filtered data
 */
function filterInput(string $data): string {
	// Strip all tags except safe ones
	$allowed_tags = '<a><i><b><em><span><strong><ul><ol><li><table><tr><td><thead><th><tbody><p><br>';
	$data = strip_tags($data, $allowed_tags);
	
	// Additional XSS protection
	$data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data);
	$data = preg_replace('/on\w+\s*=\s*["\']?[^"\']*["\']?/i', '', $data);
	$data = preg_replace('/javascript:/i', '', $data);
	
	return $data;
}

/**
 * Check version of LubbaCMS (with timeout and error handling)
 * 
 * @return void
 */
function checkVersion(): void {
	global $config;
	
	try {
		$context = stream_context_create([
			'http' => [
				'timeout' => 5,
				'user_agent' => 'LubbaCMS/' . ($config['lubbaversion'] ?? 'unknown')
			]
		]);
		
		$script = @file_get_contents("http://cms.lubbahotel.tk/version.txt", false, $context);
		$update = @file_get_contents("http://cms.lubbahotel.tk/update.txt", false, $context);
		
		if ($script === false) {
			echo '<div style="width: 100%; background-color: orange; border-radius: 5px; padding: 10px; color: white; margin-bottom: 10px; font-size: 17px;">
				Unable to check for updates. Please check your internet connection.
			</div>';
			return;
		}
		
		$version = $config['lubbaversion'] ?? 'unknown';
		$script = trim($script);
		
		if ($version === $script) {
			echo '<div style="width: 100%; background-color: green; border-radius: 5px; padding: 10px; color: white; margin-bottom: 10px; font-size: 17px;">
				This version of LubbaCMS is up to date! You have version ' . htmlspecialchars($script) . '
			</div>';
		} else {
			echo '<div style="width: 100%; background-color: red; border-radius: 5px; padding: 10px; color: white; margin-bottom: 10px; font-size: 17px;">
				There is a new version of LubbaCMS available! You have ' . htmlspecialchars($version) . ' and the latest version is ' . htmlspecialchars($script) . '
			</div>';
			
			if ($update !== false) {
				echo '<div style="width: 100%; background-color: green; border-radius: 5px; padding: 10px; color: white; margin-bottom: 10px; font-size: 17px;">
					' . htmlspecialchars(trim($update)) . '
				</div>';
			}
		}
	} catch (Throwable $e) {
		error_log('Version check failed: ' . $e->getMessage());
		echo '<div style="width: 100%; background-color: orange; border-radius: 5px; padding: 10px; color: white; margin-bottom: 10px; font-size: 17px;">
			Version check temporarily unavailable.
		</div>';
	}
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function loggedIn(): bool {
	return isset($_SESSION['id']) && is_numeric($_SESSION['id']) && $_SESSION['id'] > 0;
}

/**
 * Get user's real IP address (supports Cloudflare and proxies)
 * 
 * @return string IP address
 */
function userIp(): string {
	// Cloudflare support
	if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}
	// Behind proxy
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
	}
	// Direct connection
	else {
		$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	}
	
	// Validate IP address
	$ip = filter_var(trim($ip), FILTER_VALIDATE_IP);
	return $ip !== false ? $ip : '0.0.0.0';
}

/**
 * Generate CSRF token for form protection
 * 
 * @return string CSRF token
 */
function generateCsrfToken(): string {
	if (!isset($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string|null $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCsrfToken(?string $token): bool {
	if (!isset($_SESSION['csrf_token']) || $token === null) {
		return false;
	}
	return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize filename for safe file operations
 * 
 * @param string $filename Filename to sanitize
 * @return string Safe filename
 */
function sanitizeFilename(string $filename): string {
	$filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
	$filename = preg_replace('/\.{2,}/', '.', $filename);
	return trim($filename, '._-');
}