# Royal Education Center Management System — Setup Guide

This guide provides detailed instructions for setting up and running the Royal Education Center Management System.

## System Requirements

### Software Requirements
- **XAMPP** 8.0 or higher (includes PHP 8.0+, MySQL 8.0+, Apache)
- **PHP** 8.0 or higher
- **MySQL** 8.0 or higher / MariaDB 10.4+
- **Apache** with mod_rewrite enabled
- **Browser:** Chrome, Firefox, Edge, or Safari

### Hardware Requirements
- **RAM:** 4GB minimum (8GB recommended)
- **Disk Space:** 500MB minimum
- **Processor:** Dual-core or higher

## Installation Steps

### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Run the installer
3. Install to default location (e.g., `C:\xampp`)
4. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Deploy the Project

1. Copy the `royal-edu-center` folder to XAMPP's `htdocs` directory
   - Windows: `C:\xampp\htdocs\royal-edu-center`
   - Linux/Mac: `/opt/lampp/htdocs/royal-edu-center`

### Step 3: Configure Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a new database
3. Enter database name: `royal_edu_center`
4. Click "Create"

### Step 4: Import Database Schema

1. Select the `royal_edu_center` database
2. Click "Import" tab
3. Choose file: `database/schema.sql`
4. Click "Go" to import

### Step 5: Import Seed Data (Optional)

1. Still in phpMyAdmin, select `royal_edu_center` database
2. Click "Import" tab
3. Choose file: `database/seed.sql`
4. Click "Go" to import sample data

### Step 6: Configure Application

1. Open `app/config/config.php`
2. Update the following settings if needed:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'royal_edu_center');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application URL (update based on your setup)
define('BASE_URL', 'http://localhost/royal-edu-center/public');

// Environment (set to 'production' when live)
define('ENVIRONMENT', 'development');
```

### Step 7: Set File Permissions

**Windows:** Usually no action needed

**Linux/Mac:**
```bash
chmod -R 755 public/uploads
chmod -R 755 public/uploads/assignments
chmod -R 755 public/uploads/notes
chmod -R 755 public/uploads/videos
chmod -R 755 public/uploads/receipts
chmod -R 755 public/uploads/backups
chmod -R 755 public/uploads/profiles
```

### Step 8: Verify Installation

1. Open your browser
2. Navigate to: `http://localhost/royal-edu-center/public`
3. You should see the login page

## Login Credentials

After importing seed data, use these default credentials:

| Role | Username | Password |
|------|----------|----------|
| Administrator | admin | admin123 |
| Manager | manager | manager123 |
| Receptionist | reception | reception123 |
| Cashier | cashier | cashier123 |

**Important:** Change default passwords after first login for security.

## Troubleshooting

### Issue: Database Connection Error

**Solution:**
1. Check if MySQL is running in XAMPP Control Panel
2. Verify database credentials in `app/config/config.php`
3. Ensure database `royal_edu_center` exists

### Issue: 404 Not Found

**Solution:**
1. Ensure `.htaccess` files exist in root and `public/` directory
2. Enable mod_rewrite in Apache:
   - Open `httpd.conf` in XAMPP
   - Uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
   - Restart Apache

### Issue: URL Rewriting Not Working

**Solution:**
1. Check Apache configuration allows `.htaccess` overrides
2. In `httpd.conf`, set: `AllowOverride All` for the project directory
3. Restart Apache

### Issue: File Upload Not Working

**Solution:**
1. Check upload directory permissions
2. Verify `upload_max_filesize` and `post_max_size` in `php.ini`
3. Recommended settings:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   max_execution_time = 300
   ```

### Issue: Session Not Persisting

**Solution:**
1. Check `session.save_path` in `php.ini`
2. Ensure the directory exists and is writable
3. Clear browser cookies

### Issue: Blank Page / White Screen

**Solution:**
1. Enable error display in `app/config/config.php`:
   ```php
   define('ENVIRONMENT', 'development');
   ```
2. Check PHP error logs
3. Verify all required files exist

## Production Deployment

### Security Checklist

1. **Change Default Passwords** — Update all default user passwords
2. **Set Environment to Production:**
   ```php
   define('ENVIRONMENT', 'production');
   ```
3. **Disable Error Display** — Errors will be logged instead of shown
4. **Use HTTPS** — Configure SSL certificate
5. **Update Database Credentials** — Use strong database password
6. **Restrict File Uploads** — Validate file types on server-side
7. **Regular Backups** — Schedule automated database backups

### Database Backup

```bash
# Export database
mysqldump -u root -p royal_edu_center > backup.sql

# Import database
mysql -u root -p royal_edu_center < backup.sql
```

### Performance Optimization

1. Enable PHP OPcache
2. Use MySQL query caching
3. Optimize database indexes
4. Enable Gzip compression
5. Use CDN for static assets

## Additional Configuration

### Email Configuration (Future Enhancement)

To enable email notifications, configure SMTP settings in `app/config/config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM', 'noreply@royaledu.com');
```

### Timezone Configuration

The default timezone is set to `Asia/Colombo`. To change:

```php
date_default_timezone_set('Your/Timezone');
```

in `app/config/config.php`.

## Support

For installation issues or questions:
- Check the troubleshooting section above
- Review error logs in XAMPP
- Contact the development team

---

**Version:** 1.0.0
