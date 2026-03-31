# Copilot Instructions for Huuto

## Project Overview

**Huuto** is a Finnish online auction platform (huuto247.fi) written in vanilla PHP with MySQL. It is deployed to shared web hosting (web hotel) via FTP and has no PHP framework dependency.

## Tech Stack

- **Backend**: PHP 7.4+ (vanilla, no framework)
- **Database**: MySQL via PDO with prepared statements
- **Frontend**: TailwindCSS (CDN), vanilla JavaScript
- **Web Server**: Apache with `.htaccess` configuration
- **Authentication**: Session-based, optional Google OAuth
- **Deployment**: FTP to `/home/dajnpsku/public_html/`

## Directory Structure

```
/
├── config/           # Site and database configuration
├── app/              # Core helpers (db.php, auth.php, email.php, helpers.php)
├── src/
│   ├── models/       # PDO-based model classes (Auction, Category, Database)
│   └── views/        # Shared partials (header.php, footer.php)
├── api/              # JSON API endpoints (REST-style, return JSON)
├── database/         # SQL schema and migrations
├── assets/css/       # Custom CSS overrides (Tailwind is loaded from CDN)
├── uploads/          # User-uploaded images (PHP execution blocked by .htaccess)
├── logs/             # Application and error logs
└── tests/            # Standalone PHP test scripts
```

## Coding Conventions

- **Language of user-facing strings**: Finnish (`fi`). Error messages shown to users must be in Finnish (e.g., `"Tietokantayhteys epäonnistui."`). Internal error log messages may be in English.
- **PHP style**: No strict types declaration required; use DocBlocks for classes and public methods.
- **Database access**: Always use the `DB` singleton (`db()` helper) or model classes. Never build SQL queries with string concatenation of user input — always use PDO prepared statements with parameterized queries.
- **API responses**: All files under `api/` must return JSON. Set `Content-Type: application/json` and always return `{"ok": true, ...}` on success or `{"ok": false, "error": "..."}` on failure.
- **Security**: Validate and sanitize all user input. Use `htmlspecialchars()` when outputting untrusted data in HTML. Generate and verify CSRF tokens for all state-changing forms.
- **Passwords**: Hash with `password_hash($password, PASSWORD_BCRYPT)`, verify with `password_verify()`.
- **Sessions**: Use `session_start()` via `app/auth.php`; never expose session IDs in URLs.
- **File uploads**: Validate MIME type and size server-side; store files in `uploads/` only; never execute uploaded files.

## Database Patterns

Use the `DB` class (`app/db.php`) for database access:

```php
// Query multiple rows
$rows = db()->query('SELECT * FROM auctions WHERE status = ?', ['active']);

// Query a single row
$auction = db()->queryOne('SELECT * FROM auctions WHERE id = ?', [$id]);

// Query a scalar value
$count = db()->queryValue('SELECT COUNT(*) FROM bids WHERE auction_id = ?', [$id]);

// Insert and get new ID
$newId = db()->insert('bids', ['auction_id' => $id, 'user_id' => $userId, 'amount' => $amount]);

// Update
db()->update('auctions', ['status' => 'ended'], 'id = ?', [$id]);

// Transactions
db()->beginTransaction();
try {
    db()->execute(...);
    db()->commit();
} catch (Exception $e) {
    db()->rollback();
    throw $e;
}
```

## Configuration

There are two separate configuration files:

- **`config/config.php`** — defines application-level PHP constants (`SITE_NAME`, `BASE_URL`, `UPLOAD_DIR`, etc.) and sets the session/timezone defaults. This file is committed to the repository.
- **`config/database.php`** — reads database credentials from environment variables (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) with localhost fallbacks and returns them as an array. This file is **git-ignored** and created by the installation wizard (`asennus.php`).

All available environment variables are documented in `.env.example`. Set them at the server level (e.g., via cPanel or `.htaccess` `SetEnv`) rather than committing a `.env` file.

## Testing

Tests live in `tests/` as standalone PHP scripts. Run them with the PHP built-in server:

```bash
php -S localhost:8000
php tests/test_standalone.php
```

There is no PHPUnit setup; prefer writing self-contained test scripts that print pass/fail output.

## Key Security Rules

1. **Never** build SQL with user-controlled string concatenation — use prepared statements.
2. **Always** escape output with `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` before rendering in HTML.
3. **Always** check CSRF tokens on POST requests that mutate state.
4. **Never** store credentials or API keys in source code — use `.env` / `config/database.php` (git-ignored).
5. **Always** validate uploaded file types and sizes server-side; do not rely on client-side checks.
6. Sensitive directories (`config/`, `database/`, `app/`, `src/`, `logs/`) are protected by `.htaccess` deny rules — keep those rules intact.
