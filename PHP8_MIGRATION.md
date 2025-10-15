# LubbaCMS PHP 8 Migration Guide

## ✅ Completed Updates

### 1. **Core Files Modernized**
- ✅ `index.php` - Environment-based error handling, modern HTML5
- ✅ `global.php` - PHP 8 version check, improved loading order
- ✅ `system/app/classes/class.db.php` - Singleton pattern, PDO best practices
- ✅ `system/app/classes/functions.php` - Type hints, return types, CSRF protection
- ✅ `system/app/classes/class.user.php` - Modern password hashing, validation

### 2. **New Security Features**
- ✅ `class.security.php` - Security headers, rate limiting, CSRF protection
- ✅ `class.config.php` - Environment variable management
- ✅ `.env.example` - Configuration template

### 3. **PHP 8 Features Implemented**
- ✅ `declare(strict_types=1)` in all core files
- ✅ Type hints for function parameters
- ✅ Return type declarations
- ✅ Null-safe operators (`??`)
- ✅ `str_starts_with()`, `str_ends_with()` functions
- ✅ Named arguments support
- ✅ Match expressions ready

### 4. **Security Improvements**
- ✅ Bcrypt password hashing (cost 12)
- ✅ Automatic MD5 to Bcrypt migration
- ✅ CSRF token generation and validation
- ✅ Rate limiting for login/register
- ✅ Security headers (XSS, Clickjacking, MIME sniffing)
- ✅ Secure session configuration
- ✅ Input sanitization improvements
- ✅ IP validation with proxy support

### 5. **Database Security**
- ✅ PDO with prepared statements
- ✅ `ATTR_EMULATE_PREPARES = false`
- ✅ UTF8MB4 charset
- ✅ Exception mode enabled
- ✅ Singleton pattern for connection

## 📋 Configuration Steps

### Step 1: Create .env File
```bash
cp .env.example .env
```

Edit `.env` with your settings:
```env
ENVIRONMENT=development
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=lubba
DB_USER=root
DB_PASS=your_secure_password
```

### Step 2: Create Logs Directory
```bash
mkdir logs
chmod 755 logs
```

### Step 3: Update PHP Configuration
Ensure your `php.ini` has:
```ini
; PHP 8.0+
zend.exception_ignore_args = Off
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
```

### Step 4: Test PHP Version
```bash
php -v
# Should show PHP 8.0 or higher
```

## 🔒 Security Checklist

### Production Deployment
- [ ] Set `ENVIRONMENT=production` in `.env`
- [ ] Change database password from default
- [ ] Create separate database user (not root)
- [ ] Enable HTTPS and update URLs
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Disable directory listing
- [ ] Configure firewall rules
- [ ] Set up automated backups
- [ ] Configure error logging
- [ ] Test CSRF protection
- [ ] Test rate limiting

### Database Security
```sql
-- Create dedicated database user
CREATE USER 'lubba_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT SELECT, INSERT, UPDATE, DELETE ON lubba.* TO 'lubba_user'@'localhost';
FLUSH PRIVILEGES;
```

Update `.env`:
```env
DB_USER=lubba_user
DB_PASS=strong_password_here
```

## 🚀 New Features Available

### CSRF Protection
```php
// In forms
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

// In handlers
if (!Security::validateCsrfToken()) {
    die('Invalid CSRF token');
}
```

### Rate Limiting
```php
// Check rate limit before processing
if (!Security::checkRateLimit('login', 5, 300)) {
    die('Too many attempts. Please try again later.');
}
```

### Environment Variables
```php
// Get config values
$dbHost = Config::get('DB_HOST', '127.0.0.1');
$isProduction = Config::getBool('ENVIRONMENT') === 'production';
```

### Security Logging
```php
Security::logSecurityEvent('Failed login attempt', [
    'username' => $username,
    'reason' => 'invalid_password'
]);
```

## ⚠️ Breaking Changes

### 1. Strict Types
All core files now use `declare(strict_types=1)`. This means:
- Type mismatches will throw TypeError
- String '1' is NOT equal to int 1
- Update function calls to match type hints

### 2. Function Signatures Changed
```php
// OLD
function filter($data) { ... }

// NEW
function filter(string $data): string { ... }
```

### 3. Session Handling
Sessions are now started in `global.php` after Security::init().
Remove any `session_start()` calls from other files.

### 4. Database Connection
```php
// OLD
global $dbh;

// NEW (still works, but now uses singleton)
global $dbh; // Returns PDO instance with proper configuration
```

## 🐛 Common Issues & Solutions

### Issue: "Cannot declare strict_types"
**Solution:** Ensure `declare(strict_types=1)` is the FIRST line after `<?php`

### Issue: "Call to undefined function str_starts_with()"
**Solution:** Update to PHP 8.0+. Check with `php -v`

### Issue: "Session already started"
**Solution:** Remove duplicate `session_start()` calls. It's now in `global.php`

### Issue: "Database connection failed"
**Solution:** Check `.env` file exists and has correct credentials

### Issue: "CSRF token validation failed"
**Solution:** Ensure forms include `<?= generateCsrfToken() ?>` hidden input

## 📊 Performance Improvements

- **Opcache:** Enable for 2-3x performance boost
- **PDO Prepared Statements:** Faster than emulated prepares
- **Session Security:** Minimal overhead with maximum security
- **Type Declarations:** JIT compiler optimizations in PHP 8

## 🔄 Rollback Plan

If issues occur, you can rollback:

1. Keep backup of old files in `backup/` directory
2. Restore old files:
   ```bash
   cp backup/index.php index.php
   cp backup/global.php global.php
   # etc.
   ```
3. Remove new files:
   ```bash
   rm system/app/classes/class.security.php
   rm system/app/classes/class.config.php
   ```

## 📝 Testing Checklist

- [ ] Homepage loads without errors
- [ ] User registration works
- [ ] User login works
- [ ] Password reset works
- [ ] Admin panel accessible
- [ ] Client loads properly
- [ ] Database queries execute
- [ ] CSRF protection active
- [ ] Rate limiting works
- [ ] Error logging functional

## 🎯 Next Steps

### Recommended Additional Updates
1. Update remaining classes with type hints
2. Implement API endpoints with proper validation
3. Add unit tests for critical functions
4. Set up automated security scanning
5. Implement database migrations system
6. Add Redis/Memcached for session storage
7. Implement proper logging framework (Monolog)
8. Add API rate limiting per user
9. Implement 2FA for admin accounts
10. Set up automated backups

### Files Still Needing Updates
- `class.html.php` - Add type hints
- `class.admin.php` - Add type hints and CSRF
- `class.game.php` - Add type hints
- Template files - Add CSRF tokens to forms
- AJAX handlers - Add CSRF validation

## 📚 Resources

- [PHP 8 Migration Guide](https://www.php.net/manual/en/migration80.php)
- [OWASP Security Cheat Sheet](https://cheatsheetseries.owasp.org/)
- [PDO Security Best Practices](https://www.php.net/manual/en/pdo.prepared-statements.php)

## 🆘 Support

If you encounter issues:
1. Check logs in `/logs/php_errors.log`
2. Enable development mode: `ENVIRONMENT=development`
3. Check PHP error log: `tail -f /var/log/php_errors.log`
4. Review this guide for common solutions

---

**Version:** 2.0  
**Last Updated:** 2025-09-29  
**PHP Requirement:** 8.0+
