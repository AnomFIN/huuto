# Huuto Auction Platform - Setup Guide

## Overview

This guide will help you set up the upgraded Huuto Finnish Auction Platform on your cPanel hosting or local development environment.

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- cPanel access (for production deployment)
- Composer (optional, for dependency management)

## File Structure

```
/public_html/
├── app/
│   ├── config.php          # Main configuration
│   ├── db.php              # Database wrapper
│   ├── auth.php            # Authentication system
│   ├── email.php           # Email system
│   └── helpers.php         # Helper functions
├── assets/
│   ├── css/
│   │   ├── theme.css       # Design system & CSS variables
│   │   ├── components.css  # Reusable components
│   │   ├── pages.css       # Page-specific styles
│   │   └── utilities.css   # Utility classes
│   └── js/
│       ├── ui.js           # UI interactions
│       └── main.js         # Main application logic
├── auth/
│   ├── login.php           # Login page
│   ├── register.php        # Registration page
│   ├── verify.php          # Email verification
│   ├── reset-password.php  # Password reset
│   └── google-callback.php # Google OAuth callback
├── config/
│   ├── config.php          # Legacy config
│   └── database.php        # Database config
├── src/
│   ├── models/             # Data models
│   └── views/              # View templates
├── uploads/                # User uploads
├── logs/                   # Application logs
├── index.php               # Homepage
├── auction.php             # Auction detail page
├── category.php            # Category listings
├── dashboard.php           # User dashboard
├── admin.php               # Admin panel
└── asennus.php             # Installation wizard
```

## Step 1: Upload Files

### Using cPanel File Manager

1. Log in to your cPanel account
2. Navigate to File Manager
3. Go to `public_html` directory
4. Upload all files maintaining the directory structure
5. Ensure file permissions are set correctly:
   - Directories: `755`
   - PHP files: `644`
   - `uploads/` directory: `755` (writable)
   - `logs/` directory: `755` (writable)

### Using FTP

1. Connect to your server via FTP client (FileZilla recommended)
2. Navigate to `public_html` directory
3. Upload all files and folders
4. Set appropriate permissions as above

## Step 2: Database Setup

### Create Database

1. Log in to cPanel
2. Go to MySQL Databases
3. Create a new database (e.g., `yourdomain_huuto`)
4. Create a new MySQL user with a strong password
5. Add the user to the database with ALL PRIVILEGES
6. Note down:
   - Database name
   - Database user
   - Database password
   - Database host (usually `localhost`)

### Run Migrations

**Option A: Via Installation Wizard (Recommended)**

1. Navigate to `https://yourdomain.com/asennus.php`
2. Follow the installation wizard
3. Enter database credentials
4. Create admin account
5. Wait for tables to be created

**Option B: Via phpMyAdmin**

1. Log in to phpMyAdmin from cPanel
2. Select your database
3. Click on "Import" tab
4. Upload `migrations_full.sql` file
5. Click "Go" to execute

**Option C: Via MySQL Command Line**

```bash
mysql -u your_db_user -p your_db_name < migrations_full.sql
```

## Step 3: Configuration

### Create .env File

1. Copy `.env.example` to `.env` in the root directory
2. Edit `.env` file with your settings:

```env
# Application
APP_ENV=production
APP_DEBUG=false
SITE_NAME="Huuto - Suomalainen Huutokauppa"
BASE_URL=https://yourdomain.com
TIMEZONE=Europe/Helsinki

# Database
DB_HOST=localhost
DB_NAME=yourdomain_huuto
DB_USER=your_db_user
DB_PASS=your_strong_password
DB_CHARSET=utf8mb4

# Security
SESSION_LIFETIME=7200
PASSWORD_MIN_LENGTH=8
MAX_LOGIN_ATTEMPTS=5
LOGIN_LOCKOUT_TIME=900

# Email (Dev Mode for testing)
MAIL_ENABLED=false
MAIL_DEV_MODE=true
MAIL_FROM=noreply@yourdomain.com
MAIL_FROM_NAME=Huuto

# Authentication
AUTH_METHOD=email
REQUIRE_EMAIL_VERIFICATION=false

# Google OAuth (Optional)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
```

### Configure Database (Alternative Method)

If not using `.env`, edit `/public_html/config/database.php`:

```php
<?php
return [
    'host' => 'localhost',
    'dbname' => 'yourdomain_huuto',
    'username' => 'your_db_user',
    'password' => 'your_strong_password',
    'charset' => 'utf8mb4'
];
```

## Step 4: Enable HTTPS (SSL/TLS)

### Via cPanel

1. Log in to cPanel
2. Go to "SSL/TLS Status"
3. Click "Run AutoSSL" for your domain
4. Wait for SSL certificate to be installed

### Force HTTPS Redirect

Edit `/public_html/.htaccess` and add at the top:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.env$">
    Order allow,deny
    Deny from all
</FilesMatch>

# PHP settings
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value max_input_time 300
```

## Step 5: File Permissions & Security

### Set Correct Permissions

```bash
# Make uploads directory writable
chmod 755 /public_html/uploads
chmod 755 /public_html/uploads/auctions
chmod 755 /public_html/uploads/avatars

# Make logs directory writable
chmod 755 /public_html/logs

# Protect .env file
chmod 600 /public_html/.env

# Protect logs directory
echo "Deny from all" > /public_html/logs/.htaccess
```

### Protect Sensitive Directories

Create `/public_html/logs/.htaccess`:

```apache
Deny from all
```

Create `/public_html/app/.htaccess`:

```apache
# Deny direct access to PHP files in app directory
<FilesMatch "\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

## Step 6: Email Configuration

### Development Mode (Default)

By default, emails are logged to `/logs/auth.log` instead of being sent.

To view logged emails:
```bash
tail -f /public_html/logs/auth.log
```

### Production Mode (SMTP)

1. Edit `.env` file:

```env
MAIL_ENABLED=true
MAIL_DEV_MODE=false
SMTP_HOST=mail.yourdomain.com
SMTP_PORT=587
SMTP_USERNAME=noreply@yourdomain.com
SMTP_PASSWORD=your_email_password
SMTP_ENCRYPTION=tls
```

2. For better email delivery, consider using:
   - SendGrid
   - Mailgun
   - Amazon SES
   - Your hosting provider's SMTP

## Step 7: Google OAuth Setup (Optional)

### Create Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one.
3. In the left sidebar, go to **APIs & Services → OAuth consent screen** and configure the consent screen (user type, app name, support email, and authorized domains), then save.
4. Go to **APIs & Services → Credentials**, click **Create credentials** → **OAuth client ID**.
5. Application type: **Web application**.
6. Authorized redirect URIs: `https://yourdomain.com/auth/google-callback.php`
7. Save and then copy the **Client ID** and **Client Secret**.

### Configure in .env

```env
AUTH_METHOD=both
GOOGLE_CLIENT_ID=your_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret_here
```

## Step 8: Cron Jobs (Optional but Recommended)

### Set Up Automated Tasks

In cPanel, go to "Cron Jobs" and add:

```bash
# Clean up expired tokens every hour
0 * * * * php /home/username/public_html/cron/cleanup-tokens.php

# Send ending soon notifications (every 15 minutes)
*/15 * * * * php /home/username/public_html/cron/send-notifications.php

# End expired auctions (every 5 minutes)
*/5 * * * * php /home/username/public_html/cron/end-auctions.php

# Clean up old rate limit records (daily)
0 2 * * * php /home/username/public_html/cron/cleanup-rate-limits.php
```

## Step 9: Testing

### Test Basic Functionality

1. **Homepage**: Visit `https://yourdomain.com/`
   - Should display featured auctions
   - Categories should load
   - No PHP errors

2. **Registration**: Go to `/auth/register.php`
   - Create a test account
   - Check logs for verification email (if dev mode)
   - Verify email works

3. **Login**: Go to `/auth/login.php`
   - Test password login
   - Test magic code login
   - Test "remember me"

4. **Create Auction**: After login
   - Go to user dashboard
   - Create a test auction
   - Upload images

5. **Bidding**: 
   - Place a bid on an auction
   - Test anti-snipe feature
   - Check bid history

6. **Admin Panel**: Log in as admin
   - Access `/admin.php`
   - View users, auctions, bids
   - Test moderation features

### Check Logs

```bash
# Check PHP errors
tail -f /public_html/logs/error.log

# Check auth emails (dev mode)
tail -f /public_html/logs/auth.log

# Check Apache errors
tail -f /var/log/apache2/error.log
```

## Step 10: Go Live

### Pre-Launch Checklist

- [ ] Database is backed up
- [ ] All tables created successfully
- [ ] Admin account created and tested
- [ ] HTTPS is enabled and forced
- [ ] Email sending works (or dev mode is intentional)
- [ ] File uploads work
- [ ] All pages load without errors
- [ ] Security headers configured
- [ ] Rate limiting tested
- [ ] Backup strategy in place

### Post-Launch Tasks

1. **Monitor Logs**: Check error logs regularly for the first few days
2. **Backup Database**: Set up automated daily backups
3. **Monitor Performance**: Use tools like Google PageSpeed Insights
4. **Security Updates**: Keep PHP and dependencies updated
5. **User Feedback**: Collect and address user feedback

## Common Issues & Solutions

### Issue: "Database connection failed"

**Solution**: 
- Check database credentials in `.env` or `config/database.php`
- Ensure database user has correct permissions
- Verify database host (might need to use IP instead of localhost)

### Issue: "Permission denied" when uploading files

**Solution**:
```bash
chmod 755 /public_html/uploads
chown -R username:username /public_html/uploads
```

### Issue: "Session data cannot be written"

**Solution**:
```bash
chmod 755 /tmp
# Or in php.ini:
session.save_path = "/home/username/tmp"
```

### Issue: Emails not being sent

**Solution**:
- Check SMTP credentials
- Verify firewall allows outgoing connections on port 587/465
- Enable dev mode to test without actual email sending
- Check `/logs/auth.log` for logged emails

### Issue: "Too many redirects" error

**Solution**:
- Check `.htaccess` for conflicting redirect rules
- Ensure CloudFlare SSL mode is set to "Full" not "Flexible"

## Security Recommendations

1. **Change Default Admin Password**: Immediately after installation
2. **Enable 2FA**: Consider implementing two-factor authentication
3. **Regular Backups**: Database and file backups daily
4. **Update Regularly**: Keep PHP and dependencies updated
5. **Monitor Logs**: Check for suspicious activity
6. **Use Strong Passwords**: Enforce strong password policy
7. **HTTPS Only**: Never serve site over HTTP
8. **Hide PHP Version**: In php.ini: `expose_php = Off`
9. **Disable Directory Listing**: In .htaccess: `Options -Indexes`
10. **Rate Limiting**: Already implemented, monitor and adjust as needed

## Performance Optimization

### Enable Caching

In `.htaccess`:

```apache
# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### Database Optimization

```sql
-- Add indexes for frequently queried columns
CREATE INDEX idx_auction_status_end ON auctions(status, end_time);
CREATE INDEX idx_bid_auction_user ON bids(auction_id, user_id);
CREATE INDEX idx_watchlist_user ON watchlist(user_id);

-- Optimize tables monthly
OPTIMIZE TABLE users, auctions, bids, watchlist;
```

### PHP Optimization

In `php.ini` or `.user.ini`:

```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M
opcache.enable = 1
opcache.memory_consumption = 128
```

## Support & Maintenance

### Regular Maintenance Tasks

**Daily**:
- Check error logs
- Monitor disk space
- Verify backups completed

**Weekly**:
- Review security logs
- Check for suspicious activity
- Update content/categories

**Monthly**:
- Apply security updates
- Optimize database
- Review and archive old data
- Test backup restore process

### Getting Help

- Check `/logs/error.log` for error messages
- Review this documentation
- Contact your hosting provider for server issues
- Consult PHP/MySQL documentation

## Upgrade Path

When new versions are released:

1. **Backup Everything**: Database + files
2. **Test on Staging**: Never upgrade production directly
3. **Read Changelog**: Check for breaking changes
4. **Run Migrations**: Apply new database changes
5. **Test Thoroughly**: All functionality
6. **Deploy**: Replace files, keeping config
7. **Monitor**: Watch for issues post-upgrade

## Conclusion

Your Huuto Auction Platform is now set up and ready to use! For additional features or customization, refer to the codebase documentation and comments.

Remember to regularly backup your database and files, monitor logs, and keep your system updated.
