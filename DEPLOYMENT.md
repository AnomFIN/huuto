# 🚀 Web Hotel Deployment Checklist

## Quick Start - Essential Commands

**After FTP Upload, run these commands immediately:**
```bash
# Set writable directories (REQUIRED)
chmod 777 /home/dajnpsku/public_html/config/
chmod 777 /home/dajnpsku/public_html/uploads/
chmod 777 /home/dajnpsku/public_html/storage/logs/
chmod 777 /home/dajnpsku/public_html/logs/

# Set security files (REQUIRED)
chmod 644 /home/dajnpsku/public_html/.htaccess
chmod 644 /home/dajnpsku/public_html/uploads/.htaccess
chmod 644 /home/dajnpsku/public_html/logs/.htaccess

# Then visit: https://www.huuto247.fi/asennus.php
```

**Using FTP Client:** Right-click directory → File Permissions → Enter: 777 or 644

---

## Pre-Deployment Checklist

### ✅ Repository Status
- [ ] All files committed and pushed to GitHub
- [ ] No uncommitted changes
- [ ] `.gitignore` excludes `config/installed.lock` and `config/database.php`
- [ ] All PHP files tested locally
- [ ] No Python dependencies in production files

### ✅ File Structure Verification
```
Repository Root (= Web Root):
├── .htaccess                    ✓ Security + Apache config
├── index.php                    ✓ Homepage with lock check
├── asennus.php                  ✓ Installer with lock mechanism
├── auction.php                  ✓ With lock check
├── category.php                 ✓ With lock check
├── add_product.php              ✓ With lock check
├── config/
│   ├── config.php              ✓ BASE_URL auto-detect
│   ├── config.example.php      ✓ Example config
│   └── database.php            ⚠️ Created by installer
├── database/
│   ├── schema.sql              ✓ Database schema
│   └── sample_data.sql         ✓ Optional data
├── app/
│   ├── auth.php                ✓ Auth helpers
│   ├── db.php                  ✓ Database wrapper
│   ├── config.php              ✓ Config loader
│   ├── email.php               ✓ Email system
│   └── helpers.php             ✓ Utilities
├── src/
│   ├── models/
│   │   ├── Database.php        ✓ PDO singleton
│   │   ├── Auction.php         ✓ Auction model
│   │   └── Category.php        ✓ Category model
│   └── views/
│       ├── header.php          ✓ Site header
│       └── footer.php          ✓ Site footer
├── assets/
│   └── css/
│       └── theme.css           ✓ Styles
└── uploads/
    ├── .htaccess               ✓ Security (no PHP)
    └── .gitkeep                ✓ Keep directory
```

---

## Deployment Steps

### 1️⃣ FTP Upload

**Target Server:**
- Host: ftp.huuto247.fi (or your FTP host)
- Username: dajnpsku
- Directory: `/home/dajnpsku/public_html/`

**Upload Process:**
```bash
# Option A: Command Line FTP
ftp ftp.huuto247.fi
# Login with credentials
cd /home/dajnpsku/public_html/
# Upload all files from repository root
put -r *

# Option B: FileZilla / cPanel File Manager
# 1. Connect to FTP server
# 2. Navigate to /home/dajnpsku/public_html/
# 3. Upload ALL files from repository root
# 4. Verify upload complete (check file count)
```

**What to Upload:**
- ✅ All .php files
- ✅ .htaccess (IMPORTANT!)
- ✅ All directories (config/, database/, app/, src/, assets/, uploads/)
- ✅ All subdirectories and files
- ❌ DO NOT upload: .git/, .github/, *.md (optional)

---

### 2️⃣ Set File Permissions

**IMPORTANT:** After uploading files via FTP, you MUST set proper permissions for the application to work correctly.

#### 🔧 Required Permissions

**Writable Directories (chmod 777):**
These directories need write permissions so the application can create files:

```bash
# Config directory - installer writes database.php and installed.lock
chmod 777 /home/dajnpsku/public_html/config/

# Uploads directory - stores uploaded product images
chmod 777 /home/dajnpsku/public_html/uploads/

# Storage/logs directory - application writes error logs
chmod 777 /home/dajnpsku/public_html/storage/
chmod 777 /home/dajnpsku/public_html/storage/logs/

# Logs directory - additional logging
chmod 777 /home/dajnpsku/public_html/logs/
```

**Security Files (chmod 644):**
These .htaccess files must be readable by Apache:

```bash
# Root .htaccess - Apache security configuration
chmod 644 /home/dajnpsku/public_html/.htaccess

# Uploads .htaccess - prevents PHP execution in uploads
chmod 644 /home/dajnpsku/public_html/uploads/.htaccess

# Logs .htaccess - blocks direct access to logs
chmod 644 /home/dajnpsku/public_html/logs/.htaccess
```

**PHP Files (chmod 644):**
All PHP files should have standard read permissions:

```bash
# Main PHP files
chmod 644 /home/dajnpsku/public_html/*.php

# PHP files in subdirectories (recursive)
find /home/dajnpsku/public_html -name "*.php" -type f -exec chmod 644 {} \;
```

**Standard Directories (chmod 755):**
All other directories should have standard permissions:

```bash
# Root directory
chmod 755 /home/dajnpsku/public_html/

# All subdirectories except writable ones
chmod 755 /home/dajnpsku/public_html/app/
chmod 755 /home/dajnpsku/public_html/auth/
chmod 755 /home/dajnpsku/public_html/database/
chmod 755 /home/dajnpsku/public_html/src/
chmod 755 /home/dajnpsku/public_html/src/models/
chmod 755 /home/dajnpsku/public_html/src/views/
chmod 755 /home/dajnpsku/public_html/assets/
chmod 755 /home/dajnpsku/public_html/assets/css/
chmod 755 /home/dajnpsku/public_html/tests/
```

#### 📋 Quick Reference Table

| Path | Permission | Numeric | Reason |
|------|-----------|---------|--------|
| `config/` | rwxrwxrwx | **777** | Installer writes database.php |
| `uploads/` | rwxrwxrwx | **777** | Image uploads stored here |
| `storage/logs/` | rwxrwxrwx | **777** | Application error logs |
| `logs/` | rwxrwxrwx | **777** | Additional logging |
| `.htaccess` | rw-r--r-- | **644** | Apache must read security rules |
| `uploads/.htaccess` | rw-r--r-- | **644** | Prevents PHP execution |
| `logs/.htaccess` | rw-r--r-- | **644** | Blocks log access |
| All `.php` files | rw-r--r-- | **644** | Standard PHP file permissions |
| All other directories | rwxr-xr-x | **755** | Standard directory access |

#### 🖱️ Using FTP Clients

**FileZilla:**
1. Connect to your FTP server
2. Navigate to `/home/dajnpsku/public_html/`
3. Right-click on each directory/file → File Permissions
4. Enter numeric value (777, 755, or 644) or check boxes:
   - **777**: Read, Write, Execute for Owner, Group, Public (all checkboxes)
   - **755**: Owner=rwx, Group=rx, Public=rx
   - **644**: Owner=rw, Group=r, Public=r
5. For directories, check "Recurse into subdirectories" if needed

**cPanel File Manager:**
1. Log into cPanel
2. Open File Manager
3. Navigate to `public_html/`
4. Select directory/file → Permissions (top menu)
5. Enter numeric value (777, 755, or 644) or use checkboxes
6. Click "Change Permissions"

**WinSCP:**
1. Connect to FTP server
2. Right-click directory/file → Properties → Permissions
3. Set octal value (777, 755, or 644)
4. Click OK

#### ⚠️ Important Notes

**Why chmod 777?**
- **777** means "anyone can read, write, and execute"
- Required for directories where PHP needs to create files
- **Only use on**: config/, uploads/, storage/logs/, logs/
- **Never use on**: PHP files or root directory

**Security Considerations:**
- ✅ **Safe to use 777**: On writable directories (config, uploads, logs)
- ✅ **.htaccess files protect**: uploads/ from PHP execution
- ❌ **Never 777**: PHP files (.php) or root directory
- ❌ **Never 777**: .htaccess files (use 644)

**After Installation:**
Once installation is complete, you can optionally tighten permissions:
```bash
# After installer runs, you can reduce config permissions
chmod 755 /home/dajnpsku/public_html/config/
chmod 644 /home/dajnpsku/public_html/config/database.php
chmod 644 /home/dajnpsku/public_html/config/installed.lock

# Keep uploads and logs writable
chmod 777 /home/dajnpsku/public_html/uploads/
chmod 777 /home/dajnpsku/public_html/storage/logs/
chmod 777 /home/dajnpsku/public_html/logs/
```

---

### 3️⃣ Database Preparation

**Via cPanel MySQL Databases:**

1. **Create Database:**
   - Name: `dajnpsku_huuto` (or your chosen name)
   - Character Set: `utf8mb4`
   - Collation: `utf8mb4_unicode_ci`

2. **Create Database User:**
   - Username: `dajnpsku_huuto`
   - Password: [Generate secure password]
   - Note credentials for installer

3. **Grant Privileges:**
   - Grant ALL PRIVILEGES to user on database
   - Verify user can connect

**Alternative: phpMyAdmin**
```sql
CREATE DATABASE dajnpsku_huuto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dajnpsku_huuto'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON dajnpsku_huuto.* TO 'dajnpsku_huuto'@'localhost';
FLUSH PRIVILEGES;
```

---

### 4️⃣ Run Installation

**Visit Installer:**
```
https://www.huuto247.fi/asennus.php
```

**Step 1: Database Configuration**
- Database Host: `localhost` (usually)
- Database Name: `dajnpsku_huuto`
- Database User: `dajnpsku_huuto`
- Database Password: [Your password]

**Step 2: Admin Account**
- Admin Email: admin@huuto247.fi
- Admin Name: Administrator
- Admin Password: [Secure password]

**What Happens:**
1. Installer connects to database
2. Creates all tables from `database/schema.sql`
3. Inserts default categories
4. Creates admin user account
5. Writes `config/database.php`
6. Creates `config/installed.lock` (prevents reinstall)
7. Shows success page

---

### 5️⃣ Post-Installation Verification

**Test All Pages:**

1. **Homepage**
   - URL: `https://www.huuto247.fi/`
   - ✅ Should show homepage with categories
   - ✅ No database errors
   - ✅ CSS loads correctly (Tailwind CDN)

2. **Category Page**
   - URL: `https://www.huuto247.fi/category.php`
   - ✅ Shows category list
   - ✅ Links work

3. **Single Auction**
   - URL: `https://www.huuto247.fi/auction.php?id=1`
   - ✅ Shows auction details
   - ✅ Images display (or placeholder)
   - ✅ Countdown timer works

4. **Add Product**
   - URL: `https://www.huuto247.fi/add_product.php`
   - ✅ Form displays
   - ✅ Can submit test auction

5. **Reinstall Prevention**
   - URL: `https://www.huuto247.fi/asennus.php`
   - ✅ Shows "Already installed" message
   - ✅ Lock file prevents reinstall

---

## Security Verification

### ✅ Apache Security

**Test .htaccess Protection:**

```bash
# These should return 403 Forbidden:
https://www.huuto247.fi/config/
https://www.huuto247.fi/database/
https://www.huuto247.fi/app/
https://www.huuto247.fi/src/

# These should work:
https://www.huuto247.fi/
https://www.huuto247.fi/category.php
https://www.huuto247.fi/asennus.php
```

### ✅ Upload Security

**Test uploads/.htaccess:**

1. Create test file: `uploads/test.php` with `<?php echo "UNSAFE"; ?>`
2. Visit: `https://www.huuto247.fi/uploads/test.php`
3. ✅ Should NOT execute PHP (should download or show 403)
4. Delete test file

### ✅ Installation Lock

**Verify Lock File:**
```bash
# Via FTP or cPanel File Manager:
# File should exist: /home/dajnpsku/public_html/config/installed.lock
# Content: Installation timestamp + admin email
```

---

## Troubleshooting

### ❌ 500 Internal Server Error

**Causes:**
1. `.htaccess` syntax error
2. mod_rewrite not enabled
3. PHP syntax error

**Fix:**
```bash
# 1. Check Apache error log (cPanel → Error Log)
# 2. Rename .htaccess temporarily to isolate issue
mv .htaccess .htaccess.bak
# 3. If site works, .htaccess is the issue
# 4. Check Apache modules (contact host if mod_rewrite missing)
```

### ❌ Database Connection Failed

**Causes:**
1. Wrong credentials
2. Database doesn't exist
3. User lacks privileges

**Fix:**
```bash
# 1. Delete config files and retry:
rm /home/dajnpsku/public_html/config/database.php
rm /home/dajnpsku/public_html/config/installed.lock

# 2. Verify database exists (cPanel → MySQL Databases)
# 3. Verify user has ALL PRIVILEGES
# 4. Re-run installer
```

### ❌ CSS Not Loading

**Causes:**
1. Tailwind CDN blocked
2. Wrong path to theme.css

**Fix:**
```bash
# 1. Check src/views/header.php has:
<script src="https://cdn.tailwindcss.com"></script>

# 2. Verify assets/css/theme.css exists
# 3. Check browser console for errors
```

### ❌ Images Not Uploading

**Causes:**
1. uploads/ not writable
2. PHP upload size limit

**Fix:**
```bash
# 1. Set permissions:
chmod 777 /home/dajnpsku/public_html/uploads/

# 2. Check PHP settings (cPanel → PHP Configuration):
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 10
```

### ❌ Permission Denied Errors

**Causes:**
1. Wrong file/directory permissions
2. Web server can't write to directories
3. .htaccess files not readable

**Fix:**
```bash
# 1. Check current permissions:
ls -la /home/dajnpsku/public_html/config/
ls -la /home/dajnpsku/public_html/uploads/

# 2. Reset all required permissions:
chmod 777 /home/dajnpsku/public_html/config/
chmod 777 /home/dajnpsku/public_html/uploads/
chmod 777 /home/dajnpsku/public_html/storage/logs/
chmod 777 /home/dajnpsku/public_html/logs/
chmod 644 /home/dajnpsku/public_html/.htaccess
chmod 644 /home/dajnpsku/public_html/uploads/.htaccess

# 3. Verify ownership (should match web server user):
# Contact hosting support if ownership is incorrect
```

**Common Error Messages:**
- "Failed to open stream: Permission denied" → Directory not writable (need 777)
- "Warning: mkdir(): Permission denied" → Parent directory not writable
- "Unable to write to config/database.php" → config/ needs chmod 777

### ❌ Redirect Loop

**Causes:**
1. Lock file exists but database.php missing
2. Permissions issue

**Fix:**
```bash
# Check both files exist:
ls -la /home/dajnpsku/public_html/config/installed.lock
ls -la /home/dajnpsku/public_html/config/database.php

# If lock exists but database.php missing, delete lock:
rm /home/dajnpsku/public_html/config/installed.lock
```

---

## Performance Optimization (Optional)

### Enable PHP OPcache

**cPanel → MultiPHP INI Editor:**
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### Enable Gzip Compression

**Add to .htaccess:**
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### Browser Caching

**Add to .htaccess:**
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## Backup Strategy

### Daily Backups

**Database:**
```bash
# Via cPanel → Backup Wizard
# Or phpMyAdmin → Export
mysqldump -u dajnpsku_huuto -p dajnpsku_huuto > backup_$(date +%Y%m%d).sql
```

**Files:**
```bash
# Via cPanel → Backup Wizard
# Or download via FTP
tar -czf huuto_backup_$(date +%Y%m%d).tar.gz /home/dajnpsku/public_html/
```

**Automation:**
- cPanel Backup: Enable daily automatic backups
- Store backups off-site (Google Drive, Dropbox)

---

## Monitoring

### Check Logs

**PHP Errors:**
```bash
# cPanel → Error Log
# Or via SSH:
tail -f /home/dajnpsku/public_html/storage/logs/error.log
```

**Apache Errors:**
```bash
# cPanel → Error Log
tail -f /var/log/apache2/error.log
```

### Uptime Monitoring

**Services:**
- UptimeRobot (free)
- Pingdom
- StatusCake

**Monitor:**
- `https://www.huuto247.fi/` (every 5 minutes)
- Alert if down > 5 minutes

---

## Success Criteria

### ✅ Deployment Complete When:

1. **Site Accessible**
   - ✅ `https://www.huuto247.fi/` loads without errors
   - ✅ All pages work (index, category, auction, add_product)
   - ✅ CSS and layout correct

2. **Database Working**
   - ✅ Can view listings
   - ✅ Can add new auction
   - ✅ Categories display correctly

3. **Security Active**
   - ✅ config/, database/, app/, src/ return 403
   - ✅ uploads/ doesn't execute PHP
   - ✅ Installation locked (can't reinstall)

4. **No Errors**
   - ✅ No 500 errors
   - ✅ No database connection errors
   - ✅ No missing file errors
   - ✅ No PHP warnings/notices

---

## Rollback Plan

**If Deployment Fails:**

1. **Keep Backup:**
   - Download current files before deploy
   - Export database before changes

2. **Quick Rollback:**
   ```bash
   # Delete new files
   rm -rf /home/dajnpsku/public_html/*
   
   # Restore backup
   # Upload old files via FTP
   
   # Restore database
   mysql -u user -p database < backup.sql
   ```

3. **Debug Offline:**
   - Download logs
   - Test locally
   - Fix issues
   - Re-deploy

---

## Contact Information

**Support Resources:**
- Repository: https://github.com/AnomFIN/huuto
- Documentation: README.md
- Schema: database/schema.sql

**Hosting Support:**
- Web Hotel: [Your hosting support]
- FTP Issues: [FTP support]
- Database: [MySQL support]

---

**Deployment Date:** _________________  
**Deployed By:** _________________  
**Status:** ⬜ Success  ⬜ Issues  ⬜ Rollback  

**Notes:**
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
