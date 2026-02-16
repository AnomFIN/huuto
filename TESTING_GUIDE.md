# Testing Guide - Verify No HTTP 500 Errors

This guide helps you verify that all fixes are working correctly and no HTTP 500 errors occur.

## Prerequisites

1. PHP 8.2+ installed
2. MySQL/MariaDB server running (optional for basic tests)
3. Clone the repository

## Quick Syntax Verification

Run this to verify all PHP files have valid syntax:

```bash
# Main pages
php -l index.php
php -l auction.php
php -l category.php
php -l add_product.php
php -l edit_auction.php
php -l admin.php

# Auth pages
php -l auth/login.php
php -l auth/register.php
php -l auth/logout.php

# Models
php -l src/models/Database.php
php -l src/models/Auction.php
php -l src/models/Category.php
```

Expected output for each: `No syntax errors detected in [filename]`

## Automated Tests

### Test 1: Core Functionality Test
```bash
php tests/test_standalone.php
```

Expected output:
```
✅ All includes loaded successfully
⚠️  Model initialization failed (expected if DB not set up): Tietokantayhteys epäonnistui...
✅ Session active: yes
✅ is_logged_in() = false
✅ CSRF token available: yes

=== TEST PASSED ===
No HTTP 500 errors detected. All core functionality loads correctly.
```

### Test 2: HTTP Errors Test
```bash
php tests/test_http_errors.php
```

Expected output: All tests should pass with green checkmarks.

## Manual Testing with PHP Dev Server

### 1. Start the server
```bash
php -S localhost:8000
```

### 2. Test Each Page

Visit these URLs and verify they load without HTTP 500 errors:

#### Main Pages
- ✅ http://localhost:8000/ (index.php)
- ✅ http://localhost:8000/auction.php (should redirect or show message if no ID)
- ✅ http://localhost:8000/category.php
- ✅ http://localhost:8000/admin.php
- ✅ http://localhost:8000/add_product.php

#### Auth Pages
- ✅ http://localhost:8000/auth/login.php
- ✅ http://localhost:8000/auth/register.php
- ✅ http://localhost:8000/auth/reset-password.php

#### Installation Page (if database not set up)
- ✅ http://localhost:8000/asennus.php

### 3. Test Registration Flow

1. Visit http://localhost:8000/auth/register.php
2. Fill out the form:
   - Full Name: Test User
   - Email: test@example.com
   - Password: Test1234
   - Confirm Password: Test1234
   - Check terms box
3. Submit form
4. Should see success message (no HTTP 500 error)

### 4. Test Login Flow

1. Visit http://localhost:8000/auth/login.php
2. Fill out the form:
   - Email: test@example.com
   - Password: Test1234
3. Submit form
4. Should either login or show error message (no HTTP 500 error)

### 5. Test Admin Page

1. Visit http://localhost:8000/admin.php
2. Should load auction list or show "No auctions" message
3. No HTTP 500 error should occur

## What to Look For

### ✅ Good Signs
- Pages load (even if showing "Database connection failed")
- Forms can be submitted
- Navigation works
- Graceful error messages displayed
- No blank white pages

### ❌ Bad Signs (HTTP 500 errors)
- Blank white page
- "Internal Server Error" message
- PHP Fatal Error messages
- Parse errors
- "Cannot modify header information" errors

## Common Scenarios

### Scenario 1: Fresh Installation (No Database)
Expected behavior:
- ✅ Pages load but show database connection errors
- ✅ Redirect to asennus.php (installer)
- ✅ Forms render correctly
- ✅ No fatal errors or blank pages

### Scenario 2: Database Configured
Expected behavior:
- ✅ Index page loads with auctions (or empty state)
- ✅ Registration works
- ✅ Login works
- ✅ Admin page shows auction list
- ✅ All pages fully functional

### Scenario 3: Database Misconfigured
Expected behavior:
- ✅ Pages load but show "Database connection failed" message
- ✅ No HTTP 500 errors
- ✅ Graceful degradation

## Troubleshooting

### Problem: "Session already started" warning
**Solution**: Already fixed! Make sure you're using the latest code.

### Problem: "Undefined constant" error
**Solution**: Ensure .env file exists or constants are defined in app/config.php

### Problem: "Class 'Database' not found"
**Solution**: Check that all require_once statements use correct paths with app/config.php

### Problem: "Cannot modify header information"
**Solution**: Check for any output before header() calls. Session is handled by app/config.php.

## Security Note

The test files in `tests/` directory are for development only. In production:
1. Remove or restrict access to test files
2. Set APP_DEBUG=false in .env
3. Ensure proper file permissions
4. Use HTTPS

## Success Criteria

All tests pass when:
- ✅ No PHP syntax errors
- ✅ No HTTP 500 errors on any page
- ✅ Sessions work correctly
- ✅ Database errors are handled gracefully
- ✅ Forms can be submitted without crashes
- ✅ All helper functions available
- ✅ All required constants defined

---

**Last Updated**: 2026-02-16
**Status**: All tests passing ✅
