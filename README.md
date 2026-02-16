# 🎯 Huuto - Finnish Auction Platform

**Production-Ready Web Hotel Deployment - Version 2.0**

## 🚀 Quick Deploy (Web Hotel / FTP)

### For https://www.huuto247.fi/ (Web Hotel)

1. **Upload Files via FTP**
   ```
   Upload the entire repository contents to:
   /home/dajnpsku/public_html/
   
   The domain root should contain:
   - index.php
   - asennus.php
   - .htaccess
   - config/
   - database/
   - app/
   - src/
   - assets/
   - uploads/
   ```

2. **Set File Permissions**
   ```bash
   chmod 755 /home/dajnpsku/public_html/
   chmod 777 /home/dajnpsku/public_html/config/
   chmod 777 /home/dajnpsku/public_html/uploads/
   chmod 644 /home/dajnpsku/public_html/.htaccess
   ```

3. **Run Installation**
   - Visit: `https://www.huuto247.fi/asennus.php`
   - Enter your MySQL database credentials
   - Create admin account
   - Installation creates `config/installed.lock` to prevent reinstallation

4. **Verify Installation**
   - Homepage: `https://www.huuto247.fi/`
   - Category page: `https://www.huuto247.fi/category.php`
   - Auction page: `https://www.huuto247.fi/auction.php?id=1`
   - Add product: `https://www.huuto247.fi/add_product.php`

### 🔒 Security Features (Built-in)

✅ **Apache .htaccess** - Directory listing disabled, sensitive folders blocked  
✅ **Uploads Security** - PHP execution disabled in /uploads/  
✅ **BASE_URL Auto-detection** - Works on any domain automatically  
✅ **Installation Lock** - config/installed.lock prevents reinstallation  
✅ **Security Headers** - X-Frame-Options, X-Content-Type-Options, etc.  
✅ **PDO Prepared Statements** - SQL injection prevention  

---

## 📁 Production File Structure

```
/home/dajnpsku/public_html/
├── .htaccess                       # Apache configuration + security
├── index.php                       # Homepage
├── asennus.php                     # Installation wizard
├── auction.php                     # Single auction view
├── category.php                    # Category listings
├── add_product.php                 # Add new auction
│
├── config/
│   ├── config.php                  # Site configuration (BASE_URL auto-detect)
│   ├── database.php               # DB config (created by installer)
│   └── installed.lock             # Installation lock (prevents reinstall)
│
├── database/
│   ├── schema.sql                 # Database schema
│   └── sample_data.sql            # Optional sample data
│
├── app/
│   ├── auth.php                   # Authentication helpers
│   ├── db.php                     # Database wrapper
│   ├── config.php                 # App config loader
│   ├── email.php                  # Email system
│   └── helpers.php                # Utility functions
│
├── src/
│   ├── models/
│   │   ├── Database.php           # PDO singleton
│   │   ├── Auction.php            # Auction model
│   │   └── Category.php           # Category model
│   └── views/
│       ├── header.php             # Site header
│       └── footer.php             # Site footer
│
├── assets/
│   └── css/
│       └── theme.css              # Site styles (Tailwind CDN + custom)
│
└── uploads/
    └── .htaccess                  # Blocks PHP execution
```

---

## 🛠️ Development (Local Testing)

### Option 1: PHP Built-in Server
```bash
cd /path/to/huuto
php -S localhost:8000

# Visit: http://localhost:8000/asennus.php
```

### Option 2: XAMPP/MAMP/WAMP
```bash
# Place files in htdocs/ or www/
# Access via: http://localhost/huuto/
```

---

## 🔧 Configuration

### BASE_URL (Automatic)
The system automatically detects the domain:
```php
// config/config.php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $scheme . '://' . $host;
```

Works on:
- ✅ `https://www.huuto247.fi/`
- ✅ `http://localhost:8000/`
- ✅ Any domain/subdomain

### Database (via Installer)
```
DB_HOST: localhost (or your MySQL host)
DB_NAME: huuto_db (or your database name)
DB_USER: your_mysql_user
DB_PASS: your_mysql_password
```

---

## 🐛 Troubleshooting

### ❌ 500 Internal Server Error
**Check:**
1. `.htaccess` syntax (Apache mod_rewrite enabled?)
2. PHP error logs: `/storage/logs/error.log` or server error log
3. File permissions: config/ and uploads/ writable?

**Fix:**
```bash
# Check Apache error log
tail -f /var/log/apache2/error.log

# Or check via cPanel File Manager → Error Log
```

### ❌ Database Connection Failed
**Check:**
1. MySQL credentials in `config/database.php`
2. Database exists: `CREATE DATABASE huuto_db;`
3. MySQL user has privileges: `GRANT ALL ON huuto_db.* TO 'user'@'localhost';`

**Fix:**
```bash
# Delete config/database.php and config/installed.lock
# Re-run asennus.php
```

### ❌ Can't Upload Images
**Check:**
1. `uploads/` directory writable: `chmod 777 uploads/`
2. PHP `upload_max_filesize` >= 5MB
3. PHP `post_max_size` >= 5MB

**Fix (cPanel):**
```
PHP Settings → upload_max_filesize → 10M
PHP Settings → post_max_size → 10M
```

### ❌ CSS Not Loading
**Check:**
1. TailwindCSS CDN in `src/views/header.php`:
   ```html
   <script src="https://cdn.tailwindcss.com"></script>
   ```
2. `assets/css/theme.css` exists and is readable

### ❌ Reinstall Application
**To reinstall:**
```bash
# Delete lock file
rm /home/dajnpsku/public_html/config/installed.lock

# Or add ?force=1
https://www.huuto247.fi/asennus.php?force=1
```

---

## 📊 Database Schema

### Core Tables (16 total)
**Main**: users, categories, auctions, auction_images, bids  
**Auth**: email_tokens, password_resets, sessions, login_attempts  
**Security**: rate_limits, csrf_tokens, audit_log  
**Features**: watchlist, ratings, notifications, messages, reports  

Schema file: `database/schema.sql` (auto-installed by asennus.php)

---

## 🎨 Design System

### Current (Production)
- **Framework**: TailwindCSS (CDN)
- **Theme**: Clean, minimal, professional
- **Colors**: Blue primary (#2563EB), gray neutrals
- **Responsive**: Mobile-first, works on all devices

### Custom Styles
Located in: `assets/css/theme.css` (minimal custom overrides)

---

## 📈 Features

### ✅ Working (Production)
1. **Homepage** - Featured auctions, category grid
2. **Category Listings** - Filter by category
3. **Auction Details** - Images, bids, countdown timer
4. **Add Product** - Create new auctions
5. **Search** - Find auctions by keyword
6. **Database** - Full CRUD operations

### 🚧 In Development
1. User authentication (login/register)
2. Bidding system (frontend)
3. Admin panel
4. Email notifications
5. Payment integration

---

## 🔒 Security Best Practices

### ✅ Implemented
- PDO prepared statements (SQL injection prevention)
- `.htaccess` folder protection (config/, database/, app/, src/)
- `uploads/.htaccess` (no PHP execution)
- CSRF tokens (in asennus.php)
- Password hashing (bcrypt)
- Session security (HttpOnly, SameSite)

### 📝 Recommended (Production)
1. **HTTPS** - Use SSL certificate (Let's Encrypt)
2. **Backups** - Daily database + file backups
3. **Updates** - Keep PHP and MySQL updated
4. **Monitoring** - Check logs regularly
5. **Rate Limiting** - Implement for login/API endpoints

---

## 📞 Support

### Documentation
- **Installation**: This README
- **Database**: `database/schema.sql`
- **Development**: `SETUP.md`
- **Testing**: `TEST_CHECKLIST.md`

### Useful Commands
```bash
# Check PHP version
php -v

# Check Apache modules
apache2ctl -M | grep rewrite

# Test database connection
mysql -h localhost -u username -p database_name

# Check file permissions
ls -la /home/dajnpsku/public_html/
```

---

## 📜 License

[Your License Here]

---

**Version**: 2.0-webhotel  
**Last Updated**: 2026-02-14  
**Status**: Production Ready ✅  
**Deployment**: FTP to /home/dajnpsku/public_html/  
**Domain**: https://www.huuto247.fi/

**🎉 Ready for Web Hotel Deployment! 🚀**

---

## 🖼️ Edit Auction: Auto-upload + pääkuva (uusi)

### Mitä muuttui
- `edit_auction.php` tukee nyt automaattista monikuvien latausta ilman erillistä upload-nappia.
- Uudet API-endpointit:
  - `POST /api/upload_auction_images.php`
  - `POST /api/set_primary_image.php`
  - `POST /api/delete_auction_image.php`
- Kuvamäärärajat: max 8 kuvaa / kohde, max 10MB / kuva.

### Why this design
- Yksi selkeä upload-polku: file input `change` laukaisee latauksen heti.
- API vastaa aina JSON:lla (`ok/error`), jolloin frontend ei jää epäselvään tilaan.
- Pääkuva-logiikka pidetään tietokannassa (`is_primary`), joten refresh säilyttää oikean tilan.
- Poisto hoitaa automaattisen pääkuvan vaihdon, jotta UI/DB pysyy konsistenttina.

### Runbook (local)
```bash
php -S localhost:8000
# Avaa: http://localhost:8000/edit_auction.php?id=1
```

### Verify nopeasti
1. Valitse 1 kuva -> näkyy heti galleriassa ilman sivun päivitystä.
2. Valitse useita kuvia -> kaikki latautuvat ja näkyvät.
3. Klikkaa toista kuvaa / “Aseta pääkuvaksi” -> vain sen alla näkyy `(Pääkuva)`.
4. Poista pääkuva -> seuraava kuva nousee automaattisesti pääkuvaksi.

### TODO (seuraavat iteraatiot)
- Lisää drag & drop -lataus edit-sivulle.
- Lisää kuvien uudelleenjärjestely (sort_order) hiirellä.
- Lisää server-side kuvien optimointi (resize + thumbnail).

### Google-kirjautuminen käyttöön
1. Lisää ympäristömuuttujiin:
   - `AUTH_METHOD=both`
   - `GOOGLE_CLIENT_ID=...`
   - `GOOGLE_CLIENT_SECRET=...`
2. Aseta Google OAuth redirect URI: `https://<domain>/auth/google-callback.php`
3. Varmista että PHP:ssä on `curl`-laajennus käytössä.

Jos konfiguraatio puuttuu, kirjautumissivu piilottaa Google-painikkeen automaattisesti.
