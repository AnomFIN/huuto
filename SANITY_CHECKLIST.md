# Sanity Checklist - Huuto Application

## Purpose
This checklist ensures all critical functionality works after the config path standardization.

## Pre-Deployment Checklist

### ✅ File Integrity
- [x] All PHP files have no syntax errors
- [x] bootstrap.php created and tested
- [x] All entry points updated to use bootstrap
- [x] View includes use SRC_PATH constant
- [x] No duplicate requires found
- [x] logs/.htaccess created for security

### ✅ Path Configuration
- [x] BASE_PATH defined: `/home/dajnpsku/public_html` (or repo root)
- [x] APP_PATH defined: `BASE_PATH/app`
- [x] SRC_PATH defined: `BASE_PATH/src`
- [x] All config paths use BASE_PATH
- [x] Upload directory: `BASE_PATH/uploads/`
- [x] Logs directory: `BASE_PATH/logs/`

### ✅ Error Handling
- [x] Custom fatal error handler registered
- [x] Custom exception handler registered
- [x] Error logging to `logs/php_errors.log`
- [x] Friendly error pages for users
- [x] Development mode configurable via .env

## Production Testing Checklist

### Homepage (index.php)
- [ ] Page loads without HTTP 500 error
- [ ] Page loads without fatal PHP errors
- [ ] Carousel displays (if data available)
- [ ] Categories display correctly
- [ ] Login/Register buttons visible
- [ ] Navigation links work
- [ ] Logo displays
- [ ] No JavaScript errors in console
- [ ] Countdown timers work (if auctions exist)

### Category Page (category.php)
- [ ] Page loads without HTTP 500 error
- [ ] Categories list displays
- [ ] Single category view works
- [ ] Auction listings display
- [ ] Filters work (if implemented)
- [ ] Navigation works

### Auction Page (auction.php)
- [ ] Page loads with valid auction ID
- [ ] Redirects properly if no ID
- [ ] Auction details display
- [ ] Images display correctly
- [ ] Countdown timer works
- [ ] Bid form shows (if logged in)
- [ ] No PHP errors

### Add Product (add_product.php)
- [ ] Page loads without HTTP 500 error
- [ ] Form displays correctly
- [ ] Category dropdown populated
- [ ] Image upload field works
- [ ] Form submission works
- [ ] Validation messages display
- [ ] Success redirect works

### Edit Auction (edit_auction.php)
- [ ] Page loads with valid auction ID
- [ ] Redirects if no ID parameter
- [ ] Form pre-populated with auction data
- [ ] Image upload works
- [ ] Update functionality works
- [ ] Validation works

### Admin Page (admin.php)
- [ ] Page loads without HTTP 500 error
- [ ] Admin functions accessible
- [ ] Settings save correctly
- [ ] No fatal errors

### Authentication System

#### Registration (auth/register.php)
- [ ] Page loads without HTTP 500 error
- [ ] Registration form displays
- [ ] Form validation works
- [ ] Email validation works
- [ ] Password strength check works
- [ ] User created successfully
- [ ] Redirect after registration works
- [ ] Verification email sent (if enabled)

#### Login (auth/login.php)
- [ ] Page loads without HTTP 500 error
- [ ] Login form displays
- [ ] Valid credentials authenticate
- [ ] Invalid credentials show error
- [ ] Rate limiting works
- [ ] Session created correctly
- [ ] Redirect after login works

#### Logout (auth/logout.php)
- [ ] Logout executes without error
- [ ] Session destroyed
- [ ] Redirect to homepage works

#### Password Reset (auth/reset-password.php)
- [ ] Page loads without HTTP 500 error
- [ ] Reset form displays
- [ ] Email sent (if configured)
- [ ] Token validation works

#### Email Verification (auth/verify.php)
- [ ] Page loads without HTTP 500 error
- [ ] Verification code accepted
- [ ] Invalid code rejected
- [ ] Account activated successfully

### API Endpoints

#### AI Analyze API (api_ai_analyze.php)
- [ ] Returns 405 for GET requests
- [ ] Accepts POST requests
- [ ] Returns JSON response
- [ ] Error handling works
- [ ] No HTTP 500 errors

### Debug Pages

#### Debug (debug.php)
- [ ] Page loads without fatal error
- [ ] Shows config status
- [ ] Shows database status
- [ ] Gracefully handles DB errors
- [ ] Displays helpful error messages

## Asset Verification

### Images
- [ ] Logo displays (`/logo.png`)
- [ ] Favicon works (if configured)
- [ ] Placeholder images work
- [ ] Uploaded images display correctly

### CSS
- [ ] Tailwind CDN loads
- [ ] Custom CSS loads (`/assets/css/theme.css`)
- [ ] Styles applied correctly
- [ ] Responsive design works

### JavaScript
- [ ] No console errors
- [ ] Countdown timers work
- [ ] Carousel navigation works
- [ ] Carousel auto-scroll works
- [ ] Form validation works

## Error Handling Tests

### Database Connection Failure
- [ ] Page loads with friendly error (not HTTP 500)
- [ ] Error logged to `logs/php_errors.log`
- [ ] User sees "Yritä myöhemmin uudelleen" message

### Missing File Error
- [ ] Bootstrap catches missing includes
- [ ] Error logged with stack trace
- [ ] User sees friendly error page

### PHP Fatal Error
- [ ] Shutdown handler catches error
- [ ] Error logged to logs
- [ ] User sees friendly message
- [ ] No stack trace visible to user (in production)

## Security Checks

### File Access
- [ ] `.env` file not accessible via web
- [ ] `logs/` directory returns 403
- [ ] `*.sql` files return 403
- [ ] `*.lock` files return 403
- [ ] PHP files in `app/` execute correctly

### Upload Security
- [ ] `uploads/.htaccess` prevents PHP execution
- [ ] Only allowed file types accepted
- [ ] File size limits enforced

## Performance Checks

### Page Load Times
- [ ] Homepage loads < 2 seconds
- [ ] Category page loads < 2 seconds
- [ ] Auction page loads < 2 seconds
- [ ] Admin page loads < 3 seconds

### Database Queries
- [ ] No N+1 query problems
- [ ] Proper indexing used
- [ ] Connection pooling works

## Logging Verification

### Log Files
- [ ] `logs/php_errors.log` created automatically
- [ ] Errors logged with timestamps
- [ ] Stack traces included
- [ ] No sensitive data in logs

### Log Rotation
- [ ] Logs don't grow indefinitely
- [ ] Old logs archived (if configured)

## Deployment Verification

### FTP Upload
- [ ] All files uploaded to public_html/
- [ ] File permissions correct (755 for directories, 644 for files)
- [ ] `.htaccess` uploaded and working
- [ ] uploads/ directory writable (777)
- [ ] logs/ directory writable (755)

### Environment Configuration
- [ ] `.env` file created with production settings
- [ ] `APP_ENV=production` set
- [ ] `APP_DEBUG=false` set
- [ ] Database credentials correct
- [ ] BASE_URL set to `https://huuto247.fi`

### Final Checks
- [ ] No HTTP 500 errors on any page
- [ ] All forms work
- [ ] All redirects work
- [ ] HTTPS works (if applicable)
- [ ] WWW redirect works (if configured)

## Rollback Plan

If any critical issues found:

1. **Immediate**: Keep backup of old files
2. **Document**: Note what failed in testing
3. **Fix**: Address issues in development
4. **Retest**: Run through checklist again
5. **Redeploy**: Upload fixed files

## Sign-Off

- Date Tested: _______________
- Tester: _______________
- Environment: ☐ Development  ☐ Staging  ☐ Production
- Status: ☐ Pass  ☐ Fail  ☐ Pass with notes

**Notes**:
_______________________________________
_______________________________________
_______________________________________
_______________________________________
