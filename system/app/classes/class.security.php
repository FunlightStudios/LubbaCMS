<?php
declare(strict_types=1);

if (!defined('BRAIN_CMS')) {
	die('Sorry but you cannot access this file!');
}

/**
 * Security Manager
 * Handles CSRF protection, rate limiting, and security headers
 * 
 * @package LubbaCMS
 * @version 2.0
 */
class Security {
	
	private static array $rateLimitStore = [];
	
	/**
	 * Initialize security measures
	 * 
	 * @return void
	 */
	public static function init(): void {
		self::setSecurityHeaders();
		self::startSecureSession();
	}
	
	/**
	 * Set security headers
	 * 
	 * @return void
	 */
	public static function setSecurityHeaders(): void {
		// Prevent clickjacking
		header('X-Frame-Options: SAMEORIGIN');
		
		// XSS Protection
		header('X-XSS-Protection: 1; mode=block');
		
		// Prevent MIME sniffing
		header('X-Content-Type-Options: nosniff');
		
		// Referrer Policy
		header('Referrer-Policy: strict-origin-when-cross-origin');
		
		// Content Security Policy (adjust as needed)
		if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
			header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;");
		}
		
		// HSTS (only in production with HTTPS)
		if (defined('ENVIRONMENT') && ENVIRONMENT === 'production' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
			header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
		}
	}
	
	/**
	 * Start secure session with proper settings
	 * 
	 * @return void
	 */
	public static function startSecureSession(): void {
		if (session_status() === PHP_SESSION_NONE) {
			// Secure session configuration
			ini_set('session.cookie_httponly', '1');
			ini_set('session.use_only_cookies', '1');
			ini_set('session.cookie_samesite', 'Lax');
			
			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
				ini_set('session.cookie_secure', '1');
			}
			
			// Regenerate session ID periodically
			if (isset($_SESSION['last_regeneration'])) {
				if (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
					session_regenerate_id(true);
					$_SESSION['last_regeneration'] = time();
				}
			} else {
				$_SESSION['last_regeneration'] = time();
			}
		}
	}
	
	/**
	 * Rate limiting check
	 * 
	 * @param string $action Action identifier
	 * @param int $maxAttempts Maximum attempts allowed
	 * @param int $timeWindow Time window in seconds
	 * @return bool True if allowed, false if rate limited
	 */
	public static function checkRateLimit(string $action, int $maxAttempts = 5, int $timeWindow = 300): bool {
		$ip = userIp();
		$key = $action . '_' . $ip;
		$now = time();
		
		// Clean old entries
		if (isset(self::$rateLimitStore[$key])) {
			self::$rateLimitStore[$key] = array_filter(
				self::$rateLimitStore[$key],
				fn($timestamp) => ($now - $timestamp) < $timeWindow
			);
		} else {
			self::$rateLimitStore[$key] = [];
		}
		
		// Check if limit exceeded
		if (count(self::$rateLimitStore[$key]) >= $maxAttempts) {
			error_log("Rate limit exceeded for action: $action from IP: $ip");
			return false;
		}
		
		// Add current attempt
		self::$rateLimitStore[$key][] = $now;
		
		return true;
	}
	
	/**
	 * Validate CSRF token from request
	 * 
	 * @return bool True if valid
	 */
	public static function validateCsrfToken(): bool {
		$token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? null;
		return verifyCsrfToken($token);
	}
	
	/**
	 * Sanitize input array (recursive)
	 * 
	 * @param array $data Input data
	 * @return array Sanitized data
	 */
	public static function sanitizeInput(array $data): array {
		$sanitized = [];
		
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$sanitized[$key] = self::sanitizeInput($value);
			} else {
				$sanitized[$key] = htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
			}
		}
		
		return $sanitized;
	}
	
	/**
	 * Check if request is from allowed origin
	 * 
	 * @param array $allowedOrigins Allowed origins
	 * @return bool True if allowed
	 */
	public static function checkOrigin(array $allowedOrigins): bool {
		$origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
		
		if (empty($origin)) {
			return true; // Allow requests without origin
		}
		
		foreach ($allowedOrigins as $allowed) {
			if (str_starts_with($origin, $allowed)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Generate secure random token
	 * 
	 * @param int $length Token length
	 * @return string Random token
	 */
	public static function generateToken(int $length = 32): string {
		return bin2hex(random_bytes($length));
	}
	
	/**
	 * Hash sensitive data (for logging, comparison)
	 * 
	 * @param string $data Data to hash
	 * @return string Hashed data
	 */
	public static function hashData(string $data): string {
		return hash('sha256', $data);
	}
	
	/**
	 * Validate file upload security
	 * 
	 * @param array $file $_FILES array element
	 * @param array $allowedTypes Allowed MIME types
	 * @param int $maxSize Maximum file size in bytes
	 * @return bool True if valid
	 */
	public static function validateFileUpload(array $file, array $allowedTypes, int $maxSize): bool {
		// Check for upload errors
		if ($file['error'] !== UPLOAD_ERR_OK) {
			return false;
		}
		
		// Check file size
		if ($file['size'] > $maxSize) {
			return false;
		}
		
		// Check MIME type
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $file['tmp_name']);
		finfo_close($finfo);
		
		if (!in_array($mimeType, $allowedTypes, true)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Log security event
	 * 
	 * @param string $event Event description
	 * @param array $context Additional context
	 * @return void
	 */
	public static function logSecurityEvent(string $event, array $context = []): void {
		$logEntry = [
			'timestamp' => date('Y-m-d H:i:s'),
			'event' => $event,
			'ip' => userIp(),
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
			'user_id' => $_SESSION['id'] ?? 'guest',
			'context' => $context
		];
		
		error_log('SECURITY: ' . json_encode($logEntry));
	}
}
