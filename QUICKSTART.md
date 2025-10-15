# LubbaCMS Quick Start Guide (PHP 8)

## 🚀 5-Minute Setup

### Prerequisites
- ✅ PHP 8.0 or higher
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ Apache/Nginx web server
- ✅ Composer (optional, for future dependencies)

### Step 1: Check PHP Version
```bash
php -v
```
Should show PHP 8.0.0 or higher.

### Step 2: Configure Environment
```bash
# Copy environment template
cp .env.example .env

# Edit with your settings
notepad .env
```

**Minimum required settings:**
```env
ENVIRONMENT=development
DB_HOST=127.0.0.1
DB_NAME=lubba
DB_USER=root
DB_PASS=
```

### Step 3: Import Database
```bash
# Using MySQL command line
mysql -u root -p lubba < #DB/LubbaFixed.sql

# Or using phpMyAdmin
# 1. Create database 'lubba'
# 2. Import #DB/LubbaFixed.sql
```

### Step 4: Set Permissions (Linux/Mac)
```bash
chmod -R 755 .
chmod -R 777 logs/
chmod 600 .env
```

### Step 5: Test Installation
Open browser: `http://localhost/`

You should see the LubbaCMS homepage!

## 🔒 Security Setup (Production)

### 1. Create Dedicated Database User
```sql
CREATE USER 'lubba_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON lubba.* TO 'lubba_user'@'localhost';
FLUSH PRIVILEGES;
```

Update `.env`:
```env
DB_USER=lubba_user
DB_PASS=YourStrongPassword123!
```

### 2. Switch to Production Mode
```env
ENVIRONMENT=production
```

### 3. Update URLs
```env
HOTEL_URL=https://yourdomain.com
```

### 4. Enable HTTPS
- Get SSL certificate (Let's Encrypt recommended)
- Configure Apache/Nginx for HTTPS
- Force HTTPS redirect

## 🧪 Testing

### Test User Registration
1. Go to `/register`
2. Create test account
3. Verify email validation works
4. Check password hashing in database

### Test Login
1. Go to `/index`
2. Login with test account
3. Verify session is created
4. Check rate limiting (try 6+ failed logins)

### Test Admin Panel
1. Update user rank in database to 7
2. Go to `/housekeeping`
3. Verify access granted
4. Test user management features

## 🐛 Troubleshooting

### "PHP version too low"
**Solution:** Upgrade to PHP 8.0+
```bash
# Ubuntu/Debian
sudo apt install php8.0

# Windows XAMPP
Download PHP 8.0+ from php.net
```

### "Database connection failed"
**Solution:** Check credentials in `.env`
```bash
# Test MySQL connection
mysql -u root -p -e "SHOW DATABASES;"
```

### "Cannot write to logs directory"
**Solution:** Set proper permissions
```bash
chmod 777 logs/
```

### "Session errors"
**Solution:** Check PHP session configuration
```bash
# Check session save path
php -i | grep session.save_path
```

### "CSRF token validation failed"
**Solution:** Clear browser cache and cookies

## 📊 Performance Tips

### Enable OPcache (php.ini)
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### MySQL Optimization
```sql
-- Add indexes for frequently queried columns
ALTER TABLE users ADD INDEX idx_username (username);
ALTER TABLE users ADD INDEX idx_email (mail);
```

### Enable Gzip Compression (.htaccess)
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

## 🔐 Security Checklist

- [ ] Changed default database password
- [ ] Created dedicated database user (not root)
- [ ] Set `ENVIRONMENT=production` in `.env`
- [ ] Enabled HTTPS
- [ ] Set proper file permissions
- [ ] Disabled directory listing
- [ ] Configured firewall
- [ ] Set up automated backups
- [ ] Tested CSRF protection
- [ ] Tested rate limiting
- [ ] Reviewed error logs

## 📝 Default Credentials

**Database:**
- Host: 127.0.0.1
- Port: 3306
- Database: lubba
- User: root (change this!)
- Password: (empty - change this!)

**Admin Account:**
Check database `users` table for existing admin accounts or create one:
```sql
-- Create admin user
INSERT INTO users (username, password, mail, rank) 
VALUES ('admin', '$2y$12$...', 'admin@example.com', 7);
```

## 🆘 Getting Help

1. **Check logs:** `logs/php_errors.log`
2. **Enable debug mode:** Set `ENVIRONMENT=development` in `.env`
3. **Review documentation:** See `PHP8_MIGRATION.md`
4. **Common issues:** Check troubleshooting section above

## 🎯 Next Steps

1. ✅ Complete security checklist
2. ✅ Customize hotel settings in `system/brain-config.php`
3. ✅ Upload hotel logo and assets
4. ✅ Configure email settings for password reset
5. ✅ Set up automated database backups
6. ✅ Configure emulator connection
7. ✅ Test client connection
8. ✅ Customize templates/themes

## 📚 Additional Resources

- **Full Migration Guide:** `PHP8_MIGRATION.md`
- **Configuration Reference:** `system/brain-config.php`
- **Security Features:** `system/app/classes/class.security.php`
- **Environment Variables:** `.env.example`

---

**Need help?** Check the logs first, then review the troubleshooting section!

**Version:** 2.0  
**Last Updated:** 2025-09-29
