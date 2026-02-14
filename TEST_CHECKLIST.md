# Huuto Auction Platform - Test Checklist

## Pre-Deployment Testing

### ✅ Installation & Setup

- [ ] Installation wizard (`asennus.php`) loads without errors
- [ ] Database connection test succeeds
- [ ] All database tables created successfully
- [ ] Admin user created successfully
- [ ] Sample data loaded correctly
- [ ] Configuration files writable
- [ ] Upload directories created with correct permissions
- [ ] Log directories created and writable

### ✅ Authentication System

#### Password-Based Registration
- [ ] Registration form loads and displays correctly
- [ ] Username validation works (alphanumeric only)
- [ ] Email validation works (valid format required)
- [ ] Password strength validation enforced (min 8 chars, letters + numbers)
- [ ] Duplicate email prevention works
- [ ] Verification email sent/logged
- [ ] Email verification link works
- [ ] User status changes from 'pending' to 'active' after verification
- [ ] Unverified user can/cannot login (based on config)

#### Password-Based Login
- [ ] Login form loads correctly
- [ ] Valid credentials allow login
- [ ] Invalid credentials show error message
- [ ] Rate limiting works (5 failed attempts = locked for 15 min)
- [ ] "Remember me" checkbox persists session
- [ ] Last login timestamp updated
- [ ] Audit log entry created on login
- [ ] Redirect to intended page after login works

#### Magic Code Login
- [ ] Magic code request form works
- [ ] 6-digit code generated correctly
- [ ] Email sent/logged with code
- [ ] Code valid for 10 minutes
- [ ] Invalid code shows error
- [ ] Expired code shows error
- [ ] Rate limiting works (3 attempts per 5 minutes)
- [ ] Code is single-use (cannot reuse)
- [ ] Email auto-verified on magic code login

#### Password Reset
- [ ] Forgot password form works
- [ ] Reset email sent/logged
- [ ] Reset link contains valid token
- [ ] Reset form loads with valid token
- [ ] Token expires after 1 hour
- [ ] Password update successful
- [ ] Old password no longer works
- [ ] New password allows login
- [ ] Token is single-use
- [ ] Rate limiting works

#### Google OAuth (if enabled)
- [ ] "Sign in with Google" button displays
- [ ] OAuth redirect to Google works
- [ ] Google authentication completes
- [ ] Callback URL processes correctly
- [ ] User created or linked with Google ID
- [ ] Email auto-verified for OAuth users
- [ ] Avatar from Google displayed
- [ ] Logout works correctly

### ✅ Security Features

#### CSRF Protection
- [ ] CSRF tokens generated on all forms
- [ ] Form submissions without token rejected
- [ ] Expired tokens rejected
- [ ] Token regeneration on new session works

#### Rate Limiting
- [ ] Login attempts limited (5 per 15 min)
- [ ] Magic code requests limited (3 per 5 min)
- [ ] Password reset requests limited (3 per hour)
- [ ] Bid attempts limited (10 per minute)
- [ ] Rate limit messages display correctly
- [ ] Rate limits reset after time window

#### XSS Prevention
- [ ] HTML in user input escaped on display
- [ ] JavaScript in comments escaped
- [ ] Special characters handled correctly
- [ ] No script execution from user input

#### SQL Injection Prevention
- [ ] All queries use prepared statements
- [ ] User input in WHERE clauses sanitized
- [ ] Search queries safe from injection
- [ ] No direct SQL string concatenation

#### Audit Logging
- [ ] User registration logged
- [ ] Login attempts logged
- [ ] Logout logged
- [ ] Bid placements logged
- [ ] Auction creation logged
- [ ] Admin actions logged
- [ ] Log entries include IP and user agent

### ✅ User Dashboard

#### My Bids
- [ ] Active bids display correctly
- [ ] Bid amounts show accurately
- [ ] Auction end times displayed
- [ ] Winning/losing status indicated
- [ ] "You've been outbid" badge shows
- [ ] Quick rebid button works

#### My Listings
- [ ] All user's auctions listed
- [ ] Draft/Active/Ended status shown
- [ ] Current bid displayed
- [ ] Views count accurate
- [ ] Watchers count accurate
- [ ] Edit listing button works (for active)
- [ ] Delete listing works (for drafts)

#### My Watchlist
- [ ] Watched auctions display
- [ ] Add to watchlist button works
- [ ] Remove from watchlist works
- [ ] Notification preferences saveable
- [ ] Badge shows count of watched items
- [ ] "Ending soon" highlighted

#### Account Settings
- [ ] Profile information editable
- [ ] Email change requires verification
- [ ] Password change works
- [ ] Phone number validation
- [ ] Avatar upload works
- [ ] Avatar displays correctly
- [ ] Notification preferences saved

### ✅ Auction Functionality

#### Creating Auction
- [ ] Create listing form loads
- [ ] All required fields validated
- [ ] Title min/max length enforced
- [ ] Description accepts formatting
- [ ] Starting price must be positive
- [ ] Buy now price validation (must be > starting)
- [ ] Reserve price validation (optional)
- [ ] End date must be future
- [ ] Category selection works
- [ ] Image upload works (up to 8 images)
- [ ] Image type validation (jpg, png, gif, webp)
- [ ] Image size validation (max 5MB)
- [ ] Primary image selection works
- [ ] Draft save works
- [ ] Publish works

#### Viewing Auction
- [ ] Auction detail page loads
- [ ] Image gallery displays
- [ ] Image thumbnails clickable
- [ ] Full description visible
- [ ] Current bid displays
- [ ] Bid history table populated
- [ ] Seller information shown
- [ ] Seller rating displayed
- [ ] Time remaining countdown accurate
- [ ] "Ending soon" indicator shows (< 1 hour)
- [ ] Views counter increments
- [ ] Watchers count accurate
- [ ] Reserve met/not met indicator (if applicable)

#### Bidding
- [ ] Bid input form displays
- [ ] Minimum bid calculated correctly (current + increment)
- [ ] Cannot bid below minimum
- [ ] Cannot bid on own auction
- [ ] Must be logged in to bid
- [ ] Bid placement updates current price
- [ ] Previous high bidder notified (email/logged)
- [ ] Bid history updates real-time
- [ ] User's own bids highlighted
- [ ] Rate limiting enforced

#### Anti-Sniping
- [ ] Bid in last 60 seconds extends auction by 2 minutes
- [ ] Extension count tracked
- [ ] Maximum 5 extensions enforced
- [ ] New end time displayed
- [ ] Watchers notified of extension
- [ ] Original end time still visible

#### Buy Now
- [ ] "Buy Now" button displays (if set)
- [ ] Buy now price shown
- [ ] Clicking buy now confirms
- [ ] Auction ends immediately
- [ ] Buyer marked as winner
- [ ] Seller notified
- [ ] Auction status changes to "sold"

#### Reserve Price
- [ ] Reserve price not visible to bidders
- [ ] "Reserve not met" indicator shows
- [ ] "Reserve met" indicator shows when reached
- [ ] Auction cannot end below reserve
- [ ] Seller notified when reserve met

#### Auction Ending
- [ ] Cron job ends auctions on time
- [ ] Winner determined correctly (highest bid)
- [ ] Winner notified via email/logged
- [ ] Seller notified
- [ ] Auction status changes to "ended"
- [ ] Final price locked
- [ ] No more bids accepted

### ✅ Categories & Search

#### Categories
- [ ] All categories display on homepage
- [ ] Category icons show correctly
- [ ] Active auction count accurate
- [ ] Category page loads
- [ ] Auctions filtered by category
- [ ] Subcategories work (if implemented)

#### Search
- [ ] Search form works
- [ ] Keyword search in title/description
- [ ] Results sorted by relevance
- [ ] Empty results handled gracefully
- [ ] Search pagination works
- [ ] Advanced filters work (price, location, condition)

#### Filters & Sorting
- [ ] Price range filter works
- [ ] Location filter works
- [ ] Condition filter works
- [ ] "Ending soon" sort works
- [ ] "Newly listed" sort works
- [ ] "Price: Low to High" works
- [ ] "Price: High to Low" works
- [ ] Multiple filters combine correctly

### ✅ Watchlist & Notifications

#### Watchlist
- [ ] Add to watchlist button visible
- [ ] Button toggles add/remove state
- [ ] Watchlist count updates
- [ ] Cannot watch own auctions
- [ ] Must be logged in to watch

#### Notifications
- [ ] Outbid notification sent/logged
- [ ] Ending soon notification (1 hour before)
- [ ] Auction won notification
- [ ] Auction sold notification (to seller)
- [ ] Watch auction updates (optional)
- [ ] Notification badge shows unread count
- [ ] Mark as read works
- [ ] Notification preferences respected

### ✅ Seller Ratings

#### Leaving Rating
- [ ] Rating form accessible to buyers only
- [ ] Can rate after auction ends
- [ ] 1-5 star selection works
- [ ] Comment field optional
- [ ] Cannot rate same auction twice
- [ ] Cannot rate own auctions

#### Viewing Ratings
- [ ] Seller profile shows average rating
- [ ] Total ratings count shown
- [ ] Individual ratings listed
- [ ] Most recent shown first
- [ ] Seller can respond to ratings
- [ ] Response displayed below rating

### ✅ Admin Dashboard

#### Users Management
- [ ] All users listed
- [ ] Search users works
- [ ] Filter by role (user/admin)
- [ ] Filter by status (active/suspended/banned)
- [ ] View user details
- [ ] Edit user information
- [ ] Change user role
- [ ] Suspend user works
- [ ] Ban user works
- [ ] Banned users cannot login
- [ ] Delete user works (with confirmation)

#### Auctions Management
- [ ] All auctions listed
- [ ] Filter by status
- [ ] Search auctions works
- [ ] View auction details
- [ ] Edit auction
- [ ] Feature auction (homepage highlight)
- [ ] End auction early
- [ ] Cancel auction
- [ ] Delete auction

#### Bids Management
- [ ] All bids listed
- [ ] Filter by auction
- [ ] Filter by user
- [ ] Sort by date/amount
- [ ] View bid details
- [ ] Delete suspicious bids

#### Reports & Moderation
- [ ] User reports listed
- [ ] Report details viewable
- [ ] Mark report as reviewed
- [ ] Take action on reports
- [ ] Ban reported users
- [ ] Remove reported auctions
- [ ] Respond to reporter

### ✅ UI/UX

#### Design System
- [ ] CSS variables loaded correctly
- [ ] Nordic color scheme applied
- [ ] Typography scales responsively
- [ ] Spacing consistent (12/16/24px rhythm)
- [ ] Shadows applied appropriately
- [ ] Border radius consistent

#### Components
- [ ] Buttons: primary/secondary/ghost styles work
- [ ] Buttons: loading state displays
- [ ] Forms: inline validation works
- [ ] Forms: password reveal toggle works
- [ ] Forms: floating labels animate
- [ ] Cards: hover effects work
- [ ] Tables: zebra striping displays
- [ ] Tables: sticky header on scroll
- [ ] Modals: open/close smoothly
- [ ] Modals: backdrop dismisses
- [ ] Toasts: success/error/info display
- [ ] Toasts: auto-dismiss after 5 seconds

#### Dark Mode
- [ ] Dark mode toggle button visible
- [ ] Toggle switches themes
- [ ] Preference saved to localStorage
- [ ] All colors inverted appropriately
- [ ] Images visibility maintained
- [ ] Contrast ratios acceptable

#### Responsive Design
- [ ] Mobile (< 640px): Layout adapts
- [ ] Mobile: Hamburger menu works
- [ ] Mobile: Touch targets sized appropriately
- [ ] Tablet (640-1024px): Layout optimized
- [ ] Desktop (> 1024px): Full layout
- [ ] Images responsive
- [ ] Tables scroll horizontally on mobile

#### Accessibility
- [ ] Focus states visible
- [ ] Tab navigation works
- [ ] ARIA labels present
- [ ] Alt text on images
- [ ] Color contrast sufficient (WCAG AA)
- [ ] Forms associated with labels
- [ ] Error messages announced

#### Interactions
- [ ] Sticky header on scroll
- [ ] Mobile drawer menu opens/closes
- [ ] Dropdown menus work
- [ ] Countdown timers update in real-time
- [ ] Skeleton loaders show while loading
- [ ] Lazy loading images works
- [ ] Scroll reveal animations smooth
- [ ] Hover states on interactive elements

### ✅ Performance

#### Page Load
- [ ] Homepage loads < 3 seconds
- [ ] Auction detail page loads < 2 seconds
- [ ] Search results load < 2 seconds
- [ ] Dashboard loads < 2 seconds
- [ ] Admin panel loads < 3 seconds

#### Optimization
- [ ] Images compressed
- [ ] CSS minified
- [ ] JavaScript minified
- [ ] Gzip compression enabled
- [ ] Browser caching configured
- [ ] Database queries optimized (< 50ms)
- [ ] No N+1 query problems

### ✅ Email System

#### Development Mode
- [ ] Emails logged to /logs/auth.log
- [ ] Log entries readable
- [ ] Codes/tokens visible in logs
- [ ] Dev mode warning displayed

#### Production Mode
- [ ] SMTP connection succeeds
- [ ] Verification emails delivered
- [ ] Magic code emails delivered
- [ ] Password reset emails delivered
- [ ] Notification emails delivered
- [ ] Email templates render correctly
- [ ] Unsubscribe links work

### ✅ File Uploads

#### Image Upload
- [ ] File type validation works
- [ ] File size validation works (max 5MB)
- [ ] Multiple files upload (max 8)
- [ ] Thumbnails generated automatically
- [ ] Original images preserved
- [ ] Upload progress indicated
- [ ] Failed uploads show error

#### File Security
- [ ] Uploaded files not executable
- [ ] MIME type validated
- [ ] Filename sanitized
- [ ] Files stored outside web root (or protected)

### ✅ Database

#### Schema
- [ ] All tables created
- [ ] All indexes created
- [ ] Foreign keys enforced
- [ ] Triggers working
- [ ] Default data inserted

#### Backups
- [ ] Backup script works
- [ ] Automated backups run
- [ ] Restore process tested
- [ ] Backup files compressed

## Production Deployment

### ✅ Server Configuration

- [ ] PHP version 7.4+ confirmed
- [ ] MySQL version 5.7+ confirmed
- [ ] Required PHP extensions installed (pdo, pdo_mysql, gd, curl, mbstring)
- [ ] File permissions correct (755 for dirs, 644 for files)
- [ ] Upload directory writable
- [ ] Logs directory writable
- [ ] HTTPS enabled
- [ ] SSL certificate valid

### ✅ Security Hardening

- [ ] `.env` file protected from web access
- [ ] `/app/` directory protected
- [ ] `/logs/` directory protected
- [ ] Directory listing disabled
- [ ] PHP version hidden
- [ ] Error display off in production
- [ ] Error logging enabled
- [ ] Session cookies secure and httponly
- [ ] CORS headers configured (if needed)

### ✅ Monitoring

- [ ] Error logs checked daily
- [ ] Disk space monitored
- [ ] Database size monitored
- [ ] Backup verification automated
- [ ] Uptime monitoring configured
- [ ] Performance monitoring tools installed

## Post-Launch Checklist

### Week 1
- [ ] Monitor error logs daily
- [ ] Check user feedback
- [ ] Fix critical bugs immediately
- [ ] Optimize slow queries
- [ ] Verify backups working

### Month 1
- [ ] Review audit logs for suspicious activity
- [ ] Analyze user behavior and pain points
- [ ] Optimize performance bottlenecks
- [ ] Update documentation
- [ ] Plan feature improvements

## Sign-Off

| Test Category | Status | Tester | Date | Notes |
|--------------|--------|--------|------|-------|
| Installation | ☐ Pass ☐ Fail | | | |
| Authentication | ☐ Pass ☐ Fail | | | |
| Security | ☐ Pass ☐ Fail | | | |
| Auctions | ☐ Pass ☐ Fail | | | |
| Admin | ☐ Pass ☐ Fail | | | |
| UI/UX | ☐ Pass ☐ Fail | | | |
| Performance | ☐ Pass ☐ Fail | | | |

**QA Lead**: _____________________ **Date**: __________

**Project Manager**: _____________________ **Date**: __________

**Approved for Launch**: ☐ Yes ☐ No (see notes)

---

## Notes & Issues

Use this space to document any issues found during testing:

1. 
2. 
3. 

---

**Last Updated**: 2026-02-14
**Version**: 1.0
