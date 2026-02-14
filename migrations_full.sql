-- Huuto Auction Platform - Comprehensive Database Schema
-- Enhanced version with authentication, security, and pro features

-- ============================================================
-- Core Tables (Enhanced from existing)
-- ============================================================

-- Users table (enhanced with OAuth and verification)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) DEFAULT NULL,  -- NULL for OAuth-only users
    full_name VARCHAR(100),
    phone VARCHAR(20),
    google_id VARCHAR(255) DEFAULT NULL,      -- Google OAuth ID
    avatar_url VARCHAR(500) DEFAULT NULL,     -- Profile picture
    email_verified BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'active', 'suspended', 'banned') DEFAULT 'pending',
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    rating_avg DECIMAL(3,2) DEFAULT NULL,     -- Average seller rating
    rating_count INT DEFAULT 0,               -- Number of ratings received
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_google_id (google_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    parent_id INT DEFAULT NULL,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auctions table (enhanced with reserve price, anti-sniping)
CREATE TABLE IF NOT EXISTS auctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    starting_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    current_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    reserve_price DECIMAL(10,2) DEFAULT NULL,       -- Minimum acceptable price
    reserve_met BOOLEAN DEFAULT FALSE,              -- Whether reserve price is met
    buy_now_price DECIMAL(10,2) DEFAULT NULL,      -- Fixed buy-now option
    bid_increment DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NOT NULL,
    original_end_time TIMESTAMP NOT NULL,           -- Track original end for anti-snipe
    anti_snipe_count INT DEFAULT 0,                 -- Number of extensions
    status ENUM('draft', 'active', 'ended', 'cancelled', 'sold') DEFAULT 'draft',
    views INT DEFAULT 0,
    watchers_count INT DEFAULT 0,                   -- Cached count of watchers
    location VARCHAR(200),
    condition_description VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    INDEX idx_end_time (end_time),
    INDEX idx_category (category_id),
    INDEX idx_user (user_id),
    INDEX idx_active_ending (status, end_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auction images table
CREATE TABLE IF NOT EXISTS auction_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255) DEFAULT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    file_size INT DEFAULT 0,                        -- In bytes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    INDEX idx_auction (auction_id),
    INDEX idx_primary (auction_id, is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bids table (enhanced with auto-bidding support)
CREATE TABLE IF NOT EXISTS bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    bid_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_auto_bid BOOLEAN DEFAULT FALSE,
    max_auto_amount DECIMAL(10,2) DEFAULT NULL,     -- For proxy bidding
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_auction (auction_id),
    INDEX idx_user (user_id),
    INDEX idx_amount (amount),
    INDEX idx_bid_time (bid_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Watchlist table
CREATE TABLE IF NOT EXISTS watchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    auction_id INT NOT NULL,
    notify_on_outbid BOOLEAN DEFAULT TRUE,
    notify_on_ending BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_watch (user_id, auction_id),
    INDEX idx_user (user_id),
    INDEX idx_auction (auction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Authentication & Security Tables
-- ============================================================

-- Email verification tokens (for magic codes and verification)
CREATE TABLE IF NOT EXISTS email_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,                       -- NULL for registration codes
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL,                     -- For email verification links
    code VARCHAR(6) DEFAULT NULL,                   -- For 6-digit magic codes
    type ENUM('verification', 'magic_login', 'password_reset') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_code (code),
    INDEX idx_email (email),
    INDEX idx_expires (expires_at),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table (optional, for database-backed sessions)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT DEFAULT NULL,
    data TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login attempts (enhanced rate limiting)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(100) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_agent VARCHAR(255) DEFAULT NULL,
    INDEX idx_ip_email (ip_address, email),
    INDEX idx_attempt_time (attempt_time),
    INDEX idx_ip_time (ip_address, attempt_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rate limiting table (generic rate limiting)
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,               -- IP, user_id, email, etc.
    action_type VARCHAR(50) NOT NULL,               -- 'login', 'bid', 'api', etc.
    attempts INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier_action (identifier, action_type),
    INDEX idx_window (window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CSRF tokens
CREATE TABLE IF NOT EXISTS csrf_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit log (for critical events)
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(50) NOT NULL,                    -- 'login', 'bid_placed', 'listing_created', etc.
    entity_type VARCHAR(50) DEFAULT NULL,           -- 'auction', 'user', 'bid', etc.
    entity_id INT DEFAULT NULL,
    details TEXT DEFAULT NULL,                      -- JSON data
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Pro Feature Tables
-- ============================================================

-- Seller ratings
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    buyer_id INT NOT NULL,
    auction_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT DEFAULT NULL,
    response TEXT DEFAULT NULL,                     -- Seller's response
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rating (auction_id, buyer_id),
    INDEX idx_seller (seller_id),
    INDEX idx_buyer (buyer_id),
    INDEX idx_auction (auction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,                      -- 'outbid', 'won', 'ending_soon', etc.
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved searches
CREATE TABLE IF NOT EXISTS saved_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    query_params TEXT NOT NULL,                     -- JSON encoded search parameters
    notify_new_matches BOOLEAN DEFAULT TRUE,
    last_notified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages (buyer-seller communication)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    auction_id INT DEFAULT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    parent_id INT DEFAULT NULL,                     -- For threading
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES messages(id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    INDEX idx_recipient (recipient_id),
    INDEX idx_auction (auction_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin reports (user reports for moderation)
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT NOT NULL,
    reported_user_id INT DEFAULT NULL,
    auction_id INT DEFAULT NULL,
    reason VARCHAR(50) NOT NULL,                    -- 'spam', 'inappropriate', 'scam', etc.
    description TEXT NOT NULL,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    resolved_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reporter (reporter_id),
    INDEX idx_reported_user (reported_user_id),
    INDEX idx_auction (auction_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Insert Default Categories
-- ============================================================

INSERT IGNORE INTO categories (name, slug, description, icon, sort_order) VALUES
('Maakunnittain', 'maakunnittain', 'Tuotteet maakunnittain', '📍', 1),
('Ajoneuvot ja tarvikkeet', 'ajoneuvot', 'Autot, moottoripyörät ja muut ajoneuvot', '🚗', 2),
('Työkoneet ja raskas kalusto', 'tyokoneet', 'Työkoneet, traktorit ja raskaat ajoneuvot', '🚜', 3),
('Asunnot, mökit, toimitilat ja tontit', 'asunnot', 'Kiinteistöt ja tontit', '🏠', 4),
('Harrastusvälineet ja vapaa-aika', 'harrastus', 'Urheiluvälineet ja harrastustarvikkeet', '⚽', 5),
('Piha ja puutarha', 'piha', 'Puutarhakalusteet ja -työkalut', '🌳', 6),
('Työkalut ja työkalusarjat', 'tyokalut', 'Käsityökalut ja sähkötyökalut', '🔧', 7),
('Rakennustarvikkeet', 'rakennus', 'Rakentamiseen ja remontointiin', '🏗️', 8),
('Sisustaminen ja koti', 'sisustus', 'Huonekalut ja sisustus', '🛋️', 9),
('Elektroniikka', 'elektroniikka', 'Tietokoneet, puhelimet ja elektroniikka', '💻', 10),
('Keräily', 'keraily', 'Keräilyesineet ja antiikki', '🎨', 11),
('Tukkuerät', 'tukkuerat', 'Suuret erät ja tukkumyynnit', '📦', 12),
('Muut', 'muut', 'Muut tuotteet', '📌', 13),
('Perinteiset huutokaupat', 'perinteiset', 'Perinteiset fyysiset huutokaupat', '⚖️', 14),
('Ulosotto', 'ulosotto', 'Ulosoton kohteet', '⚖️', 15),
('Konkurssipesät', 'konkurssi', 'Konkurssipesien myynnit', '💼', 16),
('Puolustusvoimat', 'puolustusvoimat', 'Puolustusvoimien ylijäämäkalusto', '🎖️', 17),
('Metsähallitus', 'metsahallitus', 'Metsähallituksen kohteet', '🌲', 18),
('Rahoitusyhtiöt', 'rahoitus', 'Rahoitusyhtiöiden myynnit', '💰', 19),
('Julkinen sektori', 'julkinen', 'Julkisen sektorin kohteet', '🏛️', 20),
('Päättyvät pian', 'paattyvat', 'Pian päättyvät huutokaupat', '⏰', 21);

-- ============================================================
-- Triggers for maintaining cached counts
-- ============================================================

DELIMITER //

-- Update watchers_count when watchlist changes
CREATE TRIGGER IF NOT EXISTS update_watchers_count_insert
AFTER INSERT ON watchlist
FOR EACH ROW
BEGIN
    UPDATE auctions 
    SET watchers_count = (SELECT COUNT(*) FROM watchlist WHERE auction_id = NEW.auction_id)
    WHERE id = NEW.auction_id;
END//

CREATE TRIGGER IF NOT EXISTS update_watchers_count_delete
AFTER DELETE ON watchlist
FOR EACH ROW
BEGIN
    UPDATE auctions 
    SET watchers_count = (SELECT COUNT(*) FROM watchlist WHERE auction_id = OLD.auction_id)
    WHERE id = OLD.auction_id;
END//

-- Update user rating when new rating is added
CREATE TRIGGER IF NOT EXISTS update_user_rating_insert
AFTER INSERT ON ratings
FOR EACH ROW
BEGIN
    UPDATE users 
    SET rating_avg = (SELECT AVG(rating) FROM ratings WHERE seller_id = NEW.seller_id),
        rating_count = (SELECT COUNT(*) FROM ratings WHERE seller_id = NEW.seller_id)
    WHERE id = NEW.seller_id;
END//

CREATE TRIGGER IF NOT EXISTS update_user_rating_update
AFTER UPDATE ON ratings
FOR EACH ROW
BEGIN
    UPDATE users 
    SET rating_avg = (SELECT AVG(rating) FROM ratings WHERE seller_id = NEW.seller_id),
        rating_count = (SELECT COUNT(*) FROM ratings WHERE seller_id = NEW.seller_id)
    WHERE id = NEW.seller_id;
END//

-- Check reserve price when new bid is placed
CREATE TRIGGER IF NOT EXISTS check_reserve_price
AFTER INSERT ON bids
FOR EACH ROW
BEGIN
    UPDATE auctions 
    SET reserve_met = CASE 
        WHEN reserve_price IS NULL THEN FALSE
        WHEN NEW.amount >= reserve_price THEN TRUE
        ELSE FALSE
    END
    WHERE id = NEW.auction_id;
END//

DELIMITER ;
